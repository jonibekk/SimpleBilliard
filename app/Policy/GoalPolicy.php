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
            ($this->isCoach($goal['id'])) ||
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

    public function scope($type = 'read'): array
    {
        if ($this->isTeamAdmin()) {
            return ['conditions' => ['Goal.team_id' => $this->teamId]];
        }
        /** @var Goal **/
        $Goal = ClassRegistry::init('Goal');
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');

        $allPublicQuery = $Goal->publicGoalsSubquery();
        $allGroupsQuery = $GoalGroup->goalByUserIdSubQuery($this->userId);
        $allCoacheesQuery = '';
        if ($type === 'read') {
            $allCoacheesQuery = $Goal->coacheeGoalsSubquery($this->userId);
        }

        $result =  [
            'conditions' => [
                'Goal.id in (' . $allPublicQuery . ') OR 
                 Goal.id in (' . $allCoacheesQuery . ') OR
                 Goal.id in (' . $allGroupsQuery . ')'
            ],
        ];

        return $result;
    }
}
