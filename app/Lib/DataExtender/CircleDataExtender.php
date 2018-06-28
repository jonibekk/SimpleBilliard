<?php
App::uses("Circle", "Model");
App::import('Lib/DataExtender', 'DataExtender');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/12
 * Time: 15:40
 */
class CircleDataExtender extends DataExtender
{
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

        $fetchedData = $Circle->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. Circle ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.Circle.id'))));
        }

        return $fetchedData;
    }
}