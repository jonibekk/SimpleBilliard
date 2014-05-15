<?php
App::uses('AppModel', 'Model');

/**
 * CommentLike Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */
class CommentLike extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'comment_id' => ['uuid' => ['rule' => ['uuid']]],
        'user_id'    => ['uuid' => ['rule' => ['uuid']]],
        'team_id'    => ['uuid' => ['rule' => ['uuid']]],
        'del_flg'    => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Comment',
        'User',
        'Team',
    ];
}
