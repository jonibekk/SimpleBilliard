<?php
App::uses('Goal', 'Model');
App::uses('GoalGroup', 'Model');
App::uses('GoalMember', 'Model');
App::import('Policy', 'BasePolicy');

/**
 * Class GoalPolicy
 */
class GoalPolicy extends BasePolicy
{
    public function read($goal): bool
    {
        return ($this->isSameGroup($goal));
            //($this->isTeamAdminForItem($goal['team_id'])) ||
            //($this->isCoach($goal['id'])) ||
            //($this->isActiveEvaluator($goal['id'])) ||
    }

    public function update($goal): bool
    {
        return $this->isOwner($goal);
    }

    public function delete($goal): bool
    {
        return $this->isOwner($goal);
    }

    public function isOwner($goal): bool
    {
        /** @var GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');

        $isOwnerConditions = [
            'GoalMember.goal_id' => $goal['id'],
            'GoalMember.user_id' => $this->userId,
            'GoalMember.type' => $GoalMember::TYPE_OWNER
        ];

        return $GoalMember->hasAny($isOwnerConditions);
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
        //if ($this->isTeamAdmin()) {
            //return ['conditions' => ['Goal.team_id' => $this->teamId]];
        //}
        
        /** @var Goal **/
        $Goal = ClassRegistry::init('Goal');
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init('GoalGroup');

        $allPublicQuery = $Goal->publicGoalsSubquery();
        $allGroupsQuery = $GoalGroup->goalByUserIdSubQuery($this->userId);
        //$allCoacheesQuery = $Goal->coacheeGoalsSubquery($this->userId, $this->teamId);
        //$allEvaluateesQuery = $Goal->evaluateeGoalsSubquery($this->userId, $this->teamId);

        $fullQuery = 'Goal.id in (' . $allPublicQuery . ') OR 
                      Goal.id in (' . $allGroupsQuery . ')';

        //if ($type === 'read') {
            //$query = 'Goal.id in (' . $allCoacheesQuery . ') OR ';

            //if ($this->evaluationSettingEnabled()) {
                //$query .= 'Goal.id in (' . $allEvaluateesQuery . ') OR ';
            //}

            //$fullQuery = $query . $fullQuery;
        //}

        return [
            'conditions' => [
                'Goal.team_id' => $this->teamId,
                '(' . $fullQuery . ')',
            ]
        ];
    }
}
