<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'TopicService');
App::import('Service', 'MessageService');
App::import('Service', 'TranslationService');
App::import('Service/Api', 'ApiMessageService');
App::uses('TopicMember', 'Model');
App::uses('Topic', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TimeExHelper', 'View/Helper');
App::import('Lib/ElasticSearch', 'ESPagingRequest');
App::import('Service/Paging/Search', 'MessageSearchPagingService');

use Goalous\Enum as Enum;

/**
 * Class ApiTopicService
 */
class ApiTopicService extends ApiService
{
    /* Default number of topics displaying */
    const DEFAULT_TOPICS_NUM = 10;
//    const DEFAULT_MESSAGES_NUM = 10;

    /**
     * process topic data
     * - change upper model name to lower
     * - adjust data structure of topic
     * - add util property to per topic
     *
     * @param array $topicsByModel
     * @param int   $userId
     *
     * @return array
     */
    function process(array $topicsByModel, int $userId): array
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        $TimeEx = new TimeExHelper(new View());
        $resData = $this->formatResponseData($topicsByModel);
        // change data structure and add util property
        $topics = [];
        foreach ($resData as $i => $data) {
            $topics[$i] = $data['topic'];
            $topics[$i]['latest_message'] = $data['latest_message'];
            // convert created time for human readable
            $topics[$i]['latest_message']['display_created'] = $TimeEx->elapsedTime(
                $data['latest_message']['created'], 'normal', false
            );
            // change latest message's body when only attached files.
            if (!$data['latest_message']['body'] and $data['latest_message']['attached_file_count'] > 0) {
                $topics[$i]['latest_message']['body'] = __('Sent file(s).');
            }
            // add last message sent user to head of body
            $senderUserName = '';
            if ($data['latest_message']['sender_user_id'] == $userId) {
                $senderUserName = __("You");
            } else {
                $senderUserName = $data['latest_message']['sender_user']['display_first_name'];
            }
            $topics[$i]['latest_message']['body'] = "{$senderUserName} : {$topics[$i]['latest_message']['body']}";
            // add util properties
            $topics[$i]['read_count'] = $this->calcReadCount($data['latest_message'], $data['topic_members']);
            $topics[$i]['members_count'] = count($data['topic_members']);
            $topics[$i]['can_leave_topic'] = $topics[$i]['members_count'] >= 3;
            $readMembers = Hash::extract($data['topic_members'],
                "{n}[last_read_message_id={$data['latest_message']['id']}].user_id");
            // set is_read true when topic latest message is mine for frontend
            $topics[$i]['is_unread'] = $data['latest_message']['sender_user_id'] != $userId && !in_array($userId,
                    $readMembers);
            // set topics user info without mine
            $topics[$i]['users'] = [];
            foreach ($data['topic_members'] as $member) {
                if ($member['user']['id'] == $userId) {
                    continue;
                }
                $topics[$i]['users'][] = $member['user'];
            }
            // set display title
            $topicTitle = $data['topic']['title'];
            $topics[$i]['display_title'] = !empty($topicTitle) ? $topicTitle : $TopicService->extractUsersFirstname($topics[$i]['users']);
        }

        return $topics;
    }

    /**
     * calc read count of latest message
     *
     * @param array $lastMessage
     * @param array $topicMembers
     *
     * @return int
     */
    function calcReadCount(array $lastMessage, array $topicMembers): int
    {
        $condition = "{n}[last_read_message_id={$lastMessage['id']}][user_id!={$lastMessage['sender_user_id']}]";
        $readMembers = Hash::extract($topicMembers, $condition);
        return count($readMembers);
    }

    /**
     * Find topic detail including latest messages.
     *
     * @param int $topicId
     * @param int $loginUserId
     * @param int $messageId
     *
     * @return array
     */
    function findTopicDetailInitData(int $topicId, int $loginUserId, $messageId = null): array
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        $topicDetail = $TopicService->findTopicDetail($topicId, $loginUserId);

        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init('ApiMessageService');
        $limit = null;
        $direction = Enum\Model\Message\MessageDirection::OLD;
        $pagingType = ApiMessageService::PAGING_TYPE_BOTH;
        $messageData = $ApiMessageService->findMessages($topicId, $loginUserId, $messageId, $limit, $direction,
            $pagingType);

        // Get translation status
        $translationStatus = false;
        $translationLimitReached = false;

        try {
            /** @var TranslationService $TranslationService */
            $TranslationService = ClassRegistry::init('TranslationService');
            $translationStatus = $TranslationService->canTranslate($topicDetail['team_id'], false);

            if ($translationStatus){
                /** @var TeamTranslationStatus $TeamTranslationStatus */
                $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
                $translationLimitReached = $TeamTranslationStatus->getUsageStatus($topicDetail['team_id'])->isLimitReached();
            }

        } catch (Exception $e) {
            GoalousLog::error('Failed in getting translation status for message.', [
                'message'  => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
                'topic_id' => $topicId,
                'user_id'  => $loginUserId
            ]);
        }

        $ret = [
            'topic'                     => $topicDetail,
            'messages'                  => $messageData,
            'translation_enabled'       => $translationStatus,
            'translation_limit_reached' => $translationLimitReached
        ];

        return $ret;
    }

    /**
     * Find topic detail including latest messages.
     *
     * @param int   $topicId
     * @param int   $loginUserId
     * @param int   $teamId
     * @param array $query
     *
     * @return array
     */
    function findInitSearchMessages(int $topicId, int $loginUserId, int $teamId, array $query): array
    {
        /** @var TopicService $TopicService */
        $TopicService = ClassRegistry::init('TopicService');
        $topicDetail = $TopicService->findTopicDetail($topicId, $loginUserId);

        $pagingRequest = new ESPagingRequest();
        $pagingRequest->setQuery($query);
        $pagingRequest->addCondition('pn', 1);
//        $pagingRequest->addCondition('limit', self::DEFAULT_MESSAGES_NUM);
        $pagingRequest->addCondition('topic_id', $topicId);

        $pagingRequest->addTempCondition('team_id', $teamId);
        $pagingRequest->addTempCondition('user_id', $loginUserId);

        /** @var MessageSearchPagingService $MessageSearchPagingService */
        $MessageSearchPagingService = ClassRegistry::init('MessageSearchPagingService');
        $searchResult = $MessageSearchPagingService->getDataWithPaging($pagingRequest);

        $ret = [
            'topic'    => $topicDetail,
            'messages' => $searchResult,
        ];
        return $ret;
    }

    /**
     * Find read members of latest message
     *
     * @param int $topicId
     *
     * @return array
     */
    function findReadMembers(int $topicId): array
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');

        $latestMessageId = $Topic->getLatestMessageId($topicId);
        $members = $TopicMember->findReadMembers($latestMessageId);
        return $members;
    }

    /**
     * get topic with last message and members
     *
     * @param int $topicId
     *
     * @return array
     */
    function get(int $topicId, int $userId)
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        $res = $Topic->getWithLatestMesasge($topicId);
        $res = $this->process([$res], $userId)[0];
        return $res;
    }

}
