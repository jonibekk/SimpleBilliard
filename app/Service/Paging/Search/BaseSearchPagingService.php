<?php
App::import('Lib/ElasticSearch', 'ESPagingRequest');

/**
 * Base class for searching using elastic search
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/20/2018
 * Time: 6:49 PM
 */
abstract class BaseSearchPagingService
{
    const DEFAULT_PAGE_LIMIT = 10;
    const ES_SEARCH_PARAM_MODEL = 'model';

    /**
     * Get search data using paging
     *
     * @param ESPagingRequest $pagingRequest
     *
     * @return array
     */
    public function getDataWithPaging(
        ESPagingRequest $pagingRequest
    ): array {

        $pageResult = [
            'data'   => [],
            'count'  => 0,
            'paging' => ''
        ];

        $pagingRequest = $this->setCondition($pagingRequest);

        if (empty($pagingRequest->getCondition('keyword'))) {
            return $pageResult;
        }
        $searchResult = $this->fetchData($pagingRequest);

        $data = $searchResult->getData(static::ES_SEARCH_PARAM_MODEL);

        if (empty($data)) {
            return $pageResult;
        }

        if ($data->hasMore()) {
            $pageResult['paging'] = $this->createPointer($pagingRequest);
        }

        $pageResult['count'] = $searchResult->getData(static::ES_SEARCH_PARAM_MODEL)->getTotalResultCount();
        $arrayData = $this->convertData($searchResult->getData(static::ES_SEARCH_PARAM_MODEL));

        $pageResult['data'] = $this->extendData($arrayData, $pagingRequest);

        return $pageResult;
    }

    /**
     * Copy necessary ocndition from query
     *
     * @param ESPagingRequest $pagingRequest
     *
     * @return ESPagingRequest
     */
    abstract protected function setCondition(ESPagingRequest $pagingRequest): ESPagingRequest;

    /**
     * Fetch data from ES client
     *
     * @param ESPagingRequest $pagingRequest
     *
     * @return ESSearchResponse
     */
    abstract protected function fetchData(ESPagingRequest $pagingRequest): ESSearchResponse;

    /**
     * Convert data to array
     *
     * @param ESSearchObject $rawData
     *
     * @return array
     */
    protected function convertData(ESSearchObject $rawData): array
    {
        $convertedResult = [];

        $searchResult = $rawData->getSearchResult();

        foreach ($searchResult as $rawObject) {
            $convertedResult[] = $rawObject;
        }

        return $convertedResult;
    }

    /**
     * Convert cursor for getting next page
     *
     * @param ESPagingRequest $params
     *
     * @return string
     */
    protected function createPointer(ESPagingRequest $params): string
    {
        $currentPage = $params->getCondition('pn');
        $params->addCondition('pn', $currentPage + 1, true);

        return $params->getBase64();
    }

    /**
     * Extend search data
     *
     * @param array           $baseData
     * @param ESPagingRequest $request
     *
     * @return array
     */
    abstract protected function extendData(array $baseData, ESPagingRequest $request): array;
}
