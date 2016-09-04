<?php
App::uses('AppModel', 'Model');

/**
 * GoalLabel Model
 *
 * @property Team  $Team
 * @property Goal  $Goal
 * @property Label $Label
 */
class GoalLabel extends AppModel
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
    public $belongsTo = array(
        'Team',
        'Goal',
        'Label',
    );
}
