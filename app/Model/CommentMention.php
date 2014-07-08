<?php
App::uses('AppModel', 'Model');

/**
 * CommentMention Model
 *
 * @property Post $Post
 * @property User $User
 * @property Team $Team
 */
class CommentMention extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean']]],
    ];

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
}
