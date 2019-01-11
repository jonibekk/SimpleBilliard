<?php
App::import('Lib/DataExtender/Extension', 'CircleExtension');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");
App::import('Service', 'ImageStorageService');
App::import('Service/Paging/Search', 'BaseSearchPagingService');
App::uses('Circle', 'Model');
App::uses('TimeExHelper', 'View/Helper');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 12/3/2018
 * Time: 10:58 AM
 */
class CircleSearchPagingService extends BaseSearchPagingService
{
    const ES_SEARCH_PARAM_MODEL = 'circle';

    protected function setCondition(ESPagingRequest $pagingRequest): ESPagingRequest
    {
        $pagingRequest->addQueryToCondition('keyword', false);
        $pagingRequest->addQueryToCondition('limit', false, self::DEFAULT_PAGE_LIMIT);

        return $pagingRequest;
    }

    protected function fetchData(ESPagingRequest $pagingRequest): ESSearchResponse
    {
        $ESClient = new ESClient();

        $query = $pagingRequest->getCondition('keyword');

        $teamId = $pagingRequest->getTempCondition('team_id');

        $params[static::ES_SEARCH_PARAM_MODEL] = [
            'pn'      => intval($pagingRequest->getCondition('pn')),
            'rn'      => intval($pagingRequest->getCondition('limit')),
            'user_id' => intval($pagingRequest->getTempCondition('user_id'))
        ];

        return $ESClient->search($query, $teamId, $params);
    }

    protected function extendData(array $baseData, ESPagingRequest $request): array
    {
        if (empty($baseData)) {
            return [];
        }

        /** @var CircleExtension $CircleExtension */
        $CircleExtension = ClassRegistry::init('CircleExtension');
        $resultArray = $CircleExtension->extendMulti($baseData, "{n}.id");

        // Set image url each circle
        /** @var ImageStorageService $ImageStorageService */
        $ImageStorageService = ClassRegistry::init('ImageStorageService');

        // For extending display_last_post_created
        $TimeEx = new TimeExHelper(new View());

        foreach ($resultArray as &$result) {
            $result['img_url'] = $ImageStorageService->getImgUrlEachSize($result['circle'], 'Circle')['medium_large'];
            $displayLatestPostCreated = '';
            if (!empty($result['circle']['latest_post_created'])) {
                $displayLatestPostCreated = $TimeEx->elapsedTime($result['circle']['latest_post_created'], 'rough', false);
            }
            $result['display_last_post_created'] = $displayLatestPostCreated;
        }

        return $resultArray;
    }
}