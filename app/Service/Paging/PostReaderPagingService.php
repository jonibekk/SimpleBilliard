<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender/Extension', "UserExtension");
App::import('Lib/Paging', 'PagingRequest');
App::uses('PostRead', 'Model');
App::uses('User', 'Model');
App::import('Lib/DataExtender', 'PostReadExtender');

class PostReaderPagingService extends BasePagingService
{
    const MAIN_MODEL = 'PostRead';

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

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $result = $PostRead->useType()->find('all', $options);

        return Hash::extract($result, "{n}.PostRead");
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        return (int)$PostRead->find('count', $options);
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var PostReadExtender $PostReadExtender */
        $PostReadExtender = ClassRegistry::init('PostReadExtender');
        $data = $PostReadExtender->extendMulti($data, $userId, $teamId, $options);
    }

    /**
     * Create the SQL query for getting the readers of the post
     *
     * @param PagingRequest $request
     *
     * @return array
     */
    private function createSearchCondition(PagingRequest $request): array
    {
        $conditions = $request->getConditions(true);

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $postId = $request->getResourceId();

        if (empty($postId)) {
            GoalousLog::error("Missing post ID for getting comments");
            throw new InvalidArgumentException("Missing post ID for getting comments");
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

        $PostRead->find('all', $conditions);

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
