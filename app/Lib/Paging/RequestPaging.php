<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:20
 */

class RequestPaging
{
    /**
     * @param ApiPagingInterface $apiService
     * @param                    $cursor
     * @param                    $limit
     * @param                    $direction
     *
     * @return array
     */
    public function searchWithPaging(&$apiService, $cursor, $limit, $direction)
    {
        $conditions = [];
        $currentId = null;

        if (isset($cursor)) {
            $cursor = self::decodeCursor($cursor);

            //Process cursor
            $conditions = $cursor['conditions'];
            $currentId = $cursor['start'];

            $apiService->setPagingParameters($cursor['conditions']);
        }

        $apiService->beforeRead();
        $apiService->readData($currentId, $limit, $direction);
        $apiService->afterRead();

        //Create paging

        return ['data', 'paging', 'count'];
    }

    /**
     * Create next cursor for API requests
     *
     * @param array $conditions Conditions for the search, e.g. SQL query
     * @param int   $startId    Table ID of start point of the page. Will NOT include this ID
     * @param int   $limit      Number of records for the page
     *
     * @return string Encoded next paging cursor
     */
    public static function createNextPageCursor(
        array $conditions = null,
        int $startId = null,
        int $limit = null
    ): string {
        $array = array();

        if (!empty($conditions)) {
            $array['conditions'] = $conditions;
        }
        if (!empty($startId)) {
            $array['start'] = $startId;
        }
        if (!empty($limit)) {
            $array['limit'] = $limit;
        }

        return base64_encode(json_encode($array));
    }

    /**
     * Create previous  cursor for API requests
     *
     * @param array $conditions Conditions for the search, e.g. SQL query
     * @param int   $endId      Table ID of end point of the page. Will NOT include this ID
     * @param int   $limit      Number of records for the page
     *
     * @return string Encoded next paging cursor
     */
    public static function createPrevPageCursor(
        array $conditions = null,
        int $endId = null,
        int $limit = null
    ): string {
        $array = array();

        if (!empty($conditions)) {
            $array['conditions'] = $conditions;
        }
        if (!empty($endId)) {
            $array['end'] = $endId;
        }
        if (!empty($limit)) {
            $array['limit'] = $limit;
        }

        return base64_encode(json_encode($array));
    }

    /**
     * Decode a cursor into multi-dimensional array
     *
     * @param string $cursor
     *
     * @return array
     */
    public static function decodeCursor(string $cursor): array
    {
        return json_decode(base64_decode($cursor), true);
    }
}