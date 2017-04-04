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

    /**
     * Finding messages. It will returns data as API response
     *
     * @param int         $topicId
     * @param int         $loginUserId
     * @param int|null    $cursor
     * @param int|null    $limit
     * @param string|null $direction "old" or "new"
     *
     * @return array
     */
    function findMessages(int $topicId, int $loginUserId, $cursor = null, $limit = null, $direction = Message::DIRECTION_OLD
    ): array
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
        $messages = $MessageService->findMessages($topicId, $cursor, $limit + 1, $direction);
        // converting key names for response data
        foreach ($messages as &$message) {
            $message = $this->convertKeyNames($message);
        }

        $ret['data'] = $messages;

        // update user last read message id
        $this->updateLastReadMessageId($ret['data'], $topicId, $loginUserId);

        if ($direction == Message::DIRECTION_OLD) {
            $ret = $this->setPaging($ret, $topicId, $limit);
        }
        return $ret;
    }

    /**
     * Getting a message as api response data
     *
     * @param int $messageId
     *
     * @return array
     */
    function get(int $messageId): array
    {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');
        // find latest message
        $message = $MessageService->get($messageId);
        // converting key names for response data
        $message = $this->convertKeyNames($message);
        return $message;
    }

    /**
     * Converting key names for response data
     *
     * @param array $message
     *
     * @return array
     */
    function convertKeyNames(array $message): array
    {
        $innerMessage = $message['Message'];
        $senderUser = $message['SenderUser'];
        $attachedFiles = Hash::extract($message['MessageFile'], '{n}.AttachedFile');

        $message = $innerMessage;
        $message['user'] = $senderUser;
        $message['attached_files'] = $attachedFiles;
        return $message;
    }

    /**
     * Setting paging Information
     * - $data includes extra record that will be removed.
     *
     * @param array $data
     * @param int   $topicId
     * @param int   $limit
     *
     * @return array
     */
    private function setPaging(array $data, int $topicId, int $limit): array
    {
        // If next page is not exists, return
        if (count($data['data']) < $limit + 1) {
            return $data;
        }
        // exclude that extra record for paging
        array_pop($data['data']);
        $cursor = end($data['data'])['id'];
        $queryParams = am(compact('cursor'), compact('limit'));

        $data['paging']['next'] = "/api/v1/topics/{$topicId}/messages?" . http_build_query($queryParams);
        return $data;
    }

    /**
     * update latest read message id
     *
     * @param  array  $messages
     * @param  int    $topicId
     * @param  int    $loginUserId
     */
    function updateLastReadMessageId(array $messages, int $topicId, int $loginUserId)
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        // fetch latest message id
        $latestMessageId = $Topic->getLatestMessageId($topicId);
        if (empty($latestMessageId)) {
            $this->log(sprintf("Failed to get latest message. topicId: %s loginUserId: %s", $topicId, $loginUserId));
        }

        // extract latest message by messages
        $latestMessage = Hash::extract($messages, "{n}[id={$latestMessageId}]");
        if (empty($latestMessage)) {
            return;
        }

        // need not update if latest message is mine
        if ($latestMessage[0]['user']['id'] == $loginUserId) {
            return;
        }

        // update
        $TopicMember->updateLastReadMessageId($topicId, $latestMessageId, $loginUserId);
        return;
    }

}
