<?php
App::uses('ApiController', 'Controller/Api');
App::uses('Topic', 'Model');
App::uses('TopicMember', 'Model');
App::import('Service', 'MessageService');
App::import('Service/Api', 'ApiMessageService');

/**
 * Class MessagesController
 */
class MessagesController extends ApiController
{
    /**
     * Send a message
     * url: POST /api/v1/messages
     *
     * @data integer $topic_id required
     * @data string $body optional
     * @data array $file_ids optional
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Send+message
     */
    function post()
    {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');

        $userId = $this->Auth->user('id');

        // filter fields
        $postedData = AppUtil::filterWhiteList($this->request->data, ['topic_id', 'body', 'file_ids']);

        $topicId = $postedData['topic_id'];

        // checking 403 or 404
        $errResponse = $this->_validateCreateForbiddenOrNotFound($topicId, $userId);
        if ($errResponse !== true) {
            return $errResponse;
        }

        // validation
        // remove sender_user_id validation rule, cause that is not included in posted data
        $validationResult = $MessageService->validatePostMessage($postedData, ['sender_user_id']);
        if ($validationResult !== true) {
            return $this->_getResponseValidationFail($validationResult);
        }
        // saving datas
        $messageId = $MessageService->add($postedData, $userId);
        if ($messageId === false) {
            return $this->_getResponseInternalServerError();
        }

        $topicId = $postedData['topic_id'];

        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MESSAGE, $messageId);
        $socketId = $this->request->data('socket_id');
        $MessageService->execPushMessageEvent($topicId, $socketId);

        // find the message as response data
        $latestMessages = $this->_findLatestMessages($topicId, $messageId);
        return $this->_getResponseSuccess(['latest_messages' => $latestMessages]);
    }

    /**
     * Find Messages for api response
     * @param int $topicId
     * @param int $newMessageId
     *
     * @return array
     */
    private function _findLatestMessages(int $topicId, int $newMessageId) {
        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init('ApiMessageService');

        $loginUserId = $this->Auth->user('id');
        $lastMessageId = $this->request->data('last_message_id');
        if (empty($lastMessageId)) {
            $message = $ApiMessageService->get($newMessageId);
            return [$message];
        }
        // Get the latest message based on the ID of the last displayed message to prevent the message list from missing teeth
        $messages = $ApiMessageService->findMessages($topicId, $loginUserId, $lastMessageId, null, Message::DIRECTION_NEW);
        return $messages['data'];
    }

    /**
     * Send a Like message
     * url: POST /api/v1/messages/like
     *
     * @data integer $topic_id required
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Send+like+message
     */
    function post_like()
    {
        /** @var MessageService $MessageService */
        $MessageService = ClassRegistry::init('MessageService');

        $userId = $this->Auth->user('id');

        // filter fields
        $postedData = AppUtil::filterWhiteList($this->request->data, ['topic_id']);

        $topicId = $postedData['topic_id'];

        // checking 403 or 404
        $errResponse = $this->_validateCreateForbiddenOrNotFound($topicId, $userId);
        if ($errResponse !== true) {
            return $errResponse;
        }

        // saving datas
        $messageId = $MessageService->addLike($topicId, $userId);
        if ($messageId === false) {
            return $this->_getResponseInternalServerError();
        }

        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MESSAGE, $messageId);
        $socketId = $this->request->data('socket_id');
        $MessageService->execPushMessageEvent($topicId, $socketId);

        // find the message as response data
        $latestMessages = $this->_findLatestMessages($topicId, $messageId);
        return $this->_getResponseSuccess(['latest_messages' => $latestMessages]);
    }

    /**
     * validation for creating a message
     * - if not found, it will return 404 response
     * - if not have permission, it will return 403 response
     *
     * @param $topicId
     * @param $userId
     *
     * @return CakeResponse|true
     */
    private function _validateCreateForbiddenOrNotFound($topicId, $userId)
    {
        /** @var Topic $Topic */
        $Topic = ClassRegistry::init("Topic");
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init("TopicMember");

        // topic is exists?
        if (!$Topic->exists($topicId)) {
            return $this->_getResponseNotFound();
        }
        // is topic member?
        $isMember = $TopicMember->isMember($topicId, $userId);
        if (!$isMember) {
            return $this->_getResponseForbidden();
        }
        return true;
    }
}