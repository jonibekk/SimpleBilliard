<?php
App::uses("Circle", "Model");
App::uses('CircleMember', 'Model');
App::import('Lib/DataExtender/Extension', 'DataExtension');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/12
 * Time: 15:40
 */
class CircleExtension extends DataExtension
{
    private $userId;

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    protected function fetchData(array $keys): array
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init("Circle");

        //Remove null values
        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'id' => $uniqueKeys
            ],
        ];

        $fetchedData = $Circle->useType()->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. Circle ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.Circle.id'))));
        }

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        foreach ($fetchedData as $i => $circles) {
            $circleId = $circles['Circle']['id'];
            if (!empty($this->userId)) {
                $fetchedData[$i]['Circle']['is_member'] = $CircleMember->isJoined($circleId, $this->userId);
            }
        }


        return $fetchedData;
    }
}
