<?php

class ImproveTimezoneAndTerm2 extends CakeMigration
{
    private $err_terms = [];
    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'improve_timezone_and_term_2';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up'   => array(),
        'down' => array(),
    );

    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        if ($direction == 'up') {
            try {

                /** @var EvaluateTerm $EvaluateTerm */
                $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
                /** @var Goal $Goal */
                $Goal = ClassRegistry::init('Goal');

                // Get old terms
                $terms = Hash::extract(
                    $EvaluateTerm->find('all'), '{n}.EvaluateTerm'
                );

                // チームIDごとに配列を加工
                $termsEachTeamId = [];
                foreach ($terms as $term) {
                    $termsEachTeamId[$term['team_id']][] = AppUtil::filterWhiteList($term,
                        ['timezone', 'start_date', 'end_date']);
                }

                // Convert term type int to date by team timezone
                foreach ($terms as $term) {
                    $this->transferTerm($term);
                }
                unset($terms);

                foreach ($termsEachTeamId as $teamId => $terms) {
                    $teamAllGoals = Hash::extract(
                        $Goal->findAllByTeamId($teamId, ['id', 'old_start_date', 'old_end_date']), '{n}.Goal'
                    );
                    if (empty($teamAllGoals)) {
                        continue;
                    }

                    $chunkedGoals = array_chunk($teamAllGoals, 100);
                    foreach ($chunkedGoals as $goals) {
                        foreach ($goals as $goal) {
                            $this->updateGoalAndKrs($goal, $terms);
                        }
                    }
                }

                if (!empty($this->err_terms)) {
                    CakeLog::error(sprintf(
                        'Invalid terms. terms:%s',
                        var_export($this->err_terms, true)
                    ));
                }

            } catch (Exception $e) {
                // transaction rollback
                CakeLog::error($e->getMessage());
                CakeLog::error($e->getTraceAsString());

                // if return false, it will be paused to wait input.. So, exit
                exit(1);
            }
        }
        return true;
    }

    /**
     * Update goal and key_results
     *
     * @param array $goal
     * @param array $terms
     *
     * @throws Exception
     */
    private function updateGoalAndKrs(array $goal, array $terms)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');

        // Find out which terms belongs to the goal
        $timezone = null;
        foreach ($terms as $term) {
            if ($goal['old_start_date'] >= $term['start_date']
                && $goal['old_end_date'] <= $term['end_date']
            ) {
                $timezone = $term['timezone'];
            }
        }

        // Invalid goal start/end date
        if (is_null($timezone)) {
            throw new Exception(sprintf(
                'Start/End date of goal is invalid. goal:%s',
                var_export($goal, true)
            ));
        }

        // Build goal data for update
        $updateGoal = ['id' => $goal['id']];
        $updateGoal['start_date'] = $this->getDateByTimestamp($goal['old_start_date'], $timezone);
        $updateGoal['end_date'] = $this->getDateByTimestamp($goal['old_end_date'], $timezone);

        // Update goal
        if (!$Goal->save($updateGoal, false)) {
            throw new Exception(sprintf(
                'Failed to save goal. data:%s',
                var_export($updateGoal, true)
            ));
        }

        $krs = Hash::extract(
            $KeyResult->findAllByGoalId($goal['id'], ['id', 'old_start_date', 'old_end_date']), '{n}.KeyResult'
        );

        foreach ($krs as $kr) {
            // Build goal data for update
            $updateKr = ['id' => $kr['id']];
            $updateKr['start_date'] = $this->getDateByTimestamp($kr['old_start_date'], $timezone);
            $updateKr['end_date'] = $this->getDateByTimestamp($kr['old_end_date'], $timezone);

            // Update kr
            if (!$KeyResult->save($updateKr, false)) {
                throw new Exception(sprintf(
                    'Failed to save kr. data:%s',
                    var_export($updateKr, true)
                ));
            }
        }
    }

    /**
     * Data migration from old evaluate_terms table to new terms table
     *
     * @param array $term
     *
     * @throws Exception
     */
    private function transferTerm(array $term)
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');

        $term['start_date'] = $this->getDateByTimestamp($term['start_date'], $term['timezone'], true);
        $monthFirstDate = date('Y-m-d', strtotime('first day of ' . $term['start_date']));
        $term['end_date'] = $this->getDateByTimestamp($term['end_date'], $term['timezone'], true);
        $monthLastDate = date('Y-m-d', strtotime('last day of ' . $term['end_date']));

        if ($term['start_date'] !== $monthFirstDate || $term['end_date'] !== $monthLastDate) {
            $this->err_terms[] = $term;
        }

        // Insert term to new table
        if (!$Term->save($term, false)) {
            throw new Exception(sprintf(
                'Failed to save term. term:%s',
                var_export($term, true)
            ));
        }
    }

    /**
     * Get date by local timestamp
     *
     * @param int   $timestamp
     * @param float $timezone
     * @param bool  $isAddition
     *
     * @return string
     */
    private function getDateByTimestamp(int $timestamp, float $timezone, $isAddition = false)
    {
        $localTime = $isAddition ? $timestamp + ($timezone * HOUR) : $timestamp - ($timezone * HOUR);
        return AppUtil::dateYmd($localTime);
    }
}
