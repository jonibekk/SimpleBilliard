<?php
App::uses('AppUtil', 'Util');

/**
 * 期間データ生成バッチ用shell
 * Console/cake create_term
 * 説明
 * - 期間データを生成する処理
 * - 実行時間を元に対象タイムゾーンを割り出し、そのチームの期間データ(今期、来期)が存在しない場合に期間データを生成する
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
            'timezone'         => ['short' => 't', 'help' => '対象のチームのタイムゾーン', 'required' => false,],
            'currentTimestamp' => ['short' => 'c', 'help' => '現在のタイムスタンプ(テスト用途)', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * shellのメイン処理
     */
    public function main()
    {
        // テストの場合のみ現在日時のタイムスタンプをパラメータから取得
        $nowTimestamp = $this->params['currentTimestamp'] ?? time();
        // ターゲットのタイムゾーン
        $targetTimezone = $this->params['timezone'] ?? null;
        if ($targetTimezone) {
            return $this->_mainProcess($targetTimezone, $nowTimestamp);
        }
        // ターゲットのタイムゾーンが存在しない場合
        $startTodayTimestamp = strtotime('00:00:00');
        // UTC0:00と現在日時の時差(0 - 23)
        $difHourFromUtcMidnight = AppUtil::diffHourFloorByMinute($nowTimestamp, $startTodayTimestamp);
        // 時差によって対象タイムゾーンを自動判定
        if ($difHourFromUtcMidnight == 0) {
            // UTC+0:00 Western Europe Time, London
            // timezone = 0で実行
            $this->_mainProcess(0, $nowTimestamp);
        } elseif ($difHourFromUtcMidnight == 12) {
            // UTC+12:00(Auckland, Fiji)
            // timezone = +12で実行
            $this->_mainProcess(12, $nowTimestamp);
            // UTC-12:00(Eniwetok, Kwajalein)
            // timezone = -12で実行
            $this->_mainProcess(-12, $nowTimestamp);
        } elseif ($difHourFromUtcMidnight < 12) {
            // UTC-11:00(Midway Island) - UTC-1:00(Cape Verde Islands)
            // timezone = -xxで実行
            $this->_mainProcess(-$difHourFromUtcMidnight, $nowTimestamp);
        } else {
            // $timeOffset > 12
            // UTC+1:00(Central Europe Time) - UTC+11:00(Solomon Islands)
            $targetTimezone = 24 - $difHourFromUtcMidnight;
            $this->_mainProcess($targetTimezone, $nowTimestamp);
        }
    }

    /**
     * メインの保存処理
     *
     * @param float $targetTimezone
     * @param int   $timestamp
     *
     * @internal param string $targetDate
     */
    protected function _mainProcess($targetTimezone, int $timestamp)
    {
        // validate
        if (!$this->_validateTimezone($targetTimezone)) {
            $timezones = array_keys(AppUtil::getTimezoneList());
            $this->error('Invalid parameter. Timezone should be in following values.', $timezones);
        }

        // [処理対象外チーム] 対象のチームは今期の期間設定が存在しないチーム
        $teamIdsNotHaveTerm = $this->Team->findIdsNotHaveTerm($targetTimezone, $timestamp);

        // [処理対象チームの期間の終了日と期間] 対象のチームは今期の期間設定が存在し、且つ来期の期間設定が存在しないチーム
        $termEndDates = $this->Team->findAllTermEndDatesNextTermNotExists($targetTimezone, $timestamp);
        // 期間データの生成
        $insertDatas = [];
        foreach ($termEndDates as $currentTerm) {
            $startDate = AppUtil::dateTomorrow($currentTerm['end_date']);
            $endDate = AppUtil::dateYmd(strtotime($startDate . " +{$currentTerm['border_months']} month") - DAY);
            $insertDatas[] = [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'team_id'    => $currentTerm['team_id'],
            ];
        }
        // バルクインサート
        $this->Term->bulkInsert($insertDatas);

    }

    /**
     * タイムゾーンの値が正しいかチェック
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
