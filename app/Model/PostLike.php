<?php
App::uses('AppModel', 'Model');

/**
 * PostLike Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class PostLike extends AppModel
{
    public $actsAs = [
        'SoftDeletable' => [
            'delete' => false,
        ],
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'post_id' => ['numeric' => ['rule' => ['numeric'], 'allowEmpty' => false],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
            'counterScope' => ['PostLike.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    public function changeLike($post_id)
    {
        $res = [
            'created'  => false,
            'error'    => false,
            'count'    => 0,
            'is_liked' => false,
        ];

        $exists = $this->find('first', ['conditions' => ['post_id' => $post_id, 'user_id' => $this->my_uid]]);
        if (isset($exists['PostLike']['id'])) {
            $this->delete($exists['PostLike']['id']);
            $this->updateCounterCache(['post_id' => $exists['PostLike']['id']]);
        } else {
            $data = [
                'user_id' => $this->my_uid,
                'team_id' => $this->current_team_id,
                'post_id' => $post_id
            ];
            $this->create();
            try {
                if (!$this->save($data)) {
                    $res['error'] = true;
                }
            } catch (PDOException $e) {
                // post_id と user_id が重複したデータを登録しようとした場合
                $res['error'] = true;
            }
            $res['created'] = true;
            $res['is_liked'] = true;
        }
        $post = $this->Post->read('post_like_count', $post_id);
        if (isset($post['Post']['post_like_count'])) {
            $res['count'] = $post['Post']['post_like_count'];
        }
        return $res;
    }

    public function getLikedUsers($post_id)
    {
        $options = [
            'conditions' => [
                'PostLike.post_id' => $post_id,
                'PostLike.team_id' => $this->current_team_id,
            ],
            'order'      => [
                'PostLike.created' => 'desc'
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ],
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * カウント数を返す
     *
     * @param array $params
     *
     * @return int
     */
    public function getCount(array $params = [])
    {
        $params = array_merge([
            'user_id' => null,
            'start'   => null,
            'end'     => null,
        ], $params);

        $options = [
            'conditions' => [
                'PostLike.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['PostLike.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["PostLike.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostLike.created <="] = $params['end'];
        }

        return $this->find('count', $options);
    }

    /**
     * いいねをしたユニークユーザーのリストを返す
     *
     * @param array $params
     *
     * @return array
     */
    public function getUniqueUserList(array $params = [])
    {
        $params = array_merge([
            'user_id' => null,
            'start'   => null,
            'end'     => null,
        ], $params);

        $options = [
            'fields'     => [
                'PostLike.user_id',
                'PostLike.user_id', // key, value 両方 user_id にする
            ],
            'conditions' => [
                'PostLike.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['PostLike.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["PostLike.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostLike.created <="] = $params['end'];
        }
        return $this->find('list', $options);
    }

    /**
     * 投稿いいね数ランキングを返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getRanking($params = [])
    {
        $params = array_merge([
            'limit'           => null,
            'start'           => null,
            'end'             => null,
            'post_type'       => null,
            'post_user_id'    => null,
            'share_circle_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'PostLike.post_id',
                'COUNT(DISTINCT PostLike.id) as cnt',
            ],
            'conditions' => [
                'PostLike.team_id' => $this->current_team_id,
            ],
            'group'      => ['PostLike.post_id'],
            'order'      => ['cnt' => 'DESC'],
            'limit'      => $params['limit'],
            'contain'    => ['Post'],
            'joins'      => [],
        ];
        if ($params['post_type'] !== null) {
            $options['conditions']["Post.type"] = $params['post_type'];
        }
        if ($params['post_user_id'] !== null) {
            $options['conditions']["Post.user_id"] = $params['post_user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["PostLike.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["PostLike.created <="] = $params['end'];
        }
        if ($params['share_circle_id'] !== null) {
            $options['joins'][] = [
                'type'       => 'INNER',
                'table'      => 'post_share_circles',
                'alias'      => 'PostShareCircle',
                'conditions' => [
                    'PostLike.post_id = PostShareCircle.post_id',
                    'PostShareCircle.team_id'   => $this->current_team_id,
                    'PostShareCircle.circle_id' => $params['share_circle_id'],
                    'PostShareCircle.del_flg = 0',
                ],
            ];
        }
        $rows = $this->find('all', $options);

        $ranking = [];
        foreach ($rows as $v) {
            $ranking[$v['PostLike']['post_id']] = $v[0]['cnt'];
        }
        return $ranking;
    }

    /**
     * Add a like to a post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who added the like
     * @param int $teamId The team ID where this happens
     *
     * @throws Exception
     * @return bool True for successful addition
     */
    public function addPostLike(int $postId, int $userId, int $teamId): bool
    {
        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId
            ]
        ];

        //Check whether like is already exist from the user
        if (empty($this->find('first', $condition))) {
            try {
                $this->create();
                $newData = [
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'team_id' => $teamId
                ];
                $this->save($newData);
            } catch (Exception $e) {
                throw $e;
            }
            $this->updateLikeCount($postId);
            return true;
        }

        return false;
    }

    /**
     * Delete a like from a post
     *
     * @param int $postId Target post's ID
     * @param int $userId User ID who removed the like
     * @param int $teamId The team ID where this happens
     *
     * @return bool True for successful removal
     */
    public function deletePostLike(int $postId, int $userId, int $teamId): bool
    {
        $condition = [
            'conditions' => [
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId
            ]
        ];

        $existing = $this->find('first', $condition);

        if (empty($existing)) {
            return false;
        }

        $this->delete($existing['PostLike']['id']);
        $this->updateLikeCount($postId);

        return true;

    }

    /**
     * Update the count like in a post
     *
     * @param int $postId
     *
     * @return int
     */
    public function updateLikeCount(int $postId): int
    {
        $condition = [
            'conditions' => [
                'post_id' => $postId
            ]
        ];

        $count = (int)$this->find('count', $condition);

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $Post->updateAll(['Post.post_like_count' => $count], ['Post.id' => $postId]);

        return $count;
    }
}
