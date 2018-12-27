<?php
App::uses("Message", "Model");
App::import('Lib/DataExtender/Extension', 'DataExtension');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/18/2018
 * Time: 3:22 PM
 */
class MessageExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');

        $uniqueKeys = $this->filterKeys($keys);

        $condition = [
            'conditions' => [
                'Message.id'      => $uniqueKeys,
                'Message.del_flg' => false
            ]
        ];

        return $Message->useType()->find('all', $condition);
    }

}