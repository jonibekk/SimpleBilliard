<?php
App::uses('AppModel', 'Model');

/**
 * MemberGroup Model
 *
 * @property Team  $Team
 * @property User  $User
 * @property Group $Group
 */
class MemberGroup extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index_num' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'   => [
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
        'User',
        'Group',
    ];
}
