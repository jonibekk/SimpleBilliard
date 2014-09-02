<?php
App::uses('AppModel', 'Model');

/**
 * KeyResultUser Model
 *
 * @property Team      $Team
 * @property KeyResult $KeyResult
 * @property User      $User
 */
class KeyResultUser extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
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
        'KeyResult',
        'User',
    ];
}
