<?php

use Goalous\Enum\Api\SearchApiEnum;

App::import('Lib/ElasticSearch', 'ESPagingRequest');
App::import('Model/Dto/Search', 'SearchResultsDto');
App::import('Model/Dto/Search/Item', 'DefaultItemSearchDto');
App::import('Model/Dto/Search/Item', 'PostItemSearchDto');
App::import('Service/Paging/Search', 'ActionSearchPagingService');
App::import('Service/Paging/Search', 'CircleSearchPagingService');
App::import('Service/Paging/Search', 'PostSearchPagingService');
App::import('Service/Paging/Search', 'UserSearchPagingService');
App::uses('SearchApiRequestDto', 'Model/Dto/Search');
App::uses('SearchApiResponseDto', 'Model/Dto/Search');
App::uses('SearchApiServiceInterface', 'Service/Api');

/**
 * Class SearchApiService
 */
class SearchApiService implements SearchApiServiceInterface
{
    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    public function search(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void {
        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_ACTIONS])) {
            $this->getActions($searchApiRequestDto, $searchApiResponseDto);
        }

        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_CIRCLES])) {
            $this->getCircles($searchApiRequestDto, $searchApiResponseDto);
        }

        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_MEMBERS])) {
            $this->getMembers($searchApiRequestDto, $searchApiResponseDto);
        }

        if (in_array($searchApiRequestDto->type, [SearchApiEnum::TYPE_ALL, SearchApiEnum::TYPE_POSTS])) {
            $this->getPosts($searchApiRequestDto, $searchApiResponseDto);
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
        $pagingRequest->addCondition('pn', $searchApiRequestDto->pageNumber);
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
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    private function getActions(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'action');

        /** @var ActionSearchPagingService $actionSearchPagingService */
        $actionSearchPagingService = ClassRegistry::init('ActionSearchPagingService');
        $data = $actionSearchPagingService->getDataWithPaging($pagingRequest);

        $searchApiResponseDto->actions->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $searchApiResponseDto->actions->items[] = $this->mapPostData($itemData);
        }
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    private function getCircles(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'circle');

        /** @var CircleSearchPagingService $circleSearchPagingService */
        $circleSearchPagingService = ClassRegistry::init('CircleSearchPagingService');
        $data = $circleSearchPagingService->getDataWithPaging($pagingRequest);

        $searchApiResponseDto->circles->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $item = new DefaultItemSearchDto();
            $item->id = $itemData['id'];
            $item->imageUrl = $itemData['img_url'];
            $item->name = $itemData['circle']['name'];

            $searchApiResponseDto->circles->items[] = $item;
        }
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    private function getMembers(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'user');

        /** @var UserSearchPagingService $userSearchPagingService */
        $userSearchPagingService = ClassRegistry::init('UserSearchPagingService');
        $data = $userSearchPagingService->getDataWithPaging($pagingRequest);

        $searchApiResponseDto->members->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $item = new DefaultItemSearchDto();
            $item->id = $itemData['id'];
            $item->imageUrl = $itemData['img_url'];
            $item->name = $itemData['user']['display_username'];

            $searchApiResponseDto->members->items[] = $item;
        }
    }

    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    private function getPosts(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void
    {
        $pagingRequest = $this->createPagingRequest($searchApiRequestDto);
        $pagingRequest->addCondition('type', 'circle_post');

        /** @var PostSearchPagingService $postSearchPagingService */
        $postSearchPagingService = ClassRegistry::init('PostSearchPagingService');
        $data = $postSearchPagingService->getDataWithPaging($pagingRequest);

        $searchApiResponseDto->posts->totalItemsCount = $data['count'];

        foreach ($data['data'] as $itemData) {
            $searchApiResponseDto->posts->items[] = $this->mapPostData($itemData);
        }
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

        if (isset($itemData['highlight'])) {
            if (is_array($itemData['highlight'])) {
                $item->content = implode(' ', $itemData['highlight']);
            } else {
                $item->content = $itemData['highlight'];
            }
        }

        if (isset($itemData['comment'])) {
            $item->dateTime = $itemData['comment']['created'];
            $item->type = SearchApiEnum::POST_TYPE_COMMENTS;
            $item->userId = $itemData['comment']['user_id'];
            $item->userImageUrl = $itemData['comment']['user']['profile_img_url']['small'];
            $item->userName = $itemData['comment']['user']['display_username'];

            if (empty($item->content)) {
                $item->content = $itemData['comment']['body'];
            }
        } else {
            $item->dateTime = $itemData['post']['created'];
            $item->type = SearchApiEnum::POST_TYPE_POSTS;
            $item->userId = $itemData['post']['user_id'];
            $item->userImageUrl = $itemData['post']['user']['profile_img_url']['small'];
            $item->userName = $itemData['post']['user']['display_username'];

            if (empty($item->content)) {
                $item->content = $itemData['post']['body'];
            }
        }

        return $item;
    }
}
