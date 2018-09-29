<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender', "UserDataExtender");
App::import('Lib/DataExtender', "CommentLikeDataExtender");
App::import('Lib/DataExtender', "CommentReadDataExtender");
App::import('Lib/Paging', 'PagingRequest');
App::uses('Comment', 'Model');
App::uses('User', 'Model');

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
    const EXTEND_LIKE = "ext:comment:like";
    const EXTEND_READ = "ext:comment:read";
    const MAIN_MODEL = 'Comment';

    /**
     * @param PagingRequest $pagingRequest
     * @param int           $limit
     *
     * @return array
     */
    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $result = $Comment->useType()->find('all', $options);

        return Hash::extract($result, "{n}.Comment");
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        return (int)$Comment->find('count', $options);
    }

    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
    {
        if ($this->includeExt($options, self::EXTEND_USER)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.user_id");
        }
        if ($this->includeExt($options, self::EXTEND_LIKE)) {
            $userId = $request->getCurrentUserId();
            /** @var CommentLikeDataExtender $CommentLikeDataExtender */
            $CommentLikeDataExtender = ClassRegistry::init('CommentLikeDataExtender');
            $CommentLikeDataExtender->setUserId($userId);
            $resultArray = $CommentLikeDataExtender->extend($resultArray, "{n}.id", "comment_id");
        }
        if ($this->includeExt($options, self::EXTEND_READ)) {
            /** @var CommentReadDataExtender $CommentReadDataExtender */
            $CommentReadDataExtender = ClassRegistry::init('CommentReadDataExtender');
            $CommentReadDataExtender->setUserId($userId);
            $resultArray = $CommentReadDataExtender->extend($resultArray, "{n}.id", "comment_id");
        }
    }

    private function createSearchCondition(PagingRequest $request): array
    {
        $postId = $request->getResourceId();

        if (empty($postId)) {
            GoalousLog::error("Missing post ID for getting comments");
            throw new InvalidArgumentException("Missing post ID for getting comments");
        }

        $options = [
            'conditions' => [
                'Comment.post_id' => $postId,
            ],
            'table'      => 'comments',
            'alias'      => 'Comment'
        ];

        return $options;
    }

    protected function addDefaultValues(PagingRequest $pagingRequest): PagingRequest
    {
        $pagingRequest->addOrder("id", PagingRequest::PAGE_ORDER_DESC);
        return $pagingRequest;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        return new PointerTree([static::MAIN_MODEL . '.id', "<", $lastElement['id']]);
    }
}
