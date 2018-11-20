<?php
App::uses('BaseV1PagingController', 'Controller/Api/V1');
App::uses('CircleMember', 'Model');
App::import('Lib/ElasticSearch', 'PostService');
App::import('Service', 'PostService');
App::import('Service/Paging/Search', 'PostSearchPagingService');

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
     *
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
        if (!$PostService->deleteItem($postId, $userId)) {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }

    /**
     * Save favorite post
     *
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
        if (!$PostService->saveItem($postId, $userId, $this->current_team_id)) {
            return $this->_getResponseInternalServerError();
        }

        return $this->_getResponseSuccess();
    }

    /**
     * Search endpoint for posts
     */
    public function get_search()
    {
        $query = $this->request->query;
        $limit = $this->request->query('limit');
        $cursor = $this->request->query('cursor');

        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        if (empty ($userId) || empty ($teamId)) {
            return $this->_getResponseValidationFail(["Missing user/team ID"]);
        }

        if (empty($cursor)) {
            $pagingRequest = new ESPagingRequest();

            /** @var CircleMember $CircleMember */
            $CircleMember = ClassRegistry::init('CircleMember');

            $circleMember = $CircleMember->getMyCircleList();
            $circleIds = Hash::extract($circleMember, '{n}.{*}');

            $pagingRequest->setQuery($query);
            $pagingRequest->addCondition('pn', 1);
            $pagingRequest->addCondition('limit', $limit);
            $pagingRequest->addCondition('team_id', $teamId);
            $pagingRequest->addCondition('circle', $circleIds);
        } else {
            $pagingRequest = ESPagingRequest::convertBase64($cursor);
        }

        /** @var PostSearchPagingService $PostSearchPagingService */
        $PostSearchPagingService = ClassRegistry::init('PostSearchPagingService');

        $searchResult = $PostSearchPagingService->getDataWithPaging($pagingRequest);

        return $this->_getResponseSuccess($searchResult);
    }
}
