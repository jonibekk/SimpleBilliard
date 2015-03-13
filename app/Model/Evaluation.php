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
                'rule' => ['maxLength', 200]
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

    /**
     *  status type
     */
    const TYPE_STATUS_NOT_ENTERED = 0;
    const TYPE_STATUS_DRAFT       = 1;
    const TYPE_STATUS_DONE        = 2;

    /**
     * 評価リストの閲覧権限チェック
     * ・評価画面の表示条件
     * 　チームの評価機能がon かつ 自分の評価フラグがon
     *
     * @return bool
     */
    function checkAvailViewEvaluationList()
    {
        $my_team_member_status = $this->Team->TeamMember->getWithTeam();
        if (!viaIsSet($my_team_member_status['TeamMember'])) {
            throw new RuntimeException(__d('gl', "この画面にはアクセスできません。"));
        }
        if (!$my_team_member_status['TeamMember']['evaluation_enable_flg']) {
            throw new RuntimeException(__d('gl', "評価設定がoffになっています。チーム管理者にご確認ください"));
        }
        return true;
    }

    function getMyEvaluation()
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $this->my_uid,
                'team_id'           => $this->current_team_id
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }


    public function add($data, $saveType) {
        $this->_setDraftValidation();
        foreach($data as $law) {
            if(empty($law)) continue;
            if($saveType == "draft") {
                // case of saving draft
                $law['Evaluation']['status'] = 1;
            } else {
                // case of registering
                $law['Evaluation']['status'] = 2;
            }
            $this->create();
            $this->save($law);
        }
        return true;
    }

    public function getNotEnteredEvaluations($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
                'status' => self::TYPE_STATUS_NOT_ENTERED
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
