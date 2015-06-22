<?php
App::uses('AppModel', 'Model');

/**
 * GroupVision Model
 *
 * @property User  $CreateUser
 * @property User  $ModifyUser
 * @property Team  $Team
 * @property Group $Group
 */
class GroupVision extends AppModel
{

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'name';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'name'       => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'active_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'    => [
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
        'CreateUser' => [
            'className'  => 'User',
            'foreignKey' => 'create_user_id',
        ],
        'ModifyUser' => [
            'className'  => 'User',
            'foreignKey' => 'modify_user_id',
        ],
        'Team',
        'Group',
    ];
}
