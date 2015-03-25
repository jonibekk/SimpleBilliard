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

    static public $TYPE = [
        self::TYPE_ONESELF         => "",
        self::TYPE_EVALUATOR       => "",
        self::TYPE_FINAL_EVALUATOR => "",
        self::TYPE_LEADER          => ","
    ];

    /**
     * タイプの表示名をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_ONESELF] = __d('gl', "あなた");
        self::$TYPE[self::TYPE_EVALUATOR] = __d('gl', "評価者");
        self::$TYPE[self::TYPE_FINAL_EVALUATOR] = __d('gl', "最終者");
        self::$TYPE[self::TYPE_LEADER] = __d('gl', "リーダ");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

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

    function isMySelfEvalIncomplete($term_id)
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $this->my_uid,
                'evaluator_user_id' => $this->my_uid,
                'evaluate_type'     => self::TYPE_ONESELF,
                'goal_id'           => null,
                'team_id'           => $this->current_team_id,
                'evaluate_term_id'  => $term_id,
                'NOT'               => [
                    'status' => self::TYPE_STATUS_DONE
                ],
            ],
        ];
        $res = $this->find('first', $options);
        if (!empty($res)) {
            return true;
        }
        return false;
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
                'evaluate_type'     => self::TYPE_ONESELF,
            ],
            'order'      => 'Evaluation.index asc',
            'contain'    => [
                'Goal' => [
                    'KeyResult',
                    'GoalCategory',
                    'MyCollabo',
                    'ActionResult' => [
                        'conditions' => [
                            'user_id' => $evaluateeId
                        ],
                        'fields'     => [
                            'id'
                        ]
                    ]
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

    /**
     * @return bool
     */
    function startEvaluation()
    {
        //get evaluation setting.
        if (!$this->Team->EvaluationSetting->isEnabled()) {
            return false;
        }
        $this->Team->EvaluateTerm->saveTerm();
        $term_id = $this->Team->EvaluateTerm->getLastInsertID();
        $team_members_list = $this->Team->TeamMember->getAllMemberUserIdList(true, true, true);
        $evaluators = [];
        if ($this->Team->EvaluationSetting->isEnabledEvaluator()) {
            $evaluators = $this->Team->Evaluator->getEvaluatorsCombined();
        }
        $all_evaluations = [];
        //一人ずつデータを生成
        foreach ($team_members_list as $uid) {
            $all_evaluations = array_merge($all_evaluations,
                                           $this->getAddRecordsOfEvaluatee($uid, $term_id, $evaluators));
        }
        if (!empty($all_evaluations)) {
            $res = $this->saveAll($all_evaluations);
            return (bool)$res;
        }
        return false;
    }

    /**
     * @param $uid
     * @param $term_id
     * @param $evaluators
     *
     * @return array
     */
    function getAddRecordsOfEvaluatee($uid, $term_id, $evaluators)
    {
        $index = 0;
        $evaluations = [];
        //self total
        if ($this->Team->EvaluationSetting->isEnabledSelf()) {
            $evaluations[] = $this->getAddRecord($uid, $uid, $term_id, $index++, self::TYPE_ONESELF);
        }
        //evaluator total
        if ($this->Team->EvaluationSetting->isEnabledEvaluator() && viaIsSet($evaluators[$uid])) {
            $evals = $evaluators[$uid];
            foreach ($evals as $eval_uid) {
                $evaluations[] = $this->getAddRecord($uid, $eval_uid, $term_id, $index++, self::TYPE_EVALUATOR);
            }
        }
        //final total
        if ($this->Team->EvaluationSetting->isEnabledFinal() && $admin_uid = $this->Team->TeamMember->getTeamAdminUid()) {
            $evaluations[] = $this->getAddRecord($uid, $admin_uid, $term_id, $index++,
                                                 self::TYPE_FINAL_EVALUATOR);
        }

        $evaluations = array_merge($evaluations,
                                   $this->getAddRecordsOfGoalEvaluation($uid, $term_id, $evaluators, $index));

        return $evaluations;
    }

    /**
     * @param $uid
     * @param $term_id
     * @param $evaluators
     * @param $index
     *
     * @return array
     */
    function getAddRecordsOfGoalEvaluation($uid, $term_id, $evaluators, $index)
    {
        $goal_evaluations = [];
        $goal_list = $this->Goal->Collaborator->getCollaboGoalList($uid, true, null, 1, Collaborator::STATUS_APPROVAL);
        $goal_list = $this->Goal->filterThisTermIds($goal_list);
        foreach ($goal_list as $gid) {
            //self
            if ($this->Team->EvaluationSetting->isEnabledSelf()) {
                $goal_evaluations[] = $this->getAddRecord($uid, $uid, $term_id, $index++, self::TYPE_ONESELF, $gid);
            }

            //evaluator
            if ($this->Team->EvaluationSetting->isEnabledEvaluator() && viaIsSet($evaluators[$uid])) {
                $evals = $evaluators[$uid];
                foreach ($evals as $eval_uid) {
                    $goal_evaluations[] = $this->getAddRecord($uid, $eval_uid, $term_id, $index++,
                                                              self::TYPE_EVALUATOR, $gid);
                }
            }
            //leader
            if ($this->Team->EvaluationSetting->isEnabledLeader()) {
                $leader_uid = $this->Goal->Collaborator->getLeaderUid($gid);
                if ($uid !== $leader_uid) {
                    $goal_evaluations[] = $this->getAddRecord($uid, $leader_uid, $term_id, $index++,
                                                              self::TYPE_LEADER, $gid);
                }
            }
        }
        return $goal_evaluations;
    }

    /**
     * @param      $evaluatee_user_id
     * @param      $evaluator_user_id
     * @param      $term_id
     * @param      $index
     * @param      $type
     * @param null $goal_id
     *
     * @return array
     */
    function getAddRecord($evaluatee_user_id, $evaluator_user_id, $term_id, $index, $type, $goal_id = null)
    {
        $record = [
            'evaluatee_user_id' => $evaluatee_user_id,
            'evaluator_user_id' => $evaluator_user_id,
            'team_id'           => $this->current_team_id,
            'goal_id'           => $goal_id,
            'evaluate_term_id'  => $term_id,
            'evaluate_type'     => $type,
            'index'             => $index,
        ];
        return $record;
    }

    function getMyEvalStatus($term_id)
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $this->my_uid,
                'evaluate_term_id'  => $term_id,
                'team_id'           => $this->current_team_id,
                'goal_id'           => null,
            ],
            'fields'     => ['id', 'evaluate_type', 'status',],
            'order'      => ['index' => 'asc']
        ];
        $data = $this->find('all', $options);
        $data = Hash::combine($data, '{n}.Evaluation.id', '{n}.Evaluation');
        $res = [];
        $already_exists_incomplete = false;
        $evaluator_index = 1;
        foreach ($data as $val) {
            $name = self::$TYPE[$val['evaluate_type']];
            if ($val['evaluate_type'] == self::TYPE_EVALUATOR) {
                $name .= $evaluator_index;
                $evaluator_index++;
            }
            $res[] = [
                'name'    => $name,
                'status'  => $val['status'],
                'my_tarn' => !$already_exists_incomplete && $val['status'] != self::TYPE_STATUS_DONE ? true : false,
            ];
            if ($val['status'] != self::TYPE_STATUS_DONE) {
                $already_exists_incomplete = true;
            }
        }
        return $res;
    }

    function getStatus($evaluateTermId, $evaluateeId, $evaluatorId)
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $evaluateeId,
                'evaluator_user_id' => $evaluatorId,
                'evaluate_term_id'  => $evaluateTermId,
                'team_id'           => $this->current_team_id,
            ],
            'fields'     => ['status'],
            'order'      => ['index' => 'asc']
        ];
        $res = $this->find("first", $options);
        return $res['Evaluation']['status'];
    }

    function getEvaluations($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
            ],
            'order'      => 'Evaluation.index asc',
            'contain'    => [
                'Goal' => [
                    'KeyResult',
                    'GoalCategory',
                    'MyCollabo',
                    'ActionResult' => [
                        'conditions' => [
                            'user_id' => $evaluateeId
                        ],
                        'fields'     => [
                            'id'
                        ]
                    ],
                ],
                'EvaluatorUser'
            ]
        ];
        $res = $this->find('all', $options);
        return Hash::combine($res, '{n}.Evaluation.id', '{n}', '{n}.Goal.id');
    }

    function getEvaluateType($evaluateTermId, $evaluateeId) {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
            ],
            'order'      => 'Evaluation.index asc',
        ];
        $res = $this->find('first', $options);
        return $res['Evaluation']['evaluate_type'];
    }

}
