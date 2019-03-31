<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/Paging', 'PagingRequest');
App::uses('Comment', 'Model');
App::uses('User', 'Model');
App::import('Lib/DataExtender', 'CommentExtender');

class CommentPagingService extends BasePagingService
{
    const MAIN_MODEL = 'Comment';
    const DIRECTION_NEW = 'new';
    const DIRECTION_OLD = 'old';

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

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var CommentExtender $CommentExtender */
        $CommentExtender = ClassRegistry::init('CommentExtender');
        $data = $CommentExtender->extendMulti($data, $userId, $teamId, $options);
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
        $conditions = $request->getConditions();
        $cursorCommentId = Hash::get($conditions, 'cursor_comment_id');
        $direction = Hash::get($conditions, 'direction');

        if (!empty($cursorCommentId)) {
            $inequality =  $direction === self::DIRECTION_NEW ? '>' : '<';
            $options['conditions']['Comment.id '.$inequality] = $cursorCommentId;
        }


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
        $direction = Hash::get($pagingRequest->getConditions(), 'direction');
        $inequality =  $direction === self::DIRECTION_NEW ? ">" : "<";
        return new PointerTree([static::MAIN_MODEL . '.id', $inequality, $lastElement['id']]);
    }

    protected function beforeRead(PagingRequest $pagingRequest)
    {
        $pagingRequest->addCondition(['direction' => self::DIRECTION_OLD]);
        $pagingRequest->addQueriesToCondition(['cursor_comment_id', 'direction']);
        return $pagingRequest;
    }
}
