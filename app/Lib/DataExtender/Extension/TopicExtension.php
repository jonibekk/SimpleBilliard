<?php
App::uses("Topic", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/17/2018
 * Time: 5:26 PM
 */
class TopicExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        $keys = $this->filterKeys($keys);

        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        $condition =[
            'conditions' => [
                'Topic.id' => $keys,
                'Topic.del_flg' => false
            ],
        ];

        return $Topic->useType()->find('all', $condition);
    }

}