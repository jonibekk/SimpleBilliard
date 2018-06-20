<?php
App::uses('AppModel', 'Model');

/**
 * CommentLike Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */
class CommentLike extends AppModel
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
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
        'comment_id' => ['numeric' => ['rule' => ['numeric'], 'allowEmpty' => false],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Comment' => [
            "counterCache" => true,
            'counterScope' => ['CommentLike.del_flg' => false]
        ],
        'User',
        'Team',
    ];

    public function changeLike($comment_id)
    {
        $res = [
            'created' => false,
            'error'   => false,
            'count'   => 0
        ];

        $exists = $this->find('first', ['conditions' => ['comment_id' => $comment_id, 'user_id' => $this->my_uid]]);
        if (isset($exists['CommentLike']['id'])) {
            $this->delete($exists['CommentLike']['id']);
            $this->updateCounterCache(['comment_id' => $exists['CommentLike']['id']]);
        } else {
            $data = [
                'user_id'    => $this->my_uid,
                'team_id'    => $this->current_team_id,
                'comment_id' => $comment_id
            ];
            $this->create();
            try {
                if (!$this->save($data)) {
                    $res['error'] = true;
                }
            } catch (PDOException $e) {
                // comment_id と user_id が重複したデータを登録しようとした場合
                $res['error'] = true;
            }
            $res['created'] = true;
        }
        $post = $this->Comment->read('comment_like_count', $comment_id);
        if (isset($post['Comment']['comment_like_count'])) {
            $res['count'] = $post['Comment']['comment_like_count'];
        }
        return $res;
    }

    public function getLikedUsers($comment_id)
    {
        $options = [
            'conditions' => [
                'CommentLike.comment_id' => $comment_id,
                'CommentLike.team_id'    => $this->current_team_id,
            ],
            'order'      => [
                'CommentLike.created' => 'desc'
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
        $params = array_merge(
            [
                'user_id' => null,
                'start'   => null,
                'end'     => null,
            ], $params);

        $options = [
            'conditions' => [
                'CommentLike.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['CommentLike.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["CommentLike.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["CommentLike.created <="] = $params['end'];
        }

        return $this->find('count', $options);
    }

    /**
     * コメントをしたユニークユーザーのリストを返す
     *
     * @param array $params
     *
     * @return array
     */
    public function getUniqueUserList(array $params = [])
    {
        $params = array_merge(
            [
                'user_id' => null,
                'start'   => null,
                'end'     => null,
            ], $params);

        $options = [
            'fields'     => [
                'CommentLike.user_id',
                'CommentLike.user_id', // key, value 両方 user_id にする
            ],
            'conditions' => [
                'CommentLike.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['CommentLike.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["CommentLike.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["CommentLike.created <="] = $params['end'];
        }
        return $this->find('list', $options);
    }

    /**
     * Add a like to a comment
     *
     * @param int $commentId
     * @param int $userId
     * @param int $teamId
     *
     * @throws Exception
     * @return bool True on successful addition
     */
    public function addCommentLike(int $commentId, int $userId, int $teamId): bool
    {
        $newData = [
            'comment_id' => $commentId,
            'user_id'    => $userId,
            'team_id'    => $teamId
        ];

        $condition['conditions'] = $newData;

        if (empty($this->find('first', $condition))) {
            try {
                $this->create();
                $this->save($newData);
            } catch (Exception $e) {
                throw $e;
            }
            $this->updateCommentLikeCount($commentId);
            return true;
        }

        return false;
    }

    /**
     * Remove like from a comment
     *
     * @param int $commentId
     * @param int $userId
     * @param int $teamId
     *
     * @return bool True on successful removal
     */
    public function removeCommentLike(int $commentId, int $userId, int $teamId): bool
    {
        $condition = [
            'conditions' => [
                'comment_id' => $commentId,
                'user_id'    => $userId,
                'team_id'    => $teamId
            ]
        ];

        $existing = $this->find('first', $condition);

        if (empty($existing)) {
            return false;
        }

        $this->delete($existing['CommentLike']['id']);
        $this->updateCommentLikeCount($commentId);

        return true;

    }

    /**
     * Update the count like in a comment
     *
     * @param int $commentId
     *
     * @return int Updated like count
     */
    public function updateCommentLikeCount(int $commentId): int
    {
        $condition = [
            'conditions' => [
                'comment_id' => $commentId
            ]
        ];

        $count = (int)$this->find('count', $condition);

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $Comment->updateAll(['Comment.comment_like_count' => $count], ['Comment.id' => $commentId]);

        return $count;
    }
}
