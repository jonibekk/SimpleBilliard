<?php
App::uses("AppModel", 'Model');

/**
 * Evaluator Change Log Model
 *
 * @property Team $Team
 */
class EvaluatorChangeLog extends AppModel
{
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'EvaluateeUser' => [
            'className'  => 'User',
            'foreignKey' => 'user_id',
        ],
        'Team'          => [
            'className'  => 'Team',
            'foreignKey' => 'team_id',
        ]
    ];

    /**
     * Method for getting the latest changelog of an evaluatee in a team
     *
     * @param int $teamId
     * @param int $userId
     *
     * @return array|null
     */
    public function getLatestLogByUserIdAndTeamId(int $teamId, int $userId): array
    {
        $options = [
            'conditions' => [
                'team_id'           => $teamId,
                'evaluatee_user_id' => $userId
            ],
            'order'      => [
                'created' => 'DESC'
            ]
        ];

        $ret = $this->find('first', $options);

        if (empty($ret)) {
            return [];
        }

        return Hash::get($ret, 'EvaluatorChangeLog');
    }

    /**
     * Method for inserting change log of evaluator
     *
     * @param int   $teamId
     * @param int   $evaluateeId
     * @param array $evaluatorIds
     * @param int   $updaterId Id of user who changed evaluatee's evaluator(s)
     *
     * @return mixed
     * @throws Exception
     */
    public function insertEvaluatorChangelog(int $teamId, int $evaluateeId, array $evaluatorIds, int $updaterId)
    {
        $saveData = [
            'evaluatee_user_id'   => $evaluateeId,
            'evaluator_user_ids'  => implode(",", $evaluatorIds),
            'team_id'             => $teamId,
            'last_update_user_id' => $updaterId
        ];

        $this->create();
        $res = $this->save($saveData);

        return $res;
    }

}