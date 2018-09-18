<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender', "UserDataExtender");
App::import('Lib/Paging', 'PagingRequest');
App::uses('PostRead', 'Model');
App::uses('User', 'Model');

/**
 * User: MartiFloriach
 * Date: 2018/09/03
 * Time: 13:56
 */
class PostReaderPagingService extends BasePagingService
{

    const EXTEND_ALL = "ext:post_read:all";
    const EXTEND_USER = "ext:post_read:user";
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

    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
    {
        if ($this->includeExt($options, self::EXTEND_USER)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.user_id");
        }
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
