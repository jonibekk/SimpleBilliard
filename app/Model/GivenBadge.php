<?php
App::uses('AppModel', 'Model');

/**
 * GivenBadge Model
 *
 * @property User $User
 * @property User $GrantUser
 * @property Team $Team
 * @property Post $Post
 */
class GivenBadge extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => ['boolean' => ['rule' => ['boolean']]],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'GrantUser' => ['className' => 'User', 'foreignKey' => 'grant_user_id',],
        'Team',
        'Post',
    ];
}
