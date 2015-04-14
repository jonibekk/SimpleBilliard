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
        'index_num'         => [
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

    /**
     * @param $termId
     * @param $evaluateeId
     *
     * @return bool
     */
    function checkAvailParameterInEvalForm($termId, $evaluateeId)
    {
        if (!$termId || !$evaluateeId) {
            throw new RuntimeException(__d('gl', "パラメータが不正です。"));
        }

        if (!$this->Team->EvaluateTerm->checkTermAvailable($termId)) {
            throw new RuntimeException(__d('gl', "この期間の評価はできないか、表示する権限がありません。"));
        }

        if ($this->getStatus($termId, $evaluateeId, $this->my_uid) === null) {
            throw new RuntimeException(__d('gl', "この期間の評価はできないか、表示する権限がありません。"));
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

    function add($data, $saveType)
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
            if (!$this->save($law)) {
                if (!empty($this->validationErrors)) {
                    throw new RuntimeException(__d('validate', "入力内容に不足があります。"));
                }
                else {
                    throw new RuntimeException(__d('validate', "保存処理に失敗しました。"));
                }
            }
        }

        if ($saveType === "register") {
            $baseEvaId = $data[0]['Evaluation']['id'];
            $termId = $this->getTermIdByEvaluationId($baseEvaId);
            $evaluateeId = $this->getEvaluateeIdByEvaluationId($baseEvaId);
            $nextEvaluatorId = $this->getNextEvaluatorId($termId, $evaluateeId);

            if ($nextEvaluatorId) {
                $this->setMyTurnFlgOn($termId, $evaluateeId, $nextEvaluatorId);
            }
            $this->setMyTurnFlgOff($termId, $evaluateeId, $this->my_uid);
        }

        return true;

    }

    function getEvaluations($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
            ],
            'order'      => 'Evaluation.index_num asc',
            'contain'    => [
                'Goal' => [
                    'KeyResult'    => [
                        'conditions' => [
                            'NOT' => [
                                'completed' => null
                            ]
                        ]
                    ],
                    'GoalCategory',
                    'MyCollabo'    => [
                        'conditions' => [
                            'user_id' => $this->my_uid
                        ]
                    ],
                    'ActionResult' => [
                        'conditions' => [
                            'user_id' => $evaluateeId,
                        ],
                        'fields'     => [
                            'id'
                        ]
                    ]
                ],
                'EvaluatorUser'
            ]
        ];
        $res = $this->find('all', $options);
        return Hash::combine($res, '{n}.Evaluation.id', '{n}', '{n}.Goal.id');
    }

    function setDraftValidation()
    {
        $this->setAllowEmptyToComment();
        $this->setAllowEmptyToEvaluateScoreId();
        return;
    }

    function setAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notEmpty'])) {
            unset($this->validate['comment']['notEmpty']);
        }
        return;
    }

    function setNotAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notEmpty'])) {
            return;
        }
        $this->validate['comment']['notEmpty'] = ['rule' => 'notEmpty'];
        return;
    }

    function setAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notEmpty'])) {
            unset($this->validate['evaluate_score_id']['notEmpty']);
        }
        return;
    }

    function setNotAllowEmptyToEvaluateScoreId()
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
            //set my_turn
            $this->updateAll(['Evaluation.my_turn_flg' => true],
                             ['Evaluation.team_id'          => $this->current_team_id,
                              'Evaluation.evaluate_term_id' => $term_id,
                              'Evaluation.index_num'        => 0,
                             ]
            );

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
            'index_num'         => $index,
        ];
        return $record;
    }

    function getEvalStatus($term_id, $user_id)
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $user_id,
                'evaluate_term_id'  => $term_id,
                'team_id'           => $this->current_team_id,
                'goal_id'           => null,
            ],
            'fields'     => [
                'id',
                'evaluate_type',
                'status',
                'evaluator_user_id',
                'evaluatee_user_id',
                'my_turn_flg'
            ],
            'order'      => ['index_num' => 'asc'],
        ];
        $data = $this->find('all', $options);
        $data = Hash::combine($data, '{n}.Evaluation.id', '{n}.Evaluation');
        $flow = [];
        $evaluator_index = 1;
        $status_text = ['your_turn' => false, 'body' => null];
        //update flow
        foreach ($data as $val) {
            $name = self::$TYPE[$val['evaluate_type']];
            if ($val['evaluate_type'] == self::TYPE_EVALUATOR) {
                if ($val['evaluator_user_id'] == $this->my_uid) {
                    $name = __d('gl', "あなた");
                }
                else {
                    $name .= $evaluator_index;
                }
                $evaluator_index++;
            }
            //自己評価で被評価者が自分以外の場合は「メンバー」
            elseif ($val['evaluate_type'] == self::TYPE_ONESELF && $val['evaluatee_user_id'] != $this->my_uid) {
                $name = __d('gl', 'メンバー');
            }
            $flow[] = [
                'name'      => $name,
                'status'    => $val['status'],
                'this_turn' => $val['my_turn_flg'],
            ];
            //update status_text
            if ($val['my_turn_flg'] === false) {
                continue;
            }
            if ($val['evaluator_user_id'] != $this->my_uid) {
                $status_text['body'] = __d('gl', "%sの評価待ちです", $name);
                continue;
            }
            //your turn
            $status_text['your_turn'] = true;
            switch ($val['evaluate_type']) {
                case self::TYPE_ONESELF:
                    $status_text['body'] = __d('gl', "自己評価をしてください");
                    break;
                case self::TYPE_EVALUATOR:
                    $status_text['body'] = __d('gl', "評価をしてください");
                    break;
            }
        }
        if (empty($flow)) {
            return [];
        }
        $user = $this->Team->TeamMember->User->getProfileAndEmail($user_id);
        $res = array_merge(['flow' => $flow, 'status_text' => $status_text], $user);
        return $res;
    }

    function getEvaluateeEvalStatusAsEvaluator($term_id)
    {
        $evaluatee_list = $this->getEvaluateeListEvaluableAsEvaluator($term_id);
        $evaluatees = [];
        foreach ($evaluatee_list as $uid) {
            $user = $this->Team->TeamMember->User->getProfileAndEmail($uid);
            $evaluation = $this->getEvalStatus($term_id, $uid);
            $evaluatees[] = array_merge($user, $evaluation);
        }
        return $evaluatees;
    }

    function getEvaluateeListEvaluableAsEvaluator($term_id)
    {
        $options = [
            'conditions' => [
                'evaluator_user_id' => $this->my_uid,
                'evaluate_term_id'  => $term_id,
                'team_id'           => $this->current_team_id,
                'evaluate_type'     => self::TYPE_EVALUATOR
            ],
            'fields'     => ['evaluatee_user_id']
        ];
        $res = $this->find('list', $options);
        $res = array_unique($res);
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
            'order'      => ['index_num' => 'asc']
        ];
        $res = $this->find("first", $options);
        return viaIsSet($res['Evaluation']['status']);
    }

    function getEvaluateType($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
            ],
            'order'      => 'Evaluation.index_num asc',
        ];
        $res = $this->find('first', $options);

        return (isset($res['Evaluation']['evaluate_type'])) ? $res['Evaluation']['evaluate_type'] : false;
    }

    function getMyTurnCount($evaluate_type = null, $term_id = null, $is_all = true)
    {
        $options = [
            'conditions' => [
                'evaluator_user_id' => $this->my_uid,
                'team_id'           => $this->current_team_id,
                'my_turn_flg'       => true,
                'evaluate_type'     => $evaluate_type,
                'evaluate_term_id'  => $term_id
            ],
            'group'      => ['evaluate_term_id', 'evaluatee_user_id']
        ];
        if (is_null($evaluate_type)) {
            unset($options['conditions']['evaluate_type']);
        }
        if (is_null($term_id) && $is_all === true) {
            unset($options['conditions']['evaluate_term_id']);
        }
        $count = $this->find('count', $options);
        return $count;
    }

    function getTermIdByEvaluationId($evaluationId)
    {
        $res = $this->find("first", [
            'conditions' => [
                'id' => $evaluationId
            ],
            'fields'     => [
                'evaluate_term_id'
            ]
        ]);
        return viaIsSet($res['Evaluation']['evaluate_term_id']);
    }

    function getEvaluateeIdByEvaluationId($evaluateeId)
    {
        $res = $this->find("first", [
            'conditions' => [
                'id' => $evaluateeId
            ],
            'fields'     => [
                'evaluatee_user_id'
            ]
        ]);
        return viaIsSet($res['Evaluation']['evaluatee_user_id']);
    }

    function getNextEvaluatorId($termId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $evaluateeId,
                'evaluate_term_id'  => $termId,
                'goal_id'           => null
            ],
            'order'      => [
                'index_num asc'
            ]
        ];
        $res = $this->find("all", $options);
        if (empty($res)) {
            return null;
        }

        $myIndex = viaIsSet(Hash::extract($res, "{n}.Evaluation[evaluator_user_id={$this->my_uid}]")[0]['index_num']);
        if ($myIndex === null) {
            return null;
        }

        $nextIndex = (int)$myIndex + 1;
        $nextId = viaIsSet(Hash::extract($res, "{n}.Evaluation[index_num={$nextIndex}]")[0]['evaluator_user_id']);
        if (empty($nextId)) {
            return null;
        }

        return $nextId;
    }

    function setMyTurnFlgOn($termId, $evaluateeId, $targetUserId)
    {
        $conditions = [
            'evaluator_user_id' => $targetUserId,
            'evaluatee_user_id' => $evaluateeId,
            'evaluate_term_id'  => $termId
        ];
        $this->updateAll(['my_turn_flg' => true], $conditions);
    }

    function setMyTurnFlgOff($termId, $evaluateeId, $targetUserId)
    {
        $conditions = [
            'evaluator_user_id' => $targetUserId,
            'evaluatee_user_id' => $evaluateeId,
            'evaluate_term_id'  => $termId
        ];
        $this->updateAll(['my_turn_flg' => false], $conditions);
    }

    function getIncompleteNumberList()
    {
        $current_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
        $previous_term_id = $this->Team->EvaluateTerm->getPreviousTermId();

        return [
            'present'  => [
                'my_eval'       => $this->getMyTurnCount(self::TYPE_ONESELF, $current_term_id, false),
                'my_evaluatees' => $this->getMyTurnCount(self::TYPE_EVALUATOR, $current_term_id, false)
            ],
            'previous' => [
                'my_eval'       => $this->getMyTurnCount(self::TYPE_ONESELF, $previous_term_id, false),
                'my_evaluatees' => $this->getMyTurnCount(self::TYPE_EVALUATOR, $previous_term_id, false)
            ]
        ];
    }

    function getIsEditable($evaluateTermId, $evaluateeId)
    {
        $evaluationList = $this->getEvaluations($evaluateTermId, $evaluateeId);
        $nextEvaluatorId = $this->getNextEvaluatorId($evaluateTermId, $evaluateeId);
        $isMyTurn = !empty(Hash::extract($evaluationList,
                                         "{n}.{n}.Evaluation[my_turn_flg=true][evaluator_user_id={$this->my_uid}]"));
        $isNextTurn = !empty(Hash::extract($evaluationList,
                                           "{n}.{n}.Evaluation[my_turn_flg=true][evaluator_user_id={$nextEvaluatorId}]"));
        if ($isMyTurn || $isNextTurn) {
            return true;
        }
        return false;
    }

    function getAllStatusesForTeamSettings($termId)
    {
        $evaluation_statuses = [
            [
                'label' => __d('gl', "自己"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者1"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者2"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者3"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者4"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者5"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者6"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
            [
                'label' => __d('gl', "評価者7"),
                'all_num' => 0,
                'incomplete_num' => 0,
            ],
        ];
        $own_evaluation_options = [
            'conditions' => [
                'evaluate_term_id'  => $termId,
                'evaluate_type' => self::TYPE_ONESELF,
            ],
            'group' => [
                'evaluatee_user_id', 'evaluator_user_id'
            ]
        ];
        $res = $this->find("all", $own_evaluation_options);
        $oneself_all_cnt = count($res);
        $oneself_incomplete_cnt = count(Hash::extract($res, "{n}.Evaluation[status!=2]"));

        $evaluation_statuses[0]['all_num'] = $oneself_all_cnt;
        $evaluation_statuses[0]['incomplete_num'] = $oneself_incomplete_cnt;

        $evaluator_options = [
            'conditions' => [
                'evaluate_term_id'  => $termId,
                'evaluate_type'     => self::TYPE_EVALUATOR
            ],
            'group' => [
                'evaluatee_user_id', 'evaluator_user_id'
            ]
        ];
        $res = $this->find("all", $evaluator_options);
        $combined = Hash::combine($res, "{n}.Evaluation.id", "{n}", "{n}.Evaluation.evaluatee_user_id");

        // 各評価者の件数カウント
        foreach($combined as $groupedEvaluator) {
            $evaluator_index = 1;
            foreach($groupedEvaluator as $eval) {
                $evaluation_statuses[$evaluator_index]['all_num']++;
                if($eval['Evaluation']['status'] != self::TYPE_STATUS_DONE) {
                    $evaluation_statuses[$evaluator_index]['incomplete_num']++;
                }
                $evaluator_index++;
            }
        }

        return $evaluation_statuses;
    }

}
