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
    public $validate = [
        'name'               => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'type'               => ['numeric' => ['rule' => ['numeric'],],],
        'domain_limited_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'start_term_month'   => ['numeric' => ['rule' => ['numeric'],],],
        'border_months'      => ['numeric' => ['rule' => ['numeric'],],],
        'del_flg'            => ['boolean' => ['rule' => ['boolean'],],],
    ];

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
