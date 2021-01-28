<?php
App::import('Service', 'AppService');
App::uses('MemberGroup', 'Model');
App::uses('GoalMember', 'Model');
App::uses('NotifySetting', 'Model');
App::uses('NotifyBizComponent', 'Controller/Component');

class MemberGroupService extends AppService
{
    public function removeGroupMember(int $groupId, int $memberId)
    {
        /** @var MemberGroup $MemberGroup */
        $MemberGroup = ClassRegistry::init("MemberGroup");

        $MemberGroup->deleteAll([
            'user_id' => $memberId,
            'group_id' => $groupId
        ]);

        $this->removeCollaborations($groupId, $memberId);
    }

    /** 
     * When member is removed from group
     * - remove all non-goal-leader collaborations
     * - for goals where that member is the leader
     *      - for goals that he shared with that group, reassign goal leader to earliest collaborator
     *      - if no earliest collaborator present 
     *          - remove the group from the list of shared groups for the goal instead. 
     *          - maintain that user as the goal leader
        */
    private function removeCollaborations(int $groupId, int $memberId)
    {
        /** @var GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init("GoalGroup");

        $rows = $this->getForGroupAndMember($groupId, $memberId);
        $rows = $this->appendStats($rows, $memberId);
        $results = $this->processMembersGoals($rows);

        $GoalMember->updateAll(
            ['del_flg' => true], 
            ['GoalMember.id' => $results['removeCollab']]
        );

        $GoalGroup->deleteAll([
            'GoalGroup.goal_id' => $results['removeGroup'], 
            'GoalGroup.group_id' => $groupId
        ]);

        $GoalMember->updateAll(
            ['del_flg' => true], 
            ['GoalMember.goal_id' => $results['reassignLeader'], 'GoalMember.user_id' => $memberId]
        );

        foreach ($results['reassignLeader'] as $goalId) {
            $this->reassignGoalLeader($goalId);
        }
    }

    private function processMembersGoals(array $rows)
    {
        $removeCollab = [];
        $removeGroup = [];
        $reassignLeader = [];

        foreach ($rows as $row) {
            $isLeader = $row['GoalMember']['type'] == GoalMember::TYPE_OWNER;

            if ($row['shouldRetainAccess']) {
                continue;

            } else if (!$isLeader) {
                $removeCollab[] = $row['GoalMember']['id'];

            } else if ($row['otherCollabsPresent']){
                $reassignLeader[] = $row['GoalMember']['goal_id'];

            } else if ($row['multipleSharedGroups']) {
                $removeGroup[] = $row['GoalMember']['goal_id'];

            } else if (!!$row['multipleSharedGroups']) {
                $removeCollab[] = $row['GoalMember']['id'];
            }
        }

        return [
            'removeCollab' => $removeCollab,
            'removeGroup' => $removeGroup,
            'reassignLeader' => $reassignLeader,
        ];
    }

    private function getForGroupAndMember(int $groupId, int $memberId): array
    {
        /** @var GoalMember */
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
            ],
        ];

        return $GoalMember->find('all', $options);
    }

    private function appendStats(array $goalMemberRows, int $memberId): array
    {
        /** @var GoalGroup */
        $GoalGroup = ClassRegistry::init("GoalGroup");
        $goalIds = Hash::extract($goalMemberRows, '{n}.GoalMember.goal_id');

        $baseOpts = [
            'conditions' => [
                'GoalGroup.goal_id' => $goalIds
            ],
            'joins' => [],
            'group' => ['GoalGroup.goal_id'],
            'fields' => ['GoalGroup.goal_id', 'COUNT(GoalGroup.group_id) AS count']
        ];

        $otherCollabOpts = $baseOpts;
        $otherCollabOpts['joins'][] = [
            'alias' => 'MemberGroup',
            'table' => 'member_groups',
            'conditions' => [
                'MemberGroup.group_id = GoalGroup.group_id',
                'MemberGroup.del_flg != 1',
                'MemberGroup.user_id !=' => $memberId
            ]
        ];

        $ownCollabOpts = $baseOpts;
        $ownCollabOpts['joins'][] = [
            'alias' => 'MemberGroup',
            'table' => 'member_groups',
            'conditions' => [
                'MemberGroup.group_id = GoalGroup.group_id',
                'MemberGroup.del_flg != 1',
                'MemberGroup.user_id' => $memberId
            ]
        ];

        $rowsWithNumSharedGroups = $GoalGroup->find('all', $baseOpts);
        $rowsWithOtherCollabs = $GoalGroup->find('all', $otherCollabOpts);
        $rowsWithOwnCollabs = $GoalGroup->find('all', $ownCollabOpts);

        $results = [];
        foreach ($goalMemberRows as $goalMemberRow) {
            $goalId = $goalMemberRow['GoalMember']['goal_id'];

            foreach ($rowsWithOwnCollabs as $row) {
                if ($row['GoalGroup']['goal_id'] === $goalId) {
                    $accessibleGropCount = $row[0]['count'];
                    $goalMemberRow['shouldRetainAccess'] = $accessibleGropCount > 1;
                }
            }
            foreach ($rowsWithNumSharedGroups as $row) {
                if ($row['GoalGroup']['goal_id'] === $goalId) {
                    $numSharedGroups = $row[0]['count'];
                    $goalMemberRow['multipleSharedGroups'] = $numSharedGroups > 1;
                }
            }
            foreach ($rowsWithOtherCollabs as $row) {
                if ($row['GoalGroup']['goal_id'] === $goalId) {
                    $numOtherCollabs = $row[0]['count'];
                    $goalMemberRow['otherCollabsPresent'] = $numOtherCollabs > 0;
                }
            }
            $results[] = $goalMemberRow;
        }

        return $results;
    }

    private function reassignGoalLeader(int $goalId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        $NotifyBiz = new NotifyBizComponent(new ComponentCollection());

        $options = [
            'conditions' => [
                'GoalMember.goal_id' => $goalId,
                'GoalMember.type' => $GoalMember::TYPE_COLLABORATOR,
            ],
            'order' => [
                'created' => 'asc'
            ]
        ];

        $row = $GoalMember->find('first', $options);

        $GoalMember->updateAll(
            ['type' => $GoalMember::TYPE_OWNER ], 
            [
                'GoalMember.goal_id' => $goalId, 
                'GoalMember.user_id' => $row['GoalMember']['user_id'], 
            ]
        );

        $NotifyBiz->execSendNotify(NotifySetting::TYPE_EXCHANGED_LEADER, $goalId);
    }
}
