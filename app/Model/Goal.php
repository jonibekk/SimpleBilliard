<?php
App::uses('AppModel', 'Model');
App::uses('GoalMember', 'Model');
App::uses('KeyResult', 'Model');
App::uses('Term', 'Model');
App::uses('AppUtil', 'Util');

/**
 * Goal Model
 *
 * @property User         $User
 * @property Team         $Team
 * @property GoalCategory $GoalCategory
 * @property GoalLabel    $GoalLabel
 * @property Post         $Post
 * @property KeyResult    $KeyResult
 * @property GoalMember   $GoalMember
 * @property Follower     $Follower
 * @property Evaluation   $Evaluation
 * @property ActionResult $ActionResult
 */
class Goal extends AppModel
{
    /**
     * ステータス
     */
    const STATUS_DOING = 0;
    const STATUS_PAUSE = 1;
    const STATUS_COMPLETE = 2;
    static public $STATUS = [self::STATUS_DOING => "", self::STATUS_PAUSE => "", self::STATUS_COMPLETE => ""];

    /**
     * ステータスの名前をセット
     */
    private function _setStatusName()
    {
        self::$STATUS[self::STATUS_DOING] = __("In progress");
        self::$STATUS[self::STATUS_PAUSE] = __("Interruption");
        self::$STATUS[self::STATUS_COMPLETE] = __("Completed");
    }

    /**
     * 重要度の名前をセット
     */
    private function _setPriorityName()
    {
        $this->priority_list[1] = __("1 (Very low)");
        $this->priority_list[3] = __("3 (Default)");
        $this->priority_list[5] = __("5 (Very high)");
    }

    public $priority_list = [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
    ];

    /**
     * 検索用オプションをセット
     * ここ以外での各要素の設定は不要です
     * 各タイプの最初の要素がデフォルト表示になります
     */
    public function getSearchOptions()
    {
        $res = [
            'term'     => [
                'present'  => __("Current Term"),
                'next'     => __("Next Term"),
                'previous' => __("Previous Term"),
                'before'   => __("More Previous")
            ],
            'progress' => [
                'all'        => __("All"),
                'achieved'   => __("Achieved"),
                'unachieved' => __("Unachieved")
            ],
            'order'    => [
                'new'      => __("Creation Date"),
                'action'   => __("Actions number"),
                'result'   => __("Key results number"),
                'follow'   => __("Followers number"),
                'collab'   => __("Collaborators number"),
                'progress' => __("Progress rate")
            ]
        ];
        //カテゴリ取得
        $options = [
            'conditions' => [
                'GoalCategory.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'id',
                'name'
            ],
        ];
        $goal_categories = $this->GoalCategory->find('all', $options);
        $res['category'] = ['all' => __('All')];
        foreach ($goal_categories as $val) {
            $res['category'] += [$val['GoalCategory']['id'] => __($val['GoalCategory']['name'])];
        }
        return $res;
    }

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
        'name'             => [
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 200]],
            'notBlank'  => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
        ],
        'description'      => [
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
            'maxLength' => ['rule' => ['maxLength', 2000]],
        ],
        'evaluate_flg'     => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'status'           => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'priority'         => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'          => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'photo'            => [
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
            'canProcessImage' => ['rule' => 'canProcessImage',],
        ],
        'goal_category_id' => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
        ],
        'start_date'       => [
            'dateYmd' => [
                'rule' => ['date', 'ymd']
            ]
        ],
        'end_date'         => [
            'dateYmd' => [
                'rule' => ['date', 'ymd']
            ]
        ],
        'start_value'      => [
            'maxLength' => ['rule' => ['maxLength', 15]],
            'numeric'   => ['rule' => ['numeric']]
        ],
        'target_value'     => [
            'maxLength' => ['rule' => ['maxLength', 15]],
            'numeric'   => ['rule' => ['numeric']]
        ],
    ];

    public $post_validate = [
        'end_date'  => [
            'notBlank'            => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
            'isString'            => ['rule' => 'isString'],
            'dateYmd'             => [
                'rule' => ['date', 'ymd'],
            ],
            'checkRangeTerm'      => ['rule' => ['checkRangeTerm']],
            'checkAfterKrEndDate' => ['rule' => ['checkAfterKrEndDate']],
            'startEndDate'        => ['rule' => ['customValidStartEndDate']],
        ],
        'term_type' => [
            'inList'   => ['rule' => ['inList', ['current', 'next']],],
            'notBlank' => [
                //'required' => 'create',
                'rule' => 'notBlank',
            ],
        ]
    ];

    public $update_validate = [
        'end_date'  => [
            'notBlank'            => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
            'isString'            => ['rule' => 'isString'],
            'dateYmd'             => [
                'rule' => ['date', 'ymd'],
            ],
            'checkRangeTerm'      => ['rule' => ['checkRangeTerm']],
            'checkAfterKrEndDate' => ['rule' => ['checkAfterKrEndDate']],
            'startEndDate'        => ['rule' => ['customValidStartEndDate']],
        ],
        'term_type' => [
            'inList' => [
                'rule'       => ['inList', ['current', 'next']],
                'allowEmpty' => true
            ]
        ]
    ];

    public $actsAs = [
        'Upload' => [
            'photo' => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-goal.jpg',
                'quality'     => 100,
            ]
        ]
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'GoalCategory',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Post'                => [
            'dependent' => true,
        ],
        'KeyResult'           => [
            'dependent' => true,
        ],
        'ActionResult'        => [
            'dependent' => true,
        ],
        'ActionResultCount'   => [
            'className' => 'ActionResult',
        ],
        'IncompleteKeyResult' => [
            'className' => 'KeyResult'
        ],
        'CompleteKeyResult'   => [
            'className' => 'KeyResult'
        ],
        'GoalMember'          => [
            'dependent' => true,
        ],
        'Leader'              => [
            'className' => 'GoalMember',
        ],
        'MyCollabo'           => [
            'className' => 'GoalMember',
        ],
        'Follower'            => [
            'dependent' => true,
        ],
        'MyFollow'            => [
            'className' => 'Follower',
        ],
        'Evaluation',
        'GoalLabel'           => [
            'dependent' => true,
        ]
    ];

    /**
     * hasOne associations
     */
    public $hasOne = [
        'TopKeyResult'  => [
            'className' => 'KeyResult',
        ],
        'TargetCollabo' => [
            'className' => 'GoalMember',
        ],
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setStatusName();
        $this->_setPriorityName();
    }

    /**
     * 評価期間内かチェック
     *
     * @param  array $date
     *
     * @return bool
     */
    function checkRangeTerm(array $date)
    {
        $date = array_shift($date);
        $goalTerm = $this->getGoalTermFromPost($this->data);

        return $goalTerm['start_date'] <= $date && $date <= $goalTerm['end_date'];
    }

    /**
     * 終了日が開始日以前の日付でないか
     *
     * @param  array $val
     *
     * @return bool
     */
    function customValidStartEndDate($val)
    {
        $endDate = array_shift($val);
        $termType = Hash::get($this->data, 'Goal.term_type');
        if (empty($endDate) || empty($termType)) {
            return true;
        }

        $goalId = Hash::get($this->data, 'Goal.id');

        if (empty($goalId)) {
            if ($termType == Term::TERM_TYPE_CURRENT) {
                $startDateInt = (int)date('Ymd');
            } else {
                $term = $this->Team->Term->getNextTermData();
                $startDateInt = (int)date('Ymd', strtotime($term['start_date']));
            }
        } else {
            $goal = $this->getById($goalId);
            if (empty($goal)) {
                return true;
            }
            $startDateInt = (int)date('Ymd', strtotime($goal['start_date']));
        }

        $endDateInt = (int)date('Ymd', strtotime($endDate));
        if ($endDateInt < $startDateInt) {
            $this->invalidate('end_date', __("Please input end date after start date."));
            return false;
        }
        return true;
    }

    /**
     * ゴールに紐づく各KRの終了日より前の日付ではないか
     *
     * @param string $date
     *
     * @return bool
     */
    function checkAfterKrEndDate($date)
    {
        $date = array_shift($date);
        if (empty($this->data['Goal']['id'])) {
            return true;
        }
        $goalId = $this->data['Goal']['id'];
        $goal = Hash::extract($this->findById($goalId), 'Goal');
        if (empty($goal)) {
            return true;
        }
        $keyResults = Hash::extract($this->KeyResult->getKeyResults($goalId), '{n}.KeyResult');
        if (empty($keyResults)) {
            return true;
        }
        // 該当ゴールの評価期間取得
        foreach ($keyResults as $kr) {
            if ($date < $kr['end_date']) {
                $this->invalidate('end_date', __("Please input Goal end date later than Key Result end date"));
                return false;
            }
        }
        return true;
    }

    /**
     * ゴール登録処理
     * - ゴールのバリデーション(エラーの場合はfalseを返却)
     * - ゴールの期間の取得
     * - ゴールの開始日、終了日をunixtimeに変換
     * - 評価期間またぎチェック(またいでいる場合はfalseを返却)
     * - TKRデータの生成(新規ゴールの場合)
     * - コラボレータの生成(新規ゴールの場合)
     * - ゴールの保存処理
     * - ラベルの保存処理
     * - ゴール投稿(新規ゴールの場合)
     * - キャッシュ削除
     *
     * @param $data
     *
     * @return bool
     */
    function add($data)
    {
        if (!Hash::get($data, 'Goal')) {
            return false;
        }

        $data['Goal']['team_id'] = $this->current_team_id;
        $data['Goal']['user_id'] = $this->my_uid;

        if ($this->validateGoalPOST($data['Goal']) !== true) {
            return false;
        }

        $goal_term = $this->getGoalTermFromPost($data);

        $data = $this->convertGoalDateFromPost($data, $goal_term, $data['Goal']['term_type']);

        $data = $this->buildTopKeyResult($data, $goal_term);
        $data = $this->buildGoalMemberDataAsLeader($data);

        // setting default image if default image is chosen and image is not selected.
        if (Hash::get($data, 'Goal.img_url') && !Hash::get($data, 'Goal.photo')) {
            $data['Goal']['photo'] = $data['Goal']['img_url'];
            unset($data['Goal']['img_url']);
        }
        $this->create();
        $isSuccess = (bool)$this->saveAll($data);
        $newGoalId = $this->getLastInsertID();

        if (!$newGoalId) {
            return false;
        }
        if (Hash::get($data, 'Label')) {
            $isSuccess = $isSuccess && (bool)$this->GoalLabel->saveLabels($newGoalId, $data['Label']);
        }

        $isSuccess = $isSuccess && (bool)$this->Post->addGoalPost(Post::TYPE_CREATE_GOAL, $newGoalId);

        return (bool)$isSuccess;
    }

    /**
     * Postされたデータからゴールの期間を取得
     *
     * @param $data
     *
     * @return array|null
     */
    function getGoalTermFromPost($data)
    {
        $isNextTerm = (isset($data['Goal']['term_type']) && $data['Goal']['term_type'] == 'next');
        $goal_term = null;
        if ($isNextTerm) {
            $goal_term = $this->Team->Term->getNextTermData();
        } else {
            $goal_term = $this->Team->Term->getCurrentTermData();
        }
        return $goal_term;
    }

    /**
     * Postされたデータからゴールの開始日、終了日をunixtimeに変換
     *
     * @param array $data
     * @param array $goalTerm
     * @param       $termType
     *
     * @return array
     */
    function convertGoalDateFromPost($data, $goalTerm, $termType)
    {
        // 今期であれば現在日時、来期であれば来期の開始日をゴールの開始日とする
        if ($termType == 'current') {
            $data['Goal']['start_date'] = AppUtil::todayDateYmdLocal($this->Team->getTimezone());
        } else {
            $data['Goal']['start_date'] = $goalTerm['start_date'];
        }

        if (empty($data['Goal']['end_date'])) {
            //指定なしの場合は期の終了日
            $data['Goal']['end_date'] = $goalTerm['end_date'];
        }
        return $data;
    }

    /**
     * 新規保存用のtKRデータを生成
     *
     * @param array $data
     * @param array $goal_term
     *
     * @return array
     */
    function buildTopKeyResult($data, $goal_term, $add_new = true)
    {
        //tKRを保存
        if (!Hash::get($data, 'KeyResult.0')) {
            return $data;
        }

        if ($add_new) {
            $data['KeyResult'][0]['priority'] = 5;
            $data['KeyResult'][0]['tkr_flg'] = true;
            $data['KeyResult'][0]['user_id'] = $this->my_uid;
            $data['KeyResult'][0]['team_id'] = $this->current_team_id;
            $data['KeyResult'][0]['current_value'] = $data['KeyResult'][0]['start_value'];
        }

        // Set static value when Complete/Incomplete
        if ($data['KeyResult'][0]['value_unit'] == KeyResult::UNIT_BINARY) {
            $data['KeyResult'][0]['start_value'] = 0;
            $data['KeyResult'][0]['target_value'] = 1;
            $data['KeyResult'][0]['current_value'] = 0;
        }

        if (!Hash::get($data, 'KeyResult.0.start_date')) {
            $data['KeyResult'][0]['start_date'] = $data['Goal']['start_date'];
        }
        if (!Hash::get($data, 'KeyResult.0.end_date')) {
            $data['KeyResult'][0]['end_date'] = $data['Goal']['end_date'];
        }
        return $data;
    }

    /**
     * コラボレータをタイプ　リーダーで生成
     *
     * @param $data
     *
     * @return array
     */
    function buildGoalMemberDataAsLeader($data)
    {
        $data['GoalMember'][0]['user_id'] = $this->my_uid;
        $data['GoalMember'][0]['team_id'] = $this->current_team_id;
        $data['GoalMember'][0]['type'] = GoalMember::TYPE_OWNER;
        // リクエストではbooleanではなく"true","false"の文字列が渡ってくる
        $data['GoalMember'][0]['is_wish_approval'] = Hash::get($data, 'Goal.is_wish_approval') === "true";

        $priority = Hash::get($data, 'Goal.priority');
        if ($priority !== null) {
            $data['GoalMember'][0]['priority'] = $priority;
        }
        return $data;
    }

    /**
     * オーナー権限チェック
     *
     * @param $id
     *
     * @return bool
     * @throws RuntimeException
     */
    function isPermittedAdmin($id)
    {
        $this->id = $id;
        if (!$this->exists()) {
            throw new RuntimeException(__("This Goal doesn't exist."));
        }
        if (!$this->isOwner($this->my_uid, $id)) {
            throw new RuntimeException(__("You don't have a permission to edit this Goal."));
        }
        return true;
    }

    function isNotExistsEvaluation($goal_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id
            ]
        ];
        $res = $this->Evaluation->find('first', $options);
        if (!empty($res)) {
            throw new RuntimeException(__("You can't change the Goal in the evaluation."));
        }
        return true;
    }

    function getAddData($id)
    {
        $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $end_date = $this->Team->Term->getCurrentTermData()['end_date'];
        $options = [
            'conditions' => [
                'Goal.id' => $id,
            ],
            'contain'    => [
                'KeyResult'  => [
                    'conditions' => [
                        'KeyResult.end_date >=' => $start_date,
                        'KeyResult.end_date <=' => $end_date,
                        'KeyResult.team_id'     => $this->current_team_id,
                    ]
                ],
                'GoalMember' => [
                    'conditions' => [
                        'GoalMember.user_id' => $this->my_uid
                    ]
                ],
            ]
        ];
        $res = $this->find('first', $options);

        //KRの数値を変換
        if (!empty($res['KeyResult'])) {
            foreach ($res['KeyResult'] as $k => $k_val) {
                $res['KeyResult'][$k]['start_value'] = (double)$k_val['start_value'];
                $res['KeyResult'][$k]['current_value'] = (double)$k_val['current_value'];
                $res['KeyResult'][$k]['target_value'] = (double)$k_val['target_value'];
            }
        }
        //期間表示名をセット
        $res['Goal']['term_text'] = $this->Team->Term->getTermText($res['Goal']['start_date'],
            $res['Goal']['end_date']);
        return $res;
    }

    /**
     * user_idからgoal_idを取得する
     *
     * @param $user_id
     * @param $team_id
     *
     * @return array|null
     */
    function getGoalIdFromUserId($user_id, $team_id)
    {
        $options = [
            'fields'     => ['id'],
            'conditions' => [
                'Goal.user_id'     => $user_id,
                'Goal.team_id'     => $team_id,
                'Goal.end_date >=' => $this->Team->Term->getCurrentTermData()['start_date'],
                'Goal.end_date <=' => $this->Team->Term->getCurrentTermData()['end_date'],
                'Goal.del_flg'     => 0,
            ],
        ];
        return $this->find('list', $options);
    }

    /**
     * 自分が作成したゴール取得
     *
     * @param null   $limit
     * @param int    $page
     * @param string $type
     * @param null   $user_id
     * @param int    $start_date
     * @param int    $end_date
     * @param null   $kr_limit
     *
     * @return array
     */
    function getMyGoals(
        $limit = null,
        $page = 1,
        $type = "all",
        $user_id = null,
        $start_date = null,
        $end_date = null,
        $kr_limit = null
    )
    {
        $user_id = !$user_id ? $this->my_uid : $user_id;
        $start_date = !$start_date ? $this->Team->Term->getCurrentTermData()['start_date'] : $start_date;
        $end_date = !$end_date ? $this->Team->Term->getCurrentTermData()['end_date'] : $end_date;

        // get goal ids for right column
        $goal_ids = $this->GoalMember->getIncompleteGoalIdsForRightColumn($limit, $page, $user_id, $start_date,
            $end_date);

        $options = [
            'conditions' => [
                'Goal.id' => $goal_ids,
            ],
            'contain'    => [
                'MyCollabo'           => [
                    'conditions' => [
                        'MyCollabo.user_id' => $this->my_uid
                    ]
                ],
                'KeyResult'           => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.end_date >=' => $start_date,
                        'KeyResult.end_date <=' => $end_date,
                    ],
                    'order'      => [
                        'KeyResult.completed IS NULL DESC',
                        'KeyResult.tkr_flg DESC',
                        'KeyResult.priority DESC',
                        'KeyResult.end_date ASC',
                    ],
                ],
                'IncompleteKeyResult' => [
                    'conditions' => [
                        'IncompleteKeyResult.completed'   => null,
                        'IncompleteKeyResult.end_date >=' => $start_date,
                        'IncompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'IncompleteKeyResult.id'
                    ]
                ],
                'CompleteKeyResult'   => [
                    'conditions' => [
                        'NOT'                           => [
                            'CompleteKeyResult.completed' => null,
                        ],
                        'CompleteKeyResult.end_date >=' => $start_date,
                        'CompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'CompleteKeyResult.id'
                    ]
                ],
                'Evaluation'          => [
                    'conditions' => [
                        'Evaluation.evaluatee_user_id' => $user_id,
                    ],
                    'fields'     => ['Evaluation.id'],
                    'limit'      => 1,
                ],
                'TargetCollabo'       => [
                    'fields'     => [
                        'TargetCollabo.id',
                        'TargetCollabo.user_id',
                        'TargetCollabo.type',
                        'TargetCollabo.approval_status',
                        'TargetCollabo.is_wish_approval',
                        'TargetCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['TargetCollabo.user_id' => $user_id],
                ],
            ],
        ];
        if ($kr_limit) {
            $options['contain']['KeyResult']['limit'] = $kr_limit;
        }
        if ($type == "count") {
            unset($options['contain']);
            return $this->find($type, $options);
        }
        $res = $this->find('all', $options);

        /**
         * ソート
         * ソートは優先順位が低いものから処理する
         */

        //・第２優先ソート【重要度】
        //　重要度が高→低
        $res = $this->sortPriority($res);

        return $res;
    }

    /**
     * 自分が作成した前期の未評価ゴール取得
     *
     * @param null   $limit
     * @param int    $page
     * @param string $type
     * @param null   $kr_limit
     *
     * @return array
     */
    function getMyPreviousGoals($limit = null, $page = 1, $type = "all", $kr_limit = null)
    {
        $term = $this->Team->Term->getPreviousTermData();
        $start_date = $term['start_date'];
        $end_date = $term['end_date'];

        //自分がリーダーの未評価前期ゴールリストを取得
        $options = [
            'conditions' => [
                'Goal.user_id'     => $this->my_uid,
                'Goal.team_id'     => $this->current_team_id,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'fields'     => [
                'Goal.id',
                'Evaluation.status',
            ],
            'joins'      => [
                [
                    'type'       => 'left',
                    'table'      => 'evaluations',
                    'alias'      => 'Evaluation',
                    'conditions' => [
                        'Evaluation.goal_id = Goal.id',
                        'Evaluation.del_flg' => 0,
                    ],
                ],
            ],
            'group'      => [
                'Goal.id'
            ],
        ];
        $res = $this->find('all', $options);
        $goal_ids = [];
        foreach ($res as $record) {
            if (Hash::get($record, 'Evaluation.status') != 2) {
                $goal_ids[] = $record['Goal']['id'];
            }
        }

        //自分がコラボってるの未評価前期ゴールリストを取得
        $options = [
            'conditions' => [
                'Goal.id'          => $this->GoalMember->getCollaboGoalList($this->my_uid, false),
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'fields'     => [
                'Goal.id',
                'Evaluation.status',
            ],
            'joins'      => [
                [
                    'type'       => 'left',
                    'table'      => 'evaluations',
                    'alias'      => 'Evaluation',
                    'conditions' => [
                        'Evaluation.goal_id = Goal.id',
                        'Evaluation.del_flg' => 0,
                    ],
                ],
            ],
            'group'      => [
                'Goal.id'
            ],
        ];
        $res = $this->find('all', $options);
        foreach ($res as $record) {
            if (Hash::get($record, 'Evaluation.status') != 2) {
                $goal_ids[] = $record['Goal']['id'];
            }
        }

        //ゴール付加情報を取得
        $options = [
            'conditions' => [
                'Goal.id' => $goal_ids,
            ],
            'contain'    => [
                'MyCollabo'           => [
                    'conditions' => [
                        'MyCollabo.user_id' => $this->my_uid
                    ]
                ],
                'KeyResult'           => [
                    'order' => [
                        'KeyResult.progress ASC',
                        'KeyResult.start_date ASC',
                        'KeyResult.end_date ASC',
                        'KeyResult.priority DESC',
                    ],
                ],
                'IncompleteKeyResult' => [
                    'conditions' => [
                        'IncompleteKeyResult.completed'   => null,
                        'IncompleteKeyResult.end_date >=' => $start_date,
                        'IncompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'IncompleteKeyResult.id'
                    ]
                ],
                'CompleteKeyResult'   => [
                    'conditions' => [
                        'NOT'                           => [
                            'CompleteKeyResult.completed' => null,
                        ],
                        'CompleteKeyResult.end_date >=' => $start_date,
                        'CompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'CompleteKeyResult.id'
                    ]
                ],
                'Evaluation'          => [
                    'conditions' => [
                        'Evaluation.evaluatee_user_id' => $this->my_uid,
                    ],
                    'fields'     => ['Evaluation.id'],
                    'limit'      => 1,
                ],
                'TargetCollabo'       => [
                    'fields'     => [
                        'TargetCollabo.id',
                        'TargetCollabo.user_id',
                        'TargetCollabo.type',
                        'TargetCollabo.approval_status',
                        'TargetCollabo.is_wish_approval',
                        'TargetCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['TargetCollabo.user_id' => $this->my_uid],
                ],
            ],
            'limit'      => $limit,
            'page'       => $page
        ];
        if ($type == "count") {
            unset($options['contain']);
            return $this->find('count', $options);
        }
        if ($kr_limit) {
            $options['contain']['KeyResult']['limit'] = $kr_limit;
        }

        return $this->find('all', $options);
    }

    /**
     * 期限が近→遠　で並べ替え
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortEndDate($goals, $direction = SORT_ASC)
    {
        $end_date_list = array();
        foreach ($goals as $key => $goal) {
            if (isset($goal['Goal']['end_date'])) {
                $end_date_list[$key] = $goal['Goal']['end_date'];
            } else {
                //基準なしは下に
                $end_date_list[$key] = 99999999999999999;
            }
        }
        array_multisort($end_date_list, $direction, SORT_NUMERIC, $goals);
        return $goals;
    }

    /**
     * 進捗更新日で並べ替え 近→遠
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortModified($goals, $direction = SORT_DESC)
    {
        $modify_list = array();
        foreach ($goals as $key => $goal) {
            $modify_list[$key] = $goal['Goal']['modified'];
        }
        array_multisort($modify_list, $direction, SORT_NUMERIC, $goals);
        return $goals;
    }

    /**
     * 重要度が高→低 で並べ替え
     *
     * @param     $goals
     * @param int $direction
     *
     * @return bool
     */
    function sortPriority($goals, $direction = SORT_DESC)
    {
        $priority_list = array();
        foreach ($goals as $key => $goal) {
            if (isset($goal['MyCollabo'][0]['priority'])) {
                $priority_list[$key] = $goal['MyCollabo'][0]['priority'];
            }
        }
        if (!empty($priority_list)) {
            array_multisort($priority_list, $direction, SORT_NUMERIC, $goals);
        }
        return $goals;
    }

    function getMyCreateGoalsList($uid)
    {
        return $this->find(
            'list',
            [
                'conditions' => [
                    'Goal.user_id' => $uid
                ],
                'fields'     => [
                    'id'
                ]
            ]
        );
    }

    /**
     * 自分がこらぼったゴール取得
     *
     * @param null   $limit
     * @param int    $page
     * @param string $type
     * @param null   $user_id
     * @param int    $start_date
     * @param int    $end_date
     * @param null   $kr_limit
     *
     * @return array
     */
    function getMyCollaboGoals(
        $limit = null,
        $page = 1,
        $type = "all",
        $user_id = null,
        $start_date = null,
        $end_date = null,
        $kr_limit = null
    )
    {
        $user_id = !$user_id ? $this->my_uid : $user_id;
        $start_date = !$start_date ? $this->Team->Term->getCurrentTermData()['start_date'] : $start_date;
        $end_date = !$end_date ? $this->Team->Term->getCurrentTermData()['end_date'] : $end_date;

        // get goal ids for right column
        $goal_ids = $this->GoalMember->getIncompleteCollaboGoalIds($user_id, $start_date, $end_date, $limit, $page);

        if ($type == "count") {
            return $this->getCollaboGoalsByGoalId($goal_ids, $limit, $page, $type, $start_date, $end_date);
        }
        $res = $this->getCollaboGoalsByGoalId($goal_ids, $limit, $page, $type, $start_date, $end_date, $kr_limit);
        $res = $this->sortPriority($res);

        return $res;
    }

    function getGoalsWithAction($user_id, $action_limit = MY_PAGE_ACTION_NUMBER, $start_date = null, $end_date = null)
    {
        //対象が自分だった場合プラスボタンを出力する関係上、アクション件数を-1にする
        if ($user_id == $this->my_uid) {
            $action_limit--;
        }
        $goal_ids = $this->GoalMember->getCollaboGoalList($user_id, true);
        $start_date = !$start_date ? $this->Team->Term->getCurrentTermData()['start_date'] : $start_date;
        $end_date = !$end_date ? $this->Team->Term->getCurrentTermData()['end_date'] : $end_date;

        $options = [
            'conditions' => [
                'Goal.id'          => $goal_ids,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'order'      => [
                'Goal.completed ASC',
                'Goal.end_date DESC',
                'GoalMember.priority DESC',
                'Goal.created DESC'
            ],
            'fields'     => [
                'Goal.id',
                'Goal.user_id',
                'Goal.name',
                'Goal.photo_file_name',
                'Goal.start_date',
                'Goal.end_date'
            ],
            'contain'    => [
                'ActionResult'      => [
                    'fields'           => [
                        'ActionResult.id',
                        'ActionResult.name',
                        'ActionResult.photo1_file_name',
                        'ActionResult.photo2_file_name',
                        'ActionResult.photo3_file_name',
                        'ActionResult.photo4_file_name',
                        'ActionResult.photo5_file_name',
                    ],
                    'limit'            => $action_limit,
                    'conditions'       => ['ActionResult.user_id' => $user_id],
                    'order'            => ['ActionResult.created desc'],
                    'Post'             => [
                        'fields' => ['Post.id']
                    ],
                    'ActionResultFile' => [
                        'conditions' => [
                            'ActionResultFile.index_num' => 0
                        ],
                        'AttachedFile'
                    ],
                ],
                'KeyResult'         => [
                    'fields'     => [
                        'KeyResult.id',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.start_value',
                        'KeyResult.target_value',
                        'KeyResult.current_value',
                    ],
                    'conditions' => [
                        'KeyResult.end_date >=' => $start_date,
                        'KeyResult.end_date <=' => $end_date,
                    ]
                ],
                'ActionResultCount' => [
                    'fields'     => ['ActionResultCount.id'],
                    'conditions' => ['ActionResultCount.user_id' => $user_id]
                ],
                'MyCollabo'         => [
                    'fields'     => [
                        'MyCollabo.id',
                        'MyCollabo.user_id',
                        'MyCollabo.type',
                        'MyCollabo.approval_status',
                        'MyCollabo.is_wish_approval',
                        'MyCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['MyCollabo.user_id' => $this->my_uid]
                ],
                'MyFollow'          => [
                    'fields'     => ['MyFollow.id'],
                    'conditions' => ['MyFollow.user_id' => $this->my_uid]
                ],
                'Leader'            => [
                    'fields'     => [
                        'Leader.id',
                        'Leader.user_id',
                        'Leader.type',
                        'Leader.approval_status',
                        'Leader.is_wish_approval',
                        'Leader.is_target_evaluation'
                    ],
                    'conditions' => ['Leader.type' => GoalMember::TYPE_OWNER],
                ],
                'GoalMember'        => [
                    'fields'     => [
                        'GoalMember.id',
                        'GoalMember.user_id',
                        'GoalMember.type',
                        'GoalMember.approval_status',
                        'GoalMember.is_wish_approval',
                        'GoalMember.is_target_evaluation'
                    ],
                    'conditions' => ['GoalMember.type' => GoalMember::TYPE_COLLABORATOR]
                ],
                'TargetCollabo'     => [
                    'fields'     => [
                        'TargetCollabo.id',
                        'TargetCollabo.user_id',
                        'TargetCollabo.type',
                        'TargetCollabo.approval_status',
                        'TargetCollabo.is_wish_approval',
                        'TargetCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['TargetCollabo.user_id' => $user_id],
                ],
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => ['GoalMember.goal_id = Goal.id'],
                ]
            ]
        ];
        $goals = $this->find('all', $options);
        return Hash::combine($goals, '{n}.Goal.id', '{n}');
    }

    function getMyFollowedGoals(
        $limit = null,
        $page = 1,
        $type = 'all',
        $user_id = null,
        $start_date = null,
        $end_date = null
    )
    {
        $user_id = !$user_id ? $this->my_uid : $user_id;
        $start_date = !$start_date ? $this->Team->Term->getCurrentTermData()['start_date'] : $start_date;
        $end_date = !$end_date ? $this->Team->Term->getCurrentTermData()['end_date'] : $end_date;
        $follow_goal_ids = $this->Follower->getFollowList($user_id);
        $coaching_goal_ids = $this->Team->TeamMember->getCoachingGoalList($user_id);
        $collabo_goal_ids = $this->GoalMember->getCollaboGoalList($user_id, true);
        //フォローしているゴールとコーチングしているゴールをマージして、そこからコラボしているゴールを除外したものが
        //フォロー中ゴールとなる
        $goal_ids = $follow_goal_ids + $coaching_goal_ids;
        //exclude collab goal
        foreach ($collabo_goal_ids as $k => $v) {
            unset($goal_ids[$k]);
        }
        if ($type == "count") {
            return $this->getByGoalId($goal_ids, $limit, $page, $type, $start_date, $end_date);
        }
        $goals = $this->getByGoalId($goal_ids, $limit, $page, $type, $start_date, $end_date);

        $res = $this->sortModified($goals);
        $res = $this->sortEndDate($res);

        return $res;
    }

    function setFollowGoalApprovalFlag($goals)
    {
        foreach ($goals as $key => $goal) {
            if (isset($goal['GoalMember']['approval_status'])) {
                $goals[$key]['Goal']['owner_approval_flag'] = $goal['GoalMember']['approval_status'];
            }
        }
        return $goals;
    }

    /**
     * ゴールと紐付くKRを進捗付きで返す
     *
     * @param array|int $goalIds
     * @param int       $userId defaultはログインユーザ
     *
     * @return array
     */
    function getGoalAndKr($goalIds, int $userId = null): array
    {
        $userId = $userId ?? $this->my_uid;

        $options = [
            'conditions' => [
                'Goal.id'      => $goalIds,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'KeyResult'     => [
                    'fields' => [
                        'KeyResult.id',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.current_value',
                        'KeyResult.target_value',
                        'KeyResult.start_value',
                        'KeyResult.completed',
                    ],
                ],
                'GoalMember'    => [
                    'conditions' => [
                        'GoalMember.user_id' => $userId
                    ]
                ],
                'TargetCollabo' => [
                    'fields'     => [
                        'TargetCollabo.id',
                        'TargetCollabo.user_id',
                        'TargetCollabo.type',
                        'TargetCollabo.approval_status',
                        'TargetCollabo.is_wish_approval',
                        'TargetCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['TargetCollabo.user_id' => $userId],
                ],
            ]
        ];
        $res = $this->find('all', $options);

        //calc progress
        foreach ($res as $key => $goal) {
            $res[$key]['Goal']['progress'] = $this->getProgress($goal['KeyResult']);
        }
        return $res;
    }

    /**
     * @param        $goal_ids
     * @param null   $limit
     * @param int    $page
     * @param string $type
     * @param int    $start_date
     * @param int    $end_date
     * @param null   $kr_limit
     *
     * @return array|null
     */
    function getByGoalId(
        $goal_ids,
        $limit = null,
        $page = 1,
        $type = "all",
        $start_date = null,
        $end_date = null,
        $kr_limit = null
    )
    {
        $start_date = !$start_date ? $this->Team->Term->getCurrentTermData()['start_date'] : $start_date;
        $end_date = !$end_date ? $this->Team->Term->getCurrentTermData()['end_date'] : $end_date;
        $options = [
            'conditions' => [
                'Goal.id'          => $goal_ids,
                'Goal.team_id'     => $this->current_team_id,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
                'Goal.completed'   => null,
            ],
            'page'       => $page,
            'limit'      => $limit,
            'contain'    => [
                'KeyResult'           => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.end_date >=' => $start_date,
                        'KeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'KeyResult.id',
                        'KeyResult.name',
                        'KeyResult.end_date',
                        'KeyResult.action_result_count',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.completed',
                        'KeyResult.start_value',
                        'KeyResult.target_value',
                        'KeyResult.current_value',
                    ],
                    'order'      => [
                        'KeyResult.progress ASC',
                        'KeyResult.start_date ASC',
                        'KeyResult.end_date ASC',
                        'KeyResult.priority DESC',
                    ],
                ],
                'IncompleteKeyResult' => [
                    'conditions' => [
                        'IncompleteKeyResult.completed'   => null,
                        'IncompleteKeyResult.end_date >=' => $start_date,
                        'IncompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'IncompleteKeyResult.id'
                    ]
                ],
                'CompleteKeyResult'   => [
                    'conditions' => [
                        'NOT'                           => [
                            'CompleteKeyResult.completed' => null,
                        ],
                        'CompleteKeyResult.end_date >=' => $start_date,
                        'CompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'CompleteKeyResult.id'
                    ]
                ],
                'MyCollabo'           => [
                    'conditions' => [
                        'MyCollabo.user_id' => $this->my_uid
                    ]
                ],
                'Leader'              => [
                    'conditions' => ['Leader.type' => GoalMember::TYPE_OWNER],
                    'fields'     => ['Leader.id', 'Leader.user_id', 'Leader.approval_status'],
                ],
                'TargetCollabo'       => [
                    'fields'     => [
                        'TargetCollabo.id',
                        'TargetCollabo.user_id',
                        'TargetCollabo.type',
                        'TargetCollabo.approval_status',
                        'TargetCollabo.is_wish_approval',
                        'TargetCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['TargetCollabo.user_id' => $this->my_uid],
                ]
            ]
        ];

        if ($type == "count") {
            unset($options['contain']);
            return $this->find($type, $options);
        }
        if ($kr_limit) {
            $options['contain']['KeyResult']['limit'] = $kr_limit;
        }

        return $this->find('all', $options);
    }

    // for getting goal_member's goals for showing on right column
    function getCollaboGoalsByGoalId(
        $goal_ids,
        $limit = null,
        $page = 1,
        $type = "all",
        $start_date = null,
        $end_date = null,
        $kr_limit = null
    )
    {
        $start_date = !$start_date ? $this->Team->Term->getCurrentTermData()['start_date'] : $start_date;
        $end_date = !$end_date ? $this->Team->Term->getCurrentTermData()['end_date'] : $end_date;
        $options = [
            'conditions' => [
                'Goal.id' => $goal_ids,
            ],
            'contain'    => [
                'KeyResult'           => [
                    //KeyResultは期限が今期内
                    'conditions' => [
                        'KeyResult.end_date >=' => $start_date,
                        'KeyResult.end_date <=' => $end_date,
                    ],
                    'order'      => [
                        'KeyResult.progress ASC',
                        'KeyResult.start_date ASC',
                        'KeyResult.end_date ASC',
                        'KeyResult.priority DESC',
                    ],
                ],
                'IncompleteKeyResult' => [
                    'conditions' => [
                        'IncompleteKeyResult.completed'   => null,
                        'IncompleteKeyResult.end_date >=' => $start_date,
                        'IncompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'IncompleteKeyResult.id'
                    ]
                ],
                'CompleteKeyResult'   => [
                    'conditions' => [
                        'NOT'                           => [
                            'CompleteKeyResult.completed' => null,
                        ],
                        'CompleteKeyResult.end_date >=' => $start_date,
                        'CompleteKeyResult.end_date <=' => $end_date,
                    ],
                    'fields'     => [
                        'CompleteKeyResult.id'
                    ]
                ],
                'MyCollabo'           => [
                    'conditions' => [
                        'MyCollabo.user_id' => $this->my_uid
                    ]
                ],
                'Leader'              => [
                    'conditions' => ['Leader.type' => GoalMember::TYPE_OWNER],
                    'fields'     => ['Leader.id', 'Leader.user_id', 'Leader.approval_status'],
                ],
                'TargetCollabo'       => [
                    'fields'     => [
                        'TargetCollabo.id',
                        'TargetCollabo.user_id',
                        'TargetCollabo.type',
                        'TargetCollabo.approval_status',
                        'TargetCollabo.is_wish_approval',
                        'TargetCollabo.is_target_evaluation'
                    ],
                    'conditions' => ['TargetCollabo.user_id' => $this->my_uid],
                ]
            ]
        ];

        if ($type == "count") {
            unset($options['contain']);
            return $this->find($type, $options);
        }
        if ($kr_limit) {
            $options['contain']['KeyResult']['limit'] = $kr_limit;
        }

        return $this->find('all', $options);
    }

    /**
     * ゴール単独取得
     *
     * @param $id
     *
     * @return array
     */
    function getGoal($id, $collabo_user_id = null)
    {
        if (!$collabo_user_id) {
            $collabo_user_id = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'Goal.id'      => $id,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'GoalCategory',
                'Leader'     => [
                    'conditions' => ['Leader.type' => GoalMember::TYPE_OWNER],
                    'fields'     => [
                        'Leader.id',
                        'Leader.user_id',
                        'Leader.type',
                        'Leader.approval_status',
                        'Leader.is_wish_approval',
                        'Leader.is_target_evaluation',
                        'Leader.role',
                        'Leader.description',
                    ],
                    'User'       => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'GoalMember' => [
                    'conditions' => ['GoalMember.type' => GoalMember::TYPE_COLLABORATOR],
                    'fields'     => [
                        'GoalMember.id',
                        'GoalMember.user_id',
                        'GoalMember.type',
                        'GoalMember.approval_status',
                        'GoalMember.is_wish_approval',
                        'GoalMember.is_target_evaluation',
                        'GoalMember.role',
                        'GoalMember.description',
                    ],
                    'User'       => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'Follower'   => [
                    'fields' => ['Follower.id', 'Follower.user_id'],
                    'User'   => [
                        'fields' => $this->User->profileFields,
                    ]
                ],
                'MyCollabo'  => [
                    'conditions' => [
                        'MyCollabo.type'    => GoalMember::TYPE_COLLABORATOR,
                        'MyCollabo.user_id' => $collabo_user_id,
                    ],
                    'fields'     => [
                        'MyCollabo.id',
                        'MyCollabo.user_id',
                        'MyCollabo.type',
                        'MyCollabo.approval_status',
                        'MyCollabo.is_wish_approval',
                        'MyCollabo.is_target_evaluation',
                        'MyCollabo.role',
                        'MyCollabo.description',
                    ],
                ],
                'MyFollow'   => [
                    'conditions' => [
                        'MyFollow.user_id' => $collabo_user_id,
                    ],
                    'fields'     => [
                        'MyFollow.id',
                    ],
                ],
                'KeyResult'  => [
                    'fields' => [
                        'KeyResult.id',
                        'KeyResult.name',
                        'KeyResult.progress',
                        'KeyResult.priority',
                        'KeyResult.start_value',
                        'KeyResult.target_value',
                        'KeyResult.current_value',
                        'KeyResult.completed',
                    ],
                    'order'  => ['KeyResult.completed' => 'asc'],
                ],
                'User'       => [
                    'fields' => $this->User->profileFields,
                ]
            ]
        ];
        return $this->find('first', $options);
    }

    function getGoalMinimum($id)
    {
        $options = [
            'conditions' => [
                'Goal.id'      => $id,
                'Goal.team_id' => $this->current_team_id,
            ],
        ];
        return $this->find('first', $options);
    }

    /**
     * $goal_id のゴール情報 + ユーザー情報を取得
     *
     * @param $goal_id
     *
     * @return array|null
     */
    function getGoalsWithUser($goal_id)
    {
        $options = [
            'conditions' => [
                'Goal.id'      => $goal_id,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => ['User'],
        ];
        return $this->find('all', $options);
    }

    /**
     * ゴール検索
     *
     * @param        $conditions
     * @param        $offset
     * @param        $limit
     * @param string $order
     *
     * @return array
     */
    function search($conditions, $offset, $limit, $order = "")
    {
        $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $end_date = $this->Team->Term->getCurrentTermData()['end_date'];

        $options = [
            'conditions' => [
                'Goal.team_id'     => $this->current_team_id,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'fields'     => [
                'Goal.id',
                'Goal.user_id',
                'Goal.name',
                'Goal.photo_file_name',
                'Goal.completed',
            ],
            'order'      => ['Goal.created desc'],
            'limit'      => $limit,
            'offset'     => $offset,
        ];
        //
        $options = $this->setFilter($options, $conditions, $order);

        $goals = $this->find('all', $options);
        return Hash::extract($goals, '{n}.Goal');
    }

    /**
     * ゴール件数取得
     *
     * @param $conditions
     *
     * @return array|int|null
     */
    function countSearch($conditions)
    {
        $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $end_date = $this->Team->Term->getCurrentTermData()['end_date'];
        $options = [
            'conditions' => [
                'Goal.team_id'     => $this->current_team_id,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'fields'     => ['Goal.user_id'],
        ];
        $options = $this->setFilter($options, $conditions);
        $res_count = $this->find('count', $options);
        return $res_count ? $res_count : 0;
    }

    /**
     * ゴール検索条件作成
     *
     * @param $options
     * @param $conditions
     * @param $order
     *
     * @return mixed
     */
    function setFilter(array $options, array $conditions, $order = "")
    {
        // キーワード(ゴール名)
        $keyword = Hash::get($conditions, 'keyword');
        if (!empty($keyword)) {
            $options['conditions']['Goal.name LIKE'] = "%$keyword%";
        }

        // ゴールラベル
        // パフォーマンス向上の為、ラベル名ではなくラベルIDによってゴール検索を行う
        $labelNames = Hash::get($conditions, 'labels');
        $labelIds = $this->GoalLabel->Label->findIdsByNames($labelNames);
        if (!empty($labelIds)) {
            $options['joins'] = [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_labels',
                    'alias'      => 'GoalLabel',
                    'conditions' => [
                        'GoalLabel.goal_id = Goal.id',
                        'GoalLabel.del_flg'  => 0,
                        'GoalLabel.label_id' => $labelIds,
                    ],
                ],
            ];
            $options['group'] = ['Goal.id'];
        }

        //期間指定
        switch (Hash::get($conditions, 'term')) {
            case 'previous':
                $previous_term = $this->Team->Term->getPreviousTermData();
                if (!empty($previous_term)) {
                    $options['conditions']['Goal.end_date >='] = $previous_term['start_date'];
                    $options['conditions']['Goal.end_date <='] = $previous_term['end_date'];
                } else {
                    $current_term_start = $this->Team->Term->getCurrentTermData()['start_date'];
                    $options['conditions']['Goal.end_date <'] = $current_term_start;
                }
                break;
            case 'next':
                $next_term = $this->Team->Term->getNextTermData();
                if (!empty($next_term)) {
                    $options['conditions']['Goal.end_date >='] = $next_term['start_date'];
                    $options['conditions']['Goal.end_date <='] = $next_term['end_date'];
                } else {
                    $current_term_end = $this->Team->Term->getNextTermData()['end_date'];
                    $options['conditions']['Goal.end_date >'] = $current_term_end;
                }
                break;
            case 'before' :
                $previous_term = $this->Team->Term->getPreviousTermData();
                if (!empty($previous_term)) {
                    $options['conditions']['Goal.end_date <='] = $previous_term['start_date'];
                } else {
                    $current_term_start = $this->Team->Term->getCurrentTermData()['start_date'];
                    $options['conditions']['Goal.end_date <'] = $current_term_start;
                }
                unset($options['conditions']['Goal.end_date >=']);
                break;
        }

        //カテゴリ指定
        $category = Hash::get($conditions, 'category');
        if (!empty($category) && $category !== 'all') {
            $options['conditions']['Goal.goal_category_id'] = $category;
        }

        //進捗指定
        switch (Hash::get($conditions, 'progress')) {
            case 'achieved' :
                $options['conditions']['NOT']['Goal.completed'] = null;
                break;
            case 'unachieved' :
                $options['conditions']['Goal.completed'] = null;
                break;
        }

        //ソート指定
        if (!empty($order)) {
            switch ($order) {
                case 'action' :
                    $options['order'] = ['Goal.action_result_count desc'];
                    break;
                case 'result' :
                    $options['order'] = ['count_key_result desc'];
                    $options['fields'][] = 'count(KeyResult.id) as count_key_result';
                    $options['joins'] = [
                        [
                            'type'       => 'left',
                            'table'      => 'key_results',
                            'alias'      => 'KeyResult',
                            'conditions' => [
                                'KeyResult.goal_id = Goal.id',
                                'KeyResult.del_flg' => 0,
                                'NOT'               => ['KeyResult.completed' => null],
                            ],
                        ],
                    ];
                    $options['group'] = ['Goal.id'];
                    break;
                case 'follow' :
                    $options['order'] = ['count_follow desc'];
                    $options['fields'][] = 'count(Follower.id) as count_follow';
                    $options['joins'] = [
                        [
                            'type'       => 'left',
                            'table'      => 'followers',
                            'alias'      => 'Follower',
                            'conditions' => [
                                'Follower.goal_id = Goal.id',
                                'Follower.del_flg' => 0,
                            ],
                        ],
                    ];
                    $options['group'] = ['Goal.id'];
                    break;
                case 'collab' :
                    $options['order'] = ['count_goal_member desc'];
                    $options['fields'][] = 'count(GoalMember.id) as count_goal_member';
                    $options['joins'] = [
                        [
                            'type'       => 'left',
                            'table'      => 'goal_members',
                            'alias'      => 'GoalMember',
                            'conditions' => [
                                'GoalMember.goal_id = Goal.id',
                                'GoalMember.del_flg' => 0,
                            ],
                        ],
                    ];
                    $options['group'] = ['Goal.id'];
                    break;
                case 'progress' :
                    $options['order'] = ['cal_progress desc'];
                    $options['fields'][] = '(SUM(KeyResult.priority * KeyResult.progress)/(SUM(KeyResult.priority * 100)))*100 as cal_progress';
                    $options['joins'] = [
                        [
                            'type'       => 'left',
                            'table'      => 'key_results',
                            'alias'      => 'KeyResult',
                            'conditions' => [
                                'KeyResult.goal_id = Goal.id',
                                'KeyResult.del_flg' => 0,
                            ],
                        ],
                    ];
                    $options['group'] = ['Goal.id'];
                    break;
            }
        }
        return $options;
    }

    function getAllUserGoalProgress($goal_ids, $user_id)
    {
        $res = 0;
        $goals = $this->getGoalAndKr($goal_ids, $user_id);
        if (empty($goals)) {
            return $res;
        }

        $target_progress_total = 0;
        $current_progress_total = 0;
        foreach ($goals as $goal) {
            if (!Hash::get($goal, 'GoalMember.0.priority')) {
                continue;
            }
            $target_progress_total += $goal['GoalMember'][0]['priority'] * 100;
            $current_progress_total += $goal['GoalMember'][0]['priority'] * $goal['Goal']['progress'];
        }
        if ($target_progress_total != 0) {
            $res = round($current_progress_total / $target_progress_total, 2) * 100;
        }
        return $res;

    }

    function complete($goal_id)
    {
        $goal = $this->findById($goal_id);
        if (empty($goal)) {
            throw new RuntimeException(__("The Goal doesn't exist."));
        }
        $this->id = $goal_id;
        $this->saveField('progress', 100);
        $this->saveField('completed', REQUEST_TIMESTAMP);
        return true;
    }

    function incomplete($goal_id)
    {
        $goal = $this->findById($goal_id);
        if (empty($goal)) {
            throw new RuntimeException(__("The Goal doesn't exist."));
        }
        $goal['Goal']['completed'] = null;
        unset($goal['Goal']['modified']);
        $this->save($goal);
        return true;
    }

    function getCollaboModalItem($id)
    {
        $options = [
            'conditions' => [
                'Goal.id'      => $id,
                'Goal.team_id' => $this->current_team_id,
            ],
            'contain'    => [
                'MyCollabo' => [
                    'conditions' => [
                        'MyCollabo.type'    => GoalMember::TYPE_COLLABORATOR,
                        'MyCollabo.user_id' => $this->my_uid,
                    ],
                    'fields'     => [
                        'MyCollabo.id',
                        'MyCollabo.role',
                        'MyCollabo.description',
                        'MyCollabo.priority',
                    ],
                ],
            ],
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    function getAllUserGoal($start_date = null, $end_date = null)
    {

        if (!$start_date) {
            $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        }
        if (!$end_date) {
            $end_date = $this->Team->Term->getCurrentTermData()['end_date'];
        }
        $team_member_list = $this->Team->TeamMember->getAllMemberUserIdList();

        $options = [
            'conditions' => [
                'User.id' => $team_member_list
            ],
            'fields'     => $this->User->profileFields,
            'contain'    => [
                'LocalName'  => [
                    'conditions' => ['LocalName.language' => $this->me['language']],
                ],
                'GoalMember' => [
                    'conditions' => [
                        'GoalMember.team_id' => $this->current_team_id,
                    ],
                    'Goal'       => [
                        'conditions' => [
                            'Goal.end_date >=' => $start_date,
                            'Goal.end_date <=' => $end_date
                        ],
                        'GoalCategory',
                    ]
                ],
                'TeamMember' => [
                    'fields'     => [
                        'member_no',
                        'evaluation_enable_flg'
                    ],
                    'conditions' => [
                        'TeamMember.team_id' => $this->current_team_id
                    ],
                    'order'      => ['TeamMember.member_no DESC']
                ]
            ]
        ];
        $res = $this->GoalMember->User->find('all', $options);
        return $res;
    }

    function filterByTermId(int $termId, $gids)
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        $term = $Term->getById($termId);

        $options = [
            'conditions' => [
                'id'          => $gids,
                'end_date >=' => $term['start_date'],
                'end_date <=' => $term['end_date'],
            ],
            'fields'     => ['id']
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function isPresentTermGoal($goal_id)
    {
        if (empty($goal_id)) {
            return false;
        }
        $options = [
            'fields'     => ['start_date', 'end_date'],
            'conditions' => ['id' => $goal_id],
        ];
        $res = $this->find('first', $options);

        $end_date = $res['Goal']['end_date'];

        $is_present_term_flag = false;
        if ($end_date >= $this->Team->Term->getCurrentTermData()['start_date']
            && $end_date <= $this->Team->Term->getCurrentTermData()['end_date']
        ) {
            $is_present_term_flag = true;
        }

        return $is_present_term_flag;
    }

    function getAllMyGoalNameList($start, $end)
    {
        $goal_ids = $this->GoalMember->getCollaboGoalList($this->my_uid, true);
        $options = [
            'conditions' => [
                'id'          => $goal_ids,
                'end_date >=' => $start,
                'end_date <=' => $end,
            ],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function getGoalNameListByGoalIds($goal_ids, $with_all_opt = false, $separate_term = false)
    {
        $options = [
            'conditions' => ['id' => $goal_ids],
            'fields'     => ['id', 'name'],
            'order'      => ['created desc'],
        ];
        if (!$separate_term) {
            $res = $this->find('list', $options);
            if ($with_all_opt) {
                return [null => __('All')] + $res;
            }
            return $res;
        }
        $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $current_term_opt = $options;
        $current_term_opt['conditions']['end_date >='] = $start_date;
        $current_goals = $this->find('list', $current_term_opt);
        $before_term_opt = $options;
        $before_term_opt['conditions']['end_date <='] = $start_date;
        $before_goals = $this->find('list', $before_term_opt);
        $res = [];
        $res += $with_all_opt ? [null => __('All')] : null;
        $res += ['disable_value1' => '_separator_'];
        $res += $current_goals;
        $res += ['disable_value2' => '_separator_'];
        $res += $before_goals;
        return $res;
    }

    /**
     * ゴール名が $keyword にマッチするゴールを返す
     *
     * @param string $keyword
     * @param int    $limit
     *
     * @return array
     */
    public function getGoalsByKeyword($keyword, $limit = 10)
    {
        $keyword = trim($keyword);
        $options = [
            'conditions' => [
                'Goal.name LIKE' => '%' . $keyword . '%',
                'Goal.team_id'   => $this->current_team_id,
            ],
            'limit'      => $limit,
        ];
        return $this->find('all', $options);
    }

    /**
     * ゴール名が $keyword にマッチするゴールを select2 用のデータ形式にして返す
     *
     * @param string $keyword
     * @param int    $limit
     * @param null   $start_date
     * @param null   $end_date
     *
     * @return array
     */
    public function getGoalsSelect2($keyword, $limit = 10, $start_date = null, $end_date = null)
    {
        $goals = $this->getGoalsByKeyword($keyword, $limit, $start_date, $end_date);

        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $res = [];
        foreach ($goals as $val) {
            $data = [];
            $data['id'] = 'goal_' . $val['Goal']['id'];
            $data['text'] = $val['Goal']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Goal.photo', ['style' => 'small']);
            $res[] = $data;
        }
        return ['results' => $res];
    }

    /**
     * ゴールが属している評価期間のデータを返す
     *
     * @param $goalId
     * @param $teamId
     *
     * @return false|array|null
     */
    public function getGoalTermData($goalId, $teamId = null)
    {
        $goal = $this->findById($goalId);
        if (!$goal) {
            return false;
        }

        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        return $Term->getTermDataByDate($goal['Goal']['end_date'], true, $teamId);
    }

    /**
     * Tells if the goal end date already passed.
     *
     * @param $goalId
     * @param $teamId
     *
     * @return bool
     */
    public function isFinished($goalId, $teamId = null)
    {
        if (empty($goalId)) {
            return true;
        }
        $options = [
            'fields'     => ['start_date', 'end_date'],
            'conditions' => ['id' => $goalId],
        ];
        $res = $this->find('first', $options);
        $endDate = Hash::get($res, 'Goal.end_date');
        $goalTerm = $this->getGoalTermData($goalId, $teamId);
        $today = AppUtil::todayDateYmdLocal(Hash::get($goalTerm, 'timezone'));

        if ($today > $endDate) {
            return true;
        }
        return false;
    }

    /**
     * Tell if the goal is already completed.
     *
     * @param $goalId
     *
     * @return bool
     */
    public function isCompleted($goalId)
    {
        if (empty($goalId)) {
            return false;
        }
        $options = [
            'fields'     => ['completed'],
            'conditions' => ['id' => $goalId],
        ];
        $res = $this->find('first', $options);
        $completed = Hash::get($res, 'Goal.completed');

        if ($completed !== null) {
            return true;
        }
        return false;
    }

    public function getRelatedGoals($user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->my_uid;
        }
        $g_list = [];
        $g_list = array_merge($g_list, $this->Follower->getFollowList($user_id));
        $g_list = array_merge($g_list, $this->GoalMember->getCollaboGoalList($user_id, true));
        $g_list = array_merge($g_list, $this->User->TeamMember->getCoachingGoalList($user_id));
        return $g_list;
    }

    public function isCreatedForSetupBy($user_id)
    {
        $options = [
            'conditions' => [
                'Goal.user_id' => $user_id,
            ],
            'fields'     => ['Goal.id']
        ];

        return (bool)$this->findWithoutTeamId('first', $options);
    }

    public function getGoalsForSetupBy($user_id)
    {
        $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $end_date = $this->Team->Term->getCurrentTermData()['end_date'];
        $options = [
            'conditions' => [
                'Goal.user_id'     => $user_id,
                'Goal.team_id'     => $this->current_team_id,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'fields'     => [
                'Goal.id',
                'Goal.name',
                'Goal.photo_file_name'
            ]
        ];
        return $this->find('all', $options);
    }

    /**
     * Return true if user has created goal in current term.
     * @param int $userId
     * @return bool
     */
    public function isGoalCreatedInCurrentTerm(int $userId): bool
    {
        $startDate = $this->Team->Term->getCurrentTermData()['start_date'];
        $endDate = $this->Team->Term->getCurrentTermData()['end_date'];
        $options = [
            'conditions' => [
                'Goal.user_id'     => $userId,
                'Goal.team_id'     => $this->current_team_id,
                'Goal.end_date >=' => $startDate,
                'Goal.end_date <=' => $endDate,
            ],
            'fields'     => [
                'Goal.id',
                'Goal.name',
                'Goal.photo_file_name'
            ]
        ];
        return !empty($this->find('first', $options));
    }

    /**
     * POSTされたゴールのバリデーション
     * - バリデーションokの場合はtrueを、そうでない場合はバリデーションメッセージを返却
     * - $fieldsに配列で対象フィールドを指定。空の場合はすべてのフィールドをvalidateする
     *
     * @param array        $data
     * @param array        $fields
     * @param integer|null $goalId
     *
     * @return array|true
     */
    function validateGoalPOST($data, $fields = [], $goalId = null)
    {
        $validationBackup = $this->validate;
        if (empty($goalId)) {
            $originValidationRule = am($this->validate, $this->post_validate);
        } else {
            $data['id'] = $goalId;
            $originValidationRule = am($this->validate, $this->update_validate);
        }
        $validationRule = [];
        if (empty($fields)) {
            $validationRule = $originValidationRule;
        } else {
            foreach ($fields as $field) {
                $validationRule[$field] = Hash::get($originValidationRule, $field);
            }
        }
        $this->set($data);
        $this->validate = $validationRule;
        if ($this->validates()) {
            $this->validate = $validationBackup;
            return true;
        }
        return $this->validationErrors;
    }

    /**
     * ゴールの進捗をキーリザルト一覧から取得
     * TODO: GoalServiceと重複してるので、将来的には削除
     *
     * @param  array $key_results [description]
     *
     * @return array $res
     */
    function getProgress($key_results)
    {
        $res = 0;
        $target_progress_total = 0;
        $current_progress_total = 0;
        foreach ($key_results as $key_result) {
            $target_progress_total += $key_result['priority'] * 100;
            $current_progress_total += $key_result['priority'] * $key_result['progress'];
        }
        if ($target_progress_total != 0) {
            $res = round($current_progress_total / $target_progress_total, 2) * 100;
        }
        return $res;
    }

    /**
     * アクション可能なゴールリストを取得
     * 条件
     * ・ゴールメンバー
     * ・今期のゴール
     * ・未完了なKRが存在する
     *
     * @param int $userId
     *
     * @return array $res
     * @internal param array $key_results [description]
     */
    function findActionables(int $userId): array
    {
        $currentTerm = $this->Team->Term->getCurrentTermData();
        $timezone = $this->Team->getTimezone();
        $today = AppUtil::todayDateYmdLocal($timezone);

        $options = [
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = Goal.id',
                        'GoalMember.user_id' => $userId,
                        'GoalMember.team_id' => $this->current_team_id,
                    ],
                ]
            ],
            'conditions' => [
                'Goal.end_date >='   => AppUtil::todayDateYmdLocal($this->Team->getTimezone()),
                'Goal.end_date <='   => $currentTerm['end_date'],
                'Goal.completed'     => null,
                'GoalMember.del_flg' => false
            ],
            'order'      => [
                'GoalMember.priority DESC',
                'Goal.end_date ASC'
            ]
        ];
        // アクション可能なゴールを抽出(未完了なKRが存在するか)
        $options['conditions'][] = $this->buildSubQueryIncompleteKrExists();

        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.Goal');
    }

    /**
     * ゴールがアクション可能か判定
     * 条件
     * ・ゴールメンバー
     * ・今期のゴール
     * ・未完了なKRが存在する
     *
     * @param int $userId
     * @param int $goalId
     *
     * @return bool $res
     */
    function isActionable(int $userId, int $goalId): bool
    {
        $currentTerm = $this->Team->Term->getCurrentTermData();
        $timezone = $this->Team->getTimezone();
        $today = AppUtil::todayDateYmdLocal($timezone);

        $options = [
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = Goal.id',
                        'GoalMember.user_id' => $userId,
                        'GoalMember.team_id' => $this->current_team_id,
                    ],
                ]
            ],
            'conditions' => [
                'Goal.id'            => $goalId,
                'Goal.end_date >='   => $currentTerm['start_date'],
                'Goal.end_date <='   => $currentTerm['end_date'],
                'Goal.completed'     => null,
                'GoalMember.del_flg' => false
            ],
        ];
        // アクション可能なゴールを抽出(未完了なKRが存在するか)
        $options['conditions'][] = $this->buildSubQueryIncompleteKrExists();

        $res = $this->find('first', $options);
        return (bool)$res;
    }

    /**
     * finding collaborated goals
     * if $startDate and $endDate are null, fetching all term goal
     *
     * @param int    $userId
     * @param string $startDate
     * @param string $endDate
     * @param array  $fields
     *
     * @return array
     */
    function findCollaboratedGoals(
        int $userId,
        $startDate = null,
        $endDate = null,
        array $fields = ['id', 'name']
    ): array
    {
        // add prefix
        foreach ($fields as $i => $v) {
            $fields[$i] = 'Goal.' . $v;
        }

        $options = [
            'conditions' => [
                'GoalMember.del_flg' => false
            ],
            'fields'     => $fields,
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = Goal.id',
                        'GoalMember.user_id' => $userId,
                        'GoalMember.team_id' => $this->current_team_id,
                    ],
                ]
            ],
        ];
        if ($startDate) {
            $options['conditions']['Goal.end_date >='] = $startDate;
        }
        if ($endDate) {
            $options['conditions']['Goal.end_date <='] = $endDate;
        }
        $res = $this->find('all', $options);
        $res = Hash::extract($res, '{n}.Goal');
        return $res;
    }

    /**
     * 完了アクションが可能なゴールリスト取得
     *
     * @param int $userId
     *
     * @return array
     */
    function findCanComplete(int $userId): array
    {
        $options = [
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = Goal.id',
                        'GoalMember.user_id' => $userId,
                        'GoalMember.team_id' => $this->current_team_id,
                        'GoalMember.type'    => GoalMember::TYPE_OWNER,
                    ],
                ]
            ],
            'conditions' => [
                'Goal.completed' => null,
                'Goal.deleted'   => null,
            ],
        ];
        // Additional condition, incomplete KR not exists
        $options['conditions'][] = $this->buildSubQueryIncompleteKrNotExists();

        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }

        return Hash::extract($res, '{n}.Goal');
    }

    /**
     * Is the goal can complete?
     * Conditions
     * - user is goal owner
     * - goal is not completed
     * - goal doesn't have incomplete KRs
     *
     * @param int $userId
     * @param int $goalId
     *
     * @return bool
     */
    function isCanComplete(int $userId, int $goalId): bool
    {
        $options = [
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = Goal.id',
                        'GoalMember.user_id' => $userId,
                        'GoalMember.team_id' => $this->current_team_id,
                        'GoalMember.type'    => GoalMember::TYPE_OWNER,
                    ],
                ]
            ],
            'conditions' => [
                'Goal.id'        => $goalId,
                'Goal.completed' => null,
                'Goal.del_flg'   => false,
            ],
        ];
        // Additional condition, incomplete KR not exists
        $options['conditions'][] = $this->buildSubQueryIncompleteKrNotExists();

        $res = $this->find('first', $options);
        return (bool)$res;
    }

    /**
     * building subquery for finding goal with "NOT EXISTS (incomplete KRs)".
     * For using in `conditions`
     *
     * @return stdClass
     */
    function buildSubQueryIncompleteKrNotExists(): stdClass
    {
        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => ['KeyResult.id'],
            'table'      => 'key_results',
            'alias'      => 'KeyResult',
            'conditions' => [
                'KeyResult.goal_id = Goal.id',
                'KeyResult.completed' => null,
            ],
        ], $this);
        $queryObj = $db->expression("NOT EXISTS (" . $subQuery . ")");
        return $queryObj;
    }

    /**
     * building subquery for finding goal with "EXISTS (incomplete KRs)".
     * For using in `conditions`
     *
     * @return stdClass
     */
    function buildSubQueryIncompleteKrExists(): stdClass
    {
        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => ['KeyResult.id'],
            'table'      => 'key_results',
            'alias'      => 'KeyResult',
            'conditions' => [
                'KeyResult.goal_id = Goal.id',
                'KeyResult.completed' => null,
            ],
        ], $this);
        $queryObj = $db->expression("EXISTS (" . $subQuery . ")");
        return $queryObj;
    }

    /**
     * 期間で絞って全ゴールのIDを返す
     *
     * @param int $fromTimestamp
     * @param int $toTimestamp
     *
     * @return array
     */
    function findAllIdsByEndDateTimestamp(int $fromTimestamp, int $toTimestamp): array
    {
        $options = [
            'conditions' => [
                'end_date >=' => $fromTimestamp,
                'end_date <=' => $toTimestamp,
            ],
            'fields'     => ['id']
        ];
        $ret = $this->find('list', $options);
        return $ret;
    }

    /**
     * 自分が所属するゴール(リーダー or コラボレータ)のゴール名一覧を取得
     *
     * @param int    $userId
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findNameListAsMember(int $userId, string $startDate, string $endDate): array
    {
        $options = [
            'conditions' => [
                'Goal.end_date >='   => $startDate,
                'Goal.end_date <='   => $endDate,
                'Goal.team_id'       => $this->current_team_id,
                'GoalMember.user_id' => $userId,
                'GoalMember.del_flg' => false
            ],
            'fields'     => [
                'id',
                'name'
            ],
            'order'      => [
                'GoalMember.priority DESC',
                'Goal.completed ASC'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = Goal.id'
                    ],
                ]
            ]
        ];
        $ret = $this->find('all', $options);
        if (empty($ret)) {
            return $ret;
        }
        $ret = Hash::extract($ret, '{n}.Goal');
        return $ret;
    }

    /**
     * ゴールIDからtermのテキスト情報を取得
     *
     * @return string 'current'|'next'
     */
    function getTermTypeById(int $goalId): string
    {
        $goal = Hash::get($this->findById($goalId, ['start_date', 'end_date']), 'Goal');
        $term = $this->Team->Term->getTermType($goal['start_date'], $goal['end_date']);
        return $term;
    }

    /**
     * find goal list for updating krs term
     *
     * @param  string $startDate
     * @param  string $endDate
     *
     * @return array
     */
    function findForTermRangeUpdating(string $startDate, string $endDate): array
    {
        $options = [
            'conditions' => [
                'team_id'       => $this->current_team_id,
                'start_date >=' => $startDate,
                'start_date <=' => $endDate,
                'end_date >='   => $startDate,
                'end_date <='   => $endDate,
            ],
            'fields'     => [
                'id',
                'start_date',
                'end_date'
            ]
        ];

        $goals = $this->find('all', $options);
        if (!empty($goals)) {
            return $goals;
        }

        return Hash::extract($goals, '{n}.Goal');
    }

    /**
     * update end_date within range
     * - this update only 'end_date' and 'modified' columns
     * - only updating case of that end_date is over range
     *
     * @param  string $startDate
     * @param  string $endDate
     * @param  array  $additionalConditions
     *
     * @return bool
     */
    function updatEndWithinRange(string $startDate, string $endDate, array $additionalConditions = []): bool
    {
        $defaultConditions = [
            "Goal.team_id"       => $this->current_team_id,
            "Goal.start_date >=" => $startDate,
            "Goal.start_date <=" => $endDate,
            "Goal.end_date >"    => $endDate
        ];
        $conditions = $defaultConditions + $additionalConditions;

        $res = $this->updateAll(
            [
                // TODO: SQLiteの場合にデミリタが認識されない?ことへの暫定対応。要調査。
                "Goal.end_date" => "'$endDate'",
                "Goal.modified" => time()
            ],
            $conditions
        );
        return $res;
    }

    /**
     * update start_date, end_date within term range
     * - this update only 'start_date', 'end_date' and 'modified' columns
     * - only updating case of that end_date and are over range
     *
     * @param  string $startDate
     * @param  string $endDate
     * @param  array  $additionalConditions
     *
     * @return bool
     */
    function updateStartEndWithinRange(string $startDate, string $endDate, array $additionalConditions = []): bool
    {
        $defaultConditions = [
            "Goal.team_id"      => $this->current_team_id,
            "Goal.start_date >" => $endDate
        ];
        $conditions = $defaultConditions + $additionalConditions;
        $res = $this->updateAll(
            [
                // TODO: SQLiteの場合にデミリタが認識されない?ことへの暫定対応。要調査。
                "Goal.start_date" => "'$startDate'",
                "Goal.end_date"   => "'$endDate'",
                "Goal.modified"   => time()
            ],
            $conditions
        );
        return $res;
    }

    /**
     * update range in current term
     *
     * @param  string $startDate
     * @param  string $endDate
     *
     * @return bool
     */
    function updateCurrentTermRange(string $startDate, string $endDate, array $additionalConditions = []): bool
    {
        if (!$this->updatEndWithinRange($startDate, $endDate, $additionalConditions)) {
            return false;
        }
        return true;
    }

    /**
     * update range in next term
     *
     * @param  string $startDate
     * @param  string $endDate
     *
     * @return bool
     */
    function updateNextTermRange(string $startDate, string $endDate, array $additionalConditions = []): bool
    {
        if (!$this->updatEndWithinRange($startDate, $endDate, $additionalConditions)) {
            return false;
        }

        if (!$this->updateStartEndWithinRange($startDate, $endDate, $additionalConditions)) {
            return false;
        }

        return true;
    }

    /**
     * Search Goals for download
     *
     * @param int $teamId
     * @param array $conditions
     *
     * @return array
     */
    public function searchForDownload(int $teamId, array $conditions): array
    {
        // Set default condition (current term)
        $start_date = $this->Team->Term->getCurrentTermData()['start_date'];
        $end_date = $this->Team->Term->getCurrentTermData()['end_date'];

        $options = [
            'conditions' => [
                'Goal.team_id'     => $teamId,
                'Goal.end_date >=' => $start_date,
                'Goal.end_date <=' => $end_date,
            ],
            'fields'     => [
                'Goal.*'
            ],
            'order'      => ['Goal.id' => 'DESC'],
        ];
        //
        $options = $this->setFilter($options, $conditions);

        $goals = $this->find('all', $options);
        return Hash::extract($goals, '{n}.Goal');
    }
}
