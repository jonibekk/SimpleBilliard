<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('CommentLikes', 'Model');
App::uses('User', 'Model');
App::import('Lib/DataExtender', 'CommentLikeExtender');

class CommentLikesPagingService extends BasePagingService
{
    const MAIN_MODEL = 'CommentLike';

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

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        $result = $CommentLike->useType()->find('all', $options);

        return Hash::extract($result, "{n}.CommentLike");
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        return (int)$CommentLike->find('count', $options);
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var CommentLikeExtender $CommentLikeExtender */
        $CommentLikeExtender = ClassRegistry::init('CommentLikeExtender');
        $data = $CommentLikeExtender->extendMulti($data, $userId, $teamId, $options);
    }

    /**
     * Create the SQL query for getting the user who likes the comment
     *
     * @param PagingRequest $request
     *
     * @return array
     */
    private function createSearchCondition(PagingRequest $request): array
    {
        $conditions = $request->getConditions(true);

        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init('CommentLike');

        $commentId = $request->getResourceId();

        if (empty($commentId)) {
            GoalousLog::error("Missing post ID for getting users who likes the Comment");
            throw new InvalidArgumentException("Missing post ID for getting users who likes the Comment");
        }

        $conditions = [
            'conditions'    => [
                'comment_id'   => $commentId,
        ],
        'fields'=>[
            'user_id',
            'created',
            'id'
        ]];

        $CommentLike->find('all', $conditions);

        return $conditions;
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
