<?php
App::uses('AppModel', 'Model');

/**
 * EvaluateScore Model
 *
 * @property Team       $Team
 * @property Evaluation $Evaluation
 */
class EvaluateScore extends AppModel
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
        'Team'
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Evaluation'
    ];

}
