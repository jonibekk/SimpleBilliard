<?php

use Goalous\Enum\Api\SearchApiEnum;

App::import('Lib/ElasticSearch', 'ESPagingRequest');
App::import('Model/Dto/Search', 'SearchResultsDto');
App::import('Model/Dto/Search/Item', 'DefaultItemSearchDto');
App::import('Model/Dto/Search/Item', 'PostItemSearchDto');
App::import('Service/Api', 'SearchApiService');
App::import('Service/Paging/Search', 'ActionSearchPagingService');
App::import('Service/Paging/Search', 'CircleSearchPagingService');
App::import('Service/Paging/Search', 'PostSearchPagingService');
App::import('Service/Paging/Search', 'UserSearchPagingService');
App::uses('SearchApiRequestDto', 'Model/Dto/Search');
App::uses('SearchApiResponseDto', 'Model/Dto/Search');

/**
 * Class SearchApiService
 */
class SearchApiService
{
    /**
     * @param $searchModel
     *
     * @return array
     */
    public function dtoToArray($searchModel): array
    {
        $array = [];

        foreach ($searchModel as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $this->dtoToArray($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    public function search(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ) {
        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_ACTIONS])) {
            $searchApiResponseDto->actions = $this->getActions($searchApiRequestDto);
        }

        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_CIRCLES])) {
            $searchApiResponseDto->circles = $this->getCircles($searchApiRequestDto);
        }

        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_MEMBERS])) {
            $searchApiResponseDto->members = $this->getMembers($searchApiRequestDto);
        }

        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_POSTS])) {
            $searchApiResponseDto->posts = $this->getPosts($searchApiRequestDto);
        }
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     *
     * @return ESPagingRequest
     */
    private function createPagingRequest(SearchApiRequestDto $searchApiRequestDto): ESPagingRequest
    {
        $pagingRequest = new ESPagingRequest();
        $pagingRequest->addCondition('keyword', $searchApiRequestDto->keyword);
        $pagingRequest->addCondition('limit', $searchApiRequestDto->limit);
        $pagingRequest->addCondition('pn', $searchApiRequestDto->pn);
        $pagingRequest->addTempCondition('team_id', $searchApiRequestDto->teamId);
        $pagingRequest->addTempCondition('user_id', $searchApiRequestDto->userId);

        /** @var CircleMember $circleMember */
        $circleMember = ClassRegistry::init('CircleMember');
        $circleIds = Hash::extract($circleMember->getMyCircleList(), '{n}.{*}');

        $pagingRequest->addCondition('circle', $circleIds);

        return $pagingRequest;
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     *
     * @return SearchResultsDto
     */
    private function getActions(SearchApiRequestDto $searchApiRequestDto): SearchResultsDto
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'action');

        /** @var ActionSearchPagingService $actionSearchPagingService */
        $actionSearchPagingService = ClassRegistry::init('ActionSearchPagingService');
        $data = $actionSearchPagingService->getDataWithPaging($pagingRequest);

        $searchResultsDto = new SearchResultsDto();
        $searchResultsDto->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $searchResultsDto->items[] = $this->mapPostData($itemData);
        }

        return $searchResultsDto;
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     *
     * @return SearchResultsDto
     */
    private function getCircles(SearchApiRequestDto $searchApiRequestDto): SearchResultsDto
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'circle');

        /** @var CircleSearchPagingService $circleSearchPagingService */
        $circleSearchPagingService = ClassRegistry::init('CircleSearchPagingService');
        $data = $circleSearchPagingService->getDataWithPaging($pagingRequest);

        $searchResultsDto = new SearchResultsDto();
        $searchResultsDto->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $item = new DefaultItemSearchDto();
            $item->id = $itemData['id'];
            $item->imageUrl = $itemData['img_url'];
            $item->name = $itemData['circle']['name'];

            $searchResultsDto->items[] = $item;
        }

        return $searchResultsDto;
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     *
     * @return SearchResultsDto
     */
    private function getMembers(SearchApiRequestDto $searchApiRequestDto): SearchResultsDto
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'user');

        /** @var UserSearchPagingService $userSearchPagingService */
        $userSearchPagingService = ClassRegistry::init('UserSearchPagingService');
        $data = $userSearchPagingService->getDataWithPaging($pagingRequest);

        $searchResultsDto = new SearchResultsDto();
        $searchResultsDto->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $item = new DefaultItemSearchDto();
            $item->id = $itemData['id'];
            $item->imageUrl = $itemData['img_url'];
            $item->name = $itemData['user']['display_username'];

            $searchResultsDto->items[] = $item;
        }

        return $searchResultsDto;
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     *
     * @return SearchResultsDto
     */
    private function getPosts(SearchApiRequestDto $searchApiRequestDto): SearchResultsDto
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'circle_post');

        /** @var PostSearchPagingService $postSearchPagingService */
        $postSearchPagingService = ClassRegistry::init('PostSearchPagingService');
        $data = $postSearchPagingService->getDataWithPaging($pagingRequest);

        $searchResultsDto = new SearchResultsDto();
        $searchResultsDto->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $searchResultsDto->items[] = $this->mapPostData($itemData);
        }

        return $searchResultsDto;
    }

    /**
     * @param array $itemData
     * @return PostItemSearchDto
     */
    private function mapPostData($itemData): PostItemSearchDto
    {
        $item = new PostItemSearchDto();

        $item->id = $itemData['id'];
        $item->imageUrl = $itemData['img_url'];

        if (isset($itemData['comment'])) {
            $item->content = $itemData['comment']['body'];
            $item->dateTime = $itemData['comment']['created'];
            $item->type = SearchApiEnum::POST_TYPE_COMMENTS;
            $item->userId = $itemData['comment']['user_id'];
            $item->userImageUrl = $itemData['comment']['user']['profile_img_url']['small'];
            $item->userName = $itemData['comment']['user']['display_username'];
        } else {
            $item->content = $itemData['post']['body'];
            $item->dateTime = $itemData['post']['created'];
            $item->type = SearchApiEnum::POST_TYPE_POSTS;
            $item->userId = $itemData['post']['user_id'];
            $item->userImageUrl = $itemData['post']['user']['profile_img_url']['small'];
            $item->userName = $itemData['post']['user']['display_username'];
        }

        return $item;
    }
}
