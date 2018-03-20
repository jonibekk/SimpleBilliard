<?php
App::uses('AppModel', 'Model');

/**
 * Evaluator Model
 *
 * @property User $EvaluateeUser
 * @property User $EvaluatorUser
 * @property Team $Team
 * @property Term $Term
 */
class Evaluator extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index_num' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'EvaluateeUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluatee_user_id',
        ],
        'EvaluatorUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluator_user_id',
        ],
        'Team',
    ];

    /**
     * return value as below.
     * (
     * [Evaluator.evaluatee_user_id] => Array
     * (
     * [Evaluator.id] => Evaluator.evaluator_user_id
     * )
     * )
     *
     * @return array
     */
    function getEvaluatorsCombined()
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
            ],
            'order'      => [
                'evaluatee_user_id' => 'asc',
                'index_num'         => 'asc',
            ],
        ];
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.Evaluator.id', '{n}.Evaluator.evaluator_user_id',
            '{n}.Evaluator.evaluatee_user_id');
        return $res;
    }

    function getExistingEvaluatorsIds($teamId, $evaluateeUserId): array
    {
        $options = [
            'conditions' => [
                'team_id'           => $teamId,
                'evaluatee_user_id' => $evaluateeUserId,
            ]
        ];
        $res = $this->Team->Evaluator->find('list', $options);
        return $res;
    }

    function insertEvaluators(int $teamId, int $evaluateeUserId, array $evaluatorIds)
    {
        $saveData = [];
        $evaluatorCount = 0;

        foreach ($evaluatorIds as $evaluatorId) {
            $saveData[] = [
                'evaluatee_user_id' => $evaluateeUserId,
                'evaluator_user_id' => $evaluatorId,
                'team_id'           => $teamId,
                'index_num'         => $evaluatorCount++
            ];
        }

        $res = $this->bulkInsert($saveData);

        return $res;

    }

    function resetEvaluators(int $teamId, int $userId)
    {
        $conditions = [
            'Evaluator.evaluatee_user_id' => $userId,
            'Evaluator.team_id'           => $teamId
        ];
        return $this->deleteAll($conditions);
    }
}
