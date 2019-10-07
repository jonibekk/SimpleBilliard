<?php

use Goalous\Enum\Model\Team\ServiceUseStatus;
use Goalous\Enum\S3Bucket\TeamMemberBulkRegistrationBucketName;

App::uses('User', 'Model');
App::uses('Email', 'Model');
App::uses('Team', 'Model');
App::uses('TransactionManager', 'Model');
App::uses('TeamMemberBulkRegisterValidator', 'Validator/Csv');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');
App::import('Model/Csv', 'TeamMemberBulkRegister');
App::import('Model/User', 'UserSignUpFromCsv');
App::import('Service/User/Team', 'UserTeamJoiningService');
App::import('Service/User/Circle', 'UserCircleJoiningService');
App::import('Service/User', 'UserRegistererService');
App::import('Lib/Csv', 'S3Reader');

class TeamMemberBulkRegisterService
{
    const CSV_HEADERS = ['email', 'first_name', 'last_name', 'admin_flg', 'language'];
    const CSV_PATH_PREFIX = 'csv/';
    const CSV_LOG_PREFIX = 'log/';

    const ACTIVE_FLG_YES = 1;
    const EMAIL_VERIFIED_YES = 1;
    const UPDATE_EMAIL_FLG_YES = 1;

    /** @var array */
    private $params;
    /** @var TeamMemberBulkRegister */
    private $register_model;
    /** @var \Aws\S3\S3Client */
    private $s3_instance;
    /** @var TransactionManager */
    private $TransactionManager;
    /** @var User */
    private $User;
    /** @var Email */
    private $Email;
    /** @var Team */
    private $Team;
    /** @var Circle */
    private $Circle;
    /** @var UserTeamJoiningService */
    private $user_team_joining_service;
    /** @var UserCircleJoiningService */
    private $user_circle_joining_service;
    /** @var UserRegistererService */
    private $registerer_service;
    /** @var GlEmailComponent */
    private $GlEmail;
    /** * @var array */
    private $log = [];

    /**
     * TeamMemberBulkRegisterService constructor.
     * @param array $params
     */
    public function __construct(array $params) {
        $this->params = $params;
        $this->register_model = new TeamMemberBulkRegister();
        $this->TransactionManager = ClassRegistry::init("TransactionManager");
        $this->user_team_joining_service = new UserTeamJoiningService();
        $this->user_circle_joining_service = new UserCircleJoiningService();
        $this->registerer_service = new UserRegistererService();
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
        $this->Team = ClassRegistry::init('Team');
        $this->Circle = ClassRegistry::init('Circle');
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
        $this->s3_instance = AwsClientFactory::createS3ClientForFileStorage();
    }

    /**
     * @return TeamMemberBulkRegisterService
     */
    public function execute(): self
    {
        $this->initialize();

        foreach ($this->getRegisterModel()->getRecords() as $record) {
            try {
                $this->addLog(str_repeat('-=', 40));
                $this->TransactionManager->begin();

                $this->executeRecord($record);

                if ($this->isDryRun()) {
                    $this->TransactionManager->rollback();
                } else {
                    $this->TransactionManager->commit();
                }

                $this->addLog('Succeeded.');
            } catch (\Throwable $e) {
                $this->TransactionManager->rollback();
                $this->addLog('Failed. ' . $e->getMessage());
                $this->addLog($e->getTraceAsString());
            } finally {
                $this->addRecordLog($record);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * @param string $message
     */
    public function addLog(string $message): void
    {
        $this->log[] = $message;
    }

    /**
     * @return string
     */
    public function outputLog(): string
    {
        return implode("\n", $this->log) . "\n";
    }

    /**
     * @return void
     */
    public function writeResult(): void
    {
        $this->s3_instance->putObject([
            'Bucket' => $this->getBucketName(),
            'Key' => self::CSV_LOG_PREFIX . $this->getLogFilename(),
            'Body' => $this->outputLog()
        ]);
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        $this->validateLogStorageLocation();

        $team_id = $this->getTeamId();
        $team = $this->getTeamEntity()->findById($team_id);

        $this->validateTeam($team);

        $this->addLog('teamId: ' . $team_id);

        $this->extractNecessaryData($team);
    }

    /**
     * @param array $team
     */
    protected function extractNecessaryData(array $team): void
    {
        $records = $this->getCsvRecords();
        $this->addLog('CSV rows: ' . count($records));

        $csv_emails = Hash::extract($records, '{n}.email');
        $exist_users = $this->getEmailEntity()->findExistUsersByEmail($csv_emails);

        $exist_user_count = count($exist_users);
        $new_count = count($csv_emails) - $exist_user_count;

        $this->addLog('New user: ' . $new_count);
        $this->addLog('Exist user: ' . $exist_user_count);

        $this->getRegisterModel()
            ->setTeam($team)
            ->setRecords($records)
            ->setExistUsers($exist_users)
            ->setAgreedTermsOfServiceId($this->getAgreedTermsOfServiceId())
            ->setTeamAllCircleId($this->getTeamAllCircleId());
    }

    protected function getCsvRecords()
    {
        $reader = new S3Reader($this->getBucketName(), $this->getPath());
        $reader->setHeader(self::CSV_HEADERS);

        return $reader->getRecords();
    }

    /**
     * @return int
     */
    protected function getTeamAllCircleId(): int
    {
        $circle = $this->getCircleEntity()->getTeamAllCircle($this->getTeamId());
        return Hash::get($circle, 'Circle.id');
    }

    /**
     * @return int
     */
    protected function getAgreedTermsOfServiceId(): int
    {
        return Hash::get($this->getUserEntity()->TermsOfService->getCurrent(), 'id');
    }

    /**
     * @param array|null $team
     */
    protected function validateTeam(?array $team)
    {
        if ($team === null) {
            throw new \RuntimeException('Target team ID does not exist.');
        }

        $service_use_status = Hash::get($team, 'Team.service_use_status');
        if (!$this->canCstTeamMemberBulkRegistration($service_use_status)) {
            throw new \RuntimeException('The target team is a plan in which users cannot be registered in bulk.');
        }

        $team_name = Hash::get($team, 'Team.name');
        if ($this->confirmTeamName($team_name) !== 'yes') {
            throw new \RuntimeException('Stop processing');
        }
    }

    /**
     * @param int $service_user_status
     * @return bool
     */
    protected function canCstTeamMemberBulkRegistration(int $service_user_status)
    {
        return $service_user_status === ServiceUseStatus::FREE_TRIAL;
    }

    protected function confirmTeamName(string $team_name): string
    {
        echo "Is the target team name [\033[0;32m{$team_name}\033[0m] ? (yes/no) ";
        return trim(fgets(STDIN));
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return int
     */
    protected function getTeamId(): int
    {
        return (int) Hash::get($this->getParams(), 'team_id');
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return self::CSV_PATH_PREFIX . Hash::get($this->getParams(), 'path');
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return array_key_exists('dry_run', $this->getParams());
    }

    /**
     * @param array $record
     */
    protected function addRecordLog(array $record): void
    {
        foreach ($record as $key => $value) {
            $this->addLog($key . ': ' . $value);
        }
    }

    /**
     * @param array $record
     * @throws Exception
     */
    protected function executeRecord(array $record): void
    {
        TeamMemberBulkRegisterValidator::createDefaultValidator()->validate($record);

        $email = $record['email'];
        $language = $record['language'];
        $password = null;

        $user_id = $this->getRegisterModel()->getExistUserId($email);
        if ($user_id === null) {
            $this->addLog($email . ' is new user.');
            $sign_up_model = $this->createUserSignUpFromCsvModel($record);
            $password = $sign_up_model->getPassword();
            $user_id = $this->getRegisterer()->signUpFromCsv($sign_up_model);
        } else {
            $this->addLog($email . ' is exist user.');
        }

        $admin_flg = $record['admin_flg'] === 'on' ? 1 : 0;
        $this->joinTeam($user_id, $admin_flg)->joinCircle($user_id);

        if (!$this->isDryRun()) {
            $this->getGlMailComponent()->sendMailTeamMemberBulkRegistration(
                $user_id,
                $this->getRegisterModel()->getTeamId(),
                $this->getRegisterModel()->getTeamName(),
                $language,
                $email,
                $password
            );
        }
    }

    /**
     * @param array $record
     * @return UserSignUpFromCsv
     */
    protected function createUserSignUpFromCsvModel(array $record): UserSignUpFromCsv
    {
        return (new UserSignUpFromCsv())->setFirstName($record['first_name'])
            ->setLastName($record['last_name'])
            ->setLanguage($record['language'])
            ->setDefaultTeamId($this->getRegisterModel()->getTeamId())
            ->setPassword($this->randomPassword())
            ->setUpdateEmailFlg(self::UPDATE_EMAIL_FLG_YES)
            ->setTimezone($this->getRegisterModel()->getTeamTimezone())
            ->setAgreedTermsOfServiceId($this->getRegisterModel()->getAgreedTermsOfServiceId())
            ->setActiveFlg(self::ACTIVE_FLG_YES)
            ->setEmail($record['email'])
            ->setEmailVerified(self::EMAIL_VERIFIED_YES);
    }

    /**
     * @param string $user_id
     * @param bool $admin_flg
     * @return TeamMemberBulkRegisterService
     * @throws Exception
     */
    protected function joinTeam(string $user_id, bool $admin_flg): self
    {
        $team_id = $this->getRegisterModel()->getTeamId();
        if ($this->getUserTeamJoiningService()->isJoined($user_id, $team_id)) {
            throw new \RuntimeException('Already registered as a team member.');
        }

        $result = !!$this->getUserTeamJoiningService()->addMember([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'admin_flg' => $admin_flg
        ]);

        if ($result === false) {
            throw new \RuntimeException('Failed to add member to team.');
        }

        return $this;
    }

    /**
     * @param string $user_id
     * @return TeamMemberBulkRegisterService
     * @throws Exception
     */
    protected function joinCircle(string $user_id): self
    {
        $team_id = $this->getRegisterModel()->getTeamId();
        $circle_id = $this->getRegisterModel()->getTeamAllCircleId();
        if ($this->getTeamCircleService()->isJoined($circle_id, $user_id)) {
            throw new \RuntimeException('Already registered as a circle member.');
        }

        $result = !!$this->getTeamCircleService()->addMember([
            'circle_id' => $circle_id,
            'team_id' => $team_id,
            'user_id' => $user_id
        ]);

        if ($result === false) {
            throw new \RuntimeException('Failed to add member to circle.');
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function randomPassword(): string
    {
        $password_length = 8;
        $number_digits = rand(1, 4);
        $str_digits = $password_length - $number_digits;

        $str = substr(str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $str_digits);
        $number = substr(str_shuffle('123456789'), 0, $number_digits);

        return str_shuffle($str . $number);
    }

    /**
     * @return UserTeamJoiningService
     */
    protected function getUserTeamJoiningService(): UserTeamJoiningService
    {
        return $this->user_team_joining_service;
    }

    /**
     * @return UserCircleJoiningService
     */
    protected function getTeamCircleService(): UserCircleJoiningService
    {
        return $this->user_circle_joining_service;
    }

    /**
     * @return TeamMemberBulkRegister
     */
    protected function getRegisterModel(): TeamMemberBulkRegister
    {
        return $this->register_model;
    }

    /**
     * @return UserRegistererService
     */
    protected function getRegisterer(): UserRegistererService
    {
        return $this->registerer_service;
    }

    /**
     * @return Email
     */
    protected function getEmailEntity(): Email
    {
        return $this->Email;
    }

    /**
     * @return User
     */
    protected function getUserEntity(): User
    {
        return $this->User;
    }

    /**
     * @return Team
     */
    protected function getTeamEntity(): Team
    {
        return $this->Team;
    }

    /**
     * @return Circle
     */
    protected function getCircleEntity(): Circle
    {
        return $this->Circle;
    }

    /**
     * @return GlEmailComponent
     */
    protected function getGlMailComponent(): GlEmailComponent
    {
        return $this->GlEmail;
    }

    /**
     * @return void
     */
    protected function validateLogStorageLocation(): void
    {
        if (!$this->s3_instance->doesBucketExist($this->getBucketName())) {
            throw new \RuntimeException('The log s3 bucket does not exist. -> ' . $this->getBucketName());
        }
    }

    /**
     * @return string
     */
    protected function getLogFilename(): string
    {
        return ENV_NAME . '_' . date('Ymd_His.u') . '.log';
    }

    /**
     * @return string
     */
    protected function getBucketName(): string
    {
        switch (ENV_NAME) {
            case 'isao':
                return TeamMemberBulkRegistrationBucketName::ISAO;
            case 'www':
                return TeamMemberBulkRegistrationBucketName::WWW;
            default:
                return TeamMemberBulkRegistrationBucketName::DEV;
        }
    }
}
