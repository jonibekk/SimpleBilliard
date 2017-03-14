<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'TopicService');
App::import('Service', 'MessageService');
App::uses('Message', 'Model');

/**
 * Class ApiMessageService
 */
class ApiMessageService extends ApiService
{
    const MESSAGE_DEFAULT_LIMIT = 10;

    function findMessages(int $topicId, $cursor, $limit): array
    {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');

        // if no limit then it to be default limit
        if (!$limit) {
            $limit = self::MESSAGE_DEFAULT_LIMIT;
        }

        // it's default that will be returned
        $ret = [
            'data'   => [],
            'paging' => ['next' => null]
        ];

        // getting message data
        $messages = $MessageService->findMessages($topicId, $cursor, $limit + 1);
        // exchange key name

        //paging

        return $messages;
    }

}
