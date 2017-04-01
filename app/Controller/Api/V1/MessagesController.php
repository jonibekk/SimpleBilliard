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
        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init('ApiMessageService');

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
        //TODO pusherのsocket_idをフォームで渡してもらう必要がある。これはapiからのつなぎこみ時に行う。
        $socketId = "test";
        $MessageService->execPushMessageEvent($topicId, $socketId);
        // find the message as response data
        $message = $ApiMessageService->get($messageId);
        return $this->_getResponseSuccess(compact('message'));
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
        /** @var ApiMessageService $ApiMessageService */
        $ApiMessageService = ClassRegistry::init('ApiMessageService');

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
        //TODO pusherのsocket_idをフォームで渡してもらう必要がある。これはapiからのつなぎこみ時に行う。
        $socketId = "test";
        $MessageService->execPushMessageEvent($topicId, $socketId);
        // find the message as response data
        $message = $ApiMessageService->get($messageId);
        return $this->_getResponseSuccess(compact('message'));
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
