<?php
App::uses("ActionResult", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

class ActionExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");

        //Remove null values
        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'id' => $uniqueKeys
            ],
        ];

        $fetchedData = $ActionResult->useType()->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. ActionResult ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.ActionResult.id'))));
        }

        return $fetchedData;
    }
}
