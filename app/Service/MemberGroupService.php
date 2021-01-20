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
        /* @var GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /* @var GoalGroup */
        $GoalGroup = ClassRegistry::init("GoalGroup");

        $rows = $this->getCollaboratedGoalsWithGroup($groupId, $memberId);
        $goalsToRemoveCollaboration = [];
        $goalsToReassignLeader = [];
        $goalsToRemoveGroup = [];

        foreach ($rows as $row) {
            $isLeader = $row['GoalMember']['type'] == $GoalMember::TYPE_OWNER;
            $multipleSharedGroups = $row[0]['num_shared_groups'] != 0;
            $otherCollabsPresent = $row[0]['num_other_collaborators'] != 0;

            if (!$isLeader) {
                $goalsToRemoveCollaboration[] = $row['GoalMember']['id'];

            } else if ($otherCollabsPresent){
                $goalstoreassignleader[] = $row['goalmember']['goal_id'];

            } else if (!$otherCollabsPresent && $multipleSharedGroups) {
                $goalsToRemoveGroup[] = $row['GoalMember']['goal_id'];

            } else if (!$otherCollabsPresent && !$multipleSharedGroups) {
                $goalsToRemoveCollaboration[] = $row['GoalMember']['id'];
            }
        }

        $GoalMember->updateAll(['del_flg' => true], ['GoalMember.id' => $goalsToRemoveCollaboration]);
        $GoalGroup->deleteAll([
            'GoalGroup.goal_id' => $goalsToRemoveGroup, 
            'GoalGroup.group_id' => $groupId
        ]);

        $GoalMember->updateAll(
            ['del_flg' => true], 
            ['GoalMember.goal_id' => $goalsToReassignLeader, 'GoalMember.user_id' => $memberId]
        );

        foreach ($goalsToReassignLeader as $goalId) {
            $this->reassignGoalLeader($goalId);
        }
    }

    private function getCollaboratedGoalsWithGroup(int $groupId, int $memberId): array
    {
        /* @var GoalMember */
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
                ],
                [
                    'type' => 'LEFT',
                    'table' => 'goal_members',
                    'alias' => 'OtherCollaborator',
                    'conditions' => [
                        'OtherCollaborator.goal_id = GoalMember.goal_id',
                        'OtherCollaborator.del_flg != 1',
                        'OtherCollaborator.user_id !=' => $memberId,
                    ]
                ],
            ],
            'group' => 'GoalMember.goal_id',
            'fields' => [
                'GoalMember.*',
                'COUNT(GoalGroup.group_id) AS num_shared_groups',
                'COUNT(OtherCollaborator.user_id) AS num_other_collaborators',
            ]
        ];

        return $GoalMember->find('all', $options);
    }

    private function reassignGoalLeader(int $goalId)
    {
        /* @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
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
    }
}
