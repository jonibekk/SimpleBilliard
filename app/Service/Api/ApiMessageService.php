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
        // exchange response data
        $messages = $this->exchangeResponseData($messages);
        // paging
        if (count($messages) == $limit + 1) {
            // exclude that extra record for paging
            array_shift($messages);
            $cursor = $messages[0]['id'];
            $ret['paging']['next'] = "/api/v1/topics/$topicId/messages?cursor=$cursor&limit=$limit";
        }
        $ret['data'] = $messages;

        return $ret;
    }

    function exchangeResponseData(array $messages): array
    {
        foreach ($messages as &$message) {
            $innerMessage = $message['Message'];
            $senderUser = $message['SenderUser'];
            $attachedFiles = Hash::extract($message['MessageFile'], '{n}.AttachedFile');
            $message = $innerMessage;
            $message['user'] = $senderUser;
            $message['attached_files'] = $attachedFiles;
        }
        return $messages;
    }

}
