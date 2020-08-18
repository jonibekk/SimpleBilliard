<?php
App::import('Lib/DataExtender/Extension', 'GoalExtension');
App::import('Lib/ElasticSearch', "ESClient");
App::import('Lib/ElasticSearch', "ESSearchResponse");
App::import('Service', 'ImageStorageService');
App::import('Service/Paging/Search', 'BaseSearchPagingService');
App::uses('Goal', 'Model');
App::uses('TimeExHelper', 'View/Helper');

class GoalSearchPagingService extends BaseSearchPagingService
{
    const ES_SEARCH_PARAM_MODEL = 'goal';

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
        throw new Exception('Not implemented.');
    }
}
