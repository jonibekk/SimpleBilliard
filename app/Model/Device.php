<?php
App::uses('AppModel', 'Model');

/**
 * Device Model
 *
 * @property User $User
 */
class Device extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'device_token' => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'os_type'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'      => [
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
        'User',
    ];
}
