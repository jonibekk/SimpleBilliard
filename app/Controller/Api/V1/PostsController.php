<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'PostService');

/**
 * Class PostsController
 */
class PostsController extends ApiController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * Delete favorite post
     * @param $postId
     *
     * @return CakeResponse
     */
    function delete_saved_items($postId)
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init("SavedPost");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        $userId = $this->Auth->user('id');
        $post = $Post->getById($postId);
        if (empty($post)) {
            return $this->_getResponseBadFail(__("This post doesn't exist."));
        }
        if ($post['team_id'] != $this->current_team_id) {
            return $this->_getResponseForbidden();
        }

        if (empty($SavedPost->getUnique($postId, $userId))) {
            return $this->_getResponseSuccess();
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init("PostService");
        if (!$PostService->deleteItem($postId, $userId))
        {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }

    /**
     * Save favorite post
     * @param $postId
     *
     * @return CakeResponse
     */
    function post_saved_items($postId)
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init("SavedPost");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        $userId = $this->Auth->user('id');
        $post = $Post->getById($postId);
        if (empty($post)) {
            return $this->_getResponseBadFail(__("This post doesn't exist."));
        }
        if ($post['team_id'] != $this->current_team_id) {
            return $this->_getResponseForbidden();
        }

        if (!empty($SavedPost->getUnique($postId, $userId))) {
            return $this->_getResponseSuccess();
        }

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init("PostService");
        if (!$PostService->saveItem($postId, $userId, $this->current_team_id))
        {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }
}
