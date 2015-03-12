<?php
App::uses('AppModel', 'Model');

/**
 * Evaluation Model
 *
 * @property Team          $Team
 * @property User          $EvaluateeUser
 * @property User          $EvaluatorUser
 * @property EvaluateTerm  $EvaluateTerm
 * @property EvaluateScore $EvaluateScore
 * @property Goal          $Goal
 */
class Evaluation extends AppModel
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
        'evaluatee_user_id' => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'evaluator_user_id' => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'evaluate_term_id' => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'goal_id' => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'comment' => [
            'maxLength' => [
                'rule' => ['maxLength', 32]
            ]
        ],
        'evaluate_score_id' => [

        ]
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
        'EvaluateeUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluatee_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ],
        'EvaluatorUser' => [
            'className'  => 'User',
            'foreignKey' => 'evaluator_user_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ],
        'EvaluateTerm',
        'EvaluateScore',
        'Goal',
    ];

    /**
     * evaluation type
     */
    const TYPE_ONESELF         = 0;
    const TYPE_EVALUATOR       = 1;
    const TYPE_LEADER          = 2;
    const TYPE_FINAL_EVALUATOR = 3;

    public function addDrafts($data) {
        $this->_setDraftValidation();
        foreach($data as $law) {
            if(empty($law)) continue;
            $law['Evaluation']['status'] = 1;
            $this->create();
            $this->save($law);
        }
        return true;
    }

    public function addRegisters($data) {
        foreach($data as $law) {
            if(empty($law)) continue;
            // Select Validation type
            $this->create();
            $this->save($law);
        }
        return true;
    }

    public function getEvaluationList($evaluateTermId, $evaluateeId)
    {

        $options = [
            'conditions' => [
                'evaluate_term_id' => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId
            ],
            'order' => 'Evaluation.index asc'
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function _setDraftValidation()
    {

    }

    public function _setRegisterValidation()
    {

    }


}
