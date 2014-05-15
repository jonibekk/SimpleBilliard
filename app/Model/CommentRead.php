<?php
App::uses('AppModel', 'Model');

/**
 * CommentRead Model
 *
 * @property Comment $Comment
 * @property User    $User
 * @property Team    $Team
 */
class CommentRead extends AppModel
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

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Comment',
        'User',
        'Team'
    ];
}
