<?php
App::import('Lib/Paging', 'PagingServiceTrait');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/09
 * Time: 17:20
 */

class RequestPaging
{
    const DEFAULT_PAGE_LIMIT = 20;
    const PAGE_ORDER_ASC = 'asc';
    const PAGE_ORDER_DESC = 'desc';
    const PAGE_DIR_NEXT = 'next';
    const PAGE_DIR_PREV = 'prev';

    /**
     * @param PagingServiceTrait $pagingService
     * @param array              $conditions
     * @param mixed              $pivotValue
     * @param int                $limit
     * @param string             $order
     * @param string             $direction
     * @param array              $extendFlags
     *
     * @return array
     */
    public function getWithPaging(
        $pagingService,
        $conditions = [],
        $pivotValue = null,
        $limit = self::DEFAULT_PAGE_LIMIT,
        $order = self::PAGE_ORDER_DESC,
        $direction = self::PAGE_DIR_NEXT,
        $extendFlags = []
    ) {

        $finalResult = [
            'data'   => [],
            'paging' => [
                'next' => '',
                'prev' => ''
            ],
            'count'  => 0
        ];

        $pagingService->beforeRead();

        $finalResult['count'] = $pagingService->countData($conditions);

        $queryResult = $pagingService->readData($conditions, $pivotValue, $limit + 1, $order, $direction);

        //If there is further result
        if (count($queryResult) > $limit) {
            array_pop($queryResult);

            //Get the last element pivot value
            $newPivotValue = $pagingService->getPivotValue($queryResult);

            if ($direction == self::PAGE_DIR_NEXT || $direction == self::PAGE_DIR_PREV) {
                $queryResult['paging'][$direction] = self::createPageCursor($newPivotValue, $order, $conditions,
                    $direction);
            }
        }

        if (!empty($extendFlags) && !empty($queryResult)) {
            $pagingService->extendPagingResult($queryResult, $extendFlags);
        }

        $pagingService->afterRead();

        $finalResult['data'] = $queryResult;

        return $finalResult;
    }

    /**
     * Create next cursor for API requests
     *
     * @param mixed  $pivotValue Table ID of start point of the page. Will NOT include this value
     * @param string $order      Order of the query sorting
     * @param string $direction  Direction of the query sorting
     * @param array  $conditions Conditions for the search, e.g. SQL query
     *
     * @return string Encoded next paging cursor
     */
    public static function createPageCursor(
        $pivotValue,
        $conditions = null,
        $order = self::PAGE_ORDER_DESC,
        $direction = self::PAGE_DIR_NEXT
    ): string {
        $array = array();

        $array['pivot'] = $pivotValue;
        $array['order'] = $order;
        $array['direction'] = $direction;
        if (!empty($conditions)) {
            $array['conditions'] = $conditions;
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