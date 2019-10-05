<?php
App::uses("Post", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');
/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 19/01/23
 * Time: 23:34
 */

class PostExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        //Remove null values
        $uniqueKeys = $this->filterKeys($keys);

        $conditions = [
            'conditions' => [
                'id' => $uniqueKeys
            ],
        ];

        $fetchedData = $Post->useType()->find('all', $conditions);

        if (count($fetchedData) != count($uniqueKeys)) {
            GoalousLog::error("Missing data for data extension. Post ID: " . implode(',',
                    array_diff($uniqueKeys, Hash::extract($fetchedData, '{n}.Post.id'))));
        }

        return $fetchedData;
    }
}