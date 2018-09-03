<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender', "UserDataExtender");
App::import('Lib/DataExtender', "CommentLikeDataExtender");
App::import('Lib/Paging', 'PagingRequest');
App::uses('PostRead', 'Model');
App::uses('User', 'Model');

/**
 * User: MartiFloriach
 * Date: 2018/09/03
 * Time: 13:56
 */
class ReadersPagingService extends BasePagingService
{

    const EXTEND_ALL = "ext:post_reads:all";
    const EXTEND_USER = "ext:post_reads:user";
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
        if ($this->includeExt($options, self::EXTEND_LIKE)) {
            $userId = $request->getCurrentUserId();
            /** @var CommentLikeDataExtender $CommentLikeDataExtender */
            $CommentLikeDataExtender = ClassRegistry::init('CommentLikeDataExtender');
            $CommentLikeDataExtender->setUserId($userId);
            $resultArray = $CommentLikeDataExtender->extend($resultArray, "{n}.id", "comment_id");
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
                'PostRead.post_id' => $postId,
            ],
            'table'      => 'post_reads',
            'alias'      => 'PostRead'
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
    ): PointerTree {
        return new PointerTree([static::MAIN_MODEL . '.id', ">", $lastElement['id']]);
    }
}
