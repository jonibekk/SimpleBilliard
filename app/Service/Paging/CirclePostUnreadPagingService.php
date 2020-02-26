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
        $unreadCirclePostId = $pagingRequest->getCondition('unread_id');
        $circleNotificationFlg = boolval($pagingRequest->getCondition('noti') ?? true);

        $teamId = $pagingRequest->getCurrentTeamId();
        $userId = $pagingRequest->getCurrentUserId();

        $posts = $this->getPostsInCircleNotifyFromUnreadId(
            $teamId,
            $userId,
            $circleNotificationFlg,
            $unreadCirclePostId,
            self::POST_LIMIT
        );

        $pagingRequestForCursor = new PagingRequest();

        if (empty($posts)) {
            if ($circleNotificationFlg) {
                $pagingRequestForCursor->addCondition([
                    'unread_id' => null,
                    'noti' => false,
                ]);
                // There is no post of notification on circle's.
                $hasUnreadPostNotificationOff = !empty($this->getPostsInCircleNotifyFromUnreadId(
                    $teamId,
                    $userId,
                    false,
                    $unreadCirclePostId,
                    1
                ));
                $cursor = '';
                $data = [
                    [
                        'type' => \Goalous\Enum\FeedContent\FeedContent::FEED_ALL_CAUGHT_UP,
                    ]
                ];
                if ($hasUnreadPostNotificationOff) {
                    $cursor = $pagingRequestForCursor->returnCursor();
                    $data = [];
                }
                return [
                    'data'   => $data,
                    'cursor' => $cursor,
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
            $posts = $CirclePostExtender->extendMulti($posts, $userId, $teamId, [CirclePostExtender::EXTEND_ALL]);
            return [
                'data'   => $this->addTypeToPostArray($posts),
                'cursor' => $pagingRequestForCursor->returnCursor(),
                'count'  => -1
            ];
        }

        if ($circleNotificationFlg) {
            // Returning notify circle's unread post

            // Reading off notification circle post from next cursor
            $pagingRequestForCursor->addCondition([
                'unread_id' => null,
                'noti' => false,
            ]);

            $posts = $CirclePostExtender->extendMulti($posts, $userId, $teamId, [CirclePostExtender::EXTEND_ALL]);
            return [
                'data'   => array_merge(
                    $this->addTypeToPostArray($posts),
                    [
                        [
                            'type' => \Goalous\Enum\FeedContent\FeedContent::FEED_ALL_CAUGHT_UP,
                        ]
                    ]
                ),
                'cursor' => $pagingRequestForCursor->returnCursor(),
                'count'  => -1
            ];
        }

        // Returning NOT notify circle's unread post
        $posts = $CirclePostExtender->extendMulti($posts, $userId, $teamId, [CirclePostExtender::EXTEND_ALL]);
        return [
            'data'   => $this->addTypeToPostArray($posts),
            'cursor' => '',
            'count'  => -1
        ];
    }

    private function addTypeToPostArray(array $posts): array
    {
        return array_map(function (array $post) {
            return [
                'type' => \Goalous\Enum\FeedContent\FeedContent::CIRCLE_POST,
                'data' => $post,
            ];
        }, $posts);
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
        $unreadCirclePostId = null,
        int $limit = self::POST_LIMIT
    ): array
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $circleIds = $CircleMember->getJoinedCircleIds($teamId, $userId, $flagCircleNotification);

        $option = [
            'conditions' => [
                'UnreadCirclePost.team_id' => $teamId,
                'UnreadCirclePost.user_id' => $userId,
                'UnreadCirclePost.circle_id' => array_values($circleIds),
            ],
            'limit'      => $limit,
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
}
