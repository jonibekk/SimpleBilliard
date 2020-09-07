<?php
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Evaluation', 'Model');

/**
 * Class BasePolicy
 */
class BasePolicy
{
    /** @var int **/
    protected $userId;

    /** @var int **/
    protected $teamId;

    function __construct(int $userId, int $teamId)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    protected function isTeamAdmin(): bool
    {
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init('TeamMember');
        return $TeamMember->isActiveAdmin($this->userId, $this->teamId);
    }

    protected function isTeamAdminForItem(int $teamId): bool
    {
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init('TeamMember');
        return $TeamMember->isActiveAdmin($this->userId, $teamId);
    }

    protected function groupsFeatureEnabled(): bool
    {
        /** @var Team **/
        $Team = ClassRegistry::init('Team');
        $team = $Team->findById($this->teamId);
        return $team['Team']['groups_enabled_flg'];
    }

    protected function isCoach($goalId): bool
    {
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init('TeamMember');
        $result = $TeamMember->find('first', [
            'conditions' => [
                'TeamMember.coach_user_id' => $this->userId,
                'TeamMember.team_id' => $this->teamId
            ],
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.user_id = TeamMember.user_id',
                    ]
                ],
                [
                    'alias' => 'GoalGroup',
                    'table' => 'goal_groups',
                    'conditions' => [
                        'GoalGroup.group_id = MemberGroup.group_id',
                        'GoalGroup.goal_id' => $goalId,
                    ]
                ],
            ]
        ]);

        return !empty($result);
    }

    /** 
     * an evaluator should have access to:
     *   - actions made directly by evaluatee
     *   - goals related to the actions made by evaluatee
     *   - actions not made by evaluatee, but related to the goals that the evaluatee has made actions for
     **/
    protected function isActiveEvaluator($goalId): bool
    {
        /** @var Evaluation **/
        $Evaluation = ClassRegistry::init('Evaluation');
        /** @var Term **/
        $Term = ClassRegistry::init('Term');

        $result = $Evaluation->find('first', [
            'conditions' => [
                'Evaluation.evaluator_user_id' => $this->userId,
                'Evaluation.team_id' => $this->teamId
            ],
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        'MemberGroup.user_id = Evaluation.evaluatee_user_id',
                    ]
                ],
                [
                    'alias' => 'GoalGroup',
                    'table' => 'goal_groups',
                    'conditions' => [
                        'GoalGroup.group_id = MemberGroup.group_id',
                        'GoalGroup.goal_id' => $goalId,
                    ]
                ],
                [
                    'alias' => 'Term',
                    'table' => 'terms',
                    'conditions' => [
                        'Term.id = Evaluation.term_id',
                        'Term.evaluate_status' => $Term::STATUS_EVAL_IN_PROGRESS,
                    ]
                ],
            ]
        ]);



        return !empty($result);
    }
}
