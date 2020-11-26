
<?php
App::uses('Group', 'Model');
App::import('Policy', 'BasePolicy');

/**
 * Class GroupPolicy
 */
class GroupPolicy extends BasePolicy
{
    public function read($group): bool
    {
        return $this->teamId === (int) $group['team_id'];
    }

    public function create($group): bool
    {
        return $this->isTeamAdminForItem($group['team_id']);
    }

    public function update($group): bool
    {
        return $this->isTeamAdminForItem($group['team_id']);
    }

    public function scope($type = 'read'): array
    {
        if ($type === 'manage' && $this->isTeamAdmin()) {
            return [
                'conditions' => [
                    'Group.team_id' => $this->teamId
                ]
            ];
        }
        
        /** @var Group **/
        $Group = ClassRegistry::init('Group');

        $ownGroupsSubquery = $Group->groupByUserIdSubQuery($this->userId);
        $coacheeGroupsSubquery = $Group->groupForCoacheesSubQuery($this->userId);
        $evaluateeGroupsSubquery = $Group->groupForEvaluateesSubQuery($this->userId);

        $fullQuery = 'Group.id IN (' . $ownGroupsSubquery . ')';

        //if ($type === "search") {
            //$fullQuery = 'Group.id IN (' . $coacheeGroupsSubquery . ') OR ' . $fullQuery;
            //$fullQuery = 'Group.id IN (' . $evaluateeGroupsSubquery . ') OR ' . $fullQuery;
        //}

        return [
            'conditions' => [
                'Group.team_id' => $this->teamId,
                '(' . $fullQuery . ')'
            ]
        ];
    }
}
