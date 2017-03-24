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
        $validationResult = $MessageService->validatePostMessage($postedData);
        if ($validationResult !== true) {
            return $this->_getResponseValidationFail($validationResult);
        }
        // saving datas
        $messageId = $MessageService->add($postedData, $userId);
        if ($messageId === false) {
            return $this->_getResponseBadFail(null);
        }

        // tracking by mixpanel
        $this->Mixpanel->trackMessage($postedData['topic_id']);
        //TODO notification. It will be implemented on another issue.
//        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MESSAGE, $post_id, $comment_id);
//        $detail_comment = $this->Post->Comment->getComment($comment_id);
// for react..
//        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
//        $pusher->trigger('message-channel-' . $post_id, 'new_message', $convert_data,
//            $this->request->data('socket_id'));

        // find the message as response data
        $newMessage = $ApiMessageService->get($messageId);
        return $this->_getResponseSuccess($newMessage);
    }

    /**
     * Send a Like message
     * url: POST /api/v1/messages/like
     *
     * @data integer $topic_id required
     * @return CakeResponse
     * @link https://confluence.goalous.com/display/GOAL/%5BPOST%5D+Send+like+message
     *       TODO: This is mock! We have to implement it!
     */
    function post_like()
    {
        $topicId = $this->request->data('topic_id');
        $dataMock = ['message_id' => 1234];
        return $this->_getResponseSuccessSimple($dataMock);
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
