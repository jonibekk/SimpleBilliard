<?php
App::uses('AppModel', 'Model');

/**
 * LocalName Model
 *
 * @property User $User
 */
class LocalName extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'    => [
            'uuid' => [
                'rule' => ['uuid'],
            ],
        ],
        'language'   => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'first_name' => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'last_name'  => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'del_flg'    => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User'
    ];
}
