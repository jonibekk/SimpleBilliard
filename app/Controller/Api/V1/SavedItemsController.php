<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service/Api', 'ApiSavedPostService');

/**
 * Class SavedItemsController
 */
class SavedItemsController extends ApiController
{
    /**
     * Get saved items and paging data for list page
     * - url '/api/v1/saved_items'
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

        // get query params
        $limit = $this->request->query('limit') ?? ApiSavedPostService::SAVED_POST_DEFAULT_LIMIT;
        $cursor = $this->request->query('cursor') ?? 0;
        $userId = $this->Auth->user('id');

        // Check limit param under max
        if (!$ApiSavedPostService->checkMaxLimit($limit)) {
            return $this->_getResponseBadFail(__("Get count over the upper limit"));
        }
        $savedPosts = $ApiSavedPostService->find($this->current_team_id, $userId, $cursor, $limit);

        return $this->_getResponsePagingSuccess($savedPosts);
    }

    /**
     * Get saved item detail
     * - url '/api/v1/saved_items/{saved_item_id}'
     *
     * @param int $savedItemId
     *
     * @return void
     */
    function get_detail(int $savedItemId)
    {
        // TODO:
    }
}
