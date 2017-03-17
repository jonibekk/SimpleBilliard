<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'TopicService');
App::import('Service', 'MessageService');
App::import('Service/Api', 'ApiMessageService');
App::uses('TopicMember', 'Model');
App::uses('TimeExHelper', 'View/Helper');

/**
 * Class ApiTopicService
 */
class ApiTopicService extends ApiService
{
    /* Default number of topics displaying */
    const DEFAULT_TOPICS_NUM = 10;

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
            // add util properties
            $topics[$i]['read_count'] = $this->calcReadCount($data['latest_message'], $data['topic_members']);
            $topics[$i]['members_count'] = count($data['topic_members']);
            $topics[$i]['can_leave_topic'] = $topics[$i]['members_count'] >= 3;
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
            $topics[$i]['display_title'] = !empty($topicTitle) ? $topicTitle : $this->getDisplayTitle($topics[$i]['users']);
        }

        return $topics;
    }

    /**
     * calc read count of latest message
     *
     * @param  array $lastMessage
     * @param  array $topicMembers
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
     * generate topic title by users
     * - only one user, display fullname
     * - over one user, display only first name separated comma
     *
     * @param  array $users
     *
     * @return string
     */
    function getDisplayTitle(array $users): string
    {
        if (count($users) === 1) {
            return $users[0]['display_username'];
        }

        $firstNames = Hash::extract($users, '{n}.display_first_name');
        return implode(', ', $firstNames);
    }

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
