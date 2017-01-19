<?php
App::import('Service', 'GoalService');

/**
 * ゴール日次進捗集計用バッチ
 * Console/cake goal_progress_daily_log -d YYYY-MM-DD
 * 説明
 * - 指定日までの最新のKR進捗から各ゴールの進捗を求める。
 *
 * @property Team                 $Team
 * @property Goal                 $Goal
 * @property KeyResult            $KeyResult
 * @property KrProgressLog        $KrProgressLog
 * @property GoalMember           $GoalMember
 * @property GoalProgressDailyLog $GoalProgressDailyLog
 */
class GoalProgressDailyLogShell extends AppShell
{
    public $uses = array(
        'Team',
        'Goal',
        'KeyResult',
        'KrProgressLog',
        'GoalMember',
        'GoalProgressDailyLog',
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
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        // パラメータ
        $targetDate = $this->params['date'];

        // validate
        if (!$this->_targetDateValidation($targetDate)) {
            $this->error('Invalid parameter', $this->_usageString());
        }

        $this->GoalProgressDailyLog->begin();
        //該当日のデータを削除(ハードデリート)
        $this->GoalProgressDailyLog->deleteAll(['GoalProgressDailyLog.target_date' => $targetDate]);
        // 全チームのIDリスト
        $teamIds = array_keys($this->Team->find('list'));
        $saveData = [];
        //メモリ消費を抑えるためにチーム毎に集計
        foreach ($teamIds as $teamId) {
            // モデルに current_team_id をセット
            $this->_setupModels($teamId);
            // 全ゴールのIDリスト
            $goalIds = array_keys($this->Goal->find('list'));
            if (empty($goalIds)) {
                continue;
            }
            // 全ゴールを取得
            $goals = $this->Goal->getGoalAndKr($goalIds);
            //保存データの生成
            foreach ($goals as $goal) {
                $saveData[] = [
                    'team_id'     => $teamId,
                    'goal_id'     => $goal['Goal']['id'],
                    //各ゴール毎にKRからゴール進捗を求める
                    'progress'    => $GoalService->getProgress($goal['KeyResult']),
                    'target_date' => $targetDate,
                ];
            }
        }
        //ログ保存
        $result = $this->GoalProgressDailyLog->saveAll($saveData);
        if (!$result) {
            $this->GoalProgressDailyLog->rollback();
            $this->log("goal_progress_daily_log shell was failed. target_date:$targetDate.\n");
            $this->error('failed.');
        }
        $this->GoalProgressDailyLog->commit();
        //キャッシュ削除
        Cache::clear(false, 'team_info');
        Cache::clear(false, 'user_data');
        $this->out('successful!');
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
    protected function _targetDateValidation(string $date): bool
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
