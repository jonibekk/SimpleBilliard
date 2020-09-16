
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
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init('TeamMember');

        $res = $TeamMember->find('first', [
            'TeamMember.team_id' => $group['team_id'],
            'TeamMember.user_id' => $this->userId
        ]);

        return (bool)$res;
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
        if ($this->isTeamAdmin()) {
            return [
                'conditions' => [
                    'Group.team_id' => $this->teamId
                ]
            ];
        }
        /** @var Group **/
        $Group = ClassRegistry::init('Group');

        $ownGroupsSubquery = $Group->groupByUserIdSubQuery($this->userId, $this->teamId);
        $coacheeGroupsSubquery = $Group->groupForCoacheesSubQuery($this->userId, $this->teamId);
        $evaluateeGroupsSubquery = $Group->groupForEvaluateesSubQuery($this->userId, $this->teamId);

        $fullQuery = 'Group.id IN (' . $ownGroupsSubquery . ')';

        if ($type === "search") {
            $fullQuery = 'Group.id IN (' . $coacheeGroupsSubquery . ') OR ' . $fullQuery;
            $fullQuery = 'Group.id IN (' . $evaluateeGroupsSubquery . ') OR ' . $fullQuery;
        }

        return [
            'conditions' => [
                'Group.team_id' => $this->teamId,
                '(' . $fullQuery . ')'
            ]
        ];
    }
}
