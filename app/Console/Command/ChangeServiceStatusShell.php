<?php
App::uses('AppUtil', 'Util');

/**
 * Batch for changing service status of team.
 * # Description
 * ## Usage
 * - Console/cake change_service_status
 * - Console/cake change_service_status -t [target date] -c [current timestamp]
 * ## changing status in the following order:
 * - Free trial -> Read-only -> Cannot use Service -> Deleted
 * ## How to decide expire date
 * - fetching teams.service_use_state_start_date + Team::DAYS_SERVICE_USE_STATUS[status_name]
 * ## UTC or local date?
 * - UTC only
 *
 * @property Team $Team
 */
class ChangeServiceStatusShell extends AppShell
{
    public $uses = [
        'Team',
    ];

    public function startup()
    {
        parent::startup();
    }

    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $options = [
            'targetDate'       => [
                'short'   => 't',
                'help'    => 'This is target expire date. It automatically will be yesterday UTC as default',
                'default' => AppUtil::dateYesterday(date('Y-m-d')),
            ],
            'currentTimestamp' => [
                'short'   => 'c',
                'help'    => 'current timestamp',
                'default' => REQUEST_TIMESTAMP
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        debug($this->params);

    }

}
