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
    const STATUS_EVAL_IN_PROGRESS = 0;
    const STATUS_EVAL_FINISHED = 1;

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
        $start_date = $this->Team->getCurrentTermStartDate();
        $end_date = $this->Team->getCurrentTermEndDate();
        $options = [
            'conditions' => [
                'start_date >=' => $start_date,
                'end_date <='   => $end_date,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

    function getLatestTermId()
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ],
            'order'      => ['id' => 'desc']
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

    function getPreviousTermId()
    {
        $start_end = $this->Team->getBeforeTermStartEnd();
        $start_date = $start_end['start'];
        $end_date = $start_end['end'];
        $options = [
            'conditions' => [
                'start_date >=' => $start_date,
                'end_date <='   => $end_date,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

    function saveTerm()
    {
        $data = [
            'team_id'    => $this->current_team_id,
            'start_date' => $this->Team->getCurrentTermStartDate(),
            'end_date'   => $this->Team->getCurrentTermEndDate() - 1,
        ];
        $res = $this->save($data);
        return $res;
    }

    function checkTermAvailable($id)
    {
        $options = [
            'conditions' => [
                'id'      => $id,
                'team_id' => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        return (empty($res)) ? false : true;
    }

}
