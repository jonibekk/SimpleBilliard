<?php
App::uses('AppUtil', 'Util');

/**
 * TermMigration2Shell
 * Data migration for goal and krs
 *
 * @property Team      Team
 * @property Goal      Goal
 * @property KeyResult KeyResult
 * @property Term      Term
 */
class TermMigration2Shell extends AppShell
{

    public $uses = array(
        'Team',
        'Term',
        'Goal',
        'KeyResult',
    );

    public function startup()
    {
        parent::startup();
    }

    public function main()
    {
        try {
            // チームIDごとに配列を加工
            $termsEachTeamId = $this->findTermsEachTeamId();
            // チームごとに全てのゴール・KRの開始日・終了日を更新
            foreach ($termsEachTeamId as $teamId => $terms) {
                $team = $this->Team->getById($teamId);
                // チームの全てのゴール取得
                $teamAllGoals = $this->findAllGoalsByTeam($teamId);
                if (empty($teamAllGoals)) {
                    continue;
                }
                // 大量のゴールがある場合に100件ごとに分割
                $chunkedGoals = array_chunk($teamAllGoals, 100);
                foreach ($chunkedGoals as $goals) {
                    foreach ($goals as $goal) {
                        $this->updateGoalAndKrs($goal, $terms, $team);
                    }
                }
            }

        } catch (Exception $e) {
            // transaction rollback
            CakeLog::error($e->getMessage());
            CakeLog::error($e->getTraceAsString());
            // if return false, it will be paused to wait input.. So, exit
            exit(1);
        }

    }

    /**
     * Find terms each team_id
     */
    public function findTermsEachTeamId(): array
    {
        $teams = $this->Team->find('all', [
            'fields'     => [
                'Term.id',
                'Term.team_id',
                'Term.start_date',
                'Term.end_date',
            ],
            'conditions' => [
                'Team.del_flg' => false,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'terms',
                    'alias'      => 'Term',
                    'conditions' => [
                        'Term.team_id = Team.id',
                    ]
                ],
            ],
            'order'      => ['Team.id', 'Term.start_date'],
        ]);

        $ret = [];
        foreach ($teams as $team) {
            $teamId = $team['Term']['team_id'];
            $ret[$teamId][] = $team['Term'];
        }
        return $ret;
    }

    /**
     * Find all goals by team
     *
     * @param int $teamId
     *
     * @return array
     */
    public function findAllGoalsByTeam(int $teamId): array
    {
        $goals = $this->Goal->find('all', [
            'fields'     => ['Goal.id', 'Goal.old_start_date', 'Goal.old_end_date'],
            'conditions' => [
                'Goal.team_id' => $teamId
            ],
        ]);

        return Hash::extract($goals, '{n}.Goal');
    }

    /**
     * Update goal and key_results
     *
     * @param array $goal
     * @param array $terms
     * @param array $team
     *
     * @throws Exception
     */
    public function updateGoalAndKrs(array $goal, array $terms, array $team)
    {
        try {
            $this->Goal->begin();

            /* Update start_date and end_date of goal */
            $updateGoal = $this->updateGoal($goal, $terms);

            /* Update start_date and end_date of krs */
            $this->updateKrs($updateGoal, $team);
            $this->Goal->commit();
        } catch (Exception $e) {
            $this->Goal->rollback();
            throw new Exception(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
        }

    }

    /**
     * Update start_date and end_date of goal
     *
     * @param array $goal
     * @param array $terms
     *
     * @return array
     * @throws Exception
     */
    public function updateGoal(array $goal, array $terms)
    {
        // Find out which terms belongs to the goal
        $updateGoal = ['id' => $goal['id']];
        // タイムスタンプがずれているのを前提として期の範囲を開始と終了で1日ずつ拡張した上でゴールがどの期に属しているかを判定
        foreach ($terms as $i => $term) {
            $termStartTs = strtotime($term['start_date']);
            $termEndTs = strtotime($term['end_date'] . ' 23:59:59');
            $termExpandStartTs = strtotime($term['start_date'] . ' -12hour');
            $termExpandEndTs = strtotime($term['end_date'] . ' 23:59:59' . ' +12hour');

            // ゴールの開始日と終了日が期の範囲内の場合
            if ($goal['old_start_date'] >= $termExpandStartTs && $goal['old_end_date'] <= $termExpandEndTs
            ) {
                $updateGoal['start_date'] = $goal['old_start_date'] >= $termStartTs ? AppUtil::dateYmd($goal['old_start_date']) : $term['start_date'];
                $updateGoal['end_date'] = $goal['old_end_date'] <= $termEndTs ? AppUtil::dateYmd($goal['old_end_date']) : $term['end_date'];
                break;
            }
        }

        // 開始日と終了日がどの期の範囲内に無い場合期またぎとして再度チェック
        if (empty($updateGoal['start_date'])) {
            foreach ($terms as $i => $term) {
                $termStartTs = strtotime($term['start_date']);
                $termEndTs = strtotime($term['end_date'] . ' 23:59:59');
                // ゴールの終了日が期の終了日を超えている場合
                if ($goal['old_start_date'] >= $termStartTs && $goal['old_start_date'] <= $termEndTs
                    && $goal['old_end_date'] > $termEndTs
                ) {

                    $updateGoal['start_date'] = AppUtil::dateYmd($goal['old_start_date']);
                    $updateGoal['end_date'] = $term['end_date'];
                    break;
                }
            }
        }

        // Invalid goal start/end date
        if (empty($updateGoal['start_date'])) {
            throw new Exception(sprintf('Unexpected goal start/end date.  data:%s'
                , var_export(compact('terms', 'goal'), true)
            ));
        }

        // Update goal
        if (!$this->Goal->save($updateGoal, false)) {
            throw new Exception(sprintf(
                'Failed to save goal. data:%s',
                var_export($updateGoal, true)
            ));
        }
        return $updateGoal;
    }

    /**
     * Update start_date and end_date of krs
     *
     * @param array $updateGoal
     * @param array $team
     *
     * @throws Exception
     */
    public function updateKrs(array $updateGoal, array $team)
    {
        $krs = Hash::extract(
            $this->KeyResult->findAllByGoalId($updateGoal['id'], ['id', 'old_start_date', 'old_end_date']),
            '{n}.KeyResult'
        );

        $updateKrs = [];
        foreach ($krs as $kr) {
            // Build goal data for update
            $updateKr = ['id' => $kr['id']];
            // Start date
            $updateKr['start_date'] = AppUtil::dateYmd($kr['old_start_date'] + ($team['timezone'] * HOUR));
            $updateKr['start_date'] = $updateKr['start_date'] < $updateGoal['start_date'] ? $updateGoal['start_date'] : $updateKr['start_date'];
            // End date
            $updateKr['end_date'] = AppUtil::dateYmd($kr['old_end_date'] + ($team['timezone'] * HOUR));
            $updateKr['end_date'] = $updateKr['end_date'] > $updateGoal['end_date'] ? $updateGoal['end_date'] : $updateKr['end_date'];
            // Update kr
            $updateKrs[] = $updateKr;
        }
        if (!$this->KeyResult->saveAll($updateKrs, ['validate' => false])) {
            throw new Exception(sprintf(
                'Failed to update krs. data:%s',
                var_export(compact('updateKrs', 'updateGoal'), true)
            ));
        }
    }
}
