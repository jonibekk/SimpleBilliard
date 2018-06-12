<?php
App::import('Service/Paging', 'FeedPagingTrait');
App::import('Lib/Paging', 'PagingServiceInterface');
App::import('Lib/Paging', 'PagingServiceTrait');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');

/**
 * Methods assume that parameters have been validated in Controller layer
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/05/23
 * Time: 11:38
 */
class CircleFeedPaging implements PagingServiceInterface
{
    use PagingServiceTrait;
    use FeedPagingTrait;

    const EXTEND_ALL_FLAG = -1;
    const EXTEND_USER_FLAG = 0;
    const EXTEND_POST_LIKE_FLAG = 1;
    const EXTEND_CIRCLE_FLAG = 2;
    const EXTEND_COMMENT_FLAG = 3;
    const EXTEND_POST_SHARE_CIRCLE_FLAG = 4;
    const EXTEND_POST_SHARE_USER_FLAG = 5;
    const EXTEND_POST_FILE_FLAG = 6;

    /**
     * Get SQL query for IDs of posts visible to the user using default parameters
     *
     * @param array $conditions Any other required conditions
     *                          'user_id' => Currently logged in user ID
     *                          'team_id' => ID of team where the user belongs
     *
     * @return array
     */
    private function getDefaultSharedPosts($conditions = [])
    {
        $Post = new Post();
        $PostService = new PostService();
        $CircleService = new CircleService();

        $Post->my_uid = $conditions['user_id'];

        /**
         * @var DboSource $db
         */
        $db = $Post->getDataSource();

        $queryCondition['OR'][] = $PostService->getUserPostListCondition($conditions['user_id']);
        $queryCondition['OR'][] = $this->createDbExpression($db,
            $PostService->getSharedPostCondition($db, $conditions['user_id'], $conditions['team_id']));
        $queryCondition['OR'][] = $this->createDbExpression($db,
            $CircleService->getUserCirclePostCondition($db, $conditions['user_id'], $conditions['team_id']));

        return $queryCondition;
    }

    /**
     * Get query condition for posts visible to the user
     *
     * @param array $conditions
     *                         'author_id' => Author of posts
     *                         'user_id' => Currently logged in user ID
     *                         'circle_id' => ID of circle where the posts are filtered in
     *                         'team_id' => ID of team where the user belongs
     *
     * @return array
     */
    private function getSharedPosts($conditions = [])
    {
        $Post = new Post();
        $CircleService = new CircleService();

        /**
         * @var DboSource $db
         */
        $db = $Post->getDataSource();

        if (isset($conditions['circle_id'])) {
            $Circle = new Circle();
            $CircleMember = new CircleMember();

            //Check if circle belongs to current team & user has access to the circle
            $CircleMember->my_uid = $conditions['user_id'];
            if (!$Circle->isBelongCurrentTeam($conditions['circle_id'], $conditions['team_id'])
                || ($Circle->isSecret($conditions['circle_id'])
                    && !$CircleMember->isBelong($conditions['circle_id'], $conditions['user_id']))) {
                throw new RuntimeException(__("The circle dosen't exist or you don't have permission."));
            }

            $queryConditions['OR'][] = $this->createDbExpression($db,
                $CircleService->getUserCirclePostCondition($db, $conditions['user_id'], $conditions['team_id'],
                    $conditions['circle_id'],
                    PostShareCircle::SHARE_TYPE_SHARED));

        } elseif (isset($conditions['author_id'])) {

            $PostService = new PostService();

            $queryConditions['OR'][] = $this->createDbExpression($db,
                $PostService->getSharedPostCondition($db, $conditions['user_id'], $conditions['team_id'],
                    ['author_id' => $conditions['author_id']]));

            $queryConditions['OR'][] = $this->createDbExpression($db,
                $CircleService->getUserAccessibleCirclePostCondition($db, $conditions['user_id'],
                    $conditions['team_id'],
                    ['author_id' => $conditions['author_id']]));

            if ($conditions['OR']['user_id'] == $conditions['author_id']) {
                $Post->my_uid = $conditions['author_id'];
                $queryConditions['OR'][] = $Post->getConditionGetMyPostList();
            }

        } //If no parameters were set, use default values
        else {
            $queryConditions = $this->getDefaultSharedPosts($conditions);
        }

        return $queryConditions;
    }

    protected function readData(PagingCursor $pagingCursor, $limit): array
    {
        $options = [
            'conditions' => $this->getSharedPosts($pagingCursor->getConditions()),
            'fields'     => 'Post.id',
            'limit'      => $limit,
            'order'      => $pagingCursor->getOrders()
        ];

        $options['conditions']['Post.type'] = Post::TYPE_NORMAL;
        $options['conditions']['AND'][] = $pagingCursor->getPointersAsQueryOption();

        $Post = new Post();
        $result = $Post->find('all', $options);

        return Hash::extract($result, '{n}.Post');
    }

    protected function countData($conditions): int
    {
        $post = new Post();

        return (int)$post->find('count', $conditions);
    }

    protected function extendPagingResult(&$resultArray, &$conditions, $flags = [])
    {
       //TODO
    }
}