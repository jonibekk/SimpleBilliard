<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Comment', 'Model');
App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'FeedPostExtender');

class FeedPostPagingService extends BasePagingService
{
    const MAIN_MODEL = 'Post';

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->useType()->find('all', $options);

        //Remove 'Post' from array
        return Hash::extract($result, '{n}.Post');
    }

    protected function countData(PagingRequest $request): int
    {
        $options = $this->createSearchCondition($request);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        return (int)$Post->find('count', $options);
    }

    protected function extendPagingResult(array &$data, PagingRequest $request, array $options = [])
    {
        $userId = $request->getCurrentUserId();
        $teamId = $request->getCurrentTeamId();

        /** @var FeedPostExtender $FeedPostExtender */
        $FeedPostExtender = ClassRegistry::init('FeedPostExtender');
        $data = $FeedPostExtender->extendMulti($data, $userId, $teamId, $options);
    }

    /**
     * Create the SQL query for getting the circle posts
     *
     * @param PagingRequest $request
     *
     * @return array
     */
    private function createSearchCondition(PagingRequest $request): array
    {
        $teamId = $request->getCurrentTeamId();

        $options = [
            'conditions' => [
                'Post.del_flg' => false,
                'Post.team_id' => $teamId,
                'Post.type'    => [Post::TYPE_CREATE_GOAL, Post::TYPE_ACTION]
            ],
        ];

        return $options;
    }

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        return new PointerTree([static::MAIN_MODEL . '.id', "<", $lastElement['id']]);
    }

    /**
     * Change array structure
     *
     * @param array         $queryResult
     * @param PagingRequest $pagingRequest
     *
     * @return array
     */
    protected function afterRead(array $queryResult, PagingRequest $pagingRequest): array
    {
        $returnArray = [];

        foreach ($queryResult as $result) {
            $entry['type'] = $result['type'];
            $entry['data'] = $result;

            $returnArray[] = $entry;
        }

        return $returnArray;
    }

}
