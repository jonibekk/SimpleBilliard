<?php
App::uses('AppModel', 'Model');

/**
 * RecoveryCode Model
 *
 * @property User $User
 */
class RecoveryCode extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'code'          => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'available_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'       => [
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
