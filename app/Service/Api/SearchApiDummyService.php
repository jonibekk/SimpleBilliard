<?php

use Goalous\Enum\Api\SearchApiEnum;

App::import('Model/Dto/Search/Item', 'DefaultItemSearchDto');
App::import('Model/Dto/Search/Item', 'PostItemSearchDto');
App::uses('SearchApiRequestDto', 'Model/Dto/Search');
App::uses('SearchApiResponseDto', 'Model/Dto/Search');
App::uses('SearchApiServiceInterface', 'Service/Api');

class SearchApiDummyService implements SearchApiServiceInterface {
    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    public function search(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void {
        $searchApiResponseDto->actions->totalItemsCount = 30;
        $searchApiResponseDto->circles->totalItemsCount = 20;
        $searchApiResponseDto->members->totalItemsCount = 10;
        $searchApiResponseDto->posts->totalItemsCount = 4;

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
     * @return int
     */
    private function createTimestamp(): int
    {
        $dateTime = new DateTime();

        try {
            $dateTime->sub(new DateInterval(sprintf('PT%dH', mt_rand(1, 24))));
        } catch (Exception $e) {}

        return $dateTime->getTimestamp();
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
        $count = $this->itemsCountForPage(
            $searchApiResponseDto->actions->totalItemsCount,
            $searchApiRequestDto->pageNumber,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new PostItemSearchDto();
            $itemSearchModel->id = ($searchApiRequestDto->pageNumber - 1) * $searchApiRequestDto->limit + $i + 1;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/09f/fff.pageNumberg';
            $itemSearchModel->type = 1 === mt_rand(0, 1) ? SearchApiEnum::POST_TYPE_COMMENTS : SearchApiEnum::POST_TYPE_POSTS;
            $itemSearchModel->content = sprintf('Xxxx <em>%d</em>%s.', $itemSearchModel->id, str_repeat(" xxxx", mt_rand(10, 30)));
            $itemSearchModel->dateTime = $this->createTimestamp();
            $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/9fa/fff.pageNumberg';
            $itemSearchModel->userName = sprintf('Xxxx %d%s.', $itemSearchModel->id, str_repeat(" xxxx", mt_rand(1, 10)));

            $searchApiResponseDto->actions->items[] = $itemSearchModel;
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
        $count = $this->itemsCountForPage(
            $searchApiResponseDto->circles->totalItemsCount,
            $searchApiRequestDto->pageNumber,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new DefaultItemSearchDto();
            $itemSearchModel->id = ($searchApiRequestDto->pageNumber - 1) * $searchApiRequestDto->limit + $i + 1;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/9fa/fff.pageNumberg';
            $itemSearchModel->name = sprintf('Xxxx %d%s.', $itemSearchModel->id, str_repeat(" xxxx", mt_rand(1, 10)));

            $searchApiResponseDto->circles->items[] = $itemSearchModel;
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
        $count = $this->itemsCountForPage(
            $searchApiResponseDto->members->totalItemsCount,
            $searchApiRequestDto->pageNumber,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new DefaultItemSearchDto();
            $itemSearchModel->id = ($searchApiRequestDto->pageNumber - 1) * $searchApiRequestDto->limit + $i + 1;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/9fa/fff.pageNumberg';
            $itemSearchModel->name = sprintf('Xxxx %d%s.', $itemSearchModel->id, str_repeat(" xxxx", mt_rand(1, 10)));

            $searchApiResponseDto->members->items[] = $itemSearchModel;
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
        $count = $this->itemsCountForPage(
            $searchApiResponseDto->posts->totalItemsCount,
            $searchApiRequestDto->pageNumber,
            $searchApiRequestDto->limit
        );

        for ($i = 0; $i < $count; $i++) {
            $itemSearchModel = new PostItemSearchDto();
            $itemSearchModel->id = ($searchApiRequestDto->pageNumber - 1) * $searchApiRequestDto->limit + $i + 1;
            $itemSearchModel->imageUrl = 'https://via.placeholder.com/300/09f/fff.pageNumberg';
            $itemSearchModel->type = 1 === mt_rand(0, 1) ? SearchApiEnum::POST_TYPE_COMMENTS : SearchApiEnum::POST_TYPE_POSTS;
            $itemSearchModel->content = sprintf('Xxxx <em>%d</em>%s.', $itemSearchModel->id, str_repeat(" xxxx", mt_rand(10, 30)));
            $itemSearchModel->dateTime = $this->createTimestamp();
            $itemSearchModel->userImageUrl = 'https://via.placeholder.com/300/9fa/fff.pageNumberg';
            $itemSearchModel->userName = sprintf('Xxxx %d%s.', $itemSearchModel->id, str_repeat(" xxxx", mt_rand(1, 10)));

            $searchApiResponseDto->posts->items[] = $itemSearchModel;
        }
    }

    /**
     * @param int $totalCount
     * @param int $pageNumber
     * @param int $limit
     *
     * @return int
     */
    private function itemsCountForPage(int $totalCount, int $pageNumber, int $limit): int
    {
        $maxPageNumber = (int) ceil($totalCount / $limit);

        if ($pageNumber < $maxPageNumber) {
            return $limit;
        } elseif ($pageNumber === $maxPageNumber) {
            if (0 !== $totalCount % $limit) {
                return $totalCount % $limit;
            }

            return $limit;
        }

        return 0;
    }

}
