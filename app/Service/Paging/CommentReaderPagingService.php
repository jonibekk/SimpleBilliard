<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender', "UserDataExtender");
App::import('Lib/Paging', 'PagingRequest');
App::uses('CommentRead', 'Model');
App::uses('User', 'Model');

/**
 * User: MartiFloriach
 * Date: 2018/09/07
 * Time: 13:56
 */
class CommentReaderPagingService extends BasePagingService
{

    const EXTEND_ALL = "ext:comment_read:all";
    const EXTEND_USER = "ext:comment_read:user";
    const MAIN_MODEL = 'CommentRead';

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

        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        $result = $CommentRead->useType()->find('all', $options);

        return Hash::extract($result, "{n}.CommentRead");
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        return (int)$CommentRead->find('count', $options);
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

        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        $commentId = $request->getResourceId();

        if (empty($commentId)) {
            GoalousLog::error("Missing comment ID for getting readers");
            throw new InvalidArgumentException("Missing comment ID for getting readers");
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

        $CommentRead->find('all', $conditions);

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
