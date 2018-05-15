<?php
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