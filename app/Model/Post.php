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
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'         => ['uuid' => ['rule' => ['uuid'],],],
        'team_id'         => ['uuid' => ['rule' => ['uuid'],],],
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

}
