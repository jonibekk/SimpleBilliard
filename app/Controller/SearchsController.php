<?php
App::uses('AppController', 'Controller');
App::uses('Search', 'Model');

use Goalous\Model\Enum as Enum;

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
        /** @var Search $Search */
        $Search = ClassRegistry::init('Search');

        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = ['results' => []];
        if (isset($query['term']) && !empty($query['term']) && count($query['term']) <= SELECT2_QUERY_LIMIT && isset($query['page_limit']) && !empty($query['page_limit'])) {
            $with_self  = boolval($query['with_self'] ?? false);
            $res = $Search->searchByKeword($query['term'], $query['page_limit'], $with_self);
        }
        return $this->_ajaxGetResponse($res);
    }
}
