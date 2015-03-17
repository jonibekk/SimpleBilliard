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
        'comment' => [],
        'evaluate_score_id' => []
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

    var $evaluationType = null;

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
        // insert status value to save data
        if($saveType === "draft") {
            $data = Set::insert($data, '{n}.Evaluation.status', 1);
        } else {
            $data = Set::insert($data, '{n}.Evaluation.status', 2);
        }

        foreach($data as $law) {
            $this->setValidationByEvaluationId($law['Evaluation']['id'], $saveType);
            $this->create();
            if(!$this->save($law)) {

            }
        }

        return true;
    }

    public function getEditableEvaluations($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
                'OR' => [
                    ['Evaluation.status' => self::TYPE_STATUS_NOT_ENTERED],
                    ['Evaluation.status' => self::TYPE_STATUS_DRAFT]
                ]
            ],
            'order' => 'Evaluation.index asc'
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function setValidationByEvaluationId($evaluationId, $saveType)
    {
        if($saveType === "draft") {
            $this->setDraftValidation();
            return;
        }
        $total_or_goal = $this->getEvaluationTypeTotalOrGoal($evaluationId);
        $this->setRegisterValidation($total_or_goal);
    }

    public function setDraftValidation() {
        $this->setAllowEmptyToComment();
        return;
    }

    public function setRegisterValidation($total_or_goal) {
        $settings = $this->Team->EvaluationSetting->getEvaluationSetting();
        if($total_or_goal === "total") {
            $notAllowCommentEmpty = $settings['Evaluation']['self_comment_required_flg'];
        } else {
            $notAllowCommentEmpty = $settings['Evaluation']['self_goal_comment_required_flg'];
        }
        if($notAllowCommentEmpty) {
            $this->setNotAllowEmptyToComment();
        } else {
            $this->setAllowEmptyToComment();
        }
    }

    public function setAllowEmptyToComment() {
        if(isset($this->validate['comment']['notEmpty'])) {
            unset($this->validate['comment']['notEmpty']);
        }
        return;
    }

    public function setNotAllowEmptyToComment() {
        if(isset($this->validate['comment']['notEmpty'])) {
            return;
        }
        $this->validate['comment']['notEmpty'] = ['rule' => 'notEmpty'];
        return;
    }

    public function insertValidationStatus($records)
    {
        $evaSettings = $this->Team->EvaluationSetting->getEvaluationSetting();
        foreach($records as $key => $law) {
            $isTotal = empty($law['Evaluation']['goal_id']);
            if($isTotal) {
                $allowEmpty = $evaSettings['EvaluationSetting']['self_goal_comment_required_flg'];
            } else {
                $allowEmpty = $evaSettings['EvaluationSetting']['self_comment_required_flg'];
            }
            $records[$key]['Evaluation']['allow_empty'] = $allowEmpty;
        }
        return $records;
    }

    public function getEvaluationTypeTotalOrGoal($evaluationId) {
        $options = [
            'conditions' => [
                'id' => $evaluationId
            ],
            'fields' => [
                'goal_id'
            ]
        ];
        $res = $this->find('first', $options);
        return (empty($res)) ? "total" : "goal";
    }

}
