<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');

/**
 * Class ApiSavedPostService
 */
class ApiCirclePinService extends ApiService
{
    const SAVED_POST_DEFAULT_LIMIT = 20;

    /**
     * Finding saved items. It will returns data as API response
     *
     * @param int      $teamId
     * @param int      $loginUserId
     * @param array    $conditions
     * @param int|null $cursor
     * @param int|null $limit
     *
     * @return array
     */
    function search(
        int $teamId,
        int $loginUserId,
        array $conditions = [],
        $cursor = null,
        $limit = null
    ): array {
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        // if no limit then it to be default limit
        if (!$limit) {
            $limit = self::SAVED_POST_DEFAULT_LIMIT;
        }

        // it's default that will be returned
        $res = [
            'data'   => [],
            'paging' => ['next' => null],
        ];

        // Get saved items
        // The reason why get $limit + 1 is to check whether next paging data exists
        $savedItems = $SavedPostService->search($teamId, $loginUserId, $conditions, $cursor, $limit + 1);
        // Format array structure for api response
        $res['data'] = $this->convertResponseForApi($savedItems);
        // Set paging data
        $res = $this->setPaging($res, $limit, $conditions);
        // Extend data (photo, user, etc)
        $res['data'] = $this->extend($res['data'], $teamId);

        return $res;
    }
}
