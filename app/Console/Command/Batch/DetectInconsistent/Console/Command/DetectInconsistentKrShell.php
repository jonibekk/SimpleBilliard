<?php
/**
 * DetectInconsistentKrShell
 * Detect inconsistent kr data
 *
 * @property KeyResult $KeyResult
 */
class DetectInconsistentKrShell extends AppShell
{
    public $uses = array(
        'KeyResult',
    );

    public function startup()
    {
        parent::startup();
    }

    public function main()
    {
        // Find goals outside the scope of the goal.
        $inconsistentKrs = $this->findKeyResultsOutsideScopeOfGoal();
        if (!empty($inconsistentKrs)) {
            CakeLog::error(sprintf(
                'Exist inconsistent krs outside the scope of the goal. %s'
                , var_export(compact('inconsistentKrs'), true)
            ));
        }
    }

    /**
     * Find inconsistent goals
     */
    public function findKeyResultsOutsideScopeOfGoal(): array
    {
        $ret = $this->KeyResult->find('all', [
            'fields'     => [
                'KeyResult.id',
                'KeyResult.team_id',
                'KeyResult.goal_id',
                'KeyResult.start_date',
                'KeyResult.end_date',
                'Goal.start_date',
                'Goal.end_date',
            ],
            'conditions' => [
                'KeyResult.del_flg' => false,
            ],
            'joins'      => [
                // 現状チームを削除してもチームに属するゴールやKRが生きたままなので、仕方なくJOINしてチームの生存を確認
                // TODO:JOINを削除
                [
                    'type'       => 'INNER',
                    'table'      => 'teams',
                    'alias'      => 'Team',
                    'conditions' => [
                        'Team.id = KeyResult.team_id',
                        'Team.del_flg' => false,
                    ]
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'conditions' => [
                        'Goal.id = KeyResult.goal_id',
                        'Goal.del_flg' => false,
                        'OR' => [
                            'Goal.start_date > KeyResult.start_date',
                            'KeyResult.end_date > Goal.end_date',
                        ]
                    ]
                ],
            ],
        ]);
        return $ret;
    }
}
