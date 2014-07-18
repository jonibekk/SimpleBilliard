<?php
App::uses('AppModel', 'Model');

/**
 * Comment Model
 *
 * @property Post        $Post
 * @property User        $User
 * @property Team        $Team
 * @property CommentLike $CommentLike
 * @property CommentRead $CommentRead
 */
class Comment extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'comment_like_count' => ['numeric' => ['rule' => ['numeric']]],
        'comment_read_count' => ['numeric' => ['rule' => ['numeric']]],
        'del_flg'            => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Post' => [
            "counterCache" => true,
        ],
        'User',
        'Team',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CommentLike',
        'CommentRead',
        'MyCommentLike' => [
            'className' => 'CommentLike',
            'fields'    => ['id']
        ]
    ];

    /**
     * コメント
     *
     * @param      $postData
     * @param null $uid
     * @param null $team_id
     *
     * @internal param int $type
     * @return bool|mixed
     */
    public function add($postData, $uid = null, $team_id = null)
    {
        if (!isset($postData['Comment']) || empty($postData['Comment'])) {
            return false;
        }
        $this->setUidAndTeamId($uid, $team_id);
        $postData['Comment']['user_id'] = $this->uid;
        $postData['Comment']['team_id'] = $this->team_id;
        $res = $this->save($postData);
        //投稿データのmodifiedを更新
        $this->Post->id = $postData['Comment']['post_id'];
        $this->Post->saveField('modified', time());

        return $res;
    }

    public function getPostsComment($post_id, $cut_num = 0)
    {
        //既読済みに
        $this->CommentRead->red($post_id);
        $options = [
            'conditions' => [
                'Comment.post_id' => $post_id,
                'Comment.team_id' => $this->current_team_id,
            ],
            'order'      => [
                'Comment.created' => 'asc'
            ],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ],
            ],
        ];
        $res = $this->find('all', $options);
        //最後のコメントから指定件数を削除
        if ($cut_num > 0) {
            for ($i = 0; $i < $cut_num; $i++) {
                array_pop($res);
            }
        }
        return $res;
    }
}
