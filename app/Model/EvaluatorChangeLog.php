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
                'EvaluatorChangeLog.team_id'           => $teamId,
                'EvaluatorChangeLog.evaluatee_user_id' => $userId,
            ],

        ];

        $options['conditions'][] = $this->_getLatestChangeLogSubQuery();
        $ret = $this->find('first', $options);

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

        $res = $this->save($saveData);

        return $res;
    }

    /**
     * Subquery for getting latest modified changelog of an evaluatee in a team
     *
     * @return stdClass SubQuery Object
     */
    private function _getLatestChangeLogSubQuery(): stdClass
    {
        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => [
                'EvLog.evaluatee_user_id',
                'MAX(EvLog.created)'
            ],
            'table'      => 'evaluator_change_logs',
            'alias'      => 'EvLog',
            'conditions' => [
                'EvLog.team_id'           => 'EvaluatorChangeLog.team_id',
                'EvLog.evaluatee_user_id' => 'EvaluatorChangeLog.evaluatee_user_id',
            ],
        ], $this);
        $queryObj = $db->expression("EXISTS (" . $subQuery . ")");
        return $queryObj;
    }
}