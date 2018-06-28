<?php
App::import('Lib/Paging', 'BasePagingService');
/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/28
 * Time: 13:56
 */

//TODO
class CommentPaging extends BasePagingService
{

    const EXTEND_ALL = "ext:comment:all";
    const EXTEND_USER = "ext:comment:user";

    /**
     * @param PagingCursor $pagingCursor
     * @param int          $limit
     *
     * @return array
     */
    protected function readData(PagingCursor $pagingCursor, int $limit): array
    {
        $Comment = new Comment();

        return $Comment->getPostCommentsByCursor($pagingCursor, $limit);
    }

    protected function countData($conditions): int
    {
        $Comment = new Comment();

        return $Comment->getCount($conditions);
    }

    protected function extendPagingResult(&$resultArray, $conditions, $flags = [])
    {
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_USER, $flags)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.Comment.user_id");
        }
    }

}