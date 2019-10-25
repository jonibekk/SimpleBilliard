<?php

use Goalous\Exception\TeamMemberBulkRegistration\ExcludeException;
use Goalous\Enum\Model\Team\ServiceUseStatus;

App::uses('Email', 'Model');
App::uses('Team', 'Model');
App::uses('User', 'Model');
App::uses('SendMail', 'Model');
App::uses('SendMailToUser', 'Model');
App::uses('TransactionManager', 'Model');
App::uses('TeamMemberBulkRegisterValidator', 'Validator/Csv');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::import('Model/Csv', 'TeamMemberBulkRegisterAggregate');
App::import('Model/User', 'UserSignUpFromCsv');
App::import('Service/User/Team', 'UserTeamJoiningService');
App::import('Service/User/Circle', 'UserCircleJoiningService');
App::import('Service/User', 'Service');
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

    const COMPLETED_PER_COUNT = 10;

    /** @var int */
    private $teamId;
    /** @var string */
    private $fileName;
    /** @var bool */
    private $dryRun;
    /** @var TeamMemberBulkRegisterAggregate */
    private $aggregate;
    /** @var \Aws\S3\S3Client */
    private $s3Instance;
    /** @var TransactionManager */
    private $TransactionManager;
    /** @var SendMailToUser */
    private $SendMailToUser;
    /** @var SendMail */
    private $SendMail;
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
    /** * @var array */
    private $log = [];

    /**
     * TeamMemberBulkRegisterService constructor.
     * @param int $teamId
     * @param string $fileName
     * @param bool $dryRun
     */
    public function __construct(int $teamId, string $fileName, bool $dryRun) {
        $this->teamId = $teamId;
        $this->fileName = $fileName;
        $this->dryRun = $dryRun;
        $this->aggregate= new TeamMemberBulkRegisterAggregate();
        $this->TransactionManager = ClassRegistry::init("TransactionManager");
        $this->userTeamJoiningService = new UserTeamJoiningService();
        $this->userCircleJoiningService = new UserCircleJoiningService();
        $this->SendMailToUser = ClassRegistry::init('SendMailToUser');
        $this->SendMail = ClassRegistry::init('SendMail');
        $this->User = ClassRegistry::init('User');
        $this->Email = ClassRegistry::init('Email');
        $this->Team = ClassRegistry::init('Team');
        $this->Circle = ClassRegistry::init('Circle');
        $this->s3Instance = AwsClientFactory::createS3ClientForFileStorage();
    }

    /**
     * @param array $emails
     * @return array
     */
    protected function findExistUsersByEmail(array $emails): array
    {
        $result = $this->Email->find('all', [
            'fields' => ['Email.id', 'Email.email', 'Email.user_id', 'User.active_flg'],
            'conditions' => ['Email.email' => $emails, 'Email.del_flg' => false],
            'joins' => [[
                'type' => 'INNER',
                'table' => 'users',
                'alias' => 'User',
                'conditions' => ['Email.user_id = User.id', 'User.del_flg' => false],
            ]]
        ]);

        $emailUserMap = [];
        foreach ($result as $data) {
            $email = Hash::get($data, 'Email.email');
            $emailUserMap[$email] = $data;
        }
        return $emailUserMap;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        try {
            $this->validParameter();

            if ($this->isDryRun()) {
                $this->addLog('[INFO] This is dry-run');
            }

            $teamId = $this->getTeamId();
            $team = $this->getTeamEntity()->findById($teamId);
            $teamName = Hash::get($team, 'Team.name');
            $teamTimezone = Hash::get($team, 'Team.timezone');

            $this->addLog('teamId: ' . $teamId);

            $records = $this->getCsvRecords();
            $this->addLog('Total User Count: ' . count($records));

            $csvEmails = Hash::extract($records, '{n}.email');
            $emailUserMap = $this->findExistUsersByEmail($csvEmails);

            $teamAllCircleId = $this->getTeamAllCircleId();

            $this->executeStart($records, $teamId, $teamName, $teamTimezone, $emailUserMap, $teamAllCircleId);
            $this->addAggregateLog();
            if (!$this->isDryRun()) {
                $this->cleanCache();
                $this->addLog('Cleared cache.');
                $this->writeResult();
            }
        } catch (\Throwable $e) {
            throw new $e($e->getMessage());
        }
    }

    /**
     * @param array $records
     * @param int $teamId
     * @param string $teamName
     * @param float $teamTimezone
     * @param array $emailUserMap
     * @param int $teamAllCircleId
     */
    protected function executeStart(
        array $records,
        int $teamId,
        string $teamName,
        float $teamTimezone,
        array $emailUserMap,
        int $teamAllCircleId
    ) {
        foreach ($records as $index => $record) {
            try {
                $this->validateRecord($record);

                $this->TransactionManager->begin();

                $this->executeRecord(
                    $teamId,
                    $teamName,
                    $teamTimezone,
                    $record['email'],
                    $record['first_name'],
                    $record['last_name'],
                    $record['language'],
                    $record['admin_flg'] === 'on' ? 1 : 0,
                    $emailUserMap,
                    $teamAllCircleId
                );

                if ($this->isDryRun()) {
                    $this->TransactionManager->rollback();
                } else {
                    $this->TransactionManager->commit();
                }
            } catch (\Throwable $e) {
                $this->TransactionManager->rollback();
                if ($e instanceof ExcludeException) {
                    $this->getAggregate()->addExcludedCount();
                } else {
                    $this->getAggregate()->addFailedCount();
                }
                $errorMessage = 'email: ' . $this->convertHiddenEmail($record['email']) . ': ' . $e->getMessage();
                $this->addLog($errorMessage);
            } finally {
                $this->outputCompleteMessage($index + 1);
            }
        }
    }

    /**
     * @param int $completedCount
     */
    protected function outputCompleteMessage(int $completedCount)
    {
        if ($completedCount % self::COMPLETED_PER_COUNT === 0) {
            CakeLog::notice($completedCount . ' completed');
        }
    }

    /**
     * @param array $record
     * @throws Exception
     */
    protected function validateRecord(array $record)
    {
        try {
            TeamMemberBulkRegisterValidator::createDefaultValidator()->validate($record);
        } catch (\Respect\Validation\Exceptions\AllOfException $e) {
            throw new $e('Validation error occurred in csv data.');
        }
    }

    /**
     * @param int $teamId
     * @param string $teamName
     * @param float $teamTimezone
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $language
     * @param int $adminFlg
     * @param array $emailUserMap
     * @param int $teamAllCircleId
     * @throws Exception
     */
    protected function executeRecord(
        int $teamId,
        string $teamName,
        float $teamTimezone,
        string $email,
        string $firstName,
        string $lastName,
        string $language,
        int $adminFlg,
        array $emailUserMap,
        int $teamAllCircleId
    ): void {
        $userData = $emailUserMap[$email] ?? null;
        $isNewUser = $userData === null;
        $password = null;

        if ($isNewUser) {
            $password = $this->randomPassword();
            $hashedPassword = $this->User->generateHash($password);
            $userId = $this->createUser($teamId, $teamTimezone, $email, $hashedPassword, $firstName, $lastName, $language);
        } else {
            $userId = Hash::get($userData, 'Email.user_id');
            if (Hash::get($userData, 'User.active_flg')) {
                $this->updateUserDefaultTeamId($userId, $teamId);
            } else {
                $emailId = Hash::get($userData, 'Email.id');
                $password = $this->randomPassword();
                $hashedPassword = $this->User->generateHash($password);
                $this->updateUser($userId, $emailId, $teamId, $teamTimezone, $hashedPassword, $firstName, $lastName, $language);
            }
        }

        $this->joinTeam($userId, $teamId, $adminFlg);
        $this->joinCircle($userId, $teamId, $teamAllCircleId);

        if (!$this->isDryRun()) {
            $this->executeMailSend($email, $userId, $password, $teamId, $teamName, $language);
        }

        if ($isNewUser) {
            $this->getAggregate()->addSuccessCount();
            $this->getAggregate()->addNewUserCount();
        } else {
            $this->getAggregate()->addSuccessCount();
            $this->getAggregate()->addExistUserCount();
        }
    }

    /**
     * @param string $email
     * @param int $userId
     * @param string|null $password
     * @param int $teamId
     * @param string $teamName
     * @param string $language
     * @throws Exception
     */
    protected function executeMailSend(
        string $email,
        int $userId,
        ?string $password,
        int $teamId,
        string $teamName,
        string $language
    ): void {
        $item = [
            'teamName' => $teamName,
            'email' => $email,
            'password' => $password,
            'url' => 'https://' . ENV_NAME . '.goalous.com/users/agree_and_login?team_id=' . $teamId
        ];

        $newSendMailId = $this->createSendMailData($userId, $teamId, json_encode($item));

        $this->sendMail($email, $item, $teamName, $language);

        $this->updateSendMailDataTime($newSendMailId);
    }

    /**
     * @param string $email
     * @param array $viewVars
     * @param string $teamName
     * @param string $language
     */
    private function sendMail(string $email, array $viewVars, string $teamName, string $language): void
    {
        Configure::write('Config.language', $language);
        $this->SendMail->_setTemplateSubject();
        $options = SendMail::$TYPE_TMPL[SendMail::TYPE_TMPL_TEAM_MEMBER_BULK_REGISTRATION];
        $subject = '[' . $teamName . '] ' . __('Invitation for team');

        $config = (ENV_NAME === 'local') ? 'default' : 'amazon';
        $Email = new CakeEmail($config);

        $Email->config(['log' => false])
            ->to($email)
            ->subject($subject)
            ->template($options['template'], $options['layout'])
            ->viewVars($viewVars)
            ->send();
        $Email->reset();
    }

    /**
     * @param int $userId
     * @param int $teamId
     * @param string $jsonEncodedItem
     * @return int
     * @throws Exception
     */
    private function createSendMailData(int $userId, int $teamId, string $jsonEncodedItem): int
    {
        $this->SendMail->create();
        $result = $this->SendMail->save([
            'team_id' => $teamId,
            'template_type' => SendMail::TYPE_TMPL_TEAM_MEMBER_BULK_REGISTRATION,
            'item' => $jsonEncodedItem
        ]);
        if (!$result) {
            throw new \RuntimeException('Failed to save send_mails data.');
        }
        $newSendMailId = $this->SendMail->id;

        $this->SendMailToUser->create();
        $result = $this->SendMailToUser->save([
            'user_id'      => $userId,
            'send_mail_id' => $newSendMailId,
            'team_id'      => $teamId,
        ]);
        if (!$result) {
            throw new \RuntimeException('Failed to save send_mail_to_users data.');
        }

        return $newSendMailId;
    }

    /**
     * @param int $sendMailId
     * @throws Exception
     */
    private function updateSendMailDataTime(int $sendMailId): void
    {
        $this->SendMail->id = $sendMailId;
        $result = $this->SendMail->save(['sent_datetime' => REQUEST_TIMESTAMP]);
        if (!$result) {
            throw new \RuntimeException('Failed to update sent_datetime.');
        }
    }

    /**
     * @param int $userId
     * @param string $emailId
     * @param int $teamId
     * @param float $teamTimezone
     * @param string $hashedPassword
     * @param string $firstName
     * @param string $lastName
     * @param string $language
     * @throws Exception
     */
    protected function updateUser(
        int $userId,
        string $emailId,
        int $teamId,
        float $teamTimezone,
        string $hashedPassword,
        string $firstName,
        string $lastName,
        string $language
    ) {
        $this->Email->id = $emailId;
        $this->Email->saveField('email_verified', self::EMAIL_VERIFIED_YES);

        $this->User->id = $userId;
        $this->User->set([
            'primary_email_id' => $emailId,
            'default_team_id' => $teamId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => $hashedPassword,
            'update_email_flg' => self::UPDATE_EMAIL_FLG_YES,
            'timezone' => $teamTimezone,
            'language' => $language,
            'active_flg' => self::ACTIVE_FLG_YES,
        ]);

        $this->User->save();
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
        return implode("\n", $this->getlog()) . "\n";
    }

    /**
     * @return void
     */
    protected function addAggregateLog(): void
    {
        foreach ($this->getAggregateLog() as $log) {
            $this->addLog($log);
        }
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * @return void
     */
    protected function writeResult(): void
    {
        $this->s3Instance->putObject([
            'Bucket' => $this->getBucketName(),
            'Key' => self::CSV_LOG_PREFIX . $this->getLogFilename(),
            'Body' => $this->outputLog()
        ]);
    }

    /**
     * @return TeamMemberBulkRegisterAggregate
     */
    protected function getAggregate(): TeamMemberBulkRegisterAggregate
    {
        return $this->aggregate;
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
    protected function getAggregateLog(): array
    {
        return [
            'Success: ' . $this->getAggregate()->getSuccessCount(),
            'New User: ' . $this->getAggregate()->getNewUserCount(),
            'Exist User: ' . $this->getAggregate()->getExistUserCount(),
            'Failed: ' . $this->getAggregate()->getFailedCount(),
            'Excluded: ' . $this->getAggregate()->getExcludedCount()
        ];
    }

    /**
     * @return void
     */
    protected function validParameter(): void
    {
        if (empty($this->getFilename())) {
            throw new \RuntimeException('The file_name parameter is required. (--file_name <file_name>)');
        }

        if (!$this->doesBucketExist()) {
            throw new \RuntimeException('The log s3 bucket does not exist. -> ' . $this->getBucketName());
        }

        if (!$this->doesObjectExist()) {
            throw new \RuntimeException('The log s3 csv file does not exist. -> ' . $this->getBucketName() . '/' . $this->getPath());
        }

        $teamId = $this->getTeamId();
        if ($teamId <= 0) {
            throw new \RuntimeException('The team_id parameter is invalid. (--team_id <team_id>)');
        }

        $team = $this->Team->findById($teamId);
        if (!$team) {
            throw new \RuntimeException('Target team ID does not exist.');
        }

        $serviceUseStatus = Hash::get($team, 'Team.service_use_status');
        if (!$this->canCsvTeamMemberBulkRegistration($serviceUseStatus)) {
            throw new \RuntimeException('The target team is a plan in which users cannot be registered in bulk.');
        }

        $teamName = Hash::get($team, 'Team.name');
        if ($this->confirmTeamName($teamName) !== 'yes') {
            throw new \RuntimeException('Exit script.');
        }
    }

    /**
     * @return array
     */
    protected function getCsvRecords(): array
    {
        $reader = new S3Reader($this->getBucketName(), $this->getPath());
        $reader->setHeader(self::CSV_HEADERS);

        return $reader->getRecords();
    }

    /**
     * @return bool
     */
    protected function doesBucketExist(): bool
    {
        return $this->getS3Instance()->doesBucketExist($this->getBucketName());
    }

    /**
     * @return bool
     */
    protected function doesObjectExist(): bool
    {
       return $this->getS3Instance()->doesObjectExist($this->getBucketName(), $this->getPath());
    }

    /**
     * @return int
     */
    protected function getTeamAllCircleId(): int
    {
        $circle = $this->Circle->getTeamAllCircle($this->getTeamId());
        return Hash::get($circle, 'Circle.id');
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
    protected function getFilename(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return self::CSV_PATH_PREFIX . $this->fileName;
    }

    /**
     * @param int $teamId
     * @param float $teamTimezone
     * @param string $email
     * @param string $hashedPassword
     * @param string $firstName
     * @param string $lastName
     * @param string $language
     * @return int|mixed|null
     * @throws Exception
     */
    protected function createUser(
        int $teamId,
        float $teamTimezone,
        string $email,
        string $hashedPassword,
        string $firstName,
        string $lastName,
        string $language
    ) {
        $this->User->create();
        $user = $this->User->save([
            'default_team_id' => $teamId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'password' => $hashedPassword,
            'update_email_flg' => self::UPDATE_EMAIL_FLG_YES,
            'timezone' => $teamTimezone,
            'language' => $language,
            'active_flg' => self::ACTIVE_FLG_YES,
        ]);

        $userId = Hash::get($user, 'User.id');
        $this->Email->create();
        $email = $this->Email->save([
            'user_id' => $userId,
            'email' => $email,
            'email_verified' => self::EMAIL_VERIFIED_YES
        ]);

        $primaryEmailId = Hash::get($email, 'Email.id');
        $this->User->save(['User' => ['primary_email_id' => $primaryEmailId]]);

        return $userId;
    }

    /**
     * @param int $userId
     * @param int $teamId
     */
    protected function updateUserDefaultTeamId(int $userId, int $teamId)
    {
        $this->User->id = $userId;
        $this->User->saveField('default_team_id', $teamId);
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
            throw new ExcludeException('Already registered as a team member.');
        }

        $result = !!$this->userTeamJoiningService->addMember($userId, $teamId, $adminFlg);
        if ($result === false) {
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
            throw new ExcludeException('Already registered as a circle member.');
        }

        $result = !!$this->userCircleJoiningService->addMember($userId, $teamId, $circleId);

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
        $numberDigits = rand(1, self::PASSWORD_MAX_NUMBER_DIGITS);
        $strDigits = self::PASSWORD_LENGTH - $numberDigits;

        $str = substr(str_shuffle('abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'), 0, $strDigits);
        $number = substr(str_shuffle('123456789'), 0, $numberDigits);

        return str_shuffle($str . $number);
    }

    /**
     * @return Team
     */
    protected function getTeamEntity(): Team
    {
        return $this->Team;
    }

    /**
     * @return string
     */
    protected function getLogFilename(): string
    {
        return date('YmdHis') . '_' . basename($this->getFilename(), '.csv') . '.log';
    }

    /**
     * @return string
     */
    protected function getBucketName(): string
    {
        return sprintf(self::BUCKET_NAME_FORMAT, ENV_NAME);
    }

    /**
     * @return \Aws\S3\S3Client
     */
    private function getS3Instance(): \Aws\S3\S3Client
    {
        return $this->s3Instance;
    }

    /**
     * @return void
     */
    private function cleanCache(): void
    {
        $ignore_configs = [
            'session',
            'default',
        ];

        $config_list = Cache::configured();
        foreach ($config_list as $value) {
            if (in_array($value, $ignore_configs)) {
                continue;
            }
            Cache::clear(false, $value);
        }
        clearCache();
    }
}
