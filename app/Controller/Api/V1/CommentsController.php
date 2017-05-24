<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiCommentService');


/**
 * Class ActionsController
 */
class CommentsController extends ApiController
{
    /**
     * @param $id
     *
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
     *
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

        switch ($type) {
            case Post::TYPE_NORMAL:
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST, $postId,
                    $comment->id);
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                    $postId, $comment->id);
                break;
            case Post::TYPE_ACTION:
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION,
                    $postId,
                    $comment->id);
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION,
                    $postId, $comment->id);
                break;
        }
        return $this->_getResponseSuccess();
    }

    /**
     * @param $id
     * Updates a Comment.
     * Request format:
     * {
     *   "data[_Token][key]": "token",
     *   "Comment": {
     *     "body": "body"
     *   }
     * }
     *
     * @return CakeResponse
     */
    function put($id)
    {
        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");
        $data = Hash::get($this->request->data, 'Comment');
        $data['id'] = $id;

        $err = $ApiCommentService->validateUpdate($id, $this->Auth->user('id'), $data);
        if (!empty($err)) {
            return $this->_getResponseValidationFail(Hash::get($err, 'validation_errors'));
        }

        // Update the new comment
        if (!$ApiCommentService->update($data)) {
            return $this->_getResponseInternalServerError();
        }

        // Get the newest comment object and return it as its html rendered block
        $comments = array($ApiCommentService->get($id));
        $this->set(compact('comments'));
        $this->layout = 'ajax';
        $this->viewPath = 'Elements';
        $response = $this->render('Feed/ajax_comments');
        $html = $response->__toString();

        return $this->_getResponseSuccess($comments[0], $html);
    }

    /**
     * @param $comment_id
     *
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
        if ($this->Auth->user('id') != $comment[User][id]) {
            return $this->_getResponseForbidden(__("This isn't your comment."));
        }
        return true;
    }
}

