<?php

use Goalous\Enum\Model\Team\ServiceUseStatus;
use Goalous\Enum\S3Bucket\TeamMemberBulkRegistrationBucketName;

App::import('Lib/Csv', 'S3Reader');
App::import('Model/Csv', 'TeamMemberBulkRegister');
App::import('Service', 'TeamMemberBulkRegisterService');

/**
 * Class RegisterShell
 */
class TeamMemberBulkRegisterShell extends AppShell
{
    const CSV_HEADERS = ['email', 'first_name', 'last_name', 'admin_flg', 'language'];
    const CSV_PATH_PREFIX = 'csv/';
    const CSV_LOG_PREFIX = 'log/';

    public $uses = [
        'User',
        'Email',
        'Team',
        'Circle'
    ];

    /** @var \Aws\S3\S3Client */
    private $s3_instance;

    public function startup()
    {
        parent::startup();
        $this->s3_instance = AwsClientFactory::createS3ClientForFileStorage();
    }

    /** @var array */
    private $log = [];

    protected $enableOutputLogStartStop = true;

    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $options = [
            'team_id' => [
                'short'   => 't',
                'help'    => 'This is target team id.',
                'default' => null,
            ],
            'path' => [
                'short'   => 'p',
                'help'    => 'This is csv file path.',
                'default' => '',
            ],
            'dry_run' => [
                'help'    => 'This is dry run.',
                'default' => null,
            ]
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $this->validateLogStorageLocation();

        try {
            $team_id = $this->params['team_id'] ?? null;
            $path = self::CSV_PATH_PREFIX . $this->params['path'];

            $this->validateTeamId($team_id);

            $team_id = (int) $team_id;
            $team = $this->Team->findById($team_id);
            $this->validateTeam($team);
            $this->addLog('teamId: ' . $team_id);

            $reader = (new S3Reader($this->getBucketName(), $path))->setHeader(self::CSV_HEADERS);
            $records = $reader->getRecords();
            $this->addLog('CSV rows: ' . count($records));

            $agreed_terms_of_service_id = Hash::get($this->User->TermsOfService->getCurrent(), 'id');

            $csv_emails = Hash::extract($records, '{n}.email');
            $exist_users = $this->Email->findExistUsersByEmail($csv_emails);

            $exist_user_count = count($exist_users);
            $new_count = count($csv_emails) - $exist_user_count;

            $this->addLog('New user: ' . $new_count);
            $this->addLog('Exist user: ' . $exist_user_count);

            $circle = $this->Circle->getTeamAllCircle($team_id);
            $circle_id = Hash::get($circle, 'Circle.id');

            $register_model = new TeamMemberBulkRegister();
            $register_model->setTeam($team)
                ->setDryRun($this->isDryRun())
                ->setRecords($records)
                ->setExistUsers($exist_users)
                ->setAgreedTermsOfServiceId($agreed_terms_of_service_id)
                ->setTeamAllCircleId($circle_id);

            $service = new TeamMemberBulkRegisterService($register_model);

            $this->mergeLog($service->execute()->getLog());
        } catch (Throwable $e) {
            $this->addLog($e->getMessage());
            $this->addLog($e->getTraceAsString());
        } finally {
            print_r($this->outputLog());
            if (!$this->isDryRun()) {
                $this->writeResult($this->outputLog());
            }
        }
    }

    /**
     * @param $team_id
     */
    private function validateTeamId($team_id): void
    {
        if (is_null($team_id)) {
            throw new \RuntimeException('Team id parameter does not exist.');
        }

        if (filter_var($team_id, FILTER_VALIDATE_INT) === false || $team_id <= 0) {
            throw new \RuntimeException('The team id is invalid.');
        }
    }

    /**
     * @param array|null $team
     */
    private function validateTeam(?array $team): void
    {
        if ($team === null) {
            throw new \RuntimeException('Target team ID does not exist.');
        }

        $team_name = Hash::get($team, 'Team.name');
        if ($this->confirmTeamName($team_name) !== 'yes') {
            throw new \RuntimeException('Stop processing');
        }

        $service_use_status = Hash::get($team, 'Team.service_use_status');
        if (!$this->canCstTeamMemberBulkRegistration($service_use_status)) {
            throw new \RuntimeException('The target team is a plan in which users cannot be registered in bulk.');
        }
    }

    /**
     * @param int $service_user_status
     * @return bool
     */
    private function canCstTeamMemberBulkRegistration(int $service_user_status)
    {
        return $service_user_status === ServiceUseStatus::FREE_TRIAL;
    }

    private function confirmTeamName(string $team_name): string
    {
        echo "Is the target team name [\033[0;32m{$team_name}\033[0m] ? (yes/no) ";
        return trim(fgets(STDIN));
    }

    /**
     * @return void
     */
    private function validateLogStorageLocation(): void
    {
        if (!$this->s3_instance->doesBucketExist($this->getBucketName())) {
            throw new \RuntimeException('The log s3 bucket does not exist. -> ' . $this->getBucketName());
        }
    }

    /**
     * @return string
     */
    private function getLogFilename(): string
    {
        return ENV_NAME . '_' . date('Ymd_His.u') . '.log';
    }

    /**
     * @return void
     */
    private function writeResult(string $log): void
    {
        $this->s3_instance->putObject([
            'Bucket' => $this->getBucketName(),
            'Key' => self::CSV_LOG_PREFIX . $this->getLogFilename(),
            'Body' => $log
        ]);
    }

    /**
     * @param string $messge
     */
    private function addLog(string $messge): void
    {
        $this->log[] = $messge;
    }

    /**
     * @param array $log
     */
    private function mergeLog(array $log): void
    {
        $this->log = array_merge($this->log, $log);
    }

    /**
     * @return string
     */
    private function outputLog(): string
    {
        return implode("\n", $this->log) . "\n";
    }

    /**
     * @return string
     */
    private function getBucketName(): string
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

    /**
     * bool
     */
    private function isDryRun(): bool
    {
        return array_key_exists('dry_run', $this->params);
    }
}
