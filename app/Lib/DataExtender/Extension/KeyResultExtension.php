<?php
App::uses("KeyResult", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class KeyResultExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        //Remove null values
        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'id' => $uniqueKeys
            ],
        ];

        $fetchedData = $KeyResult->useType()->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. KeyResult ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.KeyResult.id'))));
        }

        return $fetchedData;
    }
}
