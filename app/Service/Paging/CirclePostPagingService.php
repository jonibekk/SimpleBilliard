<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Comment', 'Model');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'CirclePostExtender');

class CirclePostPagingService extends BasePagingService
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

        /** @var CirclePostExtender $CirclePostExtender */
        $CirclePostExtender = ClassRegistry::init('CirclePostExtender');
        $data = $CirclePostExtender->extendMulti($data, $userId, $teamId, $options);
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
        $conditions = $request->getConditions(true);

        $circleId = $request->getResourceId();
        $teamId = $request->getCurrentTeamId();

        if (empty($circleId)) {
            GoalousLog::error("Missing circle ID for post paging", $conditions);
            throw new InvalidArgumentException("Missing circle ID");
        }

        $options = [
            'conditions' => [
                'Post.del_flg' => false,
                'Post.team_id' => $teamId,
                'Post.type'    => [Post::TYPE_NORMAL, ]
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'conditions' => [
                        'PostShareCircle.post_id = Post.id',
                        'PostShareCircle.del_flg'   => false,
                        'PostShareCircle.circle_id' => $circleId
                    ]
                ]
            ]
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
}
