<?php
App::import('Service', 'AppService');
App::import('Service', "EvaluatorChangeLogService");
App::Uses("Evaluator", "Model");
App::Uses("User", "Model");

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 08/03/2018
 * Time: 12:15
 */
class EvaluatorService extends AppService
{

    /**
     * Set evaluators for a user
     *
     * @param int   $teamId
     * @param int   $userId
     *      User ID of evaluatee
     * @param array $evaluatorIds
     *      Array of user IDs of evaluators
     * @param int   $lastUpdaterUserId
     *
     * @return bool
     *      Insertion result; true for success
     */
    function setEvaluators(int $teamId, int $userId, array $evaluatorIds, int $lastUpdaterUserId = null)
    {
        try {
            $this->TransactionManager->begin();

            /** @var Evaluator $Evaluator */
            $Evaluator = ClassRegistry::init('Evaluator');
            $Evaluator->resetEvaluators($teamId, $userId);
            $Evaluator->insertEvaluators($teamId, $userId, $evaluatorIds);

            if (!empty($lastUpdaterUserId)) {
                /** @var EvaluatorChangeLogService $EvaluatorChangeLogService */
                $EvaluatorChangeLogService = ClassRegistry::init("EvaluatorChangeLogService");
                $EvaluatorChangeLogService->saveLog($teamId, $userId, $lastUpdaterUserId);
            }

            $this->TransactionManager->commit();

        } catch (Exception $e) {
            $this->TransactionManager->rollback();

            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());

            return false;
        }
        return true;

    }

    /**
     * Get evaluators of evaluatee ordered ascending by Evaluator.index_num
     *
     * @param int $teamId
     * @param int $evaluateeUserId
     *
     * @return array
     */
    function getEvaluatorsByTeamIdAndEvaluateeUserId(int $teamId, int $evaluateeUserId): array
    {
        /** @var Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');
        $options = [
            'fields'     => [
                'Evaluator.id',
                'Evaluator.evaluatee_user_id',
                'Evaluator.evaluator_user_id',
                'Evaluator.team_id',
                'Evaluator.index_num',
                'User.id',
                'User.first_name',
                'User.last_name',
                'User.photo_file_name',
            ],
            'conditions' => [
                'Evaluator.team_id'           => $teamId,
                'Evaluator.evaluatee_user_id' => $evaluateeUserId,
            ],
            'order'      => [
                'Evaluator.index_num' => 'asc',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = Evaluator.evaluator_user_id',
                    ]
                ],
            ],
        ];
        $res = $Evaluator->find('all', $options);

        /** @var User $User */
        $User = ClassRegistry::init('User');
        // This is building user name to display by user language
        $res = $User->afterFind($res);

        return $res;
    }
}