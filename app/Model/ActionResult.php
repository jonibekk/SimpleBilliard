<?php
App::uses('AppModel', 'Model');

/**
 * ActionResult Model
 *
 * @property Team   $Team
 * @property Action $Action
 * @property User   $User
 */
class ActionResult extends AppModel
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
        'Action',
        'User',
    ];
}
