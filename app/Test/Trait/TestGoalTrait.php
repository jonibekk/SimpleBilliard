<?php
App::uses('Goal', 'Model');
App::uses('KeyResult', 'Model');
App::uses('GoalMember', 'Model');
App::import('Service', 'GoalService');

use Goalous\Model\Enum as Enum;

trait TestGoalTrait
{
    /**
     * Create Goal for test
     *
     * @param int             $userId
     * @param int             $teamId
     * @param GoalousDateTime $startDate
     * @param GoalousDateTime $endDate
     *
     * @return array
     */
    public function createGoalSimple(int $userId, int $teamId, GoalousDateTime $startDate, GoalousDateTime $endDate): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        $Goal->current_team_id = $teamId;
        $Goal->my_uid = $userId;
        $KeyResult->current_team_id = $teamId;
        $GoalMember->current_team_id = $teamId;

        //実行月の期間1ヶ月で生成される。開始日:当月の月初、終了日:当月の月末
        $goalCreationData = [
            "name"             => "Goal",
            "goal_category_id" => 1,
            "labels"           => [
                "0" => "Goalous"
            ],
            'term_type'        => '',
            "priority"         => 5,
            "description"      => "Goal description",
            "is_wish_approval" => true,
            "key_result"       => [
                "value_unit"   => 0,
                "start_value"  => 0,
                "target_value" => 100,
                "name"         => "TKR1",
                "description"  => "TKR description",
            ],
            'start_date'       => $startDate->format('Y-m-d'),
            'end_date'         =>   $endDate->format('Y-m-d'),
        ];

        $createdGoalId = $GoalService->create($userId, $goalCreationData);
        return $Goal->getById($createdGoalId);
    }

    /**
     * Make a goal to be target of evaluate for test
     *
     * @param int $goalId
     */
    public function makeGoalAsTargetEvaluation(int $goalId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');

        $GoalMember->updateAll(
            [
                'GoalMember.is_target_evaluation' => GoalMember::IS_TARGET_EVALUATION,
            ],
            [
                'GoalMember.goal_id' => $goalId,
            ]
        );

        $goalMembers = $GoalMember->findAllByGoalId($goalId);
    }
}