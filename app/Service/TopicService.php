<?php
App::import('Service', 'AppService');
App::uses('Topic', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');

/**
 * Class TopicService
 */
class TopicService extends AppService
{
    /**
     * find topic detail including the following
     * - read count
     * - members count
     * - can leave topic (member cannot leave the topic if the topic member less than 2)
     * - display title (if title exists, same of title. otherwise, member names these are ordered by last_message_sent)
     *
     * @param int $topicId
     *
     * @return array
     */
    function findTopicDetail(int $topicId): array
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');

        $topic = $Topic->findTopic($topicId);
        $latestMessageId = $Message->getLatestMessageId($topicId);
        $readCount = $TopicMember->countReadMember($topicId, $latestMessageId);
        $membersCount = $TopicMember->countMember($topicId);

        if (!$topic['title']) {
            $displayTitle = $this->getMemberNameAsString($topicId, 4);
        } else {
            $displayTitle = $topic['title'];
        }

        $canLeaveTopic = true;
        if ($membersCount <= 2) {
            $canLeaveTopic = false;
        }

        $ret = array_merge($topic, [
            'display_title'   => $displayTitle,
            'read_count'      => $readCount,
            'members_count'   => $membersCount,
            'can_leave_topic' => $canLeaveTopic,
        ]);

        return $ret;
    }

    /**
     * Get member names as string.
     *
     * @param int $topicId
     * @param int $limit
     *
     * @return string
     */
    function getMemberNameAsString(int $topicId, int $limit): string
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $members = $TopicMember->findMembers($topicId, $limit);
        $names = Hash::extract($members, '{n}.User.display_first_name');
        $namesStr = implode(', ', $names);
        return (string)$namesStr;
    }

}
