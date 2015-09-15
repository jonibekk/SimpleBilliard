<?php
App::uses('AppModel', 'Model');

/**
 * CircleInsight Model
 *
 * @property Team   $Team
 * @property Circle $Circle
 */
class CircleInsight extends AppModel
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
        'Circle',
    ];
}
