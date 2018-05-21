<?php
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/21
 * Time: 12:56
 */

interface PagingServiceInterface
{
    /**
     * @param PagingCursor $pagingCursor
     * @param int          $limit
     * @param array        $extendFlags
     *
     * @return array
     */
    public function getDataWithPaging(
        $pagingCursor,
        $limit = PagingCursor::DEFAULT_PAGE_LIMIT,
        $extendFlags = []
    );

}