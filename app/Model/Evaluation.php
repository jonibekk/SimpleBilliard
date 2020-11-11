<?php
App::uses('AppModel', 'Model');
App::import('Service', 'EvaluationService');
App::import('Policy', 'GoalPolicy');

use Goalous\Enum as Enum;

/**
 * Evaluation Model
 *
 * @property Team          $Team
 * @property User          $EvaluateeUser
 * @property User          $EvaluatorUser
 * @property Term          $Term
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
        'term_id'           => [
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
        'Term',
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
    public $term_id = null;

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
        self::$TYPE[self::TYPE_ONESELF]['view'] = __("Evaluatee");
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
                    $evaluator_type_name = __("Evaluator") . $index_num;
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

        if (!$this->Team->Term->isStartedEvaluation($termId)) {
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

        // TODO: fix not to use turn flg. This is not simple.
        // Currently, we use turn flg if only fixed evaluation order
        // Move turn flg to next
        if ($saveType == self::TYPE_STATUS_DONE && $this->Team->EvaluationSetting->isFixedEvaluationOrder()) {
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

    /**
     * `getEvaluations` wrapper for getting evaluatee
     *
     * @param int $evaluateTermId
     * @param int $evaluateeId
     *
     * @return array
     */
    function getEvaluationsForEvaluatee(int $evaluateTermId, int $evaluateeId): array
    {
        return $this->getEvaluations($evaluateTermId, $evaluateeId,
            ['evaluator_user_id' => $evaluateeId, 'evaluate_type' => self::TYPE_ONESELF]);

    }

    /**
     * `getEvaluations` wrapper for getting evaluator
     *
     * @param int $evaluateTermId
     * @param int $evaluateeId
     * @param int $evaluatorId
     *
     * @return array
     */
    function getEvaluationsForEvaluatorAndEvaluatee(int $evaluateTermId, int $evaluateeId, int $evaluatorId): array
    {
        $goalPolicy = new GoalPolicy($evaluatorId, $this->current_team_id);
        $accessibleGoals = $this->Goal->find('all', $goalPolicy->scope());

        return $this->getEvaluations($evaluateTermId, $evaluateeId,
            [
                'evaluator_user_id' => [$evaluatorId, $evaluateeId],
                'evaluate_type'     => [self::TYPE_ONESELF, self::TYPE_EVALUATOR],
                'goal_id'           => Hash::extract($accessibleGoals, '{n}.Goal.id')
            ]
        );

    }

    /**
     * Getting each evaluation detail
     *
     * @param int   $evaluateTermId
     * @param int   $evaluateeId
     * @param array $conditions
     *
     * @return array
     */
    function getEvaluations(int $evaluateTermId, int $evaluateeId, array $conditions = []): array
    {
        $options = [
            'conditions' => [
                'term_id'           => $evaluateTermId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluate_type'     => [
                    self::TYPE_ONESELF,
                    self::TYPE_EVALUATOR,
                    self::TYPE_FINAL_EVALUATOR
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
                            'start_value',
                            'target_value',
                            'current_value',
                            'action_result_count'
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
        $options['conditions'] = am($options['conditions'], $conditions);
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
                'Evaluation.term_id' => $term_id,
                'Evaluation.team_id' => $team_id,
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
                'Evaluation.term_id'           => $term_id,
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
        $term_id = $this->Team->Term->getCurrentTermId();
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
            // TODO: fix not to use turn flg. This is not simple.
            // Currently, we use turn flg if only fixed evaluation order
            if ($this->Team->EvaluationSetting->isFixedEvaluationOrder()) {
                //set my_turn
                $this->updateAll(['Evaluation.my_turn_flg' => true],
                    [
                        'Evaluation.team_id'   => $this->current_team_id,
                        'Evaluation.term_id'   => $term_id,
                        'Evaluation.index_num' => 0,
                    ]
                );
            }
            $this->Term->changeToInProgress($term_id);

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
            foreach ($evals as $eval_uid => $goalIds) {
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

    function appendEvaluatorAccessibleGoals(array $evaluators): array
    {
        // evaluators {"0":{"3":"618","4":"614"} -> array<eval_id, eval_uid>
        $evaluatorGoals = [];

        foreach ($evaluators as $uid => $evals) {
            $evaluatorGoals[$uid] = [];

            foreach ($evals as $eval_uid) {
                if (!array_key_exists($eval_uid, $evaluatorGoals[$uid])) {
                    $policy = new GoalPolicy($eval_uid, $this->current_team_id);
                    $scope = $policy->scope('evaluate');
                    $options = array_merge_recursive($scope, ['fields' => 'Goal.id']);
                    $res = $this->Goal->find('all', $options);

                    $evaluatorGoals[$uid][$eval_uid] = Hash::extract($res, '{n}.Goal.id');
                } 
            }
        }

        return $evaluatorGoals;
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
        $goalList = $this->Goal->filterByTermId($termId, $goalList);
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

                foreach ($evals as $eval_uid => $goalIds) {
                    // only create evaluator if evaluator has access to the goal
                    if (in_array($gid, $goalIds)) {
                        $goalEvaluations[] = $this->getAddRecord(
                            $uid, 
                            $eval_uid, 
                            $termId, 
                            $index++,
                            self::TYPE_EVALUATOR, 
                            $gid
                        );
                    }
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
            'term_id'           => $term_id,
            'evaluate_type'     => $type,
            'index_num'         => $index,
        ];
        return $record;
    }

    function getEvaluateeListEvaluableAsEvaluator($term_id)
    {
        $options = [
            'conditions' => [
                'evaluator_user_id' => $this->my_uid,
                'term_id'           => $term_id,
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
                'term_id'           => $evaluateTermId,
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
                'term_id'           => $evaluateTermId,
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
        if ($this->Team->EvaluationSetting->isFixedEvaluationOrder()) {
            $count = $this->getEvaluableCntIfFixedEvalOrder($evaluate_type, $term_id, $is_all);
        } else {
            $count = $this->getEvaluableCntIfNotFixedEvalOrder($evaluate_type, $term_id, $is_all);

        }
        if ($is_default) {
            Cache::write($this->getCacheKey(CACHE_KEY_EVALUABLE_COUNT, true), $count, 'team_info');
        }
        return $count;
    }

    /**
     * Get evaluable count
     * condition: not fixed evaluation order
     *
     * @param int|null $evaluateType
     * @param int|null $termId
     * @param bool     $isAll
     *
     * @return int
     */
    function getEvaluableCntIfFixedEvalOrder($evaluateType, $termId, $isAll): int
    {

        $options = [
            'conditions' => [
                'evaluator_user_id' => $this->my_uid,
                'team_id'           => $this->current_team_id,
                'my_turn_flg'       => true,
                'evaluate_type'     => $evaluateType,
                'term_id'           => $termId,
                'NOT'               => [
                    ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
                    ['evaluate_type' => self::TYPE_LEADER]
                ]
            ],
            'group'      => ['term_id', 'evaluatee_user_id']
        ];
        if (is_null($evaluateType)) {
            unset($options['conditions']['evaluate_type']);
        }
        if (is_null($termId) && $isAll === true) {
            unset($options['conditions']['term_id']);
        }

        //前期以前のデータは無視する (現状の仕様上その情報に一切アクセスができないため)
        $previousStartDate = Hash::get($this->Team->Term->getPreviousTermData(), 'start_date');
        //getting timezone
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $timezone = $Team->getTimezone();

        if ($previousStartDate) {
            $options['conditions']['created >='] = AppUtil::getTimestampByTimezone($previousStartDate, $timezone);
        }

        // freeze
        $currentTermId = $this->Team->Term->getCurrentTermId();
        $previousTermId = $this->Team->Term->getPreviousTermId();
        if ($this->Team->Term->checkFrozenEvaluateTerm($currentTermId)) {
            $options['conditions']['NOT'][] = ['term_id' => $currentTermId];
        }
        if ($this->Team->Term->checkFrozenEvaluateTerm($previousTermId)) {
            $options['conditions']['NOT'][] = ['term_id' => $previousTermId];
        }
        $count = $this->find('count', $options);
        if (!$count) {
            $count = 0;
        }
        return $count;
    }

    /**
     * TODO: Move this method to service
     * Get evaluable count
     * condition: not fixed evaluation order
     *
     * @param int|null $evaluateType
     * @param int|null $termId
     * @param bool     $isAll
     *
     * @return int
     */
    function getEvaluableCntIfNotFixedEvalOrder($evaluateType, $termId, $isAll): int
    {
        // FIXME: don't call Service from Model
        /** @var EvaluationService $EvaluationService */
        $EvaluationService = ClassRegistry::init("EvaluationService");

        $options = [
            'conditions' => [
                'evaluator_user_id' => $this->my_uid,
                'team_id'           => $this->current_team_id,
                'evaluate_type'     => $evaluateType,
                'term_id'           => $termId,
                'NOT'               => [
                    ['evaluate_type' => self::TYPE_FINAL_EVALUATOR],
                    ['evaluate_type' => self::TYPE_LEADER]
                ]
            ],
            'group'      => ['term_id', 'evaluatee_user_id']
        ];
        if (is_null($evaluateType)) {
            unset($options['conditions']['evaluate_type']);
        }
        if (is_null($termId) && $isAll === true) {
            unset($options['conditions']['term_id']);
        }

        //前期以前のデータは無視する (現状の仕様上その情報に一切アクセスができないため)
        $previousStartDate = Hash::get($this->Team->Term->getPreviousTermData(), 'start_date');
        //getting timezone
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $timezone = $Team->getTimezone();

        if ($previousStartDate) {
            $options['conditions']['created >='] = AppUtil::getTimestampByTimezone($previousStartDate, $timezone);
        }

        // freeze
        $currentTermId = $this->Team->Term->getCurrentTermId();
        $previousTermId = $this->Team->Term->getPreviousTermId();
        if ($this->Team->Term->checkFrozenEvaluateTerm($currentTermId)) {
            $options['conditions']['NOT'][] = ['term_id' => $currentTermId];
        }
        if ($this->Team->Term->checkFrozenEvaluateTerm($previousTermId)) {
            $options['conditions']['NOT'][] = ['term_id' => $previousTermId];
        }
        $evaluations = $this->find('all', $options) ?? [];
        $evaluations = Hash::extract($evaluations, '{n}.Evaluation');

        // Count increase if login user can evaluate
        $count = 0;
        foreach ($evaluations as $eval) {
            if ($eval['status'] == Enum\Model\Evaluation\Status::DONE) {
                continue;
            }
            $evalStage = $EvaluationService->getEvalStageIfNotFixedEvalOrder($eval['term_id'],
                $eval['evaluatee_user_id']);
            switch ($eval['evaluate_type']) {
                case Evaluation::TYPE_ONESELF:
                    if ($evalStage == EvaluationService::STAGE_SELF_EVAL) {
                        $count++;
                    }
                    break;
                case Evaluation::TYPE_EVALUATOR:
                    if ($evalStage == EvaluationService::STAGE_EVALUATOR_EVAL) {
                        $count++;
                    }
                    break;
            }

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
                'term_id'
            ]
        ]);
        return Hash::get($res, 'Evaluation.term_id');
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
                'term_id' => $term_id
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
                'term_id' => $term_id
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
                'term_id'           => $termId,
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
            'term_id'           => $termId
        ];
        $this->updateAll(['my_turn_flg' => true], $conditions);
    }

    function setMyTurnFlgOff($termId, $evaluateeId, $targetUserId)
    {
        $conditions = [
            'evaluator_user_id' => $targetUserId,
            'evaluatee_user_id' => $evaluateeId,
            'term_id'           => $termId
        ];
        $this->updateAll(['my_turn_flg' => false], $conditions);
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
                'term_id'       => $termId,
                'evaluate_type' => self::TYPE_ONESELF,
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
                'term_id'       => $termId,
                'evaluate_type' => self::TYPE_EVALUATOR
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
                'term_id' => $termId,
                'NOT'     => [
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
                'term_id'     => $termId,
                'my_turn_flg' => true,
                'NOT'         => [
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
                'term_id'           => $termId,
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

    /**
     * @param int $termId
     * @param int $evaluateeId
     *
     * @return array
     */
    function getEvaluatorsByEvaluatee(int $termId, int $evaluateeId): array
    {
        $options = [
            'fields' =>[
                'id',
                'evaluator_user_id'
            ],
            'conditions' => [
                'term_id'           => $termId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluate_type'     => self::TYPE_EVALUATOR,
                'goal_id' => null
            ],
        ];

        $res = $this->find('all', $options);
        return Hash::extract($res, '{n}.Evaluation');
    }

    /**
     * Check whether all evaluators completed evaluating
     *
     * @param int $termId
     * @param int $evaluateeId
     *
     * @return bool
     */
    function isCompleteEvalByEvaluator(int $termId, int $evaluateeId): bool
    {
        $options = [
            'conditions' => [
                'term_id'           => $termId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluate_type'     => self::TYPE_EVALUATOR,
                'status !='         => Enum\Model\Evaluation\Status::DONE,
                'goal_id'           => null
            ],
        ];

        $res = $this->find('count', $options);
        return $res === 0;
    }

    /**
     * Check whether final evaluator completed evaluating
     *
     * @param int $termId
     * @param int $evaluateeId
     *
     * @return bool
     */
    function isCompleteEvalByFinalEvaluator(int $termId, int $evaluateeId): bool
    {
        $options = [
            'conditions' => [
                'term_id'           => $termId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluate_type'     => self::TYPE_FINAL_EVALUATOR,
                'status !='         => Enum\Model\Evaluation\Status::DONE,
                'goal_id'           => null
            ],
        ];

        $res = $this->find('count', $options);
        return $res === 0;
    }

    function getEvaluateesByEvaluator($termId, $evaluatorId)
    {
        $options = [
            'conditions' => [
                'term_id'           => $termId,
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
                'term_id'       => $termId,
                'my_turn_flg'   => true,
                'evaluate_type' => self::TYPE_ONESELF,
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
                'term_id'           => $termId,
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

    /**
     * Get unique evaluation record
     *
     * @param int  $evaluateeId
     * @param int  $evaluatorId
     * @param int  $termId
     * @param null $type
     *
     * @return array
     */
    function getUnique(int $evaluateeId, int $evaluatorId, int $termId, $type = null): array
    {
        $options = [
            'conditions' => [
                'term_id'           => $termId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluator_user_id' => $evaluatorId,
                'goal_id'           => null,
            ],
        ];

        if (is_null($type)) {
            $options['conditions']['evaluate_type !='] = self::TYPE_FINAL_EVALUATOR;
        } else {
            $options['conditions']['evaluate_type'] = $type;
        }
        $res = $this->find('first', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'Evaluation');
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

    /**
     * 評価インデックスページ用の評価一覧データを取得
     *
     * @param $termId
     * @param $userId
     *
     * @return array
     */
    function getEvaluationListForIndex(int $termId, int $userId): array
    {
        $options = [
            'conditions' => [
                'evaluatee_user_id' => $userId,
                'term_id'           => $termId,
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
            'contain'    => [
                'EvaluatorUser' => [
                    'fields' => ['first_name', 'last_name']
                ]
            ],
            'order'      => ['index_num' => 'asc'],
        ];
        $findData = $this->find('all', $options);

        $retData = [];
        foreach ($findData as $key => $val) {
            $retData[$key] = $val['Evaluation'];
            $retData[$key]['evaluator_user'] = $val['EvaluatorUser'];
        }
        return $retData;
    }

    /**
     * @param int $termId
     * @param int $evaluateeId
     *
     * @return int
     */
    function countCompletedByEvaluators(int $termId, int $evaluateeId): int
    {
        $options = [
            'conditions' => [
                'term_id'           => $termId,
                'evaluatee_user_id' => $evaluateeId,
                'evaluate_type'     => self::TYPE_EVALUATOR,
                'goal_id'           => null,
                'status'            => Enum\Model\Evaluation\Status::DONE
            ],
        ];

        $res = $this->find('count', $options);
        return $res;
    }

}
