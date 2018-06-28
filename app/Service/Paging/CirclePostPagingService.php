<?php
App::import('Lib/Paging', 'BasePagingService');
App::import('Lib/DataExtender', 'UserDataExtender');
App::import('Lib/DataExtender', 'CircleDataExtender');
App::import('Service/Paging', 'CommentPaging');
App::import('Service', 'CircleService');
App::import('Service', 'PostService');
App::uses('PagingCursor', 'Lib/Paging');
App::uses('Comment', 'Model');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 9:39
 */
class CirclePostPagingService extends BasePagingService
{
    const EXTEND_ALL = "ext:circle_post:all";
    const EXTEND_USER = "ext:circle_post:user";
    const EXTEND_CIRCLE = "ext:circle_post:circle";
    const EXTEND_COMMENT = "ext:circle_post:comment";
    const EXTEND_POST_SHARE_CIRCLE = "ext:circle_post:share_circle";
    const EXTEND_POST_SHARE_USER = "ext:circle_post:share_user";
    const EXTEND_POST_FILE = "ext:circle_post:file";

    const DEFAULT_COMMENT_COUNT = 3;

    protected function readData(PagingCursor $pagingCursor, int $limit): array
    {
        $options = $this->createSearchCondition($pagingCursor->getConditions());

        $options['limit'] = $limit;
        $options['order'] = $pagingCursor->getOrders();
        $options['conditions']['AND'][] = $pagingCursor->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->find('all', $options);

        //Remove 'Post' from array
        return Hash::extract($result, '{n}.Post');
    }

    protected function countData($conditions): int
    {
        $options = $this->createSearchCondition($conditions);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        return (int)$Post->find('count', $options);
    }

    protected function extendPagingResult(&$resultArray, $conditions, $options = [])
    {

        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_USER, $options)) {
            /** @var UserDataExtender $UserDataExtender */
            $UserDataExtender = ClassRegistry::init('UserDataExtender');
            $resultArray = $UserDataExtender->extend($resultArray, "{n}.user_id");
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_CIRCLE, $options)) {
            /** @var CircleDataExtender $CircleDataExtender */
            $CircleDataExtender = ClassRegistry::init('CircleDataExtender');
            $resultArray = $CircleDataExtender->extend($resultArray, "{n}.circle_id");
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_COMMENT, $options)) {
            /** @var CommentPaging $CommentPaging */
            $CommentPaging = ClassRegistry::init('CommentPaging');

            foreach ($resultArray as &$result) {
                $conditions = [
                    'post_id' => Hash::extract($result, 'Post.id')
                ];
                $order = [
                    'id' => 'asc'
                ];

                $cursor = new PagingCursor($conditions, [], $order);

                $comments = $CommentPaging->getDataWithPaging($cursor, self::DEFAULT_COMMENT_COUNT,
                    CommentPaging::EXTEND_ALL);

                $result['comments'] = $comments;
            }
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_POST_SHARE_CIRCLE, $options)) {
            //Postponed
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_POST_SHARE_USER, $options)) {
            //Postponed
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_POST_FILE, $options)) {
            //Postponed
        }
    }

    /**
     * Create the SQL query for getting the circle posts
     *
     * @param array $conditions
     *
     * @return array
     */
    private function createSearchCondition(array $conditions): array
    {
        $circleId = Hash::get($conditions, 'circle_id');

        if (empty($circleId)) {
            GoalousLog::error("Missing circle ID for post paging", $conditions);
            throw new RuntimeException("Missing circle ID");
        }

        $options = [
            'conditions' => [
                'Post.del_flg' => false,
                'Post.type'    => [Post::TYPE_NORMAL, Post::TYPE_CREATE_CIRCLE]
            ],
            'join'       => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'conditions' => [
                        'PostShareCircle = Post.id',
                        'PostShareCircle.del_flg'   => false,
                        'PostShareCircle.circle_id' => $circleId
                    ]
                ]
            ]
        ];

        return $options;
    }

}