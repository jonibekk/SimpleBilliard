<?php
App::uses('AppModel', 'Model');

/**
 * KrValuesDailyLog Model
 */
class KrValuesDailyLog extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'current_value' => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'start_value'   => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'target_value'  => [
            'decimal' => [
                'rule' => ['decimal'],
            ],
        ],
        'priority'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'       => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];
}
