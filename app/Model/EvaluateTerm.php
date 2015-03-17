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

    function getCurrentTermId()
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
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

}
