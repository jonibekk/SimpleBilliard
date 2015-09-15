<?php
App::uses('AppModel', 'Model');

/**
 * AccessUser Model
 *
 * @property Team $Team
 * @property User $User
 */
class AccessUser extends AppModel
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
        'User',
    ];
}
