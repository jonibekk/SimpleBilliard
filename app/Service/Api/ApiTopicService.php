<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'TopicService');
App::import('Service', 'MessageService');
App::import('Service/Api', 'ApiMessageService');
App::uses('TopicMember', 'Model');

/**
 * Class ApiTopicService
 */
class ApiTopicService extends ApiService
{

    /**
     * Find topic detail including latest messages.
     *
     * @param int $topicId
     *
     * @return array
     */
    function findTopicDetailInitData(int $topicId): array
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        $topicDetail = $TopicService->findTopicDetail($topicId);

        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init('ApiMessageService');
        $messageData = $ApiMessageService->findMessages($topicId);

        $ret = [
            'topic'    => $topicDetail,
            'messages' => $messageData,
        ];
        return $ret;
    }

}
