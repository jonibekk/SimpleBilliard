<?php
App::uses('AppModel', 'Model');

/**
 * GoalProgressDailyLog Model
 */
class GoalProgressDailyLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'progress'    => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'target_date' => [
            'date' => [
                'rule' => ['date'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
    ];
}
