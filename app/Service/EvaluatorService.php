<?php
App::import('Service', 'AppService');
App::Uses("Evaluator", "Model");

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
     *
     * @return bool
     *      Insertion result; true for success
     */
    function setEvaluators(int $teamId, int $userId, array $evaluatorIds)
    {
        try {
            $this->TransactionManager->begin();

            /** @var Evaluator $Evaluator */
            $Evaluator = ClassRegistry::init('Evaluator');
            $Evaluator->resetEvaluators($teamId, $userId);
            $Evaluator->insertEvaluators($teamId, $userId, $evaluatorIds);

            $this->TransactionManager->commit();

        } catch (Exception $e) {
            $this->TransactionManager->rollback();

            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());

            return false;
        }
        return true;

    }
}