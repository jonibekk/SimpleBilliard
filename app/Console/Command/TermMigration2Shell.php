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
                // チームの全てのゴール取得
                $teamAllGoals = $this->findAllGoalsByTeam($teamId);
                if (empty($teamAllGoals)) {
                    continue;
                }
                // 大量のゴールがある場合に100件ごとに分割
                $chunkedGoals = array_chunk($teamAllGoals, 100);
                foreach ($chunkedGoals as $goals) {
                    foreach ($goals as $goal) {
                        $this->updateGoalAndKrs($goal, $terms);
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
    private function findTermsEachTeamId(): array
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
    private function findAllGoalsByTeam(int $teamId): array
    {
        $goals = $this->Goal->find('all', [
            'fields'     => ['Goal.id', 'Goal.old_start_date', 'Goal.old_end_date'],
            'conditions' => [
                'Goal.del_flg' => false,
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
     *
     * @throws Exception
     */
    private function updateGoalAndKrs(array $goal, array $terms)
    {
        try {
            $this->Goal->begin();

            /* Update start_date and end_date of goal */

            // Find out which terms belongs to the goal
            $updateGoal = ['id' => $goal['id']];
            $timezone = null;
            $startDate = date('Y-m-d', $goal['old_start_date']);
            $endDate = date('Y-m-d', $goal['old_end_date']);
            $lastIndex = count($terms) - 1;
            // タイムスタンプがずれているのを前提として期の範囲を開始と終了で1日ずつ拡張した上でゴールがどの期に属しているかを判定
            foreach ($terms as $i => $term) {
                $termExpandStartDate = AppUtil::dateYmd(strtotime($term['start_date'] . ' -1 day'));
                $termExpandEndDate = AppUtil::dateYmd(strtotime($term['end_date'] . ' +1 day'));
                if ($startDate >= $termExpandStartDate && $endDate <= $termExpandEndDate
                ) {
                    $updateGoal['start_date'] = $startDate >= $term['start_date'] ? $startDate : $term['start_date'];
                    $updateGoal['end_date'] = $endDate <= $term['end_date'] ? $endDate : $term['end_date'];
                    break;
                }
                if ($startDate < $termExpandStartDate
                    && $endDate > $termExpandStartDate
                ) {
                    $updateGoal['start_date'] = $term['start_date'];
                    $updateGoal['end_date'] = $endDate;
                    break;
                }
                if ($i == $lastIndex && $startDate < $termExpandEndDate
                    && $endDate > $termExpandEndDate
                ) {
                    $updateGoal['start_date'] = $startDate;
                    $updateGoal['end_date'] = $term['end_date'];
                    break;
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


            /* Update start_date and end_date of krs */

            $krs = Hash::extract(
                $this->KeyResult->findAllByGoalId($goal['id'], ['id', 'old_start_date', 'old_end_date']),
                '{n}.KeyResult'
            );

            $updateKrs = [];
            foreach ($krs as $kr) {
                // Build goal data for update
                $updateKr = ['id' => $kr['id']];
                $updateKr['start_date'] = AppUtil::dateYmd($kr['old_start_date']);
                $updateKr['start_date'] = $updateKr['start_date'] < $updateGoal['start_date'] ? $updateGoal['start_date'] : $updateKr['start_date'];
                $updateKr['end_date'] = AppUtil::dateYmd($kr['old_end_date']);
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
            $this->Goal->commit();
        } catch (Exception $e) {
            $this->Goal->rollback();
            throw new Exception(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
        }

    }

}
