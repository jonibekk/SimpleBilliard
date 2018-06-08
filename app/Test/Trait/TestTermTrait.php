<?php
App::uses('Term', 'Model');

use Goalous\Enum as Enum;

trait TestTermTrait
{
    /**
     * Create Term for test
     *
     * @param int                      $teamId
     * @param GoalousDateTime          $startDate
     * @param int                      $termMonth
     * @param Enum\Model\Term\EvaluateStatus $evaluateStatus
     *
     * @return array
     */
    protected function createTerm(int $teamId, GoalousDateTime $startDate, int $termMonth, Enum\Model\Term\EvaluateStatus $evaluateStatus): array
    {
        $format = 'Y-m-d';
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        $Term->create();
        $term = $Term->save([
            'team_id'         => $teamId,
            'start_date'      => $startDate->format($format),
            'end_date'        => $startDate->addMonth($termMonth)->modify('last day of last month')->format($format),
            'evaluate_status' => $evaluateStatus->getValue(),
        ]);
        return reset($term);
    }
}