<?php
App::import('Service', 'AppService');
App::uses('Topic', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');
App::uses('User', 'Model');

/**
 * Class MessageService
 */
class MessageService extends AppService
{
    function findMessages(int $topicId, $cursor, $limit): array
    {
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        $messages = $Message->findMessages($topicId, $cursor, $limit);
        // reverse sort messages
        krsort($messages);
        // user image url
        $messages = $this->extendUserImageUrl($messages);
        // attached file url

        // filter only necessary fields

        return $messages;
    }

    function extendUserImageUrl(array $messages): array
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        foreach ($messages as &$message) {
            $message['SenderUser'] = $User->attachImgUrl($message['SenderUser'], 'User', ['medium']);
        }
        return $messages;
    }

    function extendAttachedFileUrl(array $messages): array
    {

    }

}
