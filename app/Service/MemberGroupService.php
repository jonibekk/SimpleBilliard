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
                        'GoalGroup.group_id' => $groupId
                    ]
                ]
            ]
        ];

        $goalsRows = $GoalMember->find('all', $options);
        $goalMemberIds = Hash::extract($goalsRows, '{n}.GoalMember.id');
        $GoalMember->updateAll(['del_flg' => true], ['GoalMember.id' => $goalMemberIds]);
    }
}
