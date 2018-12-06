<?php
App::import('Lib/Network/Response', 'ApiResponse');
App::import('Lib/Network/Response', 'ErrorResponse');
App::import('Lib/ElasticSearch', 'ESPagingRequest');
App::import('Lib/Paging', 'PagingRequest');
App::import('Service/Paging/Search', 'CircleSearchPagingService');
App::uses('ApiController', 'Controller/Api');

/**
 * Created by PhpStorm.
 * User: Stephen Raharja
 * Date: 11/28/2018
 * Time: 3:20 PM
 */
class CirclesController extends ApiController
{
    public function get_search()
    {
        $error = $this->validateSearch();

        if (!empty($error)) {
            return $this->_getResponseValidationFail($error);
        }

        $query = $this->request->query;
        $limit = $this->request->query('limit');
        $cursor = $this->request->query('cursor');
        $teamId = $this->current_team_id;

        if (empty($cursor)) {
            $pagingRequest = new ESPagingRequest();
            $pagingRequest->setQuery($query);
            $pagingRequest->addCondition('pn', 1);
            $pagingRequest->addCondition('limit', $limit);
        } else {
            $pagingRequest = ESPagingRequest::convertBase64($cursor);
        }

        $pagingRequest->addTempCondition('team_id', $teamId);
        $pagingRequest->addTempCondition('user_id', $this->Auth->user('id'));

        /** @var CircleSearchPagingService $CircleSearchPagingService */
        $CircleSearchPagingService = ClassRegistry::init('CircleSearchPagingService');
        $searchResult = $CircleSearchPagingService->getDataWithPaging($pagingRequest);

        return ApiResponse::ok()->withBody($searchResult)->getResponse();
    }

    private function validateSearch(): array
    {
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        if (empty ($userId)) {
            return ["No user ID"];
        }

        if (empty($teamId)) {
            return ["No team ID"];
        }

        return [];
    }
}