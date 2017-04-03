<?php
App::import('Service', 'AppService');
App::uses('Topic', 'Model');
App::uses('User', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('TopicSearchKeyword', 'Model');

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
        $members = $TopicMember->findSortedBySentMessage($topicId, $limit);
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
     * Validate create topic
     *
     * @param  array $data
     * @param  array $toUserIds
     *
     * @return array|true
     */
    function validateCreate(array $data, array $toUserIds)
    {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init("MessageService");
        /** @var User $User */
        $User = ClassRegistry::init("User");

        // validation message without ignorefields
        // topic_id: haven't created topic data yet
        // sendoer_user_id: not contain in post data
        $ignoreFields = ['topic_id', 'sender_user_id'];
        $messageValidResult = $MessageService->validatePostMessage($data, $ignoreFields);
        if ($messageValidResult !== true) {
            return $messageValidResult;
        }

        // check ToUsers are active
        if (!$User->isActiveUsers($toUserIds)) {
            return ['to_user_ids' => __('Invalid users are included in the destination.')];
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
     * create topic and first message
     *
     * @param  array $data
     * @param  array $toUserIds
     *
     * @return array|false ["topicId"=>int,"messageId"=>int]
     */
    function create(array $data, int $creatorUserId, array $toUserIds)
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        /** @var AttachedFile $AttachedFile */
        $AttachedFile = ClassRegistry::init('AttachedFile');
        /** @var TopicSearchKeyword $TopicSearchKeyword */
        $TopicSearchKeyword = ClassRegistry::init('TopicSearchKeyword');

        $Topic->begin();

        try {
            // save topic
            $topicId = $Topic->add($creatorUserId);
            if (!$topicId) {
                $errorMsg = sprintf("Failed to create topic. userId:%s, validationErrors:%s",
                    $creatorUserId,
                    var_export($Topic->validationErrors, true)
                );
                throw new Exception($errorMsg);
            }

            // save tousers
            $toUserIds[] = $creatorUserId;
            $savedMembers = $TopicMember->add($topicId, $toUserIds);
            if (!$savedMembers) {
                $errorMsg = sprintf("Failed to add members to topic. userId:%s, topicId:%s, data:%s validationErrors:%s",
                    $creatorUserId,
                    $topicId,
                    $toUserIds,
                    var_export($TopicMember->validationErrors, true)
                );
                throw new Exception($errorMsg);
            }

            // save message
            $data['topic_id'] = $topicId;
            $message = $Message->saveNormal($data, $creatorUserId);
            if ($message === false) {
                $errorMsg = sprintf("Failed to add a message. userId:%s, topicId:%s, data:%s, validationErrors:%s",
                    $creatorUserId,
                    $topicId,
                    var_export($data, true),
                    var_export($Message->validationErrors, true)
                );
                throw new Exception($errorMsg);
            }
            $messageId = $Message->getLastInsertID();

            // save attached files
            if (Hash::get($data, 'file_ids')) {
                $attachedFiles = $AttachedFile->saveRelatedFiles($messageId, AttachedFile::TYPE_MODEL_MESSAGE,
                    $data['file_ids']);
                if ($attachedFiles === false) {
                    $errorMsg = sprintf("Failed to save attached files on message. data:%s, validationErrors:%s",
                        var_export($data, true),
                        var_export($AttachedFile->validationErrors, true)
                    );
                    throw new Exception($errorMsg);
                }

                // update attached file count
                $Message->id = $messageId;
                $Message->saveField('attached_file_count', count($data['file_ids']));
            }

            // update latest message on the topic
            $updateTopic = $Topic->updateLatestMessage($topicId, $messageId);
            if ($updateTopic === false) {
                $errorMsg = sprintf("Failed to update latest message on the topic. topicId:%s, messageId:%s, validationErrors:%s",
                    $topicId,
                    $messageId,
                    var_export($Topic->validationErrors, true)
                );
                throw new Exception($errorMsg);
            }

            // create topic search record
            $keywords = $Topic->fetchSearchKeywords($topicId);
            if (!$TopicSearchKeyword->add($topicId, $keywords)) {
                $errorMsg = sprintf("Failed to add search topic record. topicId:%s",
                    $topicId
                );
                throw new Exception($errorMsg);
            }

        } catch (Exception $e) {
            $this->log($e->getMessage());
            $Topic->rollback();
            return false;
        }

        $Topic->commit();
        $ret = compact('topicId', 'messageId');

        return $ret;
    }

    /*
     * Add members to the topic.
     * - Add topic_members.
     * - Add message as add members.
     *
     * @param int    $topicId
     * @param int    $loginUserId
     * @param array  $addUserIds
     * @param string $socketId
     *
     * @return bool
     */
    function addMembers(int $topicId, int $loginUserId, array $addUserIds, string $socketId): bool
    {
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init("TopicMember");
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');
        /** @var Message $Message */
        $Message = ClassRegistry::init("Message");

        $TopicMember->begin();

        try {
            // Add topic_members
            if (!$TopicMember->bulkAdd($topicId, $addUserIds)) {
                throw new Exception(
                    sprintf("Failed to add topic_members. topicId:%s, addUserIds:%s, validationErrors:%s"
                        , $topicId
                        , var_export($addUserIds, true)
                        , var_export($TopicMember->validationErrors, true)
                    )
                );
            }

            // Add message as add members
            if (!$Message->saveAddMembers($topicId, $loginUserId, $addUserIds)) {
                throw new Exception(
                    sprintf("Failed to save add members message. topicId:%s, loginUserId:%s, addUserIds:%s, validationErrors:%s"
                        , $topicId
                        , $loginUserId
                        , var_export($addUserIds, true)
                        , var_export($Message->validationErrors, true)
                    )
                );
            }

            // Push event using pusher
            $MessageService->execPushMessageEvent($topicId, $socketId);

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
