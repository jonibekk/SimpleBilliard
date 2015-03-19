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
        'index'             => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'           => [
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
        'evaluate_term_id'  => [
            'notEmpty' => [
                'rule' => 'notEmpty'
            ]
        ],
        'comment'           => [],
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
    const TYPE_ONESELF = 0;
    const TYPE_EVALUATOR = 1;
    const TYPE_LEADER = 2;
    const TYPE_FINAL_EVALUATOR = 3;

    /**
     *  status type
     */
    const TYPE_STATUS_NOT_ENTERED = 0;
    const TYPE_STATUS_DRAFT = 1;
    const TYPE_STATUS_DONE = 2;

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

    public function add($data, $saveType)
    {
        // insert status value to save data
        if ($saveType === "draft") {
            $data = Hash::insert($data, '{n}.Evaluation.status', 1);
            $this->setDraftValidation();
        }
        else {
            $data = Hash::insert($data, '{n}.Evaluation.status', 2);
            $this->setNotAllowEmptyToComment();
            $this->setNotAllowEmptyToEvaluateScoreId();
        }

        foreach ($data as $key => $law) {
            $this->create();
            $law['Evaluation']['index'] = $key;
            if (!$this->save($law)) {
                if (!empty($this->validationErrors)) {
                    throw new RuntimeException(__d('validate', "入力内容に不足があります。"));
                }
                else {
                    throw new RuntimeException(__d('validate', "保存処理に失敗しました。"));
                }
            }
        }

        return true;
    }

    public function getEditableEvaluations($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
                'OR'                => [
                    ['Evaluation.status' => self::TYPE_STATUS_NOT_ENTERED],
                    ['Evaluation.status' => self::TYPE_STATUS_DRAFT]
                ]
            ],
            'order'      => 'Evaluation.index asc',
            'contain'    => [
                'Goal' => [
                    'KeyResult',
                    'GoalCategory',
                    'MyCollabo'
                ]
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    public function setDraftValidation()
    {
        $this->setAllowEmptyToComment();
        $this->setAllowEmptyToEvaluateScoreId();
        return;
    }

    public function setAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notEmpty'])) {
            unset($this->validate['comment']['notEmpty']);
        }
        return;
    }

    public function setNotAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notEmpty'])) {
            return;
        }
        $this->validate['comment']['notEmpty'] = ['rule' => 'notEmpty'];
        return;
    }

    public function setAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notEmpty'])) {
            unset($this->validate['evaluate_score_id']['notEmpty']);
        }
        return;
    }

    public function setNotAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notEmpty'])) {
            return;
        }
        $this->validate['evaluate_score_id']['notEmpty'] = ['rule' => 'notEmpty'];
        return;
    }

}
