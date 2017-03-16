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
    function findTopicDetailInitData(int $topicId): array
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        $topicDetail = $TopicService->findTopicDetail($topicId);

        $ret = [
            'topic'    => $topicDetail,
            'messages' => [], //TODO: start to implement in https://jira.goalous.com/browse/GL-5673
            'paging'   => [
                'next' => "",
            ]
        ];
        return $ret;
    }

}
