<?php
App::import('Service', 'GoalService');

/**
 * ゴール日次進捗集計用バッチ
 * Console/cake goal_progress_daily_log -d YYYY-MM-DD
 * 説明
 * - 指定日までの最新のKR進捗から各ゴールの進捗を求める。
 * TODO: 現時点では、過去のゴール進捗ログは書き変えない。詳しくは、 https://github.com/IsaoCorp/goalous/pull/5486
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
        // パラメータ
        $targetDate = $this->params['date'] ?? date('Y-m-d', strtotime('yesterday'));

        // validate
        if (!$this->_validateTargetDate($targetDate)) {
            $this->error('Invalid parameter', $this->_usageString());
        }

        //start transaction
        $this->GoalProgressDailyLog->begin();
        //該当日のデータを削除(ハードデリート)
        //TODO: 現時点では、この処理は行わない。過去のゴール進捗ログは書き換えることができないため。詳しくは、 https://github.com/IsaoCorp/goalous/pull/5486
        //$this->GoalProgressDailyLog->deleteAll(['GoalProgressDailyLog.target_date' => $targetDate]);

        // 全チームのIDリスト
        $teamIds = array_keys($this->Team->find('list'));

        try {
            //メモリ消費を抑えるためにチーム毎に集計し保存する。
            foreach ($teamIds as $teamId) {
                //バルクで保存
                $this->_saveGoalProgressLogsAsBulk($teamId, $targetDate);
            }
        } catch (PDOException $e) {
            //rollback transaction
            $this->GoalProgressDailyLog->rollback();
            $this->log("[Failed] goal_progress_daily_log shell. target_date:$targetDate.\n");
            $this->log("PDOException occurred!");
            $this->log($e->getMessage() . "\n");
            $this->error('failed.');
        }
        //commit transaction
        $this->GoalProgressDailyLog->commit();
        //キャッシュ削除
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        $GlRedis->deleteKeys('*:' . CACHE_KEY_GOAL_PROGRESS_LOG . ':*');
        $GlRedis->deleteKeys('*:' . CACHE_KEY_USER_GOAL_PROGRESS_LOG . ':*');

        $this->log("[Successful] goal_progress_daily_log shell. target_date:$targetDate.\n");
        $this->out('successful!');
    }

    /**
     * ゴール毎の進捗ログデータをバルクで保存する
     *
     * @param int    $teamId
     * @param string $targetDate
     *
     * @return bool
     */
    protected function _saveGoalProgressLogsAsBulk(int $teamId, string $targetDate): bool
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        // モデルに current_team_id をセット
        $this->_setupModels($teamId);
        // 全ゴールのIDリスト
        $goalIds = array_keys($this->Goal->find('list'));
        if (empty($goalIds)) {
            return false;
        }
        $saveData = [];
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
        $ret = $this->GoalProgressDailyLog->saveAllAtOnce($saveData);

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
