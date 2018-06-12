<?php
App::import('Service/Paging', 'FeedPagingTrait');
App::import('Lib/Paging', 'PagingServiceInterface');
App::import('Lib/Paging', 'PagingServiceTrait');
App::import('Service', 'CircleService');
App::import('Service', 'PostService');
App::uses('PagingCursor', 'Lib/Paging');
App::uses('Comment', 'Model');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'UserDataExtender');

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
    const EXTEND_CIRCLE_FLAG = 1;
    const EXTEND_COMMENT_FLAG = 2;
    const EXTEND_POST_SHARE_CIRCLE_FLAG = 3;
    const EXTEND_POST_SHARE_USER_FLAG = 4;
    const EXTEND_POST_FILE_FLAG = 5;

    const DEFAULT_COMMENT_COUNT = 3;

    protected function readData(PagingCursor $pagingCursor, $limit): array
    {
        $options = [
            'conditions' => $this->getSharedPosts($pagingCursor->getConditions()),
            'limit'      => $limit,
            'order'      => $pagingCursor->getOrders()
        ];

        $options['conditions']['Post.type'] = Post::TYPE_NORMAL;
        $options['conditions']['AND'][] = $pagingCursor->getPointersAsQueryOption();

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->find('all', $options);

        //Remove 'Post' from array
        return Hash::extract($result, '{n}.Post');
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
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        /**
         * @var DboSource $db
         */
        $db = $Post->getDataSource();

        if (isset($conditions['circle_id'])) {

            /** @var Circle $Circle */
            $Circle = ClassRegistry::init('Circle');

            /** @var CircleMember $CircleMember */
            $CircleMember = ClassRegistry::init('CircleMember');

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

            /** @var PostService $PostService */
            $PostService = ClassRegistry::init('PostService');

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
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        /** @var PostService $PostService */
        $PostService = ClassRegistry::init('PostService');

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

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

    protected function countData($conditions): int
    {
        $options = [
            'conditions' => $this->getSharedPosts($conditions),
        ];

        $options['conditions']['Post.type'] = Post::TYPE_NORMAL;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        return (int)$Post->find('count', $options);
    }

    protected function extendPagingResult(&$resultArray, &$conditions, $flags = [])
    {
       if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_USER_FLAG, $flags)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.Post.user_id");
        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_POST_LIKE_FLAG, $flags)) {
        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_CIRCLE_FLAG, $flags)) {
        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_COMMENT_FLAG, $flags)) {
        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_POST_SHARE_CIRCLE_FLAG, $flags)) {
        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_POST_SHARE_USER_FLAG, $flags)) {
        }
        if (in_array(self::EXTEND_ALL_FLAG, $flags) || in_array(self::EXTEND_POST_FILE_FLAG, $flags)) {

        }
    }
}