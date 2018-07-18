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
        $options = $this->createSearchCondition($pagingRequest);

        $options['limit'] = $limit;
        $options['order'] = $pagingRequest->getOrders();
        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();
        $options['conversion'] = true;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $result = $Post->find('all', $options);

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

    protected function extendPagingResult(array &$resultArray, PagingRequest $request, array $options = [])
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
                $cursor = new PagingRequest();
                $cursor->addResource('res_id', Hash::get($result, 'id'));

                $comments = $CommentPagingService->getDataWithPaging($cursor, self::DEFAULT_COMMENT_COUNT,
                    CommentPagingService::EXTEND_ALL);

                $result['comments'] = $comments;
            }
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_LIKE, $options)) {
            $userId = $request->getCurrentUserId();
            if (empty($userId)) {
                GoalousLog::error("Missing resource ID for extending like in Post");
                throw new InvalidArgumentException("Missing resource ID for extending like in Post");
            }
            /** @var PostLikeDataExtender $PostLikeDataExtender */
            $PostLikeDataExtender = ClassRegistry::init('PostLikeDataExtender');
            $PostLikeDataExtender->setUserId($userId);
            $resultArray = $PostLikeDataExtender->extend($resultArray, "{n}.id", "post_id");
        }
        if (in_array(self::EXTEND_ALL, $options) || in_array(self::EXTEND_SAVED, $options)) {
            $userId = $request->getCurrentUserId();
            if (empty($userId)) {
                GoalousLog::error("Missing resource ID for extending saved in Post");
                throw new InvalidArgumentException("Missing resource ID for extending saved in Post");
            }
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
        if (empty($teamId)) {
            GoalousLog::error("Missing team ID for post paging", $conditions);
            throw new InvalidArgumentException("Missing team ID");
        }

        $options = [
            'conditions' => [
                'Post.del_flg' => false,
                'Post.team_id' => $teamId,
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

    protected function createPointer(
        array $lastElement,
        array $headNextElement = [],
        PagingRequest $pagingRequest = null
    ): PointerTree {
        return new PointerTree(['id', "<", $lastElement['id']]);
    }
}