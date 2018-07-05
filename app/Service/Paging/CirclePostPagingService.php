<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::import('Lib/DataExtender', 'UserDataExtender');
App::import('Lib/DataExtender', 'CircleDataExtender');
App::import('Lib/DataExtender', 'PostLikeDataExtender');
App::import('Lib/DataExtender', 'PostSavedDataExtender');
App::import('Service/Paging', 'CommentPagingService');
App::import('Service', 'CircleService');
App::import('Service', 'PostService');
App::uses('PagingRequest', 'Lib/Paging');
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
    const EXTEND_LIKE = "ext:circle_post:like";
    const EXTEND_SAVED = "ext:circle_post:saved";

    const DEFAULT_COMMENT_COUNT = 3;

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
        $options = $this->createSearchCondition($pagingRequest->getConditions(true));

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions']['AND'][] = $pagingRequest->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->find('all', $options);

        //Remove 'Post' from array
        return Hash::extract($result, '{n}.Post');
    }

    protected function countData(array $conditions): int
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
            /** @var CommentPagingService $CommentPagingService */
            $CommentPagingService = ClassRegistry::init('CommentPagingService');

            foreach ($resultArray as &$result) {
                $commentSearchCondition = [
                    'post_id' => Hash::extract($result, 'Post.id')
                ];
                $order = [
                    'id' => 'asc'
                ];

                $cursor = new PagingRequest($commentSearchCondition, [], $order);

                $comments = $CommentPagingService->getDataWithPaging($cursor, self::DEFAULT_COMMENT_COUNT,
                    CommentPagingService::EXTEND_ALL);

                $result['comments'] = $comments;
            }
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_LIKE, $options)) {
            $userId = $conditions['user_id'];
            /** @var PostLikeDataExtender $PostLikeDataExtender */
            $PostLikeDataExtender = ClassRegistry::init('PostLikeDataExtender');
            $PostLikeDataExtender->setUserId($userId);
            $resultArray = $PostLikeDataExtender->extend($resultArray, "{n}.id", "post_id");
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_SAVED, $options)) {
            $userId = $conditions['user_id'];
            /** @var PostSavedDataExtender $PostSavedDataExtender */
            $PostSavedDataExtender = ClassRegistry::init('PostSavedDataExtender');
            $PostSavedDataExtender->setUserId($userId);
            $resultArray = $PostSavedDataExtender->extend($resultArray, "{n}.id", "post_id");
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
        $circleId = Hash::get($conditions, 'res_id');

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

    protected function getEndPointerValue($lastElement)
    {
        return ['id', "<", $lastElement['id']];
    }

    protected function getStartPointerValue($firstElement)
    {
        return ['id', ">", $firstElement['id']];
    }

}