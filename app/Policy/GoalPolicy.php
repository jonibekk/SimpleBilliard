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
            ($this->isActiveEvaluator($goal['id'])) ||
            ($this->isSameGroup($goal));
    }


    private function isSameGroup($goal): bool
    {
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');
        $hasGroup = $GoalGroup->hasAny(['GoalGroup.goal_id' => $goal['id']]);

        if (!$hasGroup) {
            return $this->teamId === (int) $goal['team_id'];
        }

        $results = $GoalGroup->find('all', [
            'conditions' => [
                'GoalGroup.goal_id' => (int)$goal['id']
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
        $allCoacheesQuery = $Goal->coacheeGoalsSubquery($this->userId);
        $allEvaluateesQuery = $Goal->evaluateeGoalsSubquery($this->userId);

        $fullQuery = 'Goal.id in (' . $allPublicQuery . ') OR 
                      Goal.id in (' . $allGroupsQuery . ')';

        if ($type === 'read') {
            $query = 'Goal.id in (' . $allCoacheesQuery . ') OR ';

            if ($this->evaluationSettingEnabled()) {
                $query .= 'Goal.id in (' . $allEvaluateesQuery . ') OR ';
            }

            $fullQuery = $query . $fullQuery;
        }

        return [
            'conditions' => [
                'Goal.team_id' => $this->teamId,
                '(' . $fullQuery . ')',
            ]
        ];
    }
}
