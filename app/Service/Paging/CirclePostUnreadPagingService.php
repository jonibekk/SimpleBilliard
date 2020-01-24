<?php
App::import('Lib/Paging', 'BasePagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::uses('Comment', 'Model');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('Post', 'Model');
App::import('Lib/DataExtender', 'CirclePostExtender');
App::uses('UnreadCirclePost', 'Model');

/**
 * This paging service class not extending BasePagingService
 * Not simply returning resource paging
 *
 * Class CirclePostUnreadPagingService
 */
class CirclePostUnreadPagingService
{
    const POST_LIMIT  = 7;

    public function getDataWithPaging(
        PagingRequest $pagingRequest,
        $limit = BasePagingController::DEFAULT_PAGE_LIMIT,
        $extendFlags = []
    ): array
    {
        $UnreadCirclePostId = $pagingRequest->getCondition('unread_id');
        $circleNotificationFlg = boolval($pagingRequest->getCondition('noti') ?? true);

        $teamId = $pagingRequest->getCurrentTeamId();
        $userId = $pagingRequest->getCurrentUserId();

        $posts = $this->getPostsInCircleNotifyFromUnreadId(
            $teamId,
            $userId,
            $circleNotificationFlg,
            $UnreadCirclePostId
        );

        $pagingRequestForCursor = new PagingRequest();

        if (empty($posts)) {
            if ($circleNotificationFlg) {
                $pagingRequestForCursor->addCondition([
                    'unread_id' => null,
                    'noti' => false,
                ]);
                return [
                    'data'   => [
                        // TODO: return all caught up data
                        [
                            'type' => 1,
                        ]
                    ],
                    'cursor' => $pagingRequestForCursor->returnCursor(),
                    'count'  => 0
                ];
            }
            return [
                'data'   => [],
                'cursor' => '',
                'count'  => 0
            ];
        }

        /** @var CirclePostExtender $CirclePostExtender */
        $CirclePostExtender = ClassRegistry::init('CirclePostExtender');

        $lastPost = $posts[count($posts) - 1];

        // If succeed to get full limit count posts, just return that
        if (count($posts) === self::POST_LIMIT) {
            $pagingRequestForCursor->addCondition([
                'unread_id' => $this->getUnreadCirclePostId($teamId, $userId, $lastPost['id']),
                'noti' => $circleNotificationFlg,
            ]);
            return [
                'data'   => $CirclePostExtender->extendMulti($posts, $userId, $teamId, [CirclePostExtender::EXTEND_ALL]),
                'cursor' => $pagingRequestForCursor->returnCursor(),
                'count'  => -1
            ];
        } else if ($circleNotificationFlg) {
            // Reading off notification circle post from next cursor
            $pagingRequestForCursor->addCondition([
                'unread_id' => null,
                'noti' => false,
            ]);
            $posts = $CirclePostExtender->extendMulti($posts, $userId, $teamId, [CirclePostExtender::EXTEND_ALL]);
            // TODO: return all caught up data
            array_push($posts, [
                'type' => 1,
            ]);
            return [
                'data'   => $posts,
                'cursor' => $pagingRequestForCursor->returnCursor(),
                'count'  => -1
            ];
        }

        return [
            'data'   => [],
            'cursor' => '',
            'count'  => -1
        ];
    }

    private function getUnreadCirclePostId($teamId, $userId, $postId): int
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId,
                'user_id' => $userId,
                'post_id' => $postId,
            ],
        ];

        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');
        $r = $UnreadCirclePost->find('first', $option);

        return $r['UnreadCirclePost']['id'];
    }

    private function getPostsInCircleNotifyFromUnreadId(
        int $teamId,
        int $userId,
        bool $flagCircleNotification,
        $unreadCirclePostId
    ): array
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleIds = $CircleMember->getJoinedCircleIds($teamId, $userId, $flagCircleNotification);

        $option = [
            'conditions' => [
                'UnreadCirclePost.team_id' => $teamId,
                'UnreadCirclePost.user_id' => $userId,
                'UnreadCirclePost.circle_id' => $circleIds,
            ],
            'limit'      => self::POST_LIMIT,
            'order'      => [
                'UnreadCirclePost.id' => 'desc'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'cache_unread_circle_posts',
                    'alias'      => 'UnreadCirclePost',
                    'conditions' => [
                        'UnreadCirclePost.post_id = Post.id',
                    ],
                ],
            ],
        ];

        if (!empty($unreadCirclePostId)) {
            $option['conditions'][] = 'UnreadCirclePost.id < ' . $unreadCirclePostId;
        }

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = $Post->find('all', $option);
        return Hash::extract($posts, '{n}.Post');
    }

    protected function readData(PagingRequest $pagingRequest, int $limit): array
    {
//        $options = $this->createSearchCondition($pagingRequest);
//
//        $options['limit'] = $limit;
//        $options['order'] = $pagingRequest->getOrders();
//        $options['conditions'][] = $pagingRequest->getPointersAsQueryOption();
//        $options['conversion'] = true;
//
//        /** @var Post $Post */
//        $Post = ClassRegistry::init('Post');
//
//        $result = $Post->useType()->find('all', $options);
//
//        //Remove 'Post' from array
//        return Hash::extract($result, '{n}.Post');

        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');
        $unreadCirclePosts = $UnreadCirclePost->getPostsCacheByTeamIdAndUserId($pagingRequest->getCurrentTeamId(), $pagingRequest->getCurrentUserId());

        //GoalousLog::info('$unreadCirclePosts', $unreadCirclePosts);

        $option = [
            'conditions' => [
                'UnreadCirclePost.team_id' => $pagingRequest->getCurrentTeamId(),
                'UnreadCirclePost.user_id' => $pagingRequest->getCurrentUserId(),
            ],
            'limit'      => 8,
            'order'      => [
                'UnreadCirclePost.id' => 'desc'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'cache_unread_circle_posts',
                    'alias'      => 'UnreadCirclePost',
                    'conditions' => [
                        'UnreadCirclePost.post_id = Post.id',
                    ],
                ],
            ],
        ];

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $posts = $Post->find('all', $option);
        GoalousLog::info('$posts', $posts);
//        $postIds = Hash::extract($unreadCirclePosts, '{n}.UnreadCirclePost.post_id');

        //GoalousLog::info('$postIds', $postIds);

        $pagingRequest->returnCursor();

        return [];
    }

    protected function countData(PagingRequest $request): int
    {
        return 0;
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
