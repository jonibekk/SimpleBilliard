<?php
App::uses('AppUtil', 'Util');

/**
 * # Batch processing for sending e-mail of expires alert.
 * ## Target status
 * - 0: free trial
 * - 2: read only
 * ## How to notify?
 * - e-mail
 * ## Execution timing
 * - It's defined in the following.
 *    app/Config/extra_defines.php
 *   EXPIRE_ALERT_NOTIFY_BEFORE_DAYS
 *    Expire alert will be send in specified days before expires. That days should be comma separated.
 *      ex) EXPIRE_ALERT_NOTIFY_BEFORE_DAYS=10,5,3,2,1
 *    ※ The shell will be executed at 10am(JPT)
 * ## Target User
 * - Team Admins
 *   * If user is admin in multiple teams, it will send multiple e-mails.
 * ## Usage
 * Console/cake send_alert_mail_to_admin
 *
 * @property Team       $Team
 * @property TeamMember $TeamMember
 */
class SendAlertMailToAdminShell extends AppShell
{
    public $uses = [
        'Team',
        'TeamMember'
    ];

    public function startup()
    {
        parent::startup();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            'expires' => ['short' => 'e', 'help' => 'Y-m-d', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        $this->sendEmailToFreeTrialTeams();
        $this->sendEmailToReadOnlyTeams();
        $this->sendEmailToExpiredTeams();
        return;
    }

    function sendEmailToFreeTrialTeams()
    {
        $freeTrialTeams = $this->Team->findByServiceUseStatus(Team::SERVICE_USE_STATUS_FREE_TRIAL);
        $targetTeams = $this->filterTargetTeams(Team::DAYS_SERVICE_USE_STATUS_FREE_TRIAL, $freeTrialTeams);
        foreach ($targetTeams as $team) {
            $admins = $this->TeamMember->findAdmins($team['id']);
            foreach ($admins as $toUser) {
                $this->sendEmail(1, $toUser, $team);
            }
        }

    }

    function sendEmailToReadOnlyTeams()
    {
        $readOnlyTeams = $this->Team->findByServiceUseStatus(Team::SERVICE_USE_STATUS_READ_ONLY);
        $targetTeams = $this->filterTargetTeams(Team::DAYS_SERVICE_USE_STATUS_READ_ONLY, $readOnlyTeams);

    }

    function sendEmailToExpiredTeams()
    {
        $expiredTeams = $this->Team->findByServiceUseStatus(Team::SERVICE_USE_STATUS_EXPIRED);
        $targetTeams = $this->filterTargetTeams(Team::DAYS_SERVICE_USE_STATUS_EXPIRED, $expiredTeams);

    }

    /**
     * Filter target teams for sending notification
     *
     * @param int   $daysServiceUseStatus
     * @param array $teams
     *
     * @return array
     */
    function filterTargetTeams(int $daysServiceUseStatus, array $teams): array
    {
        $filteredTeams = [];
        foreach ($teams as $team) {
            $expireDate = AppUtil::dateAfter($team['service_use_state_start_date'], $daysServiceUseStatus);
            $notifyDates = $this->getNotifyDates($expireDate);
            $todayLocalDate = AppUtil::todayDateYmdLocal($team['timezone']);
            if (in_array($todayLocalDate, $notifyDates)) {
                $team['status_use_expired_date'] = $expireDate;
                $filteredTeams[] = $team;
            }
        }
        return $filteredTeams;
    }

    /**
     * Get notify dates by EXPIRE_ALERT_NOTIFY_BEFORE_DAYS
     *
     * @param string $expireDate
     *
     * @return array e.g. ["2017/07/30","2017/07/15"]
     */
    function getNotifyDates(string $expireDate): array
    {
        $notifyBeforeDays = explode(',', EXPIRE_ALERT_NOTIFY_BEFORE_DAYS);
        $notifyDates = [];
        foreach ($notifyBeforeDays as $notifyBeforeDay) {
            $notifyDates[] = AppUtil::dateBefore($expireDate, $notifyBeforeDay);
        }
        return $notifyDates;
    }

    function sendEmail($template, $toUser, $team)
    {

    }
}
