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

        $this->searchApiService->search($searchApiRequestDto, $searchApiResponseDto);
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
                $searchApiRequestDto->limit = $this->request->query('limit');
            }

            if (!empty($this->request->query('pn'))) {
                $searchApiRequestDto->pn = $this->request->query('pn');
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
}
