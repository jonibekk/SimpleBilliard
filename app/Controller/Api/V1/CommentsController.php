<?php

use Goalous\Enum\NotificationFlag\Name as NotificationFlagName;

App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiCommentService');
App::import('Service', 'TeamTranslationLanguageService');
App::uses('Comment', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('TeamMember', 'Model');

/**
 * Class ActionsController
 */
class CommentsController extends ApiController
{
    public $components = ['Mention'];
    /**
     * @param $id
     * Get Comment data on JSON format
     *
     * @return CakeResponse
     */
    function get_detail($id)
    {
        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");

        $comment = $ApiCommentService->get($id);
        // comment does not exists
        if (empty($comment)) {
            return $this->_getResponseNotFound(__("This comment doesn't exist."));
        }
        return $this->_getResponseSuccess($comment);
    }

    /**
     * @param $id
     * Delete a comment if the request user owns it.
     *
     * @return CakeResponse
     */
    function delete($id)
    {
        $errResponse = $this->_validateEditForbiddenOrNotFound($id);
        if ($errResponse !== true) {
            return $errResponse;
        }

        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");

        if (!$ApiCommentService->delete($id)) {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccessSimple();
    }

    /**
     * Add a new comment
     *
     * @return CakeResponse
     */
    function post()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");

        $err = $ApiCommentService->validateCreate($this->request->data);
        if (!empty($err)) {
            return $this->_getResponseValidationFail(Hash::get($err, 'validation_errors'));
        }

        // Create new comment
        $comment = $ApiCommentService->create($this->request->data);
        if ($comment === false) {
            return $this->_getResponseInternalServerError();
        }

        // Get post type and notify
        $postId = Hash::get($this->request->data, 'Comment.post_id');
        $post = $Post->findById($postId);
        $type = Hash::get($post, 'Post.type');

        $notifyUsers = $this->Mention->getUserList(Hash::get($this->request->data, 'Comment.body'), $this->current_team_id, $this->my_uid);

        switch ($type) {
            case Post::TYPE_NORMAL:
                // This notification must not be sent to those who mentioned
                // because we exlude them in NotifyBiz#execSendNotify.
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST, $postId,
                    $comment->id);
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                    $postId, $comment->id);
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT, $postId, $comment->id, $notifyUsers);
                break;
            case Post::TYPE_ACTION:
                // This notification must not be sent to those who mentioned
                // because we exlude them in NotifyBiz#execSendNotify.
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION,
                    $postId,
                    $comment->id);
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION,
                    $postId, $comment->id);
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT, $postId, $comment->id, $notifyUsers);
                break;
            case Post::TYPE_CREATE_GOAL:
                $this->_notifyUserOfGoalComment($this->Auth->user('id'), $post);
                break;
        }

        // Translation usage notification
        /** @var TeamTranslationLanguage $TeamTranslationLanguage */
        $TeamTranslationLanguage = ClassRegistry::init('TeamTranslationLanguage');
        if ($TeamTranslationLanguage->canTranslate($this->current_team_id)) {
            $this->sendTranslationUsageNotification($this->current_team_id);
        }

        // Push comments notifications
        $socketId = Hash::get($this->request->data, 'socket_id');
        $this->_pushCommentToPost($postId, $socketId);

        return $this->_getResponseSuccess();
    }

    /**
     * @param $id
     *     Updates a Comment.
     *     Request format:
     *     {
     *     "data[_Token][key]": "token",
     *     "Comment": {
     *     "body": "body"
     *     }
     *     }
     *
     * @return CakeResponse
     */
    function put($id)
    {
        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        $err = $ApiCommentService->validateUpdate($id, $this->Auth->user('id'), $this->request->data);
        if (!empty($err)) {
            return $this->_getResponseValidationFail(Hash::get($err, 'validation_errors'));
        }

        // Update the new comment
        if (!$ApiCommentService->update($id, $this->request->data)) {
            return $this->_getResponseInternalServerError();
        }

        // Get the newest comment object and return it as its html rendered block
        $comments = array($ApiCommentService->get($id));

        $postId = Hash::get($comments[0], 'Comment.post_id');
        $post = $Post->getById($postId);

        $notifyUsers = $this->Mention->getUserList(Hash::get($comments[0], 'Comment.body'), $this->current_team_id, $this->my_uid);
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT, $postId, $id, $notifyUsers);
        $this->set(compact('comments'));
        $this->set('enable_translation', true);
        $this->set('post_type', $post['Post']['type']);
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';
        $this->_decideMobileAppRequest();
        $response = $this->render('Feed/ajax_comments');
        $html = $response->__toString();

        return $this->_getResponseSuccess($comments[0], $html);
    }

    /**
     * @param $comment_id
     * Validates if the comments exists and if the request
     * user owns it.
     *
     * @return bool|CakeResponse
     */
    private function _validateEditForbiddenOrNotFound($comment_id)
    {
        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");

        $comment = $ApiCommentService->get($comment_id);
        // comment does not exists
        if (empty($comment)) {
            return $this->_getResponseNotFound(__("This comment doesn't exist."));
        }
        // Is it the user comment?
        if ($this->Auth->user('id') != $comment['User']['id']) {
            return $this->_getResponseForbidden(__("This isn't your comment."));
        }
        return true;
    }

    /**
     * @param $postId
     * @param $socketId
     */
    private function _pushCommentToPost($postId, $socketId)
    {
        $notifyId = Security::hash(time());

        // リクエストデータが正しくないケース
        if (empty($postId) || empty($socketId)) {
            return;
        }

        $data = [
            'notify_id'         => $notifyId,
            'is_comment_notify' => true,
            'post_id'           => $postId
        ];
        $this->NotifyBiz->commentPush($socketId, $data);
    }

    /**
     * @param int   $commenterUserId ID of user who made the comment
     * @param array $postData        Post object where the comment belongs to
     */
    private function _notifyUserOfGoalComment(int $commenterUserId, array $postData)
    {
        $postId = $postData['Post']['id'];
        $postOwnerUserId = $postData['Post']['user_id'];

        //If commenter is not post owner, send notification to owner
        if ($commenterUserId !== $postOwnerUserId) {
            $this->NotifyBiz->sendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_GOAL, null, null,
                [$postOwnerUserId], $commenterUserId, $postData['Post']['team_id'], $postId);
        }
        $excludedUserList = array($postOwnerUserId, $commenterUserId);

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');
        $notificationReceiverUserList = $Comment->getCommentedUniqueUsersList($postId, false, $excludedUserList);

        if (!empty($notificationReceiverUserList)) {
            $this->NotifyBiz->sendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_COMMENTED_GOAL, null, null,
                $notificationReceiverUserList, $commenterUserId, $postData['Post']['team_id'], $postId);
        }
    }


    public function sendTranslationUsageNotification(int $teamId)
    {
        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');
        $teamTranslationStatus = $TeamTranslationStatus->getUsageStatus($teamId);

        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        $notificationFlagClient = new NotificationFlagClient();

        $limitReachedKey = new NotificationFlagKey($teamId, NotificationFlagName::TYPE_TRANSLATION_LIMIT_REACHED());
        $limitClosingKey = new NotificationFlagKey($teamId, NotificationFlagName::TYPE_TRANSLATION_LIMIT_CLOSING());

        if (empty($notificationFlagClient->read($limitReachedKey)) && $teamTranslationStatus->isLimitReached()) {
            $this->notifyTranslateLimitReached($teamId, $TeamMember->findAdminList($teamId) ?? []);
            $notificationFlagClient->write($limitReachedKey);
        } else if (empty($notificationFlagClient->read($limitClosingKey)) && $teamTranslationStatus->isUsageWithinPercentageOfLimit(0.1)) {
            $this->notifyTranslateLimitClosing($teamId, $TeamMember->findAdminList($teamId) ?? []);
            $notificationFlagClient->write($limitClosingKey);
        }
    }

    private function notifyTranslateLimitReached(int $teamId, array $userIds)
    {
        $this->NotifyBiz->sendNotify(
            NotifySetting::TYPE_TRANSLATION_LIMIT_REACHED,
            null,
            null,
            $userIds,
            null,
            $teamId);
    }

    private function notifyTranslateLimitClosing(int $teamId, array $userIds)
    {
        $this->NotifyBiz->sendNotify(
            NotifySetting::TYPE_TRANSLATION_LIMIT_CLOSING,
            null,
            null,
            $userIds,
            null,
            $teamId);
    }
}
