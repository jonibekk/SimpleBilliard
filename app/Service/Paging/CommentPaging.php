<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/28
 * Time: 13:56
 */

//TODO
class CommentPaging extends BasePagingService
{

    /**
     * @param PagingRequest $pagingRequest
     * @param int          $limit
     *
     * @return array
     */
    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $Comment = new Comment();

        return $Comment->getPostCommentsByCursor($pagingRequest, $limit);
    }

    protected function countData(array $conditions): int
    {
        $Comment = new Comment();

        return $Comment->getCount($conditions);
    }

    protected function extendPagingResult(&$resultArray, $conditions, $options = [])
    {
    }

}