<?php
App::import('Service', 'AppService');
App::import('Service', 'UserService');
App::uses('Topic', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');
App::uses('User', 'Model');
App::uses('AttachedFile', 'Model');
App::uses('Topic', 'Model');
App::uses('AppUtil', 'Util');

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
        // renumbering keys
        $messages = am($messages);
        // build message body
        $messages = $this->extendBody($messages);
        // build display created
        $messages = $this->extendDisplayBody($messages);
        // user image url
        $messages = $this->extendUserImageUrl($messages);
        // attached file url
        $messages = $this->extendAttachedFileUrl($messages);
        // filter only necessary fields
        $messages = $this->filterFields($messages);

        return $messages;
    }

    function extendDisplayBody(array $messages): array
    {
        $TimeEx = new TimeExHelper(new View());
        foreach ($messages as &$message) {
            $message['Message']['display_created'] = $TimeEx->elapsedTime($message['Message']['created'], 'normal',
                false);
        }
        return $messages;
    }

    function extendBody(array $messages): array
    {
        /** @var UserService $UserService */
        $UserService = ClassRegistry::init('UserService');

        foreach ($messages as &$message) {
            $type = $message['Message']['type'];
            $body = $message['Message']['body'];
            $targetUids = $message['Message']['target_user_ids'];
            $senderName = $message['SenderUser']['display_first_name'];

            switch ($type) {
                case Message::TYPE_NORMAL:
                    $outputBody = h($body);
                    break;
                case Message::TYPE_ADD_MEMBER:
                    $uids = explode(',', $targetUids);
                    $delimiter = Configure::read('Config.language') == "jpn" ? "と" : ", ";
                    $addedUserNamesStr = $UserService->getUserNamesAsString($uids, $delimiter);
                    $outputBody = __("%s added %s.", $senderName, $addedUserNamesStr);
                    break;
                case Message::TYPE_LEAVE:
                    $outputBody = __('%s left this topic.', $senderName);
                    break;
                case Message::TYPE_SET_TOPIC_NAME:
                    if ($body) {
                        $outputBody = __('%s named this topic : %s.', $senderName, $body);
                    } else {
                        $outputBody = __('%s removed the topic name.', $senderName);
                    }
                    break;
                default:
                    $outputBody = $body;
            }
            $message['Message']['body'] = $outputBody;
        }
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
        $Upload = new UploadHelper(new View());

        foreach ($messages as &$message) {
            foreach ($message['MessageFile'] as &$messageFile) {
                $urls = [
                    'download_url'  => null,
                    'preview_url'   => null,
                    'thumbnail_url' => null,
                ];

                // download url is common.
                $urls['download_url'] = '/posts/attached_file_download/file_id:' . $messageFile['AttachedFile']['id'];

                if ($messageFile['AttachedFile']['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                    // if image
                    $urls['thumbnail_url'] = $Upload->uploadUrl($messageFile, 'AttachedFile.attached',
                        ['style' => 'small']);
                    $urls['preview_url'] = $Upload->uploadUrl($messageFile, 'AttachedFile.attached',
                        ['style' => 'original']);
                } else {
                    // not image
                    if ($Upload->isCanPreview($messageFile)) {
                        $urls['preview_url'] = $Upload->attachedFileUrl($messageFile);
                    }
                }
                $messageFile['AttachedFile'] += $urls;
            }
        }
        return $messages;
    }

    function filterFields(array $messages): array
    {
        $messageFilter = [
            'id',
            'body',
            'type',
            'created',
            'display_created',
        ];

        $senderUserFilter = [
            'id',
            'display_username',
            'medium_img_url'
        ];

        $attachedFileFilter = [
            'id',
            'file_type',
            'file_ext',
            'download_url',
            'preview_url',
            'thumbnail_url'
        ];

        foreach ($messages as &$message) {
            $message['Message'] = AppUtil::filterWhiteList($message['Message'], $messageFilter);
            $message['SenderUser'] = AppUtil::filterWhiteList($message['SenderUser'], $senderUserFilter);
            foreach ($message['MessageFile'] as &$file) {
                $file['AttachedFile'] = AppUtil::filterWhiteList($file['AttachedFile'], $attachedFileFilter);
            }
        }
        return $messages;
    }

}
