<?php
App::uses('AppModel', 'Model');

/**
 * EvaluateTerm Model
 *
 * @property Team       $Team
 * @property Evaluation $Evaluation
 * @property Evaluator  $Evaluator
 */
class EvaluateTerm extends AppModel
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
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Evaluation',
        'Evaluator',
    ];

    function getMyEvaluationAllTerm()
    {

        $options = [
            'conditions' => [
                'EvaluateTerm.team_id' => $this->current_team_id,
            ],
            'order'      => ['EvaluateTerm.start_date' => 'asc'],
            'contain'    => [
                'Evaluation' => [
                    'conditions' => [
                        'Evaluation.evaluatee_user_id' => $this->my_uid,
                        'Evaluation.team_id'           => $this->current_team_id,
                    ],
                    'order'      => ['Evaluation.index' => 'asc'],
                ]
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function getCurrentTerm()
    {
        $start_date = $this->Team->getTermStartDate();
        $end_date = $this->Team->getTermEndDate();
        $options = [
            'conditions' => [
                'start_date <=' => $start_date,
                'end_date >='   => $end_date,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

}
