<?php
App::import('Service', 'KrValuesDailyLogService');

/**
 * KR日次進捗集計用バッチ
 * Console/cake kr_values_daily_log -d YYYY-MM-DD
 * 説明
 * - 指定日までの最新のKR進捗から各ゴールの進捗を求める。
 * - デフォルトの指定日は前日
 *
 * @property Team                 $Team
 * @property EvaluateTerm         $EvaluateTerm
 * @property Goal                 $Goal
 * @property KeyResult            $KeyResult
 * @property KrProgressLog        $KrProgressLog
 * @property GoalMember           $GoalMember
 * @property KrValuesDailyLog     $KrValuesDailyLog
 */
class KrValuesDailyLogShell extends AppShell
{
    public $uses = array(
        'Team',
        'EvaluateTerm',
        'KeyResult',
        'KrValuesDailyLog',
    );

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
            'date' => ['short' => 'd', 'help' => '集計日(YYYY-MM-DD)', 'required' => true,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * shellのメイン処理
     */
    public function main()
    {
        // デフォルトの指定日は前日
        $targetDate = $this->params['date'] ?? date('Y-m-d', strtotime('yesterday'));

        // validate
        if (!$this->_validateTargetDate($targetDate)) {
            $this->error('Invalid parameter', $this->_usageString());
        }

        //start transaction
        $this->KrValuesDailyLog->begin();
        // 該当日のデータを削除(ハードデリート)
        // TODO: 現時点では、この処理は行わない。過去のKR値ログは書き換えることができないため。詳しくは、 https://github.com/IsaoCorp/goalous/pull/5486
        //       レアケースだが、timezoneの変更によって同日のデータが存在する場合がある。その際に既存データを削除する以下の処理は必要。
        // $this->KrValuesDailyLog->deleteAll(['GoalProgressDailyLog.target_date' => $targetDate]);

        // 全チームのIDリスト
        $teamIds = array_keys($this->Team->find('list'));

        try {
            //メモリ消費を抑えるためにチーム毎に集計し保存する。
            foreach ($teamIds as $teamId) {
                //バルクで保存
                $this->_saveKrValuesDailyLogsAsBulk($teamId, $targetDate);
            }
        } catch (PDOException $e) {
            //rollback transaction
            $this->KrValuesDailyLog->rollback();
            $this->log("[Failed] kr_values_daily_log shell. target_date:$targetDate");
            $this->log("PDOException occurred!");
            $this->log($e->getMessage());
            $this->error('failed.');
        }

        //commit transaction
        $this->KrValuesDailyLog->commit();

        $this->out('successful!');
    }

    /**
     * 今期KR一覧の値をバルクで保存する
     * Modelのcurrent_team_idを初期化
     *
     * @param int    $teamId
     * @param string $targetDate
     *
     * @return bool
     */
    protected function _saveKrValuesDailyLogsAsBulk(int $teamId, string $targetDate): bool
    {
        /** @var GoalService $GoalService */
        $KrValuesDailyLogService = ClassRegistry::init('KrValuesDailyLogService');

        // モデルに current_team_id をセット
        $this->_setupModels($teamId);
        $ret = $KrValuesDailyLogService->saveAsBulk($teamId, $targetDate);

        return $ret;
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

}
