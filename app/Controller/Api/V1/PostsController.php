<?php
App::uses('ApiController', 'Controller/Api');
App::uses('CircleMember', 'Model');
App::import('Lib/ElasticSearch', 'PostService');
App::import('Service', 'PostService');
App::import('Service/Paging/Search', 'ActionSearchPagingService');
App::import('Service/Paging/Search', 'PostSearchPagingService');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');

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
        $error = $this->validateSearch();

        if (!empty($error)) {
            return $this->_getResponseValidationFail($error);
        }

        $query = $this->request->query;
        $limit = $this->request->query('limit');
        $cursor = $this->request->query('cursor');
        $type = $this->request->query('type') ?: "circle_post";
        $teamId = $this->current_team_id;

        if (empty($cursor)) {
            $pagingRequest = new ESPagingRequest();

            $pagingRequest->setQuery($query);
            $pagingRequest->addCondition('pn', 1);
            $pagingRequest->addCondition('limit', $limit);
            $pagingRequest->addCondition('type', $type);

            if ($type != "action") {
                $circle = $this->request->query('circle');

                if (!empty($circle)) {
                    $circleIds = explode(',', $circle);
                }

                if (empty($circleIds)) {
                    /** @var CircleMember $CircleMember */
                    $CircleMember = ClassRegistry::init('CircleMember');

                    $circleMember = $CircleMember->getMyCircleList();
                    $circleIds = Hash::extract($circleMember, '{n}.{*}');
                }
                $pagingRequest->addCondition('circle', $circleIds);
            }
        } else {
            $pagingRequest = ESPagingRequest::convertBase64($cursor);
            $type = $pagingRequest->getCondition('type');
        }

        $pagingRequest->addTempCondition('team_id', $teamId);

        switch ($type) {
            case "action":
                /** @var ActionSearchPagingService $ActionSearchPagingService */
                $ActionSearchPagingService = ClassRegistry::init('ActionSearchPagingService');
                $searchResult = $ActionSearchPagingService->getDataWithPaging($pagingRequest);
                break;
            case "circle_post":
                /** @var PostSearchPagingService $PostSearchPagingService */
                $PostSearchPagingService = ClassRegistry::init('PostSearchPagingService');
                $searchResult = $PostSearchPagingService->getDataWithPaging($pagingRequest);
                break;
            default:
                $searchResult = [];
        }

        return ApiResponse::ok()->withBody($searchResult)->getResponse();
    }

    private function validateSearch(): array
    {
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        if (empty ($userId)) {
            return ["No user ID"];
        }

        if (empty($teamId)) {
            return ["No team ID"];
        }

        return [];
    }
}
