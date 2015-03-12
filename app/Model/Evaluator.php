<?php
App::uses('AppModel', 'Model');

/**
 * Evaluator Model
 *
 * @property User         $EvaluateeUser
 * @property User         $EvaluatorUser
 * @property Team         $Team
 * @property EvaluateTerm $EvaluateTerm
 */
class Evaluator extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'index'   => [
            'numeric' => [
                'rule' => ['numeric'],
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
        'EvaluateeUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluatee_user_id',
        ],
        'EvaluatorUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluator_user_id',
        ],
        'Team',
    ];
}
