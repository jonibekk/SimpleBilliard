<?php
App::uses("User", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');
App::import('Service', 'ImageStorageService');

class UserExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        /** @var User $User */
        $User = ClassRegistry::init("User");

        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'User.id' => $uniqueKeys
            ],
            'table'      => 'users',
            'alias'      => 'User',
            'fields'     => $User->profileFields
        ];

        $fetchedData = $User->useType()->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. User ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.User.id'))));
        }

        // Set profile image url each data
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');
        foreach ($fetchedData as $i => $v) {
            $fetchedData[$i]['User']['profile_img_url'] = $ImageStorageService->getImgUrlEachSize($fetchedData[$i], 'User');
        }

        return $fetchedData;
    }
}
