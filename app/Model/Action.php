<?php
App::uses('AppModel', 'Model');

/**
 * Action Model
 *
 * @property Team         $Team
 * @property Goal         $Goal
 * @property KeyResult    $KeyResult
 * @property User         $User
 * @property ActionResult $ActionResult
 */
class Action extends AppModel
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
        'priority'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'repeat_type' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'mon_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'tues_flg'    => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'wed_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'thurs_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'fri_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'sat_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'sun_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'del_flg'     => [
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

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'ActionResult',
    ];

}
