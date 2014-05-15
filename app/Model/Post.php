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
class Post extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'user_id'         => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'team_id'         => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'comment_count'   => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'post_like_count' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'post_read_count' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'public_flg'      => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'important_flg'   => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'del_flg'         => array(
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

}
