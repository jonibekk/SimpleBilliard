<?php
App::import('Service/Paging/Search', 'BaseSearchPagingService');
App::uses('Message', 'Model');
App::uses('Topic', 'Model');
App::uses('User', 'Model');
App::import('Model/Entity', 'TopicEntity');
App::import('Model/Entity', 'UserEntity');
App::import('Lib/DataExtender/Extension', 'TopicExtension');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");
App::import('Service', "TopicService");

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/17/2018
 * Time: 2:16 PM
 */
class TopicSearchPagingService extends BaseSearchPagingService
{
    const ES_SEARCH_PARAM_MODEL = 'topic';

    protected function setCondition(ESPagingRequest $pagingRequest): ESPagingRequest
    {
        $pagingRequest->addQueryToCondition('keyword', false);
        $pagingRequest->addQueryToCondition('limit', false, self::DEFAULT_PAGE_LIMIT);
        $pagingRequest->addQueryToCondition('category', false, 1);

        return $pagingRequest;
    }

    protected function fetchData(ESPagingRequest $pagingRequest): ESSearchResponse
    {
        $ESClient = new ESClient();

        $query = $pagingRequest->getCondition('keyword');

        $teamId = $pagingRequest->getTempCondition('team_id');

        $params[static::ES_SEARCH_PARAM_MODEL] = [
            'pn'       => intval($pagingRequest->getCondition('pn')),
            'rn'       => intval($pagingRequest->getCondition('limit')),
            'category' => intval($pagingRequest->getCondition('category')),
            'user_id'  => intval($pagingRequest->getTempCondition('user_id'))
        ];

        return $ESClient->search($query, $teamId, $params);
    }

    protected function extendData(array $baseData, ESPagingRequest $request): array
    {
        if (empty($baseData)) {
            return [];
        }

        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');
        /** @var TopicExtension $TopicExtension */
        $TopicExtension = ClassRegistry::init('TopicExtension');
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');

        $resultArray = $TopicExtension->extendMulti($baseData, '{n}.id');

        //Extend display created
        $TimeEx = new TimeExHelper(new View());

        $userId = $request->getTempCondition('user_id');
        foreach ($resultArray as &$result) {
            $result['highlight_member_count'] = count(Hash::get($result,'highlight_member', []));
            $result['display_created'] = $TimeEx->elapsedTime($result['topic']['latest_message_datetime'], 'rough', false);
            $users = $Topic->getLatestSenders($result['id'], $userId, Topic::MAX_DISPLAYING_USER_PHOTO, true);
            /** @var UserEntity $user */
            $result['users'] = [];
            foreach ($users as $user){
                $result['users'][] = $user->toArray();
            }

            $result['topic']['display_title'] = $TopicService->getDisplayTopicTitle($result['topic'], $userId);
            $result['topic']['members_count'] = $TopicService->countMembers($result['topic']['id']);
        }

        return $resultArray;
    }

}
