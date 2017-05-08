<?php
App::import('Service', 'AppService');

/**
 * Class TermService
 */
class TermService extends AppService
{
    /**
     * Validate update term data
     * - check by white list
     * - check data available
     *
     * @param  array $data
     * @param  int   $userId
     *
     * @return true|string
     */
    public function validateUpdate(array $data)
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init("Term");

        // model validation
        $Term->set($data);
        $Term->validate = $Term->update_validate;
        if (!$Term->validates()) {
            $validationErrors = $this->validationExtract(
                $Term->validationErrors
            );
            return $validationErrors;
        }

        $requestStartMonth = $data['start_month'];
        $currentTerm = $Term->getCurrentTermData();
        $lowerLimit = date('Y-m', strtotime("+1 month"));
        if ($requestStartMonth < $lowerLimit) {
            // TODO: set valid error message
            return 'lower limit';
        }

        $upperLimit = date('Y-m', strtotime("{$currentTerm['start_date']} +12 month"));
        if ($requestStartMonth > $upperLimit) {
            // TODO: set valid error message
            return 'upper limit';
        }
        return true;
    }

    /**
     * Update term
     * - update team setting
     * - update current term end date
     * - update next term start and end date
     *
     * @param  array $data
     * @param  int   $userId
     *
     * @return bool
     */
    public function update(array $data, int $userId): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var Term $Term */
        $Term = ClassRegistry::init("Term");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        try {
            $Term->begin();

            $requestStartMonth = $data['start_month'];
            $termRange = $data['term_range'];
            $newNextStartDate = date('Y-m-01', strtotime($requestStartMonth));
            $newNextEndDate = date('Y-m-d', strtotime("{$newNextStartDate} +{$termRange} month yesterday"));
            $newCurrentEndDate = date('Y-m-d', strtotime("$newNextStartDate yesterday"));

            // update team
            $newStartMonth = date('m', strtotime($newNextStartDate));
            if (!$Team->updateTermSettings($newStartMonth, $termRange)) {
                throw new Exception(sprintf("Failed to update team setting. new_start_month: %s border_month: %s", $newStartMonth, $termRange));
            }

            // update term
            if (!$Term->updateCurrentTermEndDate($newCurrentEndDate)) {
                throw new Exception(sprintf("Failed to update current term setting. current_term_end_date: %s", $newCurrentEndDate));
            }
            if (!$Term->updateNextTermDate($newNextStartDate, $newNextEndDate)) {
                throw new Exception(sprintf("Failed to update next term setting. start_date: %s end_date: %s", $newNextStartDate, $newNextEndDate));
            }

            // update goals
            $currentTerm = $Term->getCurrentTermData();
            $currentStartDate = $currentTerm['start_date'];
            if (!$Goal->updateInCurrentTerm($currentStartDate, $newCurrentEndDate)) {
                throw new Exception(sprintf("Failed to update current term goal. current_start_date: %s current_term_end_date: %s", $currentStartDate, $newCurrentEndDate));
            }
            if (!$Goal->updateInNextTerm($newNextStartDate, $newNextEndDate)) {
                throw new Exception(sprintf("Failed to update next term goal setting. start_date: %s end_date: %s", $newNextStartDate, $newNextEndDate));
            }

            // update keyresults
            // current goals
            $currentGoals = $Goal->findForTermUpdating($currentStartDate, $newCurrentEndDate);
            foreach($currentGoals as $currentGoal) {
                if (!$KeyResult->updateInCurrentTerm($currentGoal['goal_id'], $currentGoal['start_date'], $currentGoal['end_date'])) {
                    throw new Exception(sprintf("Failed to update current term key results. goal_id: %s current_start_date: %s current_term_end_date: %s", $currentGoal['goal_id'], $currentGoal['start_date'], $currentGoal['end_date']));
                }
            }
            // next goals
            $nextGoals = $Goal->findForTermUpdating($newNextStartDate, $newNextEndDate);
            foreach($nextGoals as $nextGoal) {
                if (!$KeyResult->updateInNextTerm($nextGoal['goal_id'], $nextGoal['start_date'], $nextGoal['end_date'])) {
                    throw new Exception(sprintf("Failed to update current term key results. goal_id: %s current_start_date: %s current_term_end_date: %s", $nextGoal['goal_id'], $nextGoal['start_date'], $nextGoal['end_date']));
                }
            }

            $Term->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Term->rollback();
            return false;
        }

        return true;
    }
}
