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
        // ターゲットのタイムゾーン
        $targetTimezone = $this->params['timezone'] ?? null;
        if ($targetTimezone) {
            return $this->_mainProcess($targetTimezone);
        }
        // ターゲットのタイムゾーンが存在しない場合

        // テストの場合のみ利用！現在日時のタイムスタンプをパラメータから取得
        $nowTimestamp = $this->params['currentTimestamp'] ?? time();
        $startTodayTimestamp = strtotime('00:00:00');
        // UTC0:00と現在日時の時差(0 - 23)
        $difHourFromUtcMidnight = AppUtil::diffHourFloorByMinute($nowTimestamp, $startTodayTimestamp);
        // 時差によって対象タイムゾーンを自動判定
        if ($difHourFromUtcMidnight == 0) {
            // UTC+0:00 Western Europe Time, London
            // timezone = 0で実行、ログ対象は前日分
            $this->_mainProcess(0);
        } elseif ($difHourFromUtcMidnight == 12) {
            // UTC+12:00(Auckland, Fiji)
            // timezone = +12で実行、ログ対象は当日分
            $this->_mainProcess(12);
            // UTC-12:00(Eniwetok, Kwajalein)
            // timezone = -12で実行、ログ対象は前日分
            $this->_mainProcess(-12);
        } elseif ($difHourFromUtcMidnight < 12) {
            // UTC-11:00(Midway Island) - UTC-1:00(Cape Verde Islands)
            // timezone = -xxで実行、ログ対象は前日分
            $this->_mainProcess(-$difHourFromUtcMidnight);
        } else {
            // $timeOffset > 12
            // UTC+1:00(Central Europe Time) - UTC+11:00(Solomon Islands)
            $targetTimezone = 24 - $difHourFromUtcMidnight;
            // ログ対象は当日分
            $this->_mainProcess($targetTimezone);
        }
    }

    /**
     * メインの保存処理
     *
     * @param float $targetTimezone
     *
     * @internal param string $targetDate
     */
    protected function _mainProcess(float $targetTimezone)
    {
        // validate
        if (!$this->_validateTimezone($targetTimezone)) {
            $timezones = array_keys(AppUtil::getTimezoneList());
            $this->error('Invalid parameter. Timezone should be in following values.', $timezones);
        }

        // 今期のチームの期間設定が対象タイムゾーンと一致するチーム
        $teamIds = $this->Team->findIdsByTimezone($targetTimezone);

        // 期間データの生成

    }

    /**
     * @return string
     */
    protected function _usageString(): string
    {
        return 'Usage: date format should be YYYY-MM-DD.';
    }

    /**
     * @param int $teamId
     */
    protected function _setupModels(int $teamId)
    {
        foreach ($this->uses as $model) {
            $this->{$model}->current_team_id = $teamId;
        }
    }

    /**
     * @param string $date
     *
     * @return bool
     */
    protected function _validateTargetDate(string $date): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }
        list($y, $m, $d) = explode('-', $date);
        if (strlen($date) != 10 || !checkdate(intval($m), intval($d), intval($y))) {
            return false;
        }
        return true;
    }

    /**
     * タイムゾーンの値が正しいかチェック
     *
     * @param float $timezone
     *
     * @return bool
     */
    protected function _validateTimezone(float $timezone): bool
    {
        $timezones = array_keys(AppUtil::getTimezoneList());
        return in_array($timezone, $timezones);
    }

}
