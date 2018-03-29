<?php
App::import('Service', 'AppService');
App::Uses("Evaluator", "Model");
App::uses("EvaluatorChangeLog", "Model");

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 15/03/2018
 * Time: 15:10
 */
class EvaluatorChangeLogService extends AppService
{

    /**
     * @param int $teamId    Team Id of the evaluatee
     * @param int $evaluateeUserId
     * @param int $updaterId User Id of the person who updated evaluatee's evaluators
     *
     * @return bool
     */
    public function saveLog(int $teamId, int $evaluateeUserId, int $updaterId)
    {
        /** @var Evaluator $Evaluator */
        $Evaluator = ClassRegistry::init('Evaluator');
        $existingEvaluatorIds = $Evaluator->getExistingEvaluatorsIds($teamId, $evaluateeUserId);

        if (empty($existingEvaluatorIds)) {
            $existingEvaluatorIds = [];
        }

        try {
            $this->TransactionManager->begin();

            /** @var EvaluatorChangeLog $EvaluatorChangeLog */
            $EvaluatorChangeLog = ClassRegistry::init("EvaluatorChangeLog");

            $EvaluatorChangeLog->insertEvaluatorChangelog($teamId, $evaluateeUserId, $existingEvaluatorIds, $updaterId);
            $this->TransactionManager->commit();

        } catch (Exception $e) {
            $this->TransactionManager->rollback();

            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());

            return false;
        }
        return true;
    }

}