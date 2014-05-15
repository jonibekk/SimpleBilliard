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
    public $belongsTo = [
        'Image',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Badge',
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
        'GivenBadge',
        'Group',
        'Invite',
        'JobCategory',
        'Notification',
        'PostLike',
        'PostMention',
        'PostRead',
        'Post',
        'TeamMember',
        'Thread',
    ];

}
