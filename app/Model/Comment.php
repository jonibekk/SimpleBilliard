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
    public $validate = array(
        'post_id'            => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'user_id'            => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'            => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'comment_like_count' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'comment_read_count' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'del_flg'            => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

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
