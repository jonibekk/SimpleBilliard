<?php
App::uses('AppUtil', 'Util');

/**
 * DetectInconsistentTermShell
 *
 * Detect these inconsistent term data
 *  - Not exist terms in team
 *  - Term missing teeth
 *  - Invalid format about term start/end date of term
 * 
 * @property Term       $Term
 * @property Team       $Team
 */
class DetectInconsistentTermShell extends AppShell
{
    public $requestTimestamp;
    public $uses = array(
        'Team',
        'Term',
        'Evaluation'
    );

    public function startup()
    {
        parent::startup();
        $this->requestTimestamp = time();
    }

    public function main()
    {
        // Get all teams
        $allTeams = Hash::extract($this->Team->find('all', ['fields' => ['id', 'created', 'timezone']]), '{n}.Team');
        if (empty($allTeams)) {
            exit();
        }

        // Get all terms
        $allTerms = Hash::extract(
            $this->Term->find('all',
                [
                    'fields' => ['id', 'team_id', 'start_date', 'end_date'],
                    'order'  => ['id', 'start_date']
                ]

            )
            , '{n}.Term');
        // Create terms each team
        $termsEachTeam = [];
        foreach ($allTerms as $term) {
            $termsEachTeam[$term['team_id']][] = $term;
        }

        $errorsEachTeam = [];
        // Validate each team
        foreach ($allTeams as $team) {
            $teamId = $team['id'];
            if (empty($termsEachTeam[$teamId])) {
                $errors[] = sprintf("There are no terms at all. data:%s", var_export(compact('teamId'), true));
                continue;
            }

            $terms = $termsEachTeam[$teamId];
            // Validate terms
            $errors = $this->detectErrors($team, $terms);
            if (!empty($errors)) {
                $errorsEachTeam[] = [
                    'team' => $team,
                    'errors' => $errors
                ];
            }
        }

        // Output error log
        if (!empty($errorsEachTeam)) {
            CakeLog::error(sprintf(
                'Exist inconsistent term data. errors: %s'
                , var_export($errorsEachTeam, true)
            ));
        }
    }

    /**
     * Detect term errors
     *
     * @param array $team
     * @param array $terms
     *
     * @return array
     */
    public function detectErrors(array $team, array $terms): array
    {
        $errors = [];
        $lastIndex = count($terms) - 1;
        $prevTerm = "";
        foreach ($terms as $i => $term) {
            $invalidFormatErrors = $this->validateStartEndDateFormat($term);
            // 以降のチェックは開始日・終了日のフォーマットが正しいことが前提なので、フォーマットエラーの場合スキップする
            if (!empty($invalidFormatErrors)) {
                $errors = am($errors, $invalidFormatErrors);
                continue;
            }

            if ($i == 0) {
                // Check if term exists when team created
                $teamCreatedDate = date('Y-m-d', $team['created'] + ($team['timezone'] * HOUR));
                if ($term['start_date'] > $teamCreatedDate) {
                    $data = [
                        'teamCreatedDate' => $teamCreatedDate,
                        'firstTerm'       => $term
                    ];
                    $errors[] = sprintf("Missing initial term when team created. data:%s", var_export($data, true));
                }
            } elseif ($i == $lastIndex) {
                // Check if current term exists
                $currentDate = date('Y-m-d', $this->requestTimestamp + ($team['timezone'] * HOUR));
                if ($term['end_date'] < $currentDate) {
                    $data = [
                        'currentDate' => $currentDate,
                        'lastTerm'    => $term
                    ];
                    $errors[] = sprintf("Missing current term. data:%s", var_export($data, true));
                }
            }

            // Check if term missing teeth
            if (!empty($prevTerm)) {
                if ($prevTerm['end_date'] >= $term['start_date']) {
                    $errors[] = sprintf("Duplicate term range. data:%s", var_export(compact('prevTerm', 'term'), true));

                } elseif (AppUtil::diffDays($prevTerm['end_date'], $term['start_date']) > 1) {
                    $errors[] = sprintf("Missing teeth between terms. data:%s",
                        var_export(compact('prevTerm', 'term'), true));
                }
            }

            $prevTerm = $term;
        }

        return $errors;
    }

    /**
     * @param array $term
     *
     * @return string
     */
    public function validateStartEndDateFormat(array $term)
    {
        $errors = [];
        $startDate = $term['start_date'];
        $endDate = $term['end_date'];
        if ($startDate === '0000-00-00' || $endDate === '0000-00-00') {
            $errors[] = sprintf("Start/end date is empty. term:%s", var_export($term, true));
            return $errors;
        }

        // Whether start_date is the first day of the month
        if (!$this->isMonthFirstDay($startDate)) {
            $errors[] = sprintf("Start date is not the first day of the month. term:%s", var_export($term, true));
        }
        // Whether end_date is the last day of the month
        if (!$this->isMonthLastDay($endDate)) {
            $errors[] = sprintf("End date is not the last day of the month. term:%s", var_export($term, true));
        }
        return $errors;
    }

    /**
     * Whether start_date is the first day of the month
     *
     * @param string $date
     *
     * @return bool
     */
    public function isMonthFirstDay(string $date): bool
    {
        return $date === AppUtil::dateMonthFirst($date);
    }

    /**
     * Whether end_date is the last day of the month
     *
     * @param string $date
     *
     * @return bool
     */
    public function isMonthLastDay(string $date): bool
    {
        return $date === AppUtil::dateMonthLast($date);
    }
}
