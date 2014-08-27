<?php
App::uses('AppModel', 'Model');

/**
 * Goal Model
 *
 * @property User         $User
 * @property Team         $Team
 * @property GoalCategory $GoalCategory
 * @property Post         $Post
 */
class Goal extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'goal';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'valued_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'evaluate_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'status'       => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'priority'     => [
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
        'Team',
        'GoalCategory',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Post'
    ];

}
