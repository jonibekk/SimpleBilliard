<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('PostLikes', 'Model');
App::import('Lib/DataExtender', 'PostLikeExtender');

class PostLikesPagingService extends BasePagingService
{
    const MAIN_MODEL = 'PostLike';

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

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        $result = $PostLike->useType()->find('all', $options);

        return Hash::extract($result, "{n}.PostLike");
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        return (int)$PostLike->find('count', $options);
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var PostLikeExtender $PostLikeExtender */
        $PostLikeExtender = ClassRegistry::init('PostLikeExtender');
        $data = $PostLikeExtender->extendMulti($data, $userId, $teamId, $options);
    }

    /**
     * Create the SQL query for getting the user who likes the post
     *
     * @param PagingRequest $request
     *
     * @return array
     */
    private function createSearchCondition(PagingRequest $request): array
    {
        $conditions = $request->getConditions(true);

        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');

        $postId = $request->getResourceId();

        if (empty($postId)) {
            GoalousLog::error("Missing post ID for getting users who likes the post");
            throw new InvalidArgumentException("Missing post ID for getting users who likes the post");
        }

        $conditions = [
            'conditions'    => [
                'post_id'   => $postId,
        ],
        'fields'=>[
            'user_id',
            'created',
            'id'
        ]];

        $PostLike->find('all', $conditions);

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
