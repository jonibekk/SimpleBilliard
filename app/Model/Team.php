<?php
App::uses('AppModel', 'Model');

/**
 * Team Model
 *
 * @property Image          $Image
 * @property Badge          $Badge
 * @property CommentLike    $CommentLike
 * @property CommentMention $CommentMention
 * @property CommentRead    $CommentRead
 * @property Comment        $Comment
 * @property GivenBadge     $GivenBadge
 * @property Group          $Group
 * @property Invite         $Invite
 * @property JobCategory    $JobCategory
 * @property Notification   $Notification
 * @property PostLike       $PostLike
 * @property PostMention    $PostMention
 * @property PostRead       $PostRead
 * @property Post           $Post
 * @property TeamMember     $TeamMember
 * @property Thread         $Thread
 */
class Team extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'name'               => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'type'               => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'domain_limited_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'start_term_month'   => array(
            'numeric' => array(
                'rule' => array('numeric'),
            ),
        ),
        'border_months'      => array(
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
    public $belongsTo = array(
        'Image' => array(
            'className'  => 'Image',
            'foreignKey' => 'image_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Badge'          => array(
            'className'    => 'Badge',
            'foreignKey'   => 'team_id',
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
        'CommentLike'    => array(
            'className'    => 'CommentLike',
            'foreignKey'   => 'team_id',
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
        'CommentMention' => array(
            'className'    => 'CommentMention',
            'foreignKey'   => 'team_id',
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
        'CommentRead'    => array(
            'className'    => 'CommentRead',
            'foreignKey'   => 'team_id',
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
            'foreignKey'   => 'team_id',
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
            'foreignKey'   => 'team_id',
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
        'Group'          => array(
            'className'    => 'Group',
            'foreignKey'   => 'team_id',
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
        'Invite'         => array(
            'className'    => 'Invite',
            'foreignKey'   => 'team_id',
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
        'JobCategory'    => array(
            'className'    => 'JobCategory',
            'foreignKey'   => 'team_id',
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
        'Notification'   => array(
            'className'    => 'Notification',
            'foreignKey'   => 'team_id',
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
            'foreignKey'   => 'team_id',
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
            'foreignKey'   => 'team_id',
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
            'foreignKey'   => 'team_id',
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
        'Post'           => array(
            'className'    => 'Post',
            'foreignKey'   => 'team_id',
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
        'TeamMember'     => array(
            'className'    => 'TeamMember',
            'foreignKey'   => 'team_id',
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
        'Thread'         => array(
            'className'    => 'Thread',
            'foreignKey'   => 'team_id',
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

}
