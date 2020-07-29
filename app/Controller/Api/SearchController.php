<?php

use Goalous\Enum\SearchEnum;

App::uses('BasePagingController', 'Controller/Api');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Service', 'CircleService');
App::import('Service', 'CircleMemberService');
App::import('Service/Paging', 'CirclePostPagingService');
App::import('Service/Paging', 'CircleMemberPagingService');
App::import('Service/Paging', 'PostDraftPagingService');
App::import('Service/Paging', 'CircleFilesPagingService');
App::import('Service/Paging', 'SearchPostFileExtender');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');
App::uses('CheckedCircle', 'Model');
App::uses('LatestUserConfirmCircle', 'Model');
App::import('Service', 'PostDraftService');
App::import('Service/Request/Resource', 'CircleResourceRequest');
App::import('Service/Redis', 'UnreadPostsRedisService');
App::import('Validator/Request/Api/V2', 'CircleRequestValidator');
App::import('Service/Paging/Search', 'PostSearchPagingService');
App::import('Service/Paging/Search', 'ActionSearchPagingService');
App::import('Service/Paging/Search', 'CircleSearchPagingService');
App::import('Service/Paging/Search', 'GoalSearchPagingService');
App::import('Service/Paging/Search', 'UserSearchPagingService');
App::import('Service/Paging/Search', 'PostSearchPagingService');
App::import('Model/Search', 'SearchModel');
App::import('Model/Search/Item', 'DefaultItemSearchModel');
App::import('Model/Search/Item', 'PostItemSearchModel');
App::import('Enum', 'SearchEnum');

/**
 * Class SearchController
 */
class SearchController extends BasePagingController
{
    public function search()
    {
        // Authorize.
        if (empty($this->getUserId()) || empty($this->getTeamId())) {
            return ErrorResponse::unauthorized();
        }

        // Get items.
        $type = $this->request->query('type');
        $searchApiModel = new SearchModel();

        if (SearchEnum::TYPE_ALL === $type) {
            try {
                $this->searchAll($searchApiModel);
            } catch (Exception $e) {
                return ErrorResponse::badRequest();
            }
        } else {
            $this->searchType($searchApiModel);
        }

        $searchModel = new SearchModel();
        $searchModel->actions->totalItemsCount = 3;
        $searchModel->posts->totalItemsCount = 0;
        $searchModel->members->totalItemsCount = 2;
        $searchModel->circles->totalItemsCount = 3;

        $itemSearchModel = new PostItemSearchModel();
        $itemSearchModel->id = 3;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/09f/fff.png';
        $itemSearchModel->type = 'posts';
        $itemSearchModel->content = 'This is some random content that repeats. This is some random content that repeats. This is some random content that repeats. This is some random content that repeats.';
        $itemSearchModel->dateTime = '2020-07-15 12:00:00';
        $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
        $itemSearchModel->userName = 'Member A';
        $searchModel->actions->items[] = $itemSearchModel;

        $itemSearchModel = new PostItemSearchModel();
        $itemSearchModel->id = 2;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/c88/fff.png';
        $itemSearchModel->type = 'comments';
        $itemSearchModel->content = 'This is some random content that repeats. This is some random content that repeats.';
        $itemSearchModel->dateTime = '2020-07-12 11:30:00';
        $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
        $itemSearchModel->userName = 'Member A';
        $searchModel->actions->items[] = $itemSearchModel;

        $itemSearchModel = new PostItemSearchModel();
        $itemSearchModel->id = 1;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/98c/fff.png';
        $itemSearchModel->type = 'posts';
        $itemSearchModel->content = 'This is some random content that repeats. This is some random content that repeats. This is some random content that repeats. This is some random content that repeats. This is some random content that repeats. This is some random content that repeats. This is some random content that repeats.';
        $itemSearchModel->dateTime = '2020-07-10 15:20:00';
        $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/c6a/fff.png';
        $itemSearchModel->userName = 'Member B';
        $searchModel->actions->items[] = $itemSearchModel;

        $itemSearchModel = new DefaultItemSearchModel();
        $itemSearchModel->id = 1;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
        $itemSearchModel->name = 'Member A';
        $searchModel->members->items[] = $itemSearchModel;

        $itemSearchModel = new DefaultItemSearchModel();
        $itemSearchModel->id = 2;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/c6a/fff.png';
        $itemSearchModel->name = 'Member B';
        $searchModel->members->items[] = $itemSearchModel;

        $itemSearchModel = new DefaultItemSearchModel();
        $itemSearchModel->id = 1;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/36a/fff.png';
        $itemSearchModel->name = 'Circle A';
        $searchModel->circles->items[] = $itemSearchModel;

        $itemSearchModel = new DefaultItemSearchModel();
        $itemSearchModel->id = 2;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/36a/fff.png';
        $itemSearchModel->name = 'Circle B';
        $searchModel->circles->items[] = $itemSearchModel;

        $itemSearchModel = new DefaultItemSearchModel();
        $itemSearchModel->id = 3;
        $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/36a/fff.png';
        $itemSearchModel->name = 'Circle C';
        $searchModel->circles->items[] = $itemSearchModel;

        // Create response.
        $array = $this->modelToArray($searchModel);

        return ApiResponse::ok()->withBody($array)->getResponse();
    }

    /**
     * @return SearchModel
     *
     * @throws Exception
     */
    private function searchAll(SearchModel $searchApiModel): SearchModel
    {
        $keyword = $this->request->query('keyword');

        if (empty($keyword) || strlen($keyword) < SearchEnum::MIN_KEYWORD_LENGTH) {
            throw new Exception();
        }

        $searchApiModel->actions = $this->getForType(SearchEnum::TYPE_ACTIONS, 3);
        $searchApiModel->circles = $this->getForType(SearchEnum::TYPE_CIRCLES, 3);
        $searchApiModel->circles = $this->getForType(SearchEnum::TYPE_MEMBERS, 3);
        $searchApiModel->circles = $this->getForType(SearchEnum::TYPE_POSTS, 3);

        return $searchApiModel;
    }

    private function getForType($type, $keyword, $limit = 3, $pn = 1)
    {
        $pagingRequest = new ESPagingRequest();
        $pagingRequest->addCondition('keyword', $keyword);
        $pagingRequest->addCondition('limit', $limit);
        $pagingRequest->addCondition('pn', $pn);
        $pagingRequest->addTempCondition('team_id', $this->getTeamId());
        $pagingRequest->addTempCondition('user_id', $this->getUserId());

        if (SearchEnum::TYPE_ACTIONS === $type) {
            $pagingRequest->addCondition('type', 'action', true);

            /** @var ActionSearchPagingService $actionSearchPagingService */
            $actionSearchPagingService = ClassRegistry::init('ActionSearchPagingService');

            return $actionSearchPagingService->getDataWithPaging($pagingRequest);
        }

        if (SearchEnum::TYPE_CIRCLES === $type) {
            $pagingRequest->addCondition('type', 'circle', true);

            /** @var CircleSearchPagingService $circleSearchPagingService */
            $circleSearchPagingService = ClassRegistry::init('CircleSearchPagingService');

            return $circleSearchPagingService->getDataWithPaging($pagingRequest);
        }

        if (SearchEnum::TYPE_MEMBERS === $type) {
            $pagingRequest->addCondition('type', 'user', true);

            /** @var UserSearchPagingService $userSearchPagingService */
            $userSearchPagingService = ClassRegistry::init('UserSearchPagingService');

            return $userSearchPagingService->getDataWithPaging($pagingRequest);
        }

        $pagingRequest->addCondition('type', 'post', true);

        /** @var PostSearchPagingService $postSearchPagingService */
        $postSearchPagingService = ClassRegistry::init('PostSearchPagingService');

        return $postSearchPagingService->getDataWithPaging($pagingRequest);
    }

    private function searchType(SearchModel $searchModel)
    {
        // Get conditions.
        if (empty($this->request->query('cursor'))) {
            $keyword = $this->request->query('keyword');
            $limit = $this->request->query('limit');
            $pn = 1;
            $type = $this->request->query('type');
        } else {
            $cursorJson = base64_decode($this->request->query('cursor'), true);
            $cursor = json_decode($cursorJson, true);

            if (!isset($cursor['keyword'], $cursor['limit'], $cursor['pn'], $cursor['type'])) {
                return ErrorResponse::badRequest();
            }

            $limit = $cursor['limit'];
            $pn = $cursor['pn'];
            $type = $cursor['type'];
        }

        // Validate conditions.
        if (
            empty($keyword) || empty($limit) || empty($pn) || empty($type) ||
            strlen($keyword) < SearchEnum::MIN_KEYWORD_LENGTH ||
            !in_array($type, [
                SearchEnum::TYPE_ACTIONS,
                SearchEnum::TYPE_ALL,
                SearchEnum::TYPE_CIRCLES,
                SearchEnum::TYPE_MEMBERS,
                SearchEnum::TYPE_POSTS
            ])
        ) {
            return ErrorResponse::badRequest();
        }

        if ($limit > SearchEnum::MAX_LIMIT) {
            $limit = SearchEnum::MAX_LIMIT;
        }

        return $this->getForType($type, $keyword, $limit, $pn);
    }

    /**
     * @param $searchModel
     *
     * @return array
     */
    private function modelToArray($searchModel): array
    {
        $array = [];

        foreach ($searchModel as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $this->modelToArray($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
