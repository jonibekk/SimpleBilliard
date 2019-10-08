<?php

use Goalous\Enum\Model\Team\ServiceUseStatus;

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
App::import('Model/Csv', 'TeamMemberBulkRegisterAggregate');
App::import('Model/User', 'UserSignUpFromCsv');
App::import('Service/User/Team', 'UserTeamJoiningService');
App::import('Service/User/Circle', 'UserCircleJoiningService');
App::import('Service/User', 'UserRegistererService');
App::import('Lib/Csv', 'S3Reader');

class TeamMemberBulkRegisterService
{
    const BUCKET_NAME_FORMAT = 'goalous-%s-csv-bulk-registration';
    const CSV_HEADERS = ['email', 'first_name', 'last_name', 'admin_flg', 'language'];
    const CSV_PATH_PREFIX = 'csv/';
    const CSV_LOG_PREFIX = 'log/';

    const ACTIVE_FLG_YES = 1;
    const EMAIL_VERIFIED_YES = 1;
    const UPDATE_EMAIL_FLG_YES = 1;

    const PASSWORD_LENGTH = 8;
    const PASSWORD_MAX_NUMBER_DIGITS = 4;

    /** @var int */
    private $teamId;
    /** @var string */
    private $path;
    /** @var bool */
    private $dryRun;
    /** @var TeamMemberBulkRegister */
    private $registerModel;
    /** @var TeamMemberBulkRegisterAggregate */
    private $aggregateModel;
    /** @var \Aws\S3\S3Client */
    private $s3Instance;
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
    private $userTeamJoiningService;
    /** @var UserCircleJoiningService */
    private $userCircleJoiningService;
    /** @var UserRegistererService */
    private $registererService;
    /** @var GlEmailComponent */
    private $GlEmail;
    /** * @var array */
    private $log = [];

    /**
     * TeamMemberBulkRegisterService constructor.
     * @param int $teamId
     * @param string $path
     * @param bool $dryRun
     */
    public function __construct(int $teamId, string $path, bool $dryRun) {
        $this->teamId = $teamId;
        $this->path = $path;
        $this->dryRun = $dryRun;
        $this->registerModel = new TeamMemberBulkRegister();
        $this->aggregateModel = new TeamMemberBulkRegisterAggregate();
        $this->TransactionManager = ClassRegistry::init("TransactionManager");
        $this->userTeamJoiningService = new UserTeamJoiningService();
        $this->userCircleJoiningService = new UserCircleJoiningService();
        $this->registererService = new UserRegistererService();
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
        $this->Team = ClassRegistry::init('Team');
        $this->Circle = ClassRegistry::init('Circle');
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
        $this->s3Instance = AwsClientFactory::createS3ClientForFileStorage();
    }

    /**
     * @return TeamMemberBulkRegisterService
     */
    public function execute(): self
    {
        $this->initialize();

        $teamId = $this->getTeamId();
        foreach ($this->getRegisterModel()->getRecords() as $record) {
            try {
                try {
                    TeamMemberBulkRegisterValidator::createDefaultValidator()->validate($record);
                } catch (\Respect\Validation\Exceptions\AllOfException $e) {
                    throw new $e('Validation error occurred in csv data.');
                }

                $this->TransactionManager->begin();

                $this->executeRecord($teamId, $record);

                if ($this->isDryRun()) {
                    $this->TransactionManager->rollback();
                } else {
                    $this->TransactionManager->commit();
                }
            } catch (\Throwable $e) {
                $this->TransactionManager->rollback();
                $errorMessage = 'email: ' . $this->convertHiddenEmail($record['email']) . ': ' . $e->getMessage();
                $this->addLog($errorMessage);
            }
        }

        return $this;
    }

    /**
     * @param string $email
     * @return string
     */
    protected function convertHiddenEmail(string $email): string
    {
        $prefix = substr($email, 0, 4);
        $otherCount = count(str_split($email)) - 4;
        if ($otherCount < 0) {
            $otherCount = 0;
        }

        return $prefix . str_repeat('*', $otherCount);
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
        return implode("\n", array_merge($this->getlog(), $this->getAggregateLog())) . "\n";
    }

    /**
     * @return array
     */
    protected function getAggregateLog(): array
    {
        return [
            'Total User Count: ' . count($this->getCsvRecords()),
            'Success: ' . $this->getAggregateModel()->getSuccessCount(),
            'New User: ' . $this->getAggregateModel()->getNewUserCount(),
            'Exist User: ' . $this->getAggregateModel()->getExistUserCount(),
            'Failed: ' . $this->getAggregateModel()->getFailedCount(),
            'Excluded: ' . $this->getAggregateModel()->getExcludedCount()
        ];
    }

    /**
     * @return void
     */
    public function writeResult(): void
    {
        $this->s3Instance->putObject([
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
        if ($this->isDryRun()) {
            $this->addLog('[INFO] This is dry-run');
        }

        $this->validateLogStorageLocation();

        $teamId = $this->getTeamId();
        $team = $this->getTeamEntity()->findById($teamId);

        $this->validateTeam($team);

        $this->addLog('teamId: ' . $teamId);

        $this->extractNecessaryData($team);
    }

    /**
     * @param array $team
     */
    protected function extractNecessaryData(array $team): void
    {
        $records = $this->getCsvRecords();

        $csvEmails = Hash::extract($records, '{n}.email');
        $existUsers = $this->getEmailEntity()->findExistUsersByEmail($csvEmails);

        $this->getRegisterModel()
            ->setTeam($team)
            ->setRecords($records)
            ->setExistUsers($existUsers)
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

        $serviceUseStatus = Hash::get($team, 'Team.service_use_status');
        if (!$this->canCsvTeamMemberBulkRegistration($serviceUseStatus)) {
            throw new \RuntimeException('The target team is a plan in which users cannot be registered in bulk.');
        }

        $teamName = Hash::get($team, 'Team.name');
        if ($this->confirmTeamName($teamName) !== 'yes') {
            throw new \RuntimeException('Stop processing');
        }
    }

    /**
     * @param int $serviceUserStatus
     * @return bool
     */
    protected function canCsvTeamMemberBulkRegistration(int $serviceUserStatus)
    {
        return $serviceUserStatus === ServiceUseStatus::FREE_TRIAL;
    }

    protected function confirmTeamName(string $teamName): string
    {
        echo "Is the target team name [\033[0;32m{$teamName}\033[0m] ? (yes/no) ";
        return trim(fgets(STDIN));
    }

    /**
     * @return int
     */
    protected function getTeamId(): int
    {
        return $this->teamId;
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return self::CSV_PATH_PREFIX . $this->path;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * @param int $teamId
     * @param array $record
     * @throws Exception
     */
    protected function executeRecord(int $teamId, array $record): void
    {
        $email = $record['email'];
        $language = $record['language'];
        $password = null;

        $userId = $this->getRegisterModel()->getExistUserId($email);

        $isNewUser = $userId === null;
        if ($isNewUser) {
            $signUpModel = $this->createUserSignUpFromCsvModel($record);
            $password = $signUpModel->getPassword();
            $userId = $this->getRegisterer()->signUpFromCsv($signUpModel);
        }

        $adminFlg = $record['admin_flg'] === 'on' ? 1 : 0;
        $this->joinTeam($userId, $teamId, $adminFlg);
        $this->joinCircle($userId, $teamId, $this->getTeamAllCircleId());

        if (!$this->isDryRun()) {
            $this->getGlMailComponent()->sendMailTeamMemberBulkRegistration(
                $userId,
                $this->getRegisterModel()->getTeamId(),
                $this->getRegisterModel()->getTeamName(),
                $language,
                $email,
                $password
            );
        }

        if ($isNewUser) {
            $this->getAggregateModel()->addSuccessCount();
            $this->getAggregateModel()->addNewUserCount();
        } else {
            $this->getAggregateModel()->addSuccessCount();
            $this->getAggregateModel()->addExistUserCount();
        }
    }

    /**
     * @param array $record
     * @return UserSignUpFromCsv
     */
    protected function createUserSignUpFromCsvModel(array $record): UserSignUpFromCsv
    {
        $useSignUpFromCsv = new UserSignUpFromCsv();
        $useSignUpFromCsv->setFirstName($record['first_name']);
        $useSignUpFromCsv->setLastName($record['last_name']);
        $useSignUpFromCsv->setLanguage($record['language']);
        $useSignUpFromCsv->setDefaultTeamId($this->getRegisterModel()->getTeamId());
        $useSignUpFromCsv->setPassword($this->randomPassword());
        $useSignUpFromCsv->setUpdateEmailFlg(self::UPDATE_EMAIL_FLG_YES);
        $useSignUpFromCsv->setTimezone($this->getRegisterModel()->getTeamTimezone());
        $useSignUpFromCsv->setAgreedTermsOfServiceId($this->getRegisterModel()->getAgreedTermsOfServiceId());
        $useSignUpFromCsv->setActiveFlg(self::ACTIVE_FLG_YES);
        $useSignUpFromCsv->setEmail($record['email']);
        $useSignUpFromCsv->setEmailVerified(self::EMAIL_VERIFIED_YES);

        return $useSignUpFromCsv;
    }

    /**
     * @param int $userId
     * @param int $teamId
     * @param bool $adminFlg
     * @return TeamMemberBulkRegisterService
     * @throws Exception
     */
    protected function joinTeam(int $userId, int $teamId, bool $adminFlg): self
    {
        if ($this->userTeamJoiningService->isJoined($userId, $teamId)) {
            $this->getAggregateModel()->addExcludedCount();
            throw new \RuntimeException('Already registered as a team member.');
        }

        $result = !!$this->userTeamJoiningService->addMember($userId, $teamId, $adminFlg);
        if ($result === false) {
            $this->getAggregateModel()->addFailedCount();
            throw new \RuntimeException('Failed to add member to team.');
        }

        return $this;
    }

    /**
     * @param int $userId
     * @param int $teamId
     * @param int $circleId
     * @return TeamMemberBulkRegisterService
     * @throws Exception
     */
    protected function joinCircle(int $userId, int $teamId, int $circleId): self
    {
        if ($this->userCircleJoiningService->isJoined($circleId, $userId)) {
            $this->getAggregateModel()->addExcludedCount();
            throw new \RuntimeException('Already registered as a circle member.');
        }

        $result = !!$this->userCircleJoiningService->addMember([
            'circle_id' => $circleId,
            'team_id' => $teamId,
            'user_id' => $userId
        ]);

        if ($result === false) {
            $this->getAggregateModel()->addFailedCount();
            throw new \RuntimeException('Failed to add member to circle.');
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function randomPassword(): string
    {
        $numberDigits = rand(1, self::PASSWORD_MAX_NUMBER_DIGITS);
        $strDigits = self::PASSWORD_LENGTH - $numberDigits;

        $str = substr(str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $strDigits);
        $number = substr(str_shuffle('123456789'), 0, $numberDigits);

        return str_shuffle($str . $number);
    }

    /**
     * @return TeamMemberBulkRegister
     */
    protected function getRegisterModel(): TeamMemberBulkRegister
    {
        return $this->registerModel;
    }

    /**
     * @return UserRegistererService
     */
    protected function getRegisterer(): UserRegistererService
    {
        return $this->registererService;
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
        if (!$this->s3Instance->doesBucketExist($this->getBucketName())) {
            throw new \RuntimeException('The log s3 bucket does not exist. -> ' . $this->getBucketName());
        }
    }

    /**
     * @return string
     */
    protected function getLogFilename(): string
    {
        return date('Ymd_His.u') . '.log';
    }

    /**
     * @return string
     */
    protected function getBucketName(): string
    {
        return sprintf(self::BUCKET_NAME_FORMAT, ENV_NAME);
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    protected function getAggregateModel(): TeamMemberBulkRegisterAggregate
    {
        return $this->aggregateModel;
    }
}
