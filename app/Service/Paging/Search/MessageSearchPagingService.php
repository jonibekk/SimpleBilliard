<?php
App::uses('Message', 'Model');
App::uses('Topic', 'Model');
App::uses('User', 'Model');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");
App::import('Lib/DataExtender', 'MessageExtender');
App::import('Lib/DataExtender/Extension', 'MessageExtension');
App::import('Lib/DataExtender/Extension', 'TopicExtension');
App::import('Lib/DataExtender/Extension', 'UserExtension');
App::import('Service/Paging/Search', 'BaseSearchPagingService');
App::import('Model/Entity', 'UserEntity');
App::import('Service', "TopicService");

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/18/2018
 * Time: 3:19 PM
 */
class MessageSearchPagingService extends BaseSearchPagingService
{
    const ES_SEARCH_PARAM_MODEL = 'message';

    protected function setCondition(ESPagingRequest $pagingRequest): ESPagingRequest
    {
        $pagingRequest->addQueryToCondition('keyword', false);
        $pagingRequest->addQueryToCondition('limit', false, self::DEFAULT_PAGE_LIMIT);
        $pagingRequest->addQueryToCondition('topic_id', false, 0);

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
            'user_id'  => intval($pagingRequest->getTempCondition('user_id')),
            'topic_id' => intval($pagingRequest->getCondition('topic_id'))
        ];

        return $ESClient->search($query, $teamId, $params);
    }

    protected function extendData(array $baseData, ESPagingRequest $request): array
    {
        if (empty($baseData)) {
            return [];
        }

        /** @var MessageExtension $MessageExtension */
        $MessageExtension = ClassRegistry::init('MessageExtension');
        $resultArray = $MessageExtension->extendMulti($baseData, '{n}.id');

        /** @var TopicExtension $TopicExtension */
        $TopicExtension = ClassRegistry::init('TopicExtension');
        $resultArray = $TopicExtension->extendMulti($resultArray, '{n}.topic_id');

        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');

        //Extend display created
        $TimeEx = new TimeExHelper(new View());

        $userId = $request->getTempCondition('user_id');
        //No topic id means searching for topics.
        if (empty($request->getCondition('topic_id'))) {

            /** @var Topic $Topic */
            $Topic = ClassRegistry::init('Topic');

            foreach ($resultArray as $key => &$result) {
                //No topic id means searching for topics.
                $result['topic']['display_created'] = $TimeEx->elapsedTime($result['topic']['latest_message_datetime'], 'rough',
                    false);
                $users = $Topic->getLatestSenders($result['topic_id'], $userId);
                /** @var UserEntity $user */
                $result['users'] = [];
                foreach ($users as $user){
                    $result['users'][] = $user->toArray();
                }
                $result['topic']['display_title'] = $TopicService->getDisplayTopicTitle($result['topic'], $userId);
                $result['topic']['members_count'] = $TopicService->countMembers($result['topic']['id']);
            }
        } else {
            $messageIds = Hash::extract($baseData, '{n}.id');
            $messageData = $this->bulkFetchMessage($messageIds);
            $messageData = $this->bulkExtendMessage($messageData, $request);

            foreach ($resultArray as $key => &$result) {
                foreach ($messageData as $message) {
                    if ($result['id'] === $message['id']){
                        $result['message'] = $message;
                        $result['img_url'] = $message['sender']['profile_img_url']['medium_large'];
                        break;
                    }
                }
                $result['message']['display_created'] = $TimeEx->elapsedTime($result['message']['created'], 'rough', false);

            }
        }
        return $resultArray;
    }

    /**
     * Fetch multiple messages at once
     *
     * @param array $messageIds
     *
     * @return array
     */
    private function bulkFetchMessage(array $messageIds): array
    {
        $condition = [
            'conditions' => [
                'Message.id'      => $messageIds,
                'Message.del_flg' => false
            ]
        ];

        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');

        return Hash::extract($Message->find('all', $condition), '{n}.Message');

    }

    /**
     * Extend multiple messages at once
     *
     * @param array           $messages
     * @param ESPagingRequest $request
     *
     * @return array
     */
    private function bulkExtendMessage(array $messages, ESPagingRequest $request): array
    {
        $teamId = $request->getTempCondition('team_id');
        $userId = $request->getTempCondition('user_id');

        /** @var MessageExtender $MessageExtender */
        $MessageExtender = ClassRegistry::init('MessageExtender');

        return $MessageExtender->extendMulti($messages, $userId, $teamId, [$MessageExtender::EXTEND_SENDER]);
    }

}
