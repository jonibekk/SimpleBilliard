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

        // 該当日のデータを削除(ハードデリート)
        // TODO: 現時点では、この処理は行わない。過去のKR値ログは書き換えることができないため。詳しくは、 https://github.com/IsaoCorp/goalous/pull/5486
        //       レアケースだが、timezoneの変更によって同日のデータが存在する場合がある。その際に既存データを削除する以下の処理は必要。
        // $this->KrValuesDailyLog->deleteAll(['GoalProgressDailyLog.target_date' => $targetDate]);

        // 全チームのIDリスト
        $teamIds = array_keys($this->Team->find('list'));

        $this->_saveKrValuesDailyLogsAsBulk($teamIds, $targetDate);;
    }

    /**
     * 今期KR一覧の値をバルクで保存する
     * Modelのcurrent_team_idを初期化
     * - 実行が失敗した場合、一度だけ失敗したチームのみ再実行を走らせる
     *
     *
     * @param array  $teamIds
     * @param string $targetDate
     *
     * @return bool
     */
    protected function _saveKrValuesDailyLogsAsBulk(array $teamIds, string $targetDate, bool $isRerunning = false)
    {
        /** @var GoalService $GoalService */
        $KrValuesDailyLogService = ClassRegistry::init('KrValuesDailyLogService');

        //メモリ消費を抑えるためにチーム毎に集計し保存する。
        $successCount = 0;
        $failureTeams = [];
        foreach ($teamIds as $teamId) {
            // モデルに current_team_id をセット
            $this->_setupModels($teamId);
            //バルクで保存
            if ($KrValuesDailyLogService->saveAsBulk($teamId, $targetDate)) {
                $successCount++;
            } else {
                $failureTeams[] = $teamId;
            }
        }

        $this->log(sprintf('[success:%d failure:%d] Done kr_values_daily_log shell.', $successCount, count($failureTeams)));

        // 保存に失敗したチームは一度だけ再実行する
        if (count($failureTeams) > 0 && !$isRerunning) {
            $this->log(sprintf("Rerun batch for only failure teams. failureTeamIds: %s", implode(",", $failureTeams)));
            $this->_saveKrValuesDailyLogsAsBulk($failureTeams, $targetDate, true);
        }

        return;
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
