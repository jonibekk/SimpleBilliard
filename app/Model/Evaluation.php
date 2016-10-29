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
            'notBlank' => [
                'rule' => 'notBlank'
            ]
        ],
        'evaluator_user_id' => [
            'notBlank' => [
                'rule' => 'notBlank'
            ]
        ],
        'evaluate_term_id'  => [
            'notBlank' => [
                'rule' => 'notBlank'
            ]
        ],
        'comment'           => [
            'isString'  => [
                'rule'       => ['isString'],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 5000]],
        ],
        'evaluate_score_id' => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'evaluate_type'     => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
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
    public $evaluate_term_id = null;

    static public $TYPE = [
        self::TYPE_ONESELF         => [
            'index' => "",
            'view'  => "",
        ],
        self::TYPE_EVALUATOR       => [
            'index' => "",
            'view'  => "",
        ],
        self::TYPE_FINAL_EVALUATOR => [
            'index' => "",
            'view'  => "",
        ],
        self::TYPE_LEADER          => [
            'index' => "",
            'view'  => "",
        ],
    ];

    /**
     * タイプの表示名をセット
     */
    private function _setTypeName()
    {
        self::$TYPE[self::TYPE_ONESELF]['index'] = __("You");
        self::$TYPE[self::TYPE_EVALUATOR]['index'] = __("Evaluator");
        self::$TYPE[self::TYPE_FINAL_EVALUATOR]['index'] = __("Final Evaluator");
        self::$TYPE[self::TYPE_LEADER]['index'] = __("Leader");
        self::$TYPE[self::TYPE_ONESELF]['view'] = __("You");
        self::$TYPE[self::TYPE_EVALUATOR]['view'] = __("Evaluator");
        self::$TYPE[self::TYPE_FINAL_EVALUATOR]['view'] = __("Final Evaluator");
        self::$TYPE[self::TYPE_LEADER]['view'] = __("Leader");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeName();
    }

    /**
     * afterFind callback
     *
     * @param array $results Result data
     * @param mixed $primary Primary query
     *
     * @return array
     */
    public function afterFind($results, $primary = false)
    {
        if (empty($results)) {
            return $results;
        }

        // データに評価タイプ名を追加する
        /** @noinspection PhpUnusedParameterInspection */
        $this
            ->dataIter($results,
                function (&$entity, &$model) {
                    $entity = $this->setEvaluatorTypeName($entity);
                });
        return $results;
    }

    public function setEvaluatorTypeName($row)
    {
        if (!isset($row[$this->alias]['evaluate_type']) || !isset($row[$this->alias]['index_num'])) {
            return $row;
        }

        $evaluate_type = $row[$this->alias]['evaluate_type'];
        $index_num = (string)$row[$this->alias]['index_num'];
        $evaluator_type_name = '';

        if ($evaluate_type == self::TYPE_ONESELF) {
            $evaluator_type_name = __("Self");
        } else {
            if ($evaluate_type == self::TYPE_FINAL_EVALUATOR) {
                $evaluator_type_name = __("Final Evaluator");
            } else {
                if ($evaluate_type == self::TYPE_EVALUATOR) {
                    $evaluator_type_name = __("評価者{$index_num}");
                }
            }
        }
        $row[$this->alias]['evaluator_type_name'] = $evaluator_type_name;

        return $row;

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
        if (!Hash::get($my_team_member_status, 'TeamMember')) {
            throw new RuntimeException(__("You don't have access right to this page."));
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
            throw new RuntimeException(__("Parameter is invalid."));
        }

        if (!$this->Team->EvaluateTerm->isStartedEvaluation($termId)) {
            throw new RuntimeException(__("You can't evaluate in this period or don't have permission to view."));
        }

        if ($this->getStatus($termId, $evaluateeId, $this->my_uid) === null) {
            throw new RuntimeException(__("You can't evaluate in this period or don't have permission to view."));
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
        if ($saveType == self::TYPE_STATUS_DRAFT) {
            $data = Hash::insert($data, '{n}.Evaluation.status', self::TYPE_STATUS_DRAFT);
            $this->setDraftValidation();
        } else {
            $data = Hash::insert($data, '{n}.Evaluation.status', self::TYPE_STATUS_DONE);
            $this->setNotAllowEmptyToComment();
            $this->setNotAllowEmptyToEvaluateScoreId();
        }

        foreach ($data as $key => $law) {
            if (!$this->save($law)) {
                if (!empty($this->validationErrors)) {
                    throw new RuntimeException(__("There is something you can't leave empty."));
                } else {
                    throw new RuntimeException(__("Failed to save."));
                }
            }
        }

        // Move turn flg to next
        if ($saveType == self::TYPE_STATUS_DONE) {
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
                'NOT'               => [
                    ['evaluate_type' => self::TYPE_LEADER]
                ]
            ],
            'order'      => 'Evaluation.index_num asc',
            'contain'    => [
                'Goal'          => [
                    'KeyResult'    => [
                        'fields'       => [
                            'id',
                            'user_id',
                            'priority',
                            'progress',
                        ],
                        'ActionResult' => [
                            'conditions' => [
                                'user_id' => $evaluateeId,
                            ],
                            'fields'     => [
                                'id'
                            ]
                        ],
                    ],
                    'GoalCategory',
                    'MyCollabo'    => [
                        'conditions' => [
                            'user_id' => $evaluateeId
                        ]
                    ],
                    'ActionResult' => [
                        'conditions' => [
                            'user_id' => $evaluateeId,
                        ],
                        'fields'     => [
                            'id'
                        ]
                    ],
                    'GoalMember'   => [
                        'fields' => [
                            'user_id',
                            'user_id'
                        ]
                    ]
                ],
                'EvaluatorUser',
                'EvaluateScore' => [
                    'fields' => ['EvaluateScore.name']
                ],
            ]
        ];
        $res = $this->find('all', $options);
        return Hash::combine($res, '{n}.Evaluation.id', '{n}', '{n}.Goal.id');
    }

    function getAllEvaluations($term_id, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'Evaluation.evaluate_term_id' => $term_id,
                'Evaluation.team_id'          => $team_id,
            ],
            'order'      => [
                'Evaluation.evaluatee_user_id ASC',
                'Evaluation.index_num ASC'
            ],
            'contain'    => [
                'EvaluatorUser' => [
                    'fields' => $this->EvaluateeUser->profileFields
                ],
                'EvaluateScore' => [
                    'fields' => [
                        'EvaluateScore.name'
                    ]
                ]
            ]
        ];
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.Evaluation.id', '{n}', '{n}.Evaluation.evaluatee_user_id');
        return $res;
    }

    function getFinalEvaluations($term_id, $evaluatee_user_id, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'Evaluation.evaluate_term_id'  => $term_id,
                'Evaluation.team_id'           => $team_id,
                'Evaluation.evaluatee_user_id' => $evaluatee_user_id,
                'Evaluation.evaluate_type'     => self::TYPE_FINAL_EVALUATOR,
                'Evaluation.goal_id'           => null,

            ],
        ];
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.Evaluation.evaluatee_user_id', '{n}.Evaluation');
        return $res;
    }

    function setDraftValidation()
    {
        $this->setAllowEmptyToComment();
        $this->setAllowEmptyToEvaluateScoreId();
        return;
    }

    function setAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notBlank'])) {
            unset($this->validate['comment']['notBlank']);
        }
        if (!isset($this->validate['comment']['isString']['allowEmpty'])) {
            $this->validate['comment']['isString']['allowEmpty'] = true;
        }
        return;
    }

    function setNotAllowEmptyToComment()
    {
        if (isset($this->validate['comment']['notBlank'])) {
            return;
        }
        $this->validate['comment']['notBlank'] = ['rule' => 'notBlank'];

        if (isset($this->validate['comment']['isString']['allowEmpty'])) {
            unset($this->validate['comment']['isString']['allowEmpty']);
        }
        return;
    }

    function setAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notBlank'])) {
            unset($this->validate['evaluate_score_id']['notBlank']);
        }
        if (!isset($this->validate['evaluate_score_id']['numeric']['allowEmpty'])) {
            $this->validate['evaluate_score_id']['numeric']['allowEmpty'] = true;
        }
        return;
    }

    function setNotAllowEmptyToEvaluateScoreId()
    {
        if (isset($this->validate['evaluate_score_id']['notBlank'])) {
            return;
        }
        $this->validate['evaluate_score_id']['notBlank'] = ['rule' => 'notBlank'];

        if (isset($this->validate['evaluate_score_id']['numeric']['allowEmpty'])) {
            unset($this->validate['evaluate_score_id']['numeric']['allowEmpty']);
        }
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
        if (!$term_id = $this->Team->EvaluateTerm->getCurrentTermId()) {
            $this->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
            $term_id = $this->Team->EvaluateTerm->getLastInsertID();
        }
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
                [
                    'Evaluation.team_id'          => $this->current_team_id,
                    'Evaluation.evaluate_term_id' => $term_id,
                    'Evaluation.index_num'        => 0,
                ]
            );
            $this->EvaluateTerm->changeToInProgress($term_id);

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
        if ($this->Team->EvaluationSetting->isEnabledEvaluator() && Hash::get($evaluators, $uid)) {
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
     * @param $termId
     * @param $evaluators
     * @param $index
     *
     * @return array
     */
    function getAddRecordsOfGoalEvaluation($uid, $termId, $evaluators, $index)
    {
        $goalEvaluations = [];
        $goalList = $this->Goal->GoalMember->findEvaluatableGoalList($uid);
        $goalList = $this->Goal->filterThisTermIds($goalList);
        //order by priority of goal
        $goalList = $this->Goal->GoalMember->goalIdOrderByPriority($uid, $goalList);

        foreach ($goalList as $gid) {
            //self
            if ($this->Team->EvaluationSetting->isEnabledSelf()) {
                $goalEvaluations[] = $this->getAddRecord($uid, $uid, $termId, $index++, self::TYPE_ONESELF, $gid);
            }

            //evaluator
            if ($this->Team->EvaluationSetting->isEnabledEvaluator() && Hash::get($evaluators, $uid)) {
                $evals = $evaluators[$uid];
                foreach ($evals as $eval_uid) {
                    $goalEvaluations[] = $this->getAddRecord($uid, $eval_uid, $termId, $index++,
                        self::TYPE_EVALUATOR, $gid);
                }
            }
            //leader
            if ($this->Team->EvaluationSetting->isEnabledLeader()) {
                $leader_uid = $this->Goal->GoalMember->getLeaderUid($gid);
                if ($uid !== $leader_uid) {
                    $goalEvaluations[] = $this->getAddRecord($uid, $leader_uid, $termId, $index++,
                        self::TYPE_LEADER, $gid);
                }
            }
        }
        return $goalEvaluations;
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
            $name = self::$TYPE[$val['evaluate_type']]['index'];
            if ($val['evaluate_type'] == self::TYPE_EVALUATOR) {
                if ($val['evaluator_user_id'] == $this->my_uid) {
                    $name = __("You");
                } else {
                    $name .= $evaluator_index;
                }
                $evaluator_index++;
            } //自己評価で被評価者が自分以外の場合は「メンバー」
            elseif ($val['evaluate_type'] == self::TYPE_ONESELF && $val['evaluatee_user_id'] != $this->my_uid) {
                $name = __('Members');
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
                $status_text['body'] = __("Waiting for the evaluation by %s.", $name);
                continue;
            }
            //your turn
            $status_text['your_turn'] = true;
            switch ($val['evaluate_type']) {
                case self::TYPE_ONESELF:
                    $status_text['body'] = __("Please evaluate yourself.");
                    break;
                case self::TYPE_EVALUATOR:
                    $status_text['body'] = __("Please evaluate.");
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
                'NOT'               => ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
            ],
            'fields'     => ['status'],
            'order'      => ['index_num' => 'asc']
        ];
        $res = $this->find("first", $options);
        return Hash::get($res, 'Evaluation.status');
    }

    function getEvaluateType($evaluateTermId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluator_user_id' => $this->my_uid
            ],
            'order'      => 'Evaluation.index_num asc',
        ];
        $res = $this->find('first', $options);

        return (isset($res['Evaluation']['evaluate_type'])) ? $res['Evaluation']['evaluate_type'] : false;
    }

    function getMyTurnCount($evaluate_type = null, $term_id = null, $is_all = true)
    {
        $is_default = false;
        if ($evaluate_type === null && $term_id === null && $is_all === true) {
            $is_default = true;
            $count = Cache::read($this->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true), 'team_info');
            if ($count !== false) {
                return $count;
            }
        }
        $options = [
            'conditions' => [
                'evaluator_user_id' => $this->my_uid,
                'team_id'           => $this->current_team_id,
                'my_turn_flg'       => true,
                'evaluate_type'     => $evaluate_type,
                'evaluate_term_id'  => $term_id,
                'NOT'               => [
                    ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
                    ['evaluate_type' => self::TYPE_LEADER]
                ]
            ],
            'group'      => ['evaluate_term_id', 'evaluatee_user_id']
        ];
        if (is_null($evaluate_type)) {
            unset($options['conditions']['evaluate_type']);
        }
        if (is_null($term_id) && $is_all === true) {
            unset($options['conditions']['evaluate_term_id']);
        }

        //前期以前のデータは無視する (現状の仕様上その情報に一切アクセスができないため)
        $previousStartDate = Hash::get($this->Team->EvaluateTerm->getPreviousTermData(), 'start_date');
        if ($previousStartDate) {
            $options['conditions']['created >='] = $previousStartDate;
        }

        // freeze
        $currentTermId = $this->Team->EvaluateTerm->getCurrentTermId();
        $previousTermId = $this->Team->EvaluateTerm->getPreviousTermId();
        if ($this->Team->EvaluateTerm->checkFrozenEvaluateTerm($currentTermId)) {
            $options['conditions']['NOT'][] = ['evaluate_term_id' => $currentTermId];
        }
        if ($this->Team->EvaluateTerm->checkFrozenEvaluateTerm($previousTermId)) {
            $options['conditions']['NOT'][] = ['evaluate_term_id' => $previousTermId];
        }
        $count = $this->find('count', $options);
        if (!$count) {
            $count = 0;
        }
        if ($is_default) {
            Cache::write($this->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true), $count, 'team_info');
        }
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
        return Hash::get($res, 'Evaluation.evaluate_term_id');
    }

    function getEvaluateeIdByEvaluationId($evaluationId)
    {
        $res = $this->find("first", [
            'conditions' => [
                'id' => $evaluationId
            ],
            'fields'     => [
                'evaluatee_user_id'
            ]
        ]);
        return Hash::get($res, 'Evaluation.evaluatee_user_id');
    }

    function getEvaluateeIdsByTermId($term_id)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $term_id
            ],
            'fields'     => ['evaluatee_user_id', 'evaluatee_user_id'],
            'group'      => ['evaluatee_user_id'],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function getEvaluatorIdsByTermId($term_id)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $term_id
            ],
            'fields'     => ['evaluator_user_id', 'evaluator_user_id'],
            'group'      => ['evaluator_user_id'],
        ];
        $res = $this->find('list', $options);
        return $res;
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

        $myIndex = Hash::get(Hash::extract($res, "{n}.Evaluation[evaluator_user_id={$this->my_uid}]"), '0.index_num');
        if ($myIndex === null) {
            return null;
        }

        $nextIndex = (int)$myIndex + 1;
        $nextId = Hash::get(Hash::extract($res, "{n}.Evaluation[index_num={$nextIndex}]"), '0.evaluator_user_id');
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
        // check frozen
        $evalIsFrozen = $this->EvaluateTerm->checkFrozenEvaluateTerm($evaluateTermId);
        if ($evalIsFrozen) {
            return false;
        }

        // check my turn
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
        if (!$termId) {
            return null;
        }
        $evaluation_statuses = [
            self::TYPE_ONESELF   => [
                'label'          => __("Self"),
                'all_num'        => 0,
                'incomplete_num' => 0,
            ],
            self::TYPE_EVALUATOR => [
                'label'          => __("Evaluator"),
                'all_num'        => 0,
                'incomplete_num' => 0,
            ],
        ];

        // Get only oneself evaluation
        $own_evaluation_options = [
            'conditions' => [
                'evaluate_term_id' => $termId,
                'evaluate_type'    => self::TYPE_ONESELF,
            ],
            'group'      => [
                'evaluatee_user_id',
                'evaluator_user_id'
            ]
        ];
        $res = $this->find("all", $own_evaluation_options);
        $oneself_all_cnt = count($res);
        $oneself_incomplete_cnt = count(Hash::extract($res, "{n}.Evaluation[status!=2]"));

        // Set oneself count
        $evaluation_statuses[self::TYPE_ONESELF]['all_num'] = $oneself_all_cnt;
        $evaluation_statuses[self::TYPE_ONESELF]['incomplete_num'] = $oneself_incomplete_cnt;

        // Get evaluator evaluations
        $evaluator_options = [
            'conditions' => [
                'evaluate_term_id' => $termId,
                'evaluate_type'    => self::TYPE_EVALUATOR
            ],
            'group'      => [
                'evaluatee_user_id',
                'evaluator_user_id'
            ]
        ];
        $res = $this->find("all", $evaluator_options);
        $combined = Hash::combine($res, "{n}.Evaluation.id", "{n}", "{n}.Evaluation.evaluatee_user_id");

        // Increment
        foreach ($combined as $groupedEvaluator) {
            foreach ($groupedEvaluator as $eval) {
                $evaluation_statuses[self::TYPE_EVALUATOR]['all_num']++;
                if ($eval['Evaluation']['status'] != self::TYPE_STATUS_DONE) {
                    $evaluation_statuses[self::TYPE_EVALUATOR]['incomplete_num']++;
                }
            }
        }

        return $evaluation_statuses;
    }

    function getIncompleteEvaluatees($termId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $termId,
                'NOT'              => [
                    ['status' => self::TYPE_STATUS_DONE],
                    ['evaluate_type' => self::TYPE_LEADER],
                    ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
                ]
            ],
            'group'      => [
                'evaluatee_user_id',
                'evaluator_user_id'
            ],
            'contain'    => [
                'EvaluateeUser'
            ]
        ];
        $res = $this->find('all', $options);
        $combinedEvaluatees = Hash::combine($res, "{n}.Evaluation.id", "{n}", "{n}.Evaluation.evaluatee_user_id");
        $incompleteEvaluatees = [];
        foreach ($combinedEvaluatees as $evaluateeId => $evaluatees) {
            $evaluatees = Hash::insert($evaluatees, '{n}.EvaluateeUser.incomplete_count', (string)count($evaluatees));
            $incompleteEvaluatees[$evaluateeId]['User'] = Hash::extract($evaluatees, "{n}.EvaluateeUser")[0];
        }
        $incompleteEvaluatees = Hash::sort($incompleteEvaluatees, '{n}.User.incomplete_count', 'desc');
        return $incompleteEvaluatees;
    }

    function getIncompleteEvaluators($termId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $termId,
                'my_turn_flg'      => true,
                'NOT'              => [
                    ['status' => self::TYPE_STATUS_DONE],
                    ['evaluate_type' => self::TYPE_LEADER],
                    ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
                ]
            ],
            'group'      => [
                'evaluatee_user_id',
                'evaluator_user_id'
            ],
            'contain'    => [
                'EvaluatorUser'
            ]
        ];
        $res = $this->find('all', $options);
        $combined = Hash::combine($res, "{n}.Evaluation.id", "{n}", "{n}.Evaluation.evaluator_user_id");
        $incompleteEvaluators = [];
        foreach ($combined as $evaluatorId => $evaluators) {
            $evaluators = Hash::insert($evaluators, '{n}.EvaluatorUser.incomplete_count', (string)count($evaluators));
            $incompleteEvaluators[$evaluatorId]['User'] = Hash::extract($evaluators, "{n}.EvaluatorUser")[0];
        }
        $incompleteEvaluators = Hash::sort($incompleteEvaluators, '{n}.User.incomplete_count', 'desc');
        return $incompleteEvaluators;
    }

    function getEvaluators($termId, $evaluateeId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $termId,
                'evaluatee_user_id' => $evaluateeId,
                'NOT'               => [
                    ['evaluate_type' => self::TYPE_LEADER]
                ]
            ],
            'group'      => [
                'evaluator_user_id'
            ],
            'contain'    => [
                'EvaluatorUser'
            ]
        ];

        $res = $this->find('all', $options);
        return $res;
    }

    function getEvaluateesByEvaluator($termId, $evaluatorId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $termId,
                'evaluator_user_id' => $evaluatorId,
                'my_turn_flg'       => true,
                'NOT'               => [
                    ['evaluate_type' => self::TYPE_LEADER],
                    ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
                ]
            ],
            'group'      => [
                'evaluatee_user_id'
            ],
            'contain'    => [
                'EvaluateeUser'
            ]
        ];

        $res = $this->find('all', $options);
        $combined = Hash::combine($res, "{n}.Evaluation.id", "{n}", "{n}.Evaluation.evaluatee_user_id");

        $incompleteEvaluatees = [];
        foreach ($combined as $evaluateeId => $evaluatees) {
            $incompleteEvaluatees[$evaluateeId]['Evaluation'] = Hash::extract($evaluatees, "{n}.Evaluation")[0];
            $incompleteEvaluatees[$evaluateeId]['User'] = Hash::extract($evaluatees, "{n}.EvaluateeUser")[0];
        }

        return $incompleteEvaluatees;
    }

    function getIncompleteOneselfEvaluators($termId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id' => $termId,
                'my_turn_flg'      => true,
                'evaluate_type'    => self::TYPE_ONESELF,
            ],
            'group'      => [
                'evaluator_user_id'
            ],
            'contain'    => [
                'EvaluatorUser'
            ]
        ];

        $res = $this->find('all', $options);

        return $res;
    }

    function getCurrentTurnEvaluationId($evaluateeId, $termId)
    {
        $options = [
            'conditions' => [
                'evaluate_term_id'  => $termId,
                'my_turn_flg'       => true,
                'evaluatee_user_id' => $evaluateeId,
            ],
            'fields'     => [
                'id'
            ]
        ];

        $res = $this->find('first', $options);
        return Hash::get($res, 'Evaluation.id');
    }

    function isThisEvaluateType($id, $type)
    {
        return $this->find('first',
            [
                'conditions' => [
                    'id'            => $id,
                    'evaluate_type' => $type
                ]
            ]
        );
    }

}
