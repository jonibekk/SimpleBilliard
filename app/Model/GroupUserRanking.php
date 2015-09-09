<?php
App::uses('AppModel', 'Model');

/**
 * GroupUserRanking Model
 *
 * @property Team  $Team
 * @property Group $Group
 * @property User  $User
 */
class GroupUserRanking extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
        'Group',
        'User',
    ];
}
