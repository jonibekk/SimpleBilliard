<?php
App::uses('AppModel', 'Model');

/**
 * Post Model
 *
 * @property User           $User
 * @property Team           $Team
 * @property CommentMention $CommentMention
 * @property Comment        $Comment
 * @property GivenBadge     $GivenBadge
 * @property PostLike       $PostLike
 * @property PostMention    $PostMention
 * @property PostRead       $PostRead
 * @property Image          $Image
 */

/** @noinspection PhpUndefinedClassInspection */
class Post extends AppModel
{
    /**
     * 投稿タイプ
     */
    const TYPE_NORMAL = 1;
    const TYPE_ACTION = 2;
    const TYPE_BADGE = 3;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'comment_count'   => ['numeric' => ['rule' => ['numeric'],],],
        'post_like_count' => ['numeric' => ['rule' => ['numeric'],],],
        'post_read_count' => ['numeric' => ['rule' => ['numeric'],],],
        'public_flg'      => ['boolean' => ['rule' => ['boolean'],],],
        'important_flg'   => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'         => ['boolean' => ['rule' => ['boolean'],],],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        //TODO ゴールのモデルを追加した後にコメントアウト解除
        //'Goal',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CommentMention',
        'Comment',
        'GivenBadge',
        'PostLike',
        'PostMention',
        'PostRead',
    ];

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = [
        'Image',
    ];

    /**
     * 投稿
     *
     * @param      $postData
     * @param int  $type
     * @param null $uid
     * @param null $team_id
     *
     * @return bool|mixed
     */
    public function add($postData, $type = self::TYPE_NORMAL, $uid = null, $team_id = null)
    {
        if (!isset($postData['Post']) || empty($postData['Post'])) {
            return false;
        }
        $this->setUidAndTeamId($uid, $team_id);
        $postData['Post']['user_id'] = $this->uid;
        $postData['Post']['team_id'] = $this->team_id;
        $postData['Post']['type'] = $type;
        $res = $this->save($postData);
        return $res;
    }

    public function getPaginateOption()
    {
        $options = [
            'conditions' => [
                'Post.team_id' => $this->current_team_id
            ],
            'limit' => 20,
            'order'      => [
                'Post.modified' => 'desc'
            ],
            'contain'    => [
                'User'
            ]
        ];
        return $options;
    }

    /**
     * ページネータのカウンタ
     *
     * @param null  $conditions
     * @param int   $recursive
     * @param array $extra
     *
     * @return array
     */
    function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
        //extraからcontainを除去
        unset($extra['contain']);
        $params = array('conditions' => $conditions);
        $this->recursive = $recursive;
        $count = $this->find('count', array_merge($params, $extra));
        return $count;
    }
}
