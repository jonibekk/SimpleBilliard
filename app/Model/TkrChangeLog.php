<?php
App::uses('AppModel', 'Model');

/**
 * TkrChangeLog Model
 *
 * @property Team      $Team
 * @property Goal      $Goal
 * @property KeyResult $KeyResult
 * @property User      $User
 */
class TkrChangeLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'data'    => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
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
        'Goal',
        'KeyResult',
        'User',
    ];
}
