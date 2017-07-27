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
 * - 3: cannot use
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
 * - Options
 *   -f Force sending emails. If specified it,EXPIRE_ALERT_NOTIFY_BEFORE_DAYS will be ignored.
 *   -s This is about target "service_use_status". As default, target status is all. status: 0: free trial,2: read only,3:cannot use (choices: 0|2|3)
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

    private $failedCount = 0;
    private $succeededCount = 0;

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
            'force'         => [
                'short'   => 'f',
                'help'    => 'Force sending emails. If specified it, EXPIRE_ALERT_NOTIFY_BEFORE_DAYS will be ignored.',
                'default' => false,
                'boolean' => true,
            ],
            'target_status' => [
                'short'   => 's',
                'help'    => 'This is about target "service_use_status". As default, target status is all. status: 0: free trial,2: read only,3: cannot use',
                'choices' => [
                    Team::SERVICE_USE_STATUS_FREE_TRIAL,
                    Team::SERVICE_USE_STATUS_READ_ONLY,
                    Team::SERVICE_USE_STATUS_CANNOT_USE
                ],
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    function main()
    {
        if (Hash::get($this->params, 'target_status') !== null) {
            $this->_mainProcess($this->params['target_status']);
        } else {
            $this->_mainProcess(Team::SERVICE_USE_STATUS_FREE_TRIAL);
            $this->_mainProcess(Team::SERVICE_USE_STATUS_READ_ONLY);
            $this->_mainProcess(Team::SERVICE_USE_STATUS_CANNOT_USE);
        }
    }

    /**
     * Main process
     *
     * @param int $serviceUseStatus
     *
     * @return bool
     */
    function _mainProcess(int $serviceUseStatus)
    {
        // validating $serviceUseStatus
        if (!array_key_exists($serviceUseStatus, Team::DAYS_SERVICE_USE_STATUS)) {
            $this->log("Sending email for alerting expire was canceled. cause, \$serviceUseStatus was wrong. \$serviceUseStatus:$serviceUseStatus");
            return false;
        }
        $teams = $this->Team->findByServiceUseStatus($serviceUseStatus);
        foreach ($teams as $team) {
            if ($team['service_use_state_start_date'] == "0000-00-00") {
                $this->log("TeamId:{$team['id']} was skipped. Cause, 'service_use_state_start_date' is '0000-00-00'.");
                $this->failedCount++;
                continue;
            }
            $daysOfStatus = $this->_decideStatusDays($serviceUseStatus,
                Team::DAYS_SERVICE_USE_STATUS[$serviceUseStatus],
                $team);
            if ($this->_isTargetTeam($daysOfStatus, $team) === false) {
                continue;
            }
            $expireDate = AppUtil::dateAfter($team['service_use_state_start_date'], $daysOfStatus);
            $this->_sendingEmailToAdmins($team['id'], $team['name'], $expireDate, $serviceUseStatus);
        }
        $msg = sprintf("Sending email for alerting expire has been done. succeeded count:%s, failed count:%s, \$serviceUseStatus:%s",
            $this->succeededCount,
            $this->failedCount,
            $serviceUseStatus
        );
        $this->out($msg);
        // logging only failed.
        if ($this->failedCount > 0) {
            $this->log($msg);
        }
        $this->_resetCount();
    }

    /**
     * Sending Email to admins on the team.
     *
     * @param int    $teamId
     * @param string $teamName
     * @param string $expireDate
     * @param int    $serviceUseStatus
     */
    function _sendingEmailToAdmins(int $teamId, string $teamName, string $expireDate, int $serviceUseStatus)
    {
        $adminList = $this->TeamMember->findAdminList($teamId);
        if (!empty($adminList)) {
            // sending emails to each admins.
            foreach ($adminList as $toUid) {
                $this->GlEmail->sendMailServiceExpireAlert($toUid, $teamId, $teamName, $expireDate,
                    $serviceUseStatus);
            }
            $this->succeededCount++;
        } else {
            $this->log("TeamId:{$teamId} There is no admin..");
            $this->failedCount++;
        }
    }

    /**
     * Is the team target for sending email?
     *
     * @param int   $daysOfStatus
     * @param array $team
     *
     * @return bool
     */
    function _isTargetTeam(int $daysOfStatus, array $team): bool
    {
        if ($this->params['force'] === true) {
            return true;
        }
        $expireDate = AppUtil::dateAfter($team['service_use_state_start_date'], $daysOfStatus);
        $notifyBeforeDays = explode(',', EXPIRE_ALERT_NOTIFY_BEFORE_DAYS);
        $todayLocalDate = AppUtil::todayDateYmdLocal($team['timezone']);
        $diffDaysBetweenExpireAndToday = AppUtil::diffDays($todayLocalDate, $expireDate);
        if (in_array($diffDaysBetweenExpireAndToday, $notifyBeforeDays)) {
            return true;
        }
        return false;
    }

    /**
     *  Decide status days
     *  In only free trial, fetching the days from DB
     *
     * @param int   $serviceUseStatus
     * @param int   $statusDays
     * @param array $team
     *
     * @return int
     */
    function _decideStatusDays(int $serviceUseStatus, int $statusDays, array $team): int
    {
        if ($serviceUseStatus === Team::SERVICE_USE_STATUS_FREE_TRIAL) {
            $statusDays = $team['free_trial_days'] ?? $statusDays;
        }
        return $statusDays;
    }

    function _resetCount()
    {
        $this->failedCount = 0;
        $this->succeededCount = 0;
    }
}
