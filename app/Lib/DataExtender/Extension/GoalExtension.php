<?php
App::uses("Goal", "Model");
App::uses("Group", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');
App::import('Service', 'ImageStorageService');

class GoalExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        //Remove null values
        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'id' => $uniqueKeys,
            ]
        ];

        $fetchedData = $Goal->useType()->find('all', $conditions);
        $groups = $this->appendGroups($uniqueKeys);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. Goal ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.Goal.id'))));
        }

        // Set profile image url each data
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        foreach ($fetchedData as $i => $v) {
            $fetchedData[$i]['Goal']['photo_img_url'] = $ImageStorageService->getImgUrlEachSize($fetchedData[$i], 'Goal');
            $fetchedData[$i]['Goal']['groups'] = [];
            $goalId = $fetchedData[$i]['Goal']['id'];

            if (!empty($groups[$goalId])) {
                $fetchedData[$i]['Goal']['groups'] = $groups[$goalId];
            }

            unset($fetchedData[$i]['GoalGroup']);
        }

        return $fetchedData;
    }

    function appendGroups($goalIds)
    {
        /** @var Group */
        $Group = ClassRegistry::init("Group");

        $scope = [
            'joins' => [
                [
                    'alias' => 'GoalGroup',
                    'table' => 'goal_groups',
                    'conditions' => [
                        'GoalGroup.group_id = Group.id',
                        'GoalGroup.goal_id' => $goalIds
                    ]
                ]
            ],
            'group' => [
                'GoalGroup.goal_id',
            ],
            'fields' => [
                'Group.*',
                'GoalGroup.goal_id',
            ]
        ];

        $rows = $Group->findGroupsWithMemberCount($scope);

        $processed = [];
        foreach ($rows as $row) {
            $goalId = $row['GoalGroup']['goal_id'];

            if (!array_key_exists($goalId, $processed)) {
                $processed[$goalId] = [];
            }
            $processed[$goalId][] = $row['Group'];
        }

        return $processed;
    }
}
