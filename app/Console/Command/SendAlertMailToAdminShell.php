<?php
App::uses('AppUtil', 'Util');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('GlEmailComponent', 'Controller/Component');

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
 * @property Team             $Team
 * @property TeamMember       $TeamMember
 * @property GlEmailComponent $GlEmail
 */
class SendAlertMailToAdminShell extends AppShell
{
    public $uses = [
        'Team',
        'TeamMember',
    ];

    public $typeMap = [];

    public $components;

    function startup()
    {
        parent::startup();
        // initializing component
        $this->GlEmail = new GlEmailComponent(new ComponentCollection());
        $this->GlEmail->startup(new AppController());
    }

    function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            'expires' => ['short' => 'e', 'help' => 'Y-m-d', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        $this->_sendEmails(Team::SERVICE_USE_STATUS_FREE_TRIAL);
        $this->_sendEmails(Team::SERVICE_USE_STATUS_READ_ONLY);
        $this->_sendEmails(Team::SERVICE_USE_STATUS_CANNOT_USE);
        return;
    }

    /**
     * Sending emails
     *
     * @param int $serviceUseStatus
     */
    function _sendEmails(int $serviceUseStatus)
    {
        $targetTeams = $this->Team->findByServiceUseStatus($serviceUseStatus);
        $statusDays = Team::$DAYS_SERVICE_USE_STATUS[$serviceUseStatus];
        foreach ($targetTeams as $team) {
            if ($team['service_use_state_start_date'] == null) {
                continue;
            }
            // In only free trial, fetching the days from DB
            if ($serviceUseStatus === Team::SERVICE_USE_STATUS_FREE_TRIAL) {
                $statusDays = $team['free_trial_days'] ?? $statusDays;
            }
            if ($this->_isTargetTeam($statusDays, $team) === false) {
                continue;
            }
            $expireDate = AppUtil::dateAfter($team['service_use_state_start_date'], $statusDays);
            $adminList = $this->TeamMember->findAdminList($team['id']);
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailServiceExpireAlert($toUid, $team['id'], $team['name'], $expireDate,
                    $serviceUseStatus);
            }
        }
    }

    /**
     * Is the team target for sending email?
     *
     * @param int   $daysServiceUseStatus
     * @param array $team
     *
     * @return bool
     */
    function _isTargetTeam(int $daysServiceUseStatus, array $team): bool
    {
        $expireDate = AppUtil::dateAfter($team['service_use_state_start_date'], $daysServiceUseStatus);
        $notifyDates = $this->_getNotifyDates($expireDate);
        $todayLocalDate = AppUtil::todayDateYmdLocal($team['timezone']);
        if (in_array($todayLocalDate, $notifyDates)) {
            return true;
        }
        return false;
    }

    /**
     * Get notify dates by EXPIRE_ALERT_NOTIFY_BEFORE_DAYS
     *
     * @param string $expireDate
     *
     * @return array e.g. ["2017/07/30","2017/07/15"]
     */
    function _getNotifyDates(string $expireDate): array
    {
        $notifyBeforeDays = explode(',', EXPIRE_ALERT_NOTIFY_BEFORE_DAYS);
        $notifyDates = [];
        foreach ($notifyBeforeDays as $notifyBeforeDay) {
            $notifyDates[] = AppUtil::dateBefore($expireDate, $notifyBeforeDay);
        }
        return $notifyDates;
    }
}
