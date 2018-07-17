<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender', "UserDataExtender");
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

    protected function extendPagingResult(array &$resultArray, PagingRequest $conditions, array $flags = [])
    {
        if (in_array(self::EXTEND_ALL, $flags) || in_array(self::EXTEND_USER, $flags)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.user_id");
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
        $pagingRequest->addOrder("id", PagingRequest::PAGE_ORDER_ASC);
        return $pagingRequest;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): BinaryNode {
        return new BinaryNode(['id', ">", $lastElement['id']]);
    }
}