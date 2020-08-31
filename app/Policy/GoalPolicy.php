<?php
App::uses('Goal', 'Model');
App::uses('GoalGroup', 'Model');
App::import('Policy', 'BasePolicy');

/**
 * Class GoalPolicy
 */
class GoalPolicy extends BasePolicy
{
    public function read($goal): bool
    {
        return ((int)$goal['user_id'] === $this->userId) ||
            ($this->isTeamAdminForItem($goal['team_id'])) ||
            ($this->isCoach($goal)) ||
            ($this->isActiveEvaluator($goal)) ||
            ($this->isSameGroup($goal));
    }


    private function isSameGroup($goal): bool
    {
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');

        // check if goal is linked to any groups, none means it is visible to entire team
        if (!$GoalGroup->hasAny(['GoalGroup.goal_id' => $goal['id']])) {
            return $goal['team_id'] === $this->teamId;
        }

        $results = $GoalGroup->find('all', [
            'conditions' => [
                'GoalGroup.goal_id' => $goal['id']
            ],
            'joins' => [$GoalGroup->joinByUserId($this->userId)]
        ]);

        return !empty($results);
    }
}
