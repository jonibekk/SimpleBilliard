<?php
App::uses("User", "Model");
App::uses('DataExtender', 'Lib/DataExtension');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/08
 * Time: 16:46
 */
class UserDataExtender extends DataExtender
{
    protected function fetchData(array $keys): array
    {
        /** @var User $User */
        $User = ClassRegistry::init("User");

        $uniqueKeys = array_unique($keys);

        $conditions = [
            'conditions' => [
                'id' => $uniqueKeys
            ],
            'fields'     => $User->profileFields
        ];

        $fetchedData = $User->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, 'id'))));
        }

        return $fetchedData;
    }
}