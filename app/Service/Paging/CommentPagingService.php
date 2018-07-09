<?php
App::import('Lib/Paging', 'BasePagingService');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/28
 * Time: 13:56
 */
class CommentPagingService extends BasePagingService
{

    const EXTEND_ALL = "ext:comment:all";
    const EXTEND_USER = "ext:comment:user";

    /**
     * @param PagingRequest $pagingRequest
     * @param int           $limit
     *
     * @return array
     */
    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $Comment = new Comment();

        return $Comment->getPostCommentsByCursor($pagingRequest, $limit);
    }

    protected function countData(PagingRequest $request): int
    {
        $Comment = new Comment();

        return $Comment->getCount($request->getConditions());
    }

    protected function extendPagingResult(array &$resultArray, PagingRequest $conditions, array $flags = [])
    {
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_USER, $flags)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.Comment.user_id");
        }
    }

}