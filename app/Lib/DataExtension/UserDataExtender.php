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
    protected function fetchData(array $idArray): array
    {
        /** @var  .\Model\User $User */
        $User = ClassRegistry::init("User");

        $uniqueId = array_unique($idArray);

        $conditions = [
            'conditions' => [
                'id' => $uniqueId
            ],
            'fields'     => $User->profileFields
        ];

        $fetchedData = $User->find('list', $conditions);

        if (count($fetchedData) != count($uniqueId)) {
            GoalousLog::error("Missing data for data extension");
        }

        return $fetchedData;
    }
}