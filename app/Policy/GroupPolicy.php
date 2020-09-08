
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

    public function scope(): array
    {
        if ($this->isTeamAdmin()) {
            return [
                'conditions' => [
                    'Group.team_id' => $this->teamId
                ]
            ];
        }

        return [
            'conditions' => [
                'Group.team_id' => $this->teamId,
                'MemberGroup.user_id' => $this->userId,
            ],
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'type' => 'LEFT',
                    'conditions' => [
                        'MemberGroup.group_id = Group.id'
                    ]
                ]
            ]
        ];
    }
}
