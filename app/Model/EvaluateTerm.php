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
    const STATUS_EVAL_NOT_STARTED = 0;
    const STATUS_EVAL_IN_PROGRESS = 1;
    const STATUS_EVAL_FROZEN = 2;
    const STATUS_EVAL_FINISHED = 3;

    private $previous_term = [];
    private $current_term = [];
    private $next_term = [];
    private $latest_term = [];

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

    function getTermIdByDate($start, $end)
    {
        $options = [
            'conditions' => [
                'start_date <=' => $start,
                'end_date >='   => $end,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

    function getCurrentTerm()
    {

    }

    function getCurrentTermId()
    {
        $options = [
            'conditions' => [
                'start_date <=' => REQUEST_TIMESTAMP,
                'end_date >='   => REQUEST_TIMESTAMP,
                'team_id'       => $this->current_team_id
            ]
        ];
        $res = $this->find('first', $options);
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

    function getNextTermId()
    {
        $next_term = $this->Team->getAfterTermStartEnd();
        if (empty($next_term)) {
            return;
        }
        return $this->Team->EvaluateTerm->getTermIdByDate($next_term['start'], $next_term['end'] - 1);
    }

    function getLatestTermId()
    {
        $res = $this->getLatestTerm();
        if (viaIsSet($res['EvaluateTerm']['id'])) {
            return $res['EvaluateTerm']['id'];
        }
        return null;
    }

    function getLatestTerm()
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ],
            'order'      => ['id' => 'desc']
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    function getAllTerm($order_desc = true)
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id
            ],
            'order'      => [
                'start_date' => 'asc'
            ]
        ];
        if ($order_desc) {
            $options['order']['start_date'] = 'desc';
        }
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.EvaluateTerm.id', '{n}.EvaluateTerm');
        return $res;
    }

    function getPreviousTermId()
    {
        $all_term = $this->getAllTerm();
        if (count($all_term) < 2) {
            return null;
        }
        $current_term_id = $this->getCurrentTermId();
        $prev_key = null;
        $res_key = null;
        foreach ($all_term as $k => $v) {
            if ($prev_key == $current_term_id) {
                $res_key = $k;
                break;
            }
            $prev_key = $k;
        }
        return $res_key;
    }

    function saveCurrentTerm()
    {
        $start_date = $this->Team->getCurrentTermStartDate();
        $latest = $this->getLatestTerm();
        if (!empty($latest)) {
            $start_date = $latest['EvaluateTerm']['end_date'] + 1;
        }
        $res = $this->saveTerm($start_date, $this->Team->getCurrentTermEndDate() - 1);
        return $res;
    }

    function saveNextTerm()
    {
        $after_start_end = $this->Team->getAfterTermStartEnd();
        $latest = $this->getLatestTerm();
        if (!empty($latest)) {
            $start_date = $latest['EvaluateTerm']['end_date'] + 1;
        }
        $res = $this->saveTerm($start_date, $after_start_end['end'] - 1);
        return $res;
    }

    function saveTerm($start, $end)
    {
        $data = [
            'team_id'    => $this->current_team_id,
            'start_date' => $start,
            'end_date'   => $end,
        ];
        $this->create();
        $res = $this->save($data);
        return $res;
    }

    function changeToInProgress($id)
    {
        $this->id = $id;
        return $this->saveField('evaluate_status', self::STATUS_EVAL_IN_PROGRESS);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    function isAbleToStartEvaluation($id)
    {
        $options = [
            'conditions' => [
                'id'              => $id,
                'team_id'         => $this->current_team_id,
                'evaluate_status' => self::STATUS_EVAL_NOT_STARTED,
            ],
        ];
        $res = $this->find('first', $options);
        return (bool)$res;
    }

    function isStartedEvaluation($id)
    {
        $options = [
            'conditions' => [
                'id'      => $id,
                'team_id' => $this->current_team_id,
                'NOT'     => [
                    'evaluate_status' => self::STATUS_EVAL_NOT_STARTED,
                ]
            ]
        ];
        $res = $this->find('first', $options);
        return (bool)$res;
    }

    function changeFreezeStatus($id)
    {
        // Check freezable
        $options = [
            'conditions' => [
                'id'      => $id,
                'team_id' => $this->current_team_id,
            ]
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            throw new RuntimeException(__d('gl', "この期間は凍結できません。"));
        }

        $isFrozen = $this->checkFrozenEvaluateTerm($id);
        if ($isFrozen) {
            $expect_status = self::STATUS_EVAL_IN_PROGRESS;
        }
        else {
            $expect_status = self::STATUS_EVAL_FROZEN;
        }

        $this->id = $id;
        $saveData = ['evaluate_status' => $expect_status];
        $res = $this->save($saveData);
        return $res;
    }

    function checkFrozenEvaluateTerm($id)
    {
        $options = [
            'conditions' => [
                'id'              => $id,
                'team_id'         => $this->current_team_id,
                'evaluate_status' => self::STATUS_EVAL_FROZEN
            ]
        ];
        $res = $this->find('first', $options);
        return (empty($res)) ? false : true;
    }

}
