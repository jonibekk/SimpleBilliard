<?php
App::uses('Team', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Evaluator', 'Model');

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

    protected function isCoach($resource): bool
    {
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init('TeamMember');
        $result = $TeamMember->find('first', [
            'conditions' => [
                'TeamMember.user_id' => $resource['user_id'],
                'TeamMember.coach_user_id' => $this->userId,
                'TeamMember.team_id' => $this->teamId
            ]
        ]);

        return !empty($result);
    }

    protected function isActiveEvaluator($resource): bool
    {
        /** @var Evaluator **/
        $Evaluator = ClassRegistry::init('Evaluator');
        $result = $Evaluator->find('first', [
            'conditions' => [
                'Evaluator.evaluatee_user_id' => $resource['user_id'],
                'Evaluator.evaluator_user_id' => $this->userId,
                'Evaluator.team_id' => $this->teamId
            ]
        ]);

        return !empty($result);
    }
}
