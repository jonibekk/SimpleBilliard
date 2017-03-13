<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'TopicService');
App::import('Service', 'MessageService');
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
    function findTopicDetail(int $topicId): array
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        $ret = $TopicService->findTopicDetail($topicId);
        return $ret;
    }

}
