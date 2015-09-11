<?php
App::uses('AppModel', 'Model');

/**
 * TeamUserRanking Model
 *
 * @property Team $Team
 * @property User $User
 */
class TeamUserRanking extends AppModel
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
