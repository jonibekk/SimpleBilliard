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
        'Post',
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
    ];

}
