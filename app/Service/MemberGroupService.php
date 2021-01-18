<?php
App::import('Service', 'AppService');
App::uses('MemberGroup', 'Model');
App::uses('GoalMember', 'Model');

class MemberGroupService extends AppService
{
    public function removeGroupMember(int $groupId, int $memberId)
    {
        // @var MemberGroup $MemberGroup
        $MemberGroup = ClassRegistry::init("MemberGroup");

        $MemberGroup->deleteAll([
            'user_id' => $memberId,
            'group_id' => $groupId
        ]);

        $this->removeCollaborations($groupId, $memberId);
    }

    private function removeCollaborations(int $groupId, int $memberId)
    {
        // @var GoalMember $GoalMember
        $GoalMember = ClassRegistry::init("GoalMember");
        $rows = $this->getCollaboratedGoalsWithGroup($memberId);
        $goalMemberIds = [];
        $goalsToReassignLeader = [];

        foreach ($rows as $row) {
            if ($groupId === (int) $row['GoalGroup']['group_id'] ) {
                $goalMemberIds[] = $row['GoalMember']['id'];
            }
        }

        $GoalMember->updateAll(['del_flg' => true], ['GoalMember.id' => $goalMemberIds]);
    }

    private function getCollaboratedGoalsWithGroup(int $memberId): array
    {
        // @var GoalMember $GoalMember
        $GoalMember = ClassRegistry::init("GoalMember");

        $options = [
            'conditions' => [
                'GoalMember.user_id' => $memberId
            ],
            'joins' => [
                [
                    'table' => 'goal_groups',
                    'alias' => 'GoalGroup',
                    'conditions' => [
                        'GoalGroup.goal_id = GoalMember.goal_id',
                    ]
                ]
            ],
            'group' => [
                'GoalGroup.goal_id HAVING COUNT(GoalGroup.group_id) = 1'
            ],
            'fields' => [
                'GoalMember.id',
                'GoalGroup.group_id',
            ]
        ];

        return $GoalMember->find('all', $options);
    }
}
