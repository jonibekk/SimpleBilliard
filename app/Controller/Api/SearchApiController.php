<?php

use Goalous\Enum\Api\SearchApiEnum;

App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Model/Dto/Search', 'SearchApiRequestDto');
App::import('Model/Dto/Search', 'SearchApiResponseDto');
App::import('Service/Api', 'SearchApiService');
App::uses('BasePagingController', 'Controller/Api');
App::uses('Network', 'CakeResponse');

/**
 * Class SearchApiController
 */
class SearchApiController extends BasePagingController
{
    /** @var SearchApiService */
    private $searchApiService;

    /**
     * SearchController constructor.
     *
     * @param CakeRequest|null $request
     * @param CakeResponse|null $response
     */
    public function __construct(CakeRequest $request = null, CakeResponse $response = null)
    {
        parent::__construct($request, $response);

        $this->searchApiService = ClassRegistry::init('SearchApiService');
    }

    /**
     * @return CakeResponse
     */
    public function search(): CakeResponse
    {
        $searchApiRequestDto = new SearchApiRequestDto();
        $searchApiRequestDto->teamId = $this->getTeamId();
        $searchApiRequestDto->userId = $this->getUserId();

        if (empty($searchApiRequestDto->teamId) || empty($searchApiRequestDto->userId)) {
            return ErrorResponse::unauthorized();
        }

        $searchApiResponseDto = new SearchApiResponseDto();

        try {
            $this->handleRequest($searchApiRequestDto);
        } catch (Exception $e) {
            return ErrorResponse::badRequest();
        }

        if ('local' === constant('ENV_NAME')) {
             $this->_dummyResponse($searchApiRequestDto, $searchApiResponseDto);
        } else {
            $this->searchApiService->search($searchApiRequestDto, $searchApiResponseDto);
        }

        $body = $this->searchApiService->dtoToArray($searchApiResponseDto);

        return ApiResponse::ok()->withBody($body)->getResponse();
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     *
     * @throws Exception
     */
    private function handleRequest(SearchApiRequestDto $searchApiRequestDto)
    {
        if (empty($this->request->query('cursor'))) {
            $searchApiRequestDto->keyword = $this->request->query('keyword');
            $searchApiRequestDto->type = $this->request->query('type');

            if (!empty($this->request->query('limit'))) {
                $searchApiRequestDto->limit = (int) $this->request->query('limit');
            }

            if (!empty($this->request->query('pn'))) {
                $searchApiRequestDto->pn = (int) $this->request->query('pn');
            }
        } else {
            $cursorJson = base64_decode($this->request->query('cursor'), true);
            $cursor = json_decode($cursorJson, true);

            if (!isset($cursor['keyword'], $cursor['type'], $cursor['limit'], $cursor['pn'])) {
                throw new Exception();
            }

            $searchApiRequestDto->keyword = $cursor['keyword'];
            $searchApiRequestDto->type = $cursor['type'];
            $searchApiRequestDto->limit = $cursor['limit'];
            $searchApiRequestDto->pn = $cursor['pn'];
        }

        if (empty($searchApiRequestDto->keyword) || empty($searchApiRequestDto->limit) || empty($searchApiRequestDto->pn) || empty($searchApiRequestDto->type)) {
            throw new Exception();
        }

        if ($searchApiRequestDto->limit > SearchApiEnum::MAX_LIMIT) {
            $searchApiRequestDto->limit = SearchApiEnum::MAX_LIMIT;
        }
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     *
     * @return SearchApiResponseDto
     */
    private function _dummyResponse(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): SearchApiResponseDto
    {
        $searchApiResponseDto->actions->totalItemsCount = 1;
        $searchApiResponseDto->posts->totalItemsCount = 3;
        $searchApiResponseDto->members->totalItemsCount = 10;
        $searchApiResponseDto->circles->totalItemsCount = 100;

        // Actions.
        $count = $this->_dummyItemCount(
            $searchApiResponseDto->actions->totalItemsCount,
            $searchApiRequestDto->pn,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new PostItemSearchDto();
            $itemSearchModel->id = $searchApiRequestDto->pn * $i + $i;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/09f/fff.png';
            $itemSearchModel->type = 1 === mt_rand(0, 1) ? SearchApiEnum::POST_TYPE_COMMENTS : SearchApiEnum::POST_TYPE_POSTS;
            $itemSearchModel->content = sprintf('This is action %d with <em>%s</em> as keyword.', $itemSearchModel->id, $searchApiRequestDto->keyword);
            $itemSearchModel->dateTime = '2020-07-15 12:00:00';
            $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
            $itemSearchModel->userName = 'Member '.$itemSearchModel->id;

            $searchApiResponseDto->actions->items[] = $itemSearchModel;
        }

        // Posts.
        $count = $this->_dummyItemCount(
            $searchApiResponseDto->posts->totalItemsCount,
            $searchApiRequestDto->pn,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new PostItemSearchDto();
            $itemSearchModel->id = $searchApiRequestDto->pn * $i + $i;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/09f/fff.png';
            $itemSearchModel->type = 1 === mt_rand(0, 1) ? SearchApiEnum::POST_TYPE_COMMENTS : SearchApiEnum::POST_TYPE_POSTS;
            $itemSearchModel->content = sprintf('This is post %d with a body.', $itemSearchModel->id);
            $itemSearchModel->dateTime = '2020-07-15 12:00:00';
            $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
            $itemSearchModel->userName = 'Member '.$itemSearchModel->id;

            $searchApiResponseDto->posts->items[] = $itemSearchModel;
        }

        // Members.
        $count = $this->_dummyItemCount(
            $searchApiResponseDto->members->totalItemsCount,
            $searchApiRequestDto->pn,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new DefaultItemSearchDto();
            $itemSearchModel->id = $searchApiRequestDto->pn * $i + $i;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
            $itemSearchModel->name = 'Member '.$itemSearchModel->id;

            $searchApiResponseDto->members->items[] = $itemSearchModel;
        }

        // Circles.
        $count = $this->_dummyItemCount(
            $searchApiResponseDto->circles->totalItemsCount,
            $searchApiRequestDto->pn,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new DefaultItemSearchDto();
            $itemSearchModel->id = $searchApiRequestDto->pn * $i + $i;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/9fa/fff.png';
            $itemSearchModel->name = 'Circles '.$itemSearchModel->id;

            $searchApiResponseDto->circles->items[] = $itemSearchModel;
        }

        return $searchApiResponseDto;
    }

    /**
     * @param int $count
     * @param int $pn
     * @param int $limit
     *
     * @return int
     */
    private function _dummyItemCount($count, $pn, $limit): int
    {
        $maxPn = (int) ceil($count / $limit);

        if ($pn < $maxPn) {
            return $limit;
        } elseif ($pn === $maxPn) {
            if (0 !== $count % $limit) {
                return $count % $limit;
            }

            return $limit;
        }

        return 0;
    }
}
