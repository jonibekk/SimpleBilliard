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
    public $belongsTo = array(
        'User' => array(
            'className'  => 'User',
            'foreignKey' => 'user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        'Team' => array(
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ),
        //        TODO ゴールのモデルを追加した後にコメントアウト解除
        //        'Goal' => array(
        //            'className'  => 'Goal',
        //            'foreignKey' => 'goal_id',
        //            'conditions' => '',
        //            'fields'     => '',
        //            'order'      => ''
        //        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'CommentMention' => array(
            'className'    => 'CommentMention',
            'foreignKey'   => 'post_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        ),
        'Comment'        => array(
            'className'    => 'Comment',
            'foreignKey'   => 'post_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        ),
        'GivenBadge'     => array(
            'className'    => 'GivenBadge',
            'foreignKey'   => 'post_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        ),
        'PostLike'       => array(
            'className'    => 'PostLike',
            'foreignKey'   => 'post_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        ),
        'PostMention'    => array(
            'className'    => 'PostMention',
            'foreignKey'   => 'post_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        ),
        'PostRead'       => array(
            'className'    => 'PostRead',
            'foreignKey'   => 'post_id',
            'dependent'    => false,
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'limit'        => '',
            'offset'       => '',
            'exclusive'    => '',
            'finderQuery'  => '',
            'counterQuery' => ''
        )
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Image' => array(
            'className'             => 'Image',
            'joinTable'             => 'posts_images',
            'foreignKey'            => 'post_id',
            'associationForeignKey' => 'image_id',
            'unique'                => 'keepExisting',
            'conditions'            => '',
            'fields'                => '',
            'order'                 => '',
            'limit'                 => '',
            'offset'                => '',
            'finderQuery'           => '',
        )
    );

}
