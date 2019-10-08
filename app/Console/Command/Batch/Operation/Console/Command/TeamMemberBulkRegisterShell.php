<?php

App::import('Service', 'TeamMemberBulkRegisterService');

/**
 * Class RegisterShell
 */
class TeamMemberBulkRegisterShell extends AppShell
{
    protected $enableOutputLogStartStop = true;

    /**
     * @return ConsoleOptionParser
     */
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
            'dry-run' => [
                'help'    => 'This is dry run.',
                'default' => null,
            ]
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $service = new TeamMemberBulkRegisterService($this->params);

        try {
            $service->execute();
        } catch (Throwable $e) {
            $service->addLog($e->getMessage());
        } finally {
            print_r($service->outputLog());
            if (!$service->isDryRun()) {
                $service->writeResult();
            }
        }
    }
}
