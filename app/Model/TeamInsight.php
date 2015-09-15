<?php
App::uses('AppModel', 'Model');

/**
 * TeamInsight Model
 *
 * @property Team $Team
 */
class TeamInsight extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_count' => [
            'numeric' => [
                'rule' => ['numeric'],
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
        'Team',
    ];
}
