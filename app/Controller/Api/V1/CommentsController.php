<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiCommentService');


/**
 * Class ActionsController
 */
class CommentsController extends ApiController
{

    function get_detail($id)
    {
        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");

        if (!$this->request->is('ajax')) {
            throw new RuntimeException(__('Invalid access'));
        }

        $comment = $ApiCommentService->get($id);
        // comment does not exists
        if (empty($comment)) {
            return $this->_getResponseNotFound(__("This comment doesn't exist."));
        }
        return $this->_getResponseSuccess($comment);
    }

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

    function put($id)
    {
        $errResponse = $this->_validateEditForbiddenOrNotFound($id);
        if ($errResponse !== true) {
            return $errResponse;
        }

        /** @var ApiCommentService $ApiCommentService */
        $ApiCommentService = ClassRegistry::init("ApiCommentService");
        $data =  Hash::get($this->request->data, 'Comment');
        $data['id'] = $id;

        // Update the new data
        try {
            $ApiCommentService->update($data);
        } catch (Exception $e)  {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return $this->_getResponseBadFail($e->getMessage());
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

