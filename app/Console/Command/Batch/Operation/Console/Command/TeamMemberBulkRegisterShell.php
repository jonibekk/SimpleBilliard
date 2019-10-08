<?php

App::import('Service', 'TeamMemberBulkRegisterService');

/**
 * Class RegisterShell
 */
class TeamMemberBulkRegisterShell extends AppShell
{
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
        $teamId = (int) Hash::get($this->params, 'team_id');
        $path = Hash::get($this->params, 'path');
        $dryRun = array_key_exists('dry-run', $this->params);
        $service = new TeamMemberBulkRegisterService($teamId, $path, $dryRun);

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
