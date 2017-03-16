<?php
App::import('Service/Api', 'ApiService');

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
     * @param  array $dataByModel
     *
     * @return array
     */
    function process(array $topicsByModel, $userId): array
    {
        $resData = $this->formatResponseData($topicsByModel);

        // change data structure and add util property
        $topics = [];
        foreach($resData as $i => $data) {
            $topics[$i] = $data['topic'];
            $topics[$i]['latest_message'] = $data['latest_message'];
            // add members all count and able to leave me
            $topics[$i]['members_count'] = count($data['topic_members']);
            $topics[$i]['can_leave_topic'] = $topics[$i]['members_count'] >= 3;
            // set topics user info without mine
            $topics[$i]['users'] = [];
            foreach($data['topic_members'] as $member) {
                if ($member['user']['id'] == $userId) {
                    continue;
                }
                $topics[$i]['users'][] = $member['user'];
            }
        }

        return $topics;
    }

}
