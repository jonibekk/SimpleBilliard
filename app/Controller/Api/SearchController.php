<?php

use Goalous\Enum\SearchEnum;

App::import('CakeResponse', 'Network');
App::import('Controller/Api', 'BasePagingController');
App::import('Enum', 'SearchEnum');
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Model/Search', 'SearchModel');
App::import('Model/Search/Item', 'DefaultItemSearchModel');
App::import('Model/Search/Item', 'PostItemSearchModel');
App::import('Service/Api', 'SearchApiService');
App::import('Service/Paging/Search', 'PostSearchPagingService');
App::import('Service/Paging/Search', 'ActionSearchPagingService');
App::import('Service/Paging/Search', 'CircleSearchPagingService');
App::import('Service/Paging/Search', 'UserSearchPagingService');

/**
 * Class SearchController
 */
class SearchController extends BasePagingController
{
    /** @var SearchApiService */
    private $searchApiService;

    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
        parent::__construct($request, $response);

        $this->searchApiService = ClassRegistry::init('SearchApiService');
    }

    /**
     * @return CakeResponse
     */
    public function search()
    {
        // Authorize.
        if (empty($this->getUserId()) || empty($this->getTeamId())) {
            return ErrorResponse::unauthorized();
        }

        // Get items.
        $type = $this->request->query('type');
        $searchApiModel = new SearchModel();

        try {
            if (SearchEnum::TYPE_ALL === $type) {
                $this->searchAll($searchApiModel);
            } else {
                $this->searchType($searchApiModel);
            }
        } catch (Exception $e) {
            return ErrorResponse::badRequest();
        }

        // Create response.
        $body = $this->searchApiService->modelToArray($searchApiModel);

        return ApiResponse::ok()->withBody($body)->getResponse();
    }

    /**
     * @param $type
     * @param $keyword
     * @param int $limit
     * @param int $pn
     *
     * @return TypeSearchModel
     */
    private function getForType($type, $keyword, $limit = 3, $pn = 1): TypeSearchModel
    {
        $pagingRequest = new ESPagingRequest();
        $pagingRequest->addCondition('keyword', $keyword);
        $pagingRequest->addCondition('limit', $limit);
        $pagingRequest->addCondition('pn', $pn);
        $pagingRequest->addTempCondition('team_id', $this->getTeamId());
        $pagingRequest->addTempCondition('user_id', $this->getUserId());

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        $circleMember = $CircleMember->getMyCircleList();
        $circleIds = Hash::extract($circleMember, '{n}.{*}');

        $pagingRequest->addCondition('circle', $circleIds);

        $data = [
            'count' => 0,
            'data' => []
        ];

        if (SearchEnum::TYPE_ACTIONS === $type) {
            $pagingRequest->addCondition('type', 'action', true);

            /** @var ActionSearchPagingService $actionSearchPagingService */
            $actionSearchPagingService = ClassRegistry::init('ActionSearchPagingService');
            $data = $actionSearchPagingService->getDataWithPaging($pagingRequest);
        }

        if (SearchEnum::TYPE_CIRCLES === $type) {
            $pagingRequest->addCondition('type', 'circle', true);

            /** @var CircleSearchPagingService $circleSearchPagingService */
            $circleSearchPagingService = ClassRegistry::init('CircleSearchPagingService');
            $data = $circleSearchPagingService->getDataWithPaging($pagingRequest);
        }

        if (SearchEnum::TYPE_MEMBERS === $type) {
            $pagingRequest->addCondition('type', 'user', true);

            /** @var UserSearchPagingService $userSearchPagingService */
            $userSearchPagingService = ClassRegistry::init('UserSearchPagingService');
            $data = $userSearchPagingService->getDataWithPaging($pagingRequest);
        }

        if (SearchEnum::TYPE_POSTS === $type) {
            $pagingRequest->addCondition('type', 'circle_post', true);

            /** @var PostSearchPagingService $postSearchPagingService */
            $postSearchPagingService = ClassRegistry::init('PostSearchPagingService');
            $data = $postSearchPagingService->getDataWithPaging($pagingRequest);
        }

        $typeSearchModel = new TypeSearchModel();
        $typeSearchModel->totalItemsCount = $data['count'];
        $typeSearchModel->items = $data['data'];

        return $typeSearchModel;
    }

    /**
     * @throws Exception
     */
    private function searchAll(SearchModel $searchModel): void
    {
        $keyword = $this->request->query('keyword');

        if (empty($keyword)) {
            throw new Exception();
        }

        $this->getForType($searchModel, SearchEnum::TYPE_ACTIONS, $keyword);
        $this->getForType($searchModel, SearchEnum::TYPE_CIRCLES, $keyword);
        $this->getForType($searchModel, SearchEnum::TYPE_MEMBERS, $keyword);
        $this->getForType($searchModel, SearchEnum::TYPE_POSTS, $keyword);
    }


    /**
     * @param SearchModel $searchModel
     *
     * @throws Exception
     */
    private function searchType(SearchModel $searchModel): void
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
                throw new Exception();
            }

            $limit = $cursor['limit'];
            $pn = $cursor['pn'];
            $type = $cursor['type'];
        }

        // Validate conditions.
        if (empty($keyword) || empty($limit) || empty($pn) || empty($type)) {
            throw new Exception();
        }

        if ($limit > SearchEnum::MAX_LIMIT) {
            $limit = SearchEnum::MAX_LIMIT;
        }

        $this->getForType($searchModel, $type, $keyword, $limit, $pn);
    }
}
