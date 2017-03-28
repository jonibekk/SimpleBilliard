<?php
App::import('Service', 'AppService');
App::uses('Topic', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');
App::uses('TeamMember', 'Model');

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

        $topic = $Topic->get($topicId);
        if (empty($topic)) {
            return [];
        }

        $latestMessageId = $topic['latest_message_id'];
        $readCount = 0;
        if ($latestMessageId) {
            $readCount = $TopicMember->countReadMember($latestMessageId);
        }
        $membersCount = $TopicMember->countMember($topicId);

        if (!$topic['title']) {
            $displayTitle = $this->getMemberNamesAsString($topicId, 10);
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
    function getMemberNamesAsString(int $topicId, int $limit): string
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $members = $TopicMember->findMembers($topicId, $limit);
        $names = Hash::extract($members, '{n}.User.display_first_name');
        $namesStr = implode(', ', $names);
        return (string)$namesStr;
    }

    /**
     * 更新
     *
     * @param array $data
     *
     * @return bool
     */
    function update(array $data): bool
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init("Topic");

        try {
            // トランザクション開始
            $Topic->begin();

            // KR更新
            if (!$Topic->save($data, false)) {
                throw new Exception(sprintf("Failed update topic. data:%s", var_export($data, true)));
            }

            // トランザクション完了
            $Topic->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Topic->rollback();
            return false;
        }
        return true;
    }

    /**
     * leaving the topic.
     * - delete topic_members.
     * - add message as leave me.
     *
     * @param int $topicId
     * @param int $userId
     *
     * @return bool
     */
    function leaveMe(int $topicId, int $userId): bool
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init("TopicMember");
        /** @var Message $Message */
        $Message = ClassRegistry::init("Message");

        $TopicMember->begin();

        try {
            // At the first, message should be saved. Cause, validation is failed when leave the topic first.
            $saveMessage = $Message->saveLeave($topicId, $userId);
            if ($saveMessage === false) {
                throw new Exception(
                    sprintf("Failed to save leave me message. topicId:%s, userId:%s, validationErrors:%s"
                        , $topicId
                        , $userId
                        , var_export($Message->validationErrors, true)
                    )
                );
            }
            $leaveTopic = $TopicMember->leave($topicId, $userId);
            if ($leaveTopic === false) {
                throw new Exception(
                    sprintf("Failed to update topic_members to leave me. topicId:%s, userId:%s, validationErrors:%s"
                        , $topicId
                        , $userId
                        , var_export($TopicMember->validationErrors, true)
                    )
                );
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $TopicMember->rollback();
            return false;
        }
        $TopicMember->commit();
        return true;
    }

    /**
     * topic always has to have over 2 members
     *
     * @param int $topicId
     *
     * @return string|true
     */
    function validateLeaveMe(int $topicId)
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $memberCount = $TopicMember->countMember($topicId);
        if ($memberCount < 3) {
            return __('You cannot leave the topic. 3 topic members are required when someone leave.');
        }
        return true;
    }

}
