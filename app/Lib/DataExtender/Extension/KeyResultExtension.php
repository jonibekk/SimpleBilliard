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

        return $fetchedData;
    }
}
