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
     * @param int      $topicId
     * @param int|null $cursor
     * @param int|null $limit
     *
     * @return array
     */
    function findMessages(int $topicId, $cursor = null, $limit = null): array
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
        // converting key names for response data
        $messages = $this->convertKeyNames($messages);
        $ret['data'] = $messages;
        $ret = $this->setPaging($ret, $topicId, $limit);
        return $ret;
    }

    /**
     * Converting key names for response data
     *
     * @param array $messages
     *
     * @return array
     */
    function convertKeyNames(array $messages): array
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
        array_shift($data);
        $cursor = $data[0]['id'];
        $queryParams = am(compact('cursor'), compact('limit'));

        $data['paging']['next'] = "/api/v1/topics/{$topicId}/messages?" . http_build_query($queryParams);
        return $data;
    }

}
