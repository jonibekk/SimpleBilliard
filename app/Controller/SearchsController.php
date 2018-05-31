<?php
App::uses('AppController', 'Controller');
App::import('Service', 'SearchService');

/**
 * Searchs Controller
 */
class SearchsController extends AppController
{
    /**
     * Get search results
     */
    function ajax_get_search_results()
    {
        /** @var SearchService $SearchService */
        $SearchService = ClassRegistry::init('SearchService');

        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = ['results' => []];
        if (isset($query['term']) && !empty($query['term']) && count($query['term']) <= SELECT2_QUERY_LIMIT && isset($query['page_limit']) && !empty($query['page_limit'])) {
            $with_self  = boolval($query['with_self'] ?? false);
            $res = $SearchService->searchByKeword($query['term'], $query['page_limit'], $with_self);
        }
        return $this->_ajaxGetResponse($res);
    }
}
