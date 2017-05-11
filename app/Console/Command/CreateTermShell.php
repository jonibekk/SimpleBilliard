<?php
App::uses('AppUtil', 'Util');

/**
 * The shell for creating next term
 * Console/cake create_term
 * Description
 * - As normally, it's executed by cron job.
 * - You can execute it manually if cron job is failed.
 * - The target time zone is determined based on the execution time.
 * - If there is no next term data of the team, term data is generated.
 * - [Error case] If there is no current term data in a team, to create a next term data will skipped.
 *
 * @property Team $Team
 * @property Term $Term
 */
class CreateTermShell extends AppShell
{
    public $uses = [
        'Team',
        'Term',
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
            'timezone'         => [
                'short'    => 't',
                'help'     => 'target timezone. As default, it will be calculated automatically from current timestamp.',
                'required' => false,
            ],
            'currentTimestamp' => [
                'short'    => 'c',
                'help'     => '[ It is used for only test cases ]',
                'required' => false,
            ],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * Entry point of the Shell
     */
    public function main()
    {
        // currentTimestamp param is used for only test cases.
        $currentTimestamp = $this->params['currentTimestamp'] ?? time();

        $targetTimezone = $this->params['timezone'] ?? $this->_getTargetTimezoneByTimestamp($currentTimestamp);

        // validation
        if (!$this->_validateTimezone($targetTimezone)) {
            $timezones = array_keys(AppUtil::getTimezoneList());
            $this->error('Invalid parameter. Timezone should be in following values.', $timezones);
        }

        $targetDate = AppUtil::dateYmdLocal($currentTimestamp, $targetTimezone);

        $this->_mainProcess($targetTimezone, $targetDate);

        // If 12 hours difference,
        // UTC-12:00(Eniwetok, Kwajalein) should be covered as extra process.
        if ($targetTimezone == 12) {
            $this->_mainProcess(-$targetTimezone, $targetDate);
        }

        $this->_deleteTermCaches();
    }

    /**
     * Main process
     *
     * @param float  $targetTimezone
     * @param string $targetDate
     *
     * @return bool|int
     */
    protected function _mainProcess($targetTimezone, string $targetDate)
    {
        // [処理対象外チーム] 今期の期間設定が存在しないチーム [Unprocessed teams] Team not having term setting for current term
        // 取得する目的はエラーログに残す事のみ The purpose of fetching data is only to leave it in the error log
        $teamIdsNotHaveTerm = $this->Team->findIdsNotHaveTerm($targetTimezone, $targetDate);
        if (!empty($teamIdsNotHaveTerm)) {
            CakeLog::error(sprintf('Failed to find current terms. timezone: %s, team count: %s, failed team ids:%s',
                $targetTimezone, count($teamIdsNotHaveTerm), var_export($teamIdsNotHaveTerm, true)
            ));
        }

        // [処理対象チームのデータ保存に必要な情報を取得] 対象のチームは今期の期間設定が存在し、且つ来期の期間設定が存在しないチーム
        // Target teams are which have current term setting and which have not next term setting.
        $currentTerms = $this->Team->findAllTermEndDatesNextTermNotExists($targetTimezone, $targetDate);
        if (empty($currentTerms)) {
            return $this->out('There is no data to save.');
        }
        // Building saving term datas.
        $newTerms = [];
        foreach ($currentTerms as $currentTerm) {
            $startDate = AppUtil::dateTomorrow($currentTerm['end_date']);
            $newTerms[] = [
                'start_date' => AppUtil::dateTomorrow($currentTerm['end_date']),
                'end_date'   => date('Y-m-t',
                    strtotime($startDate . " +" . ($currentTerm['border_months'] - 1) . " month")),
                'team_id'    => $currentTerm['team_id'],
            ];
        }

        // Saving all team's next terms at once.
        if (!$this->Term->bulkInsert($newTerms)) {
            CakeLog::error(sprintf(
                'Failed to insert term datas. timezone: %s, data count: %s, data: %s',
                $targetTimezone, count($newTerms), var_export($newTerms, true)
            ));
            return false;
        }

        return $this->out(sprintf(
            'Success to save term datas. timezone: %s, data count: %s, data: %s',
            $targetTimezone, count($newTerms), var_export($newTerms, true)
        ));
    }

    protected function _deleteTermCaches()
    {
        // deleting caches
        Cache::delete($this->Term->getCacheKey(CACHE_KEY_TERM_CURRENT), 'team_info');
        Cache::delete($this->Term->getCacheKey(CACHE_KEY_TERM_NEXT), 'team_info');
        Cache::delete($this->Term->getCacheKey(CACHE_KEY_TERM_PREVIOUS), 'team_info');
    }

    /**
     * getting target timezone by current timestamp
     *
     * @param int $currentTimestamp
     *
     * @return int
     */
    protected function _getTargetTimezoneByTimestamp(int $currentTimestamp)
    {
        $startTodayTimestamp = strtotime('00:00:00');
        // UTC0:00と現在日時の時差(0 - 23)
        $difHourFromUtcMidnight = AppUtil::diffHourFloorByMinute($currentTimestamp, $startTodayTimestamp);
        // 時差によって対象タイムゾーンを自動判定
        if (in_array($difHourFromUtcMidnight, [0, 12])) {
            // UTC+0:00 Western Europe Time, London
            // UTC+12:00(Auckland, Fiji)
            return $difHourFromUtcMidnight;
        } elseif ($difHourFromUtcMidnight < 12) {
            // UTC-11:00(Midway Island) - UTC-1:00(Cape Verde Islands)
            // timezone = -xx
            return -$difHourFromUtcMidnight;
        } else {
            // $timeOffset > 12
            // UTC+1:00(Central Europe Time) - UTC+11:00(Solomon Islands)
            return 24 - $difHourFromUtcMidnight;
        }
    }

    /**
     * validation of timezone
     *
     * @param float $timezone
     *
     * @return bool
     */
    protected function _validateTimezone($timezone): bool
    {
        $timezones = array_keys(AppUtil::getTimezoneList());
        return in_array($timezone, $timezones);
    }

}
