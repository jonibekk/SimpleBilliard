<?php
App::import('Service', 'AppService');


/**
 * Class TermService
 */
class TermService extends AppService
{
    const MAX_TERM_MONTH_LENGTH = 12;

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
        $backupValidate = $Term->validate;
        $Term->validate = $Term->update_validate;
        if (!$Term->validates()) {
            $validationErrors = $this->validationExtract(
                $Term->validationErrors
            );
            return $validationErrors;
        }

        $requestNextStartYm = $data['next_start_ym'];
        $currentTerm = $Term->getCurrentTermData();
        $lowerLimitYm = date('Y-m', strtotime("+1 month"));
        if ($requestNextStartYm < $lowerLimitYm) {
            // TODO: set valid error message
            return 'lower limit';
        }

        $upperLimitYm = date('Y-m', strtotime("{$currentTerm['start_date']} +12 month"));
        if ($requestNextStartYm > $upperLimitYm) {
            // TODO: set valid error message
            return 'upper limit';
        }

        $Term->validate = $backupValidate;
        return true;
    }

    /**
     * Update term
     * - update team setting
     * - update current term end date
     * - update next term start and end date
     *
     * @param  array $data
     *
     * @return bool
     */
    public function update(array $data): bool
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");
        /** @var Term $Term */
        $Term = ClassRegistry::init("Term");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        try {
            $Term->begin();

            $requestNextStartYm = $data['next_start_ym'];
            $termRange = $data['term_length'];
            $newNextStartDate = date('Y-m-01', strtotime($requestNextStartYm));
            $newNextEndDate = AppUtil::getEndDate($newNextStartDate, $termRange);
            $newCurrentEndDate = date('Y-m-d', strtotime($newNextStartDate) - DAY);
            $newNextNextStartDate = date('Y-m-01', strtotime($newNextEndDate) + DAY);
            $newNextNextEndDate = AppUtil::getEndDate($newNextNextStartDate, $termRange);

            // update team
            $newStartMonth = date('m', strtotime($newNextStartDate));
            if (!$Team->updateTermSettings($newStartMonth, $termRange)) {
                throw new Exception(sprintf("Failed to update team setting. new_start_ym: %s border_month: %s", $newStartMonth, $termRange));
            }

            // update term
            // current
            if (!$Term->updateCurrentEnd($newCurrentEndDate)) {
                throw new Exception(sprintf("Failed to update current term setting. current_term_end_date: %s", $newCurrentEndDate));
            }
            // next
            if (!$Term->updateRange($newNextStartDate, $newNextEndDate, Term::TYPE_NEXT)) {
                throw new Exception(sprintf("Failed to update next term setting. new_next_start_date: %s new_next_end_date: %s", $newNextStartDate, $newNextEndDate));
            }
            // after next
            if (!$Term->updateRange($newNextNextStartDate, $newNextNextEndDate, Term::TYPE_NEXT_NEXT)) {
                throw new Exception(sprintf("Failed to update next next term setting. new_next_next_start_date: %s new_next_next_end_date: %s", $newNextNextStartDate, $newNextNextEndDate));
            }

            // update goals
            // current goals
            $currentStartDate = $Term->getCurrentTermData()['start_date'];
            if (!$Goal->updateCurrentTermRange($currentStartDate, $newCurrentEndDate)) {
                throw new Exception(sprintf("Failed to update current term goal. current_start_date: %s current_term_end_date: %s", $currentStartDate, $newCurrentEndDate));
            }
            // next goals
            if (!$Goal->updateNextTermRange($newNextStartDate, $newNextEndDate)) {
                throw new Exception(sprintf("Failed to update next term goal setting. start_date: %s end_date: %s", $newNextStartDate, $newNextEndDate));
            }

            // update keyresults
            if (!$this->updateKrsRangeWithinGoalRange($currentStartDate)) {
                throw new Exception(sprintf("Failed to update key results range setting curret_start_date: %s", $currentStartDate));
            }

            // delete term cache
            Cache::delete($Term->getCacheKey(CACHE_KEY_TERM_CURRENT, false), 'team_info');
            Cache::delete($Term->getCacheKey(CACHE_KEY_TERM_NEXT, false), 'team_info');

            $Term->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Term->rollback();
            return false;
        }

        return true;
    }

    /**
     * update current and next range within its goal range
     * - targets of updating
     *   - 1. krs only end_date is in its goal range
     *   - 2. krs only start_date is in its goal range
     *   - 3. krs start_date and end_date are out of its goal range
     *
     * @return bool
     */
    public function updateKrsRangeWithinGoalRange(string $currentTermStartDate): bool
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        if (!$KeyResult->updateStartWithinGoalRange($currentTermStartDate)) {
            return false;
        }
        if (!$KeyResult->updateEndWithinGoalRange($currentTermStartDate)) {
            return false;
        }
        if (!$KeyResult->updateStartEndWithinGoalRange($currentTermStartDate)) {
            return false;
        }
        return true;
    }

    /**
     * get selectable next term list
     * - min: next month from $currentYm
     * - max: after 12 years from $currentTermStartYm
     *
     * @param  string $currentTermStartYm
     * @param  string $currentYm
     *
     * @return array
     */
    public function getSelectableNextStartYmList(string $currentTermStartYm, string $currentYm): array
    {
        $nextMonthYm = date("Y-m", strtotime("$currentYm +1 month"));
        $nextStartYmUpper = date('Y-m', strtotime($currentTermStartYm.' +'.self::MAX_TERM_MONTH_LENGTH.' month'));
        return AppUtil::rangeYmI18n($nextMonthYm, $nextStartYmUpper);
    }
}
