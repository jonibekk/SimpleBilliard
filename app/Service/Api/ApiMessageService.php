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
    // TODO fix to 20
    const MESSAGE_DEFAULT_LIMIT = 3;

    const PAGING_TYPE_NEXT = 0;
    const PAGING_TYPE_BOTH = 1;

    /**
     * Finding messages. It will returns data as API response
     *
     * @param int         $topicId
     * @param int         $loginUserId
     * @param int|null    $cursor
     * @param int|null    $limit
     * @param string|null $direction "old" or "new"
     * @param int $pagingType
     *
     * @return array
     */
    function findMessages(
        int $topicId,
        int $loginUserId,
        $cursor = null,
        $limit = null,
        $direction = Message::DIRECTION_OLD,
        $pagingType = self::PAGING_TYPE_NEXT
    ): array {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');

        // if no limit then it to be default limit
        if (!$limit) {
            $limit = self::MESSAGE_DEFAULT_LIMIT;
        }

        // getting message data
        $messages = $MessageService->findMessages($topicId, $cursor, $limit + 1, $direction);
        // converting key names for response data
        foreach ($messages as &$message) {
            $message = $this->convertKeyNames($message);
        }

        // update user last read message id
        $this->updateLastReadMessageId($messages, $topicId, $loginUserId);

        $paging = [];
        switch ($pagingType) {
            case self::PAGING_TYPE_BOTH:
                $paging = $this->getPagingBoth($messages, $topicId, $limit, $direction);
                break;
            case self::PAGING_TYPE_NEXT:
            default:
                $paging = $this->getPaging($messages, $topicId, $limit, $direction);
                break;
        }

        $selectCountEqualToLimit = (count($messages) === ($limit + 1));
        // Remove the last message of N+1
        if ($selectCountEqualToLimit) {
            array_shift($messages);
        }

        return [
            'data' => $messages,
            'paging' => $paging,
        ];
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
     * Returning next paging parameter considering paging direction new/old.
     *
     * @param array     $messages
     * @param int       $topicId
     * @param int       $limit
     * @param string    $direction
     *
     * @return array
     */
    private function getPaging(array $messages, int $topicId, int $limit, string $direction): array
    {
        $url = null;
        switch ($direction) {
            case Message::DIRECTION_NEW:
                $url = $this->getPagingUrlNew($messages, $topicId, $limit);
                break;
            case Message::DIRECTION_OLD:
            default:
                $url = $this->getPagingUrlOld($messages, $topicId, $limit);
                break;
        }

        return [
            'next' => $url,
        ];
    }

    /**
     * Resolve next paging parameter considering paging direction new/old.
     *
     * @param array $messages
     * @param int $topicId
     * @param int $limit
     * @return array
     */
    private function getPagingBoth(array $messages, int $topicId, int $limit): array
    {
        return [
            'new' => $this->getPagingUrlNew($messages, $topicId, $limit),
            'old' => $this->getPagingUrlOld($messages, $topicId, $limit),
        ];
    }

    /**
     * Resolve the old url from messages
     *
     * @param array $messages
     * @param int $topicId
     * @param int $limit
     * @return null|string
     */
    private function getPagingUrlOld(array $messages, int $topicId, int $limit)
    {
        if (empty($messages)) {
            return null;
        }
        $selectCountLessThanLimit = (count($messages) < $limit + 1);
        if ($selectCountLessThanLimit) {
            return null;
        }
        $cursorOld = reset($messages)['id'];
        return "/api/v1/topics/{$topicId}/messages?" . http_build_query([
                'cursor' => $cursorOld,
                'direction' => Message::DIRECTION_OLD,
                'limit' => $limit,
            ]);
    }

    /**
     * Resolve the new url from messages
     *
     * @param array $messages
     * @param int $topicId
     * @param int $limit
     * @return null|string
     */
    private function getPagingUrlNew(array $messages, int $topicId, int $limit)
    {
        if (empty($messages)) {
            return null;
        }
        $lastKey = count($messages) - 1;
        $messageIdNewest = $messages[$lastKey]['id'];

        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        $cursorNew = $Message->findNewerMessageId($topicId, $messageIdNewest);

        if ($cursorNew) {
            return "/api/v1/topics/{$topicId}/messages?" . http_build_query([
                    'cursor'    => $cursorNew,
                    'direction' => Message::DIRECTION_NEW,
                    'limit'     => $limit,
                ]);
        }
        return null;
    }

    /**
     * update latest read message id
     *
     * @param  array $messages
     * @param  int   $topicId
     * @param  int   $loginUserId
     */
    function updateLastReadMessageId(array $messages, int $topicId, int $loginUserId)
    {
        if (empty($messages)) {
            return;
        }
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

        // need not update if alread read
        if ($TopicMember->getLastReadMessageId($topicId, $loginUserId) == $latestMessageId) {
            return;
        }

        // update
        $TopicMember->updateLastReadMessageIdAndDate($topicId, $latestMessageId, $loginUserId);
        return;
    }

}
