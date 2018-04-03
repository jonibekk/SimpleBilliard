<?php
App::uses('Term', 'Model');

use Goalous\Model\Enum as Enum;

trait TestTermTrait
{
    protected function createTerm(int $teamId, GoalousDateTime $startDate, int $termMonth, Enum\Term\EvaluateStatus $evaluateStatus): array
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