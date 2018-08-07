<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'CommentLike');

/**
 * CommentLike Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */

use Goalous\Enum\DataType\DataType as DataType;

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

    protected $modelConversionTable = [
        'comment_id' => DataType::INT,
        'user_id'    => DataType::INT,
        'team_id'    => DataType::INT
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
     * Update the count like in a comment
     *
     * @param int $commentId
     *
     * @return int Updated like count
     */
    public function updateCommentLikeCount(int $commentId): int
    {
        $count = $this->countCommentLike($commentId);

        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $Comment->updateAll(['Comment.comment_like_count' => $count], ['Comment.id' => $commentId]);

        return $count;
    }

    /**
     * Count the number of like of a comment
     *
     * @param int $commentId
     *
     * @return int
     */
    public function countCommentLike(int $commentId): int
    {
        $condition = [
            'conditions' => [
                'comment_id' => $commentId
            ],
            'fields'     => [
                'CommentLike.id'
            ]
        ];

        return (int)$this->find('count', $condition) ?? 0;
    }
}
