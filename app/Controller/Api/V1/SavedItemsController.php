<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiSavedPostService');
App::import('Service', 'SavedPostService');

/**
 * Class SavedItemsController
 */
class SavedItemsController extends ApiController
{
    /**
     * Get saved items and paging data for list page
     *
     * @queryParam int $limit optional
     * @queryParam int $cursor optional
     * @queryParam int $keyword optional
     * @return CakeResponse
     */
    function get_list()
    {
        /** @var ApiSavedPostService $ApiSavedPostService */
        $ApiSavedPostService = ClassRegistry::init("ApiSavedPostService");

        // Check limit param under max
        if (!$ApiSavedPostService->checkMaxLimit((int)$this->request->query('limit'))) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }
        $res = $this->_findSearchResults();

        return $this->_getResponsePagingSuccess($res);
    }
    /**
     * Get initial info for saved item list page
     */
    public function get_init_list_page()
    {
        /** @var ApiSavedPostService $ApiSavedPostService */
        $ApiSavedPostService = ClassRegistry::init("ApiSavedPostService");
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init("SavedPostService");

        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        // Search saved items
        $res['search_result'] = $this->_findSearchResults();
        // Get search conditions
        $res['search_conditions'] = $this->_fetchSearchConditions();

        // Get count total, only action, only post
        $savedItemCountEachType = $SavedPostService->countSavedPostEachType($teamId, $userId);
        $res['search_result']['counts'] = $savedItemCountEachType;
        return $this->_getResponseSuccess($res);

    }

    /**
     * Common process for search saved items
     *
     * @return array
     */
    private function _findSearchResults(): array
    {
        /** @var ApiSavedPostService $ApiSavedPostService */
        $ApiSavedPostService = ClassRegistry::init("ApiSavedPostService");

        /* リクエストパラメータ取得 */
        $cursor = $this->request->query('cursor') ?? 0;
        $limit = (int)$this->request->query('limit');
        $conditions = $this->_fetchSearchConditions();


        $limit = empty($limit) ? ApiSavedPostService::SAVED_POST_DEFAULT_LIMIT : $limit;

        // ゴール検索
        $userId = $this->Auth->user('id');
        $teamId = $this->current_team_id;

        $searchResult = $ApiSavedPostService->search($teamId, $userId, $conditions, $cursor, $limit);
        return $searchResult;
    }


    /**
     * Get search conditions
     *
     * @return array
     */
    private function _fetchSearchConditions(): array
    {
        $conditions = [
            'type'     => $this->request->query('type'),
        ];
        return $conditions;
    }
}
