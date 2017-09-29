<?php
/**
 * DetectInconsistentGoalShell
 * Detect inconsistent goal data
 *
 * @property Goal $Goal
 */
class DetectInconsistentGoalShell extends AppShell
{
    public $uses = array(
        'Goal',
    );

    public function startup()
    {
        parent::startup();
    }

    public function main()
    {
        // Find goals across the terms.
        $inconsistentGoals = $this->findGoalsOutsideScopeOfTerm();
        if (!empty($inconsistentGoals)) {
            CakeLog::error(sprintf(
                'Exist inconsistent goals outside the scope of the term. %s'
                , var_export(compact('inconsistentGoals'), true)
            ));
        }
    }

    /**
     * Find inconsistent goals
     */
    public function findGoalsOutsideScopeOfTerm(): array
    {
        $ret = $this->Goal->find('all', [
            'fields'     => [
                'Goal.id',
                'Goal.team_id',
                'Goal.start_date',
                'Goal.end_date',
            ],
            'conditions' => [
                'Goal.del_flg' => false,
            ],
            'joins'      => [
                // 現状チームを削除してもチームに属するゴールやKRが生きたままなので、仕方なくJOINしてチームの生存を確認
                // TODO:JOINを削除
                [
                    'type'       => 'INNER',
                    'table'      => 'teams',
                    'alias'      => 'Team',
                    'conditions' => [
                        'Team.id = Goal.team_id',
                        'Team.del_flg' => false,
                    ]
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'terms',
                    'alias'      => 'Term',
                    'conditions' => [
                        'Term.team_id = Goal.team_id',
                        'Term.start_date <= Goal.start_date',
                        'Goal.end_date <= Term.end_date',
                    ]
                ],
            ],
            'group' => [
                'Goal.id HAVING COUNT(Term.id) = 0'
            ],
        ]);
        return Hash::extract($ret, '{n}.Goal');
    }
}
