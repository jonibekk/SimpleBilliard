<?php
App::uses('AppUtil', 'Util');
App::import('Service', 'TeamService');

/**
 * Batch for changing service status of team.
 * # Description
 * ## Usage
 * - Console/cake change_service_status
 * - Console/cake change_service_status -t [target date]
 * ## changing status in the following order:
 * - Free trial -> Read-only -> Cannot use Service -> Deleted
 * ## UTC or local date?
 * - UTC only
 *
 * @property TeamService $TeamService
 */
class ChangeServiceStatusShell extends AppShell
{
    public $TeamService;

    public function startup()
    {
        parent::startup();
        $this->TeamService = ClassRegistry::init('TeamService');
    }

    /**
     * @return ConsoleOptionParser
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $options = [
            'targetExpireDate' => [
                'short'   => 't',
                'help'    => 'This is target expire date. It automatically will be yesterday UTC as default',
                'default' => AppUtil::dateBefore(date('Y-m-d'), 2),
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        $targetExpireDate = $this->param('targetExpireDate');
        // updating status from Free-trial to Read-only
        $this->TeamService->changeStatusAllTeamExpired(
            $targetExpireDate,
            Team::SERVICE_USE_STATUS_FREE_TRIAL,
            Team::SERVICE_USE_STATUS_READ_ONLY
        );
        // updating status from Read-only to Cannot-use-service
        $this->TeamService->changeStatusAllTeamExpired(
            $targetExpireDate,
            Team::SERVICE_USE_STATUS_READ_ONLY,
            Team::SERVICE_USE_STATUS_CANNOT_USE
        );
        $this->TeamService->deleteTeamCannotUseServiceExpired($targetExpireDate);
        $this->out('finished to change service statuses.');
    }
}
