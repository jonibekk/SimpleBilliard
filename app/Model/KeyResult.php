<?php
App::uses('AppModel', 'Model');

/**
 * KeyResult Model
 *
 * @property Team         $Team
 * @property Goal         $Goal
 * @property ActionResult $ActionResult
 * @property Post         $Post
 * @method findByGoalId($goalId)
 */
class KeyResult extends AppModel
{
    /**
     * 目標値の単位
     */
    const UNIT_PERCENT = 0;
    const UNIT_NUMBER = 1;
    const UNIT_BINARY = 2;
    const UNIT_YEN = 3;
    const UNIT_DOLLAR = 4;

    static public $UNIT = [
        self::UNIT_PERCENT => "",
        self::UNIT_YEN     => "",
        self::UNIT_DOLLAR  => "",
        self::UNIT_NUMBER  => "",
        self::UNIT_BINARY  => "",
    ];

    /**
     * 表示上先頭に持ってくる単位
     */
    static public $UNIT_HEAD = [
        self::UNIT_YEN,
        self::UNIT_DOLLAR
    ];

    /**
     * 表示上末尾に持ってくる単位
     */
    static public $UNIT_TAIL = [
        self::UNIT_PERCENT,
    ];

    /**
     * 目標値の単位の表示名をセット
     */
    function _setUnitName()
    {
        self::$UNIT[self::UNIT_PERCENT] = "%";
        self::$UNIT[self::UNIT_YEN] = '¥';
        self::$UNIT[self::UNIT_DOLLAR] = '$';
        self::$UNIT[self::UNIT_NUMBER] = __("Other Unit");
        self::$UNIT[self::UNIT_BINARY] = __('No Unit');
    }

    /**
     * 重要度の名前をセットz
     */
    private function _setPriorityName()
    {
        $this->priority_list[0] = __("0(not affect the progress)");
        $this->priority_list[1] = __("1(Very low)");
        $this->priority_list[3] = __("3(default)");
        $this->priority_list[5] = __("5(Very high)");
    }

    public $priority_list = [
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
    ];

    const MAX_LENGTH_VALUE = 15;

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
        'name'         => [
            'maxLength' => ['rule' => ['maxLength', 200]],
            'notBlank'  => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
        ],
        'del_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'priority'     => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'value_unit'   => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => 'create',
                'rule'     => 'notBlank',
            ],
            'validateEditProgressStartEnd' => [
                'rule' => 'validateEditProgressStartEnd',
            ],
        ],
        'start_value'  => [
            'requiredCaseExistUnit' => [
                'rule' => ['requiredCaseExistUnit'],
            ],
            'numeric'               => [
                'rule'       => ['numeric'],
                'allowEmpty' => true
            ],
        ],
        'target_value' => [
            'requiredCaseExistUnit' => [
                'rule' => ['requiredCaseExistUnit'],
            ],
            'numeric'               => [
                'rule'       => ['numeric'],
                'allowEmpty' => true
            ],
        ],
    ];

    public $post_validate = [
        'start_date' => [
            'isString' => ['rule' => 'isString'],
            'dateYmd'  => [
                'rule'       => ['date', 'ymd'],
                'allowEmpty' => true
            ],
        ],
        'end_date'   => [
            'isString' => ['rule' => 'isString'],
            'dateYmd'  => [
                'rule'       => ['date', 'ymd'],
                'allowEmpty' => true
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
        'Goal',
    ];

    public $hasMany = [
        'ActionResult',
        'Post',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setUnitName();
        $this->_setPriorityName();
    }

    /**
     * 単位値必須チェック
     * 単位が無い場合は値入力が無いのでチェックしない
     *
     * @param      $val
     *
     * @return array|null
     * @internal param $data
     */
    function requiredCaseExistUnit($val)
    {
        $val = array_shift($val);
        if ($this->data['KeyResult']['value_unit'] == self::UNIT_BINARY) {
            return true;
        }
        if ($val === "") {
            return false;
        }
        return true;
    }

    /**
     * バリデーション
     * KR進捗の開始/終了更新
     *
     * @param $val
     *
     * @return bool
     */
    function validateEditProgressStartEnd($val)
    {
        $unit = array_shift($val);
        $errMsg = __("Invalid Request.");
        if (empty($unit)) {
            $this->invalidate("value_unit", $errMsg);
            return false;
        }
        if ($unit == self::UNIT_BINARY) {
            return true;
        }

        $krId = Hash::get($this->data, 'KeyResult.id');
        $kr = $this->getById($krId);
        if (empty($kr)) {
            $this->invalidate("value_unit", $errMsg);
            return false;
        }

        $inputKr = $this->data['KeyResult'];
        if (empty($inputKr['target_value'])) {
            return true;
        }

        if ($inputKr['target_value'] == $kr['target_value']) {
            return true;
        }

        $inputDiffStartEnd = $inputKr['target_value'] - $kr['start_value'];
        // 開始値と目標値が同じ値でないか
        if ($inputDiffStartEnd == 0) {
            $this->invalidate('value_unit', __("You can not change start value and target value to the same value."));
            return false;
        }

        $isProgressIncrease = ($kr['target_value'] - $kr['start_value']) > 0;
        // 進捗の値が増加から減少の方向に変更してないか
        if ($isProgressIncrease && $inputDiffStartEnd < 0) {
            $this->invalidate('value_unit', __("You can not change the values from increase to decrease."));
            return false;
        }

        // 進捗の値が減少から増加の方向に変更してないか
        if (!$isProgressIncrease && $inputDiffStartEnd > 0) {
            $this->invalidate('value_unit', __("You can not change the values from decrease to increase."));
            return false;
        }

        // 目標値を現在値と同じ値への変更はOK
        if ($inputKr['target_value'] == $kr['current_value']) {
            return true;
        }

        /* 目標値が現在値未満の値でないか */
        if ($isProgressIncrease && $inputKr['target_value'] < $kr['current_value']) {
            $this->invalidate('value_unit', __("You can not change target value less than current value"));
            return false;
        }
        if (!$isProgressIncrease && $inputKr['target_value'] > $kr['current_value']) {
            $this->invalidate('value_unit', __("You can not change target value less than current value"));
            return false;
        }
        return true;
    }

    /**
     * @param      $data
     * @param      $goal_id
     * @param null $uid
     *
     * @return bool
     * @throws Exception
     */
    function add($data, $goal_id, $uid = null)
    {
        if (!$uid) {
            $uid = $this->my_uid;
        }
        if (!isset($data['KeyResult']) || empty($data['KeyResult'])) {
            throw new RuntimeException(__("There is no data of KR."));
        }
        $data['KeyResult']['goal_id'] = $goal_id;
        $data['KeyResult']['user_id'] = $uid;
        $data['KeyResult']['team_id'] = $this->current_team_id;

        if ($data['KeyResult']['value_unit'] == KeyResult::UNIT_BINARY) {
            $data['KeyResult']['start_value'] = 0;
            $data['KeyResult']['target_value'] = 1;
        }
        $data['KeyResult']['current_value'] = $data['KeyResult']['start_value'];

        $this->set($data);
        $validate_backup = $this->validate;
        $this->validate = array_merge($this->validate, $this->post_validate);
        if (!$this->validates()) {
            throw new RuntimeException(__("Failed to save KR."));
        }
        $this->validate = $validate_backup;

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($goal_id);
        //時間をunixtimeに変換
        if (!empty($data['KeyResult']['start_date'])) {
            $data['KeyResult']['start_date'] = strtotime($data['KeyResult']['start_date']) - $goal_term['timezone'] * HOUR;
        }
        //期限を+1day-1secする
        if (!empty($data['KeyResult']['end_date'])) {
            $data['KeyResult']['end_date'] = strtotime('+1 day -1 sec',
                    strtotime($data['KeyResult']['end_date'])) - $goal_term['timezone'] * HOUR;
        }
        $this->create();
        if (!$this->save($data)) {
            throw new RuntimeException(__("Failed to save KR."));
        }
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        return true;
    }

    /**
     * キーリザルトの一覧を返す
     *
     * @param        $goal_id
     * @param string $find_type
     * @param bool   $is_complete
     * @param array  $params
     *                 'limit' : find() の limit
     *                 'page'  : find() の page
     * @param bool   $with_action
     * @param int    $action_limit
     *
     * @return array|null
     */
    function getKeyResults(
        $goal_id,
        $find_type = "all",
        $is_complete = false,
        array $params = [],
        $with_action = false,
        $action_limit = MY_PAGE_ACTION_NUMBER
    ) {
        // パラメータデフォルト
        $params = array_merge([
            'limit' => null,
            'page'  => 1,
        ], $params);

        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ],
            'order'      => [
                'KeyResult.completed IS NULL DESC',
                'KeyResult.tkr_flg DESC',
                'KeyResult.priority DESC',
                'KeyResult.end_date ASC',
            ],
            'limit'      => $params['limit'],
            'page'       => $params['page'],
        ];
        if ($is_complete === true) {
            $options['conditions']['completed'] = null;
        }
        if ($with_action) {
            $options['contain']['ActionResult'] = [
                'limit'            => $action_limit,
                'order'            => ['ActionResult.created desc'],
                'Post'             => [
                    'fields' => [
                        'Post.id'
                    ]
                ],
                'ActionResultFile' => [
                    'conditions' => ['ActionResultFile.index_num' => 0],
                    'AttachedFile'
                ]
            ];
        }

        $res = $this->find($find_type, $options);
        return $res;
    }

    /**
     * 未完了KRリスト取得
     *
     * @param int $goalId
     *
     * @return array
     */
    function findIncomplete(int $goalId): array
    {
        $options = [
            'conditions' => [
                'goal_id'   => $goalId,
                'team_id'   => $this->current_team_id,
                'completed' => null
            ],
            'order'      => [
                'KeyResult.tkr_flg DESC',
                'KeyResult.priority DESC',
                'KeyResult.end_date ASC',
            ],
        ];
        $res = $this->find('all', $options);
        return Hash::extract($res, '{n}.KeyResult');
    }

    /**
     * ユーザがアクションしたKRのみ抽出
     * Extraction KR with only exist user action
     *
     * @param $goal_id
     * @param $user_id
     *
     * @return array|null
     */
    function getKrRelatedUserAction($goal_id, $user_id)
    {
        $kr_ids = $this->ActionResult->getKrIdsByGoalId($goal_id, $user_id);
        $options = [
            'conditions' => [
                'id' => $kr_ids,
            ],
            'order'      => [
                'KeyResult.progress ASC',
                'KeyResult.start_date ASC',
                'KeyResult.end_date ASC',
                'KeyResult.priority DESC',
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function getKrCount($goal_ids)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_ids,
            ],
        ];
        $res = $this->find('count', $options);
        return $res;
    }

    function getTkr($goalId)
    {
        $res = $this->find('first', [
            'conditions' => [
                'goal_id' => $goalId,
                'tkr_flg' => true,
            ],
        ]);
        return $res;
    }

    /**
     * 未完了のキーリザルト数を返す
     *
     * @param $goal_id
     *
     * @return int
     */
    function getIncompleteKrCount($goal_id)
    {
        $options = [
            'conditions' => [
                'goal_id'   => $goal_id,
                'completed' => null,
            ],
        ];
        $res = $this->find('count', $options);
        return $res;
    }

    /**
     * キーリザルト変更権限
     * コラボレータならtrueを返す
     *
     * @param $kr_id
     *
     * @return bool
     */
    function isPermitted($kr_id)
    {
        $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $kr_id]]);
        if (empty($key_result)) {
            return false;
        }
        $goal = $this->Goal->getGoalMinimum($key_result['KeyResult']['goal_id']);
        if (empty($goal)) {
            return false;
        }
        return $this->Goal->GoalMember->isCollaborated($goal['Goal']['id']);
    }

    function saveEdit($data)
    {
        if (!isset($data['KeyResult']) || empty($data['KeyResult'])) {
            return false;
        }

        //on/offの場合は現在値0,目標値1をセット
        if ($data['KeyResult']['value_unit'] == KeyResult::UNIT_BINARY) {
            $data['KeyResult']['start_value'] = 0;
            $data['KeyResult']['current_value'] = 0;
            $data['KeyResult']['target_value'] = 1;
        }

        $this->set($data);
        $validate_backup = $this->validate;
        $this->validate = array_merge($this->validate, $this->post_validate);
        if (!$this->validates()) {
            return false;
        }
        $this->validate = $validate_backup;

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($data['KeyResult']['goal_id']);

        $data['KeyResult']['start_date'] = strtotime($data['KeyResult']['start_date']) - $goal_term['timezone'] * HOUR;
        $data['KeyResult']['end_date'] = strtotime('+1 day -1 sec',
                strtotime($data['KeyResult']['end_date'])) - $goal_term['timezone'] * HOUR;
//TODO 現在値を使わないため、この計算は行わない
//        $data['KeyResult']['progress'] = $this->getProgress($data['KeyResult']['start_value'],
//                                                            $data['KeyResult']['target_value'],
//                                                            $data['KeyResult']['current_value']);
        return $this->save($data);
    }

    function complete($kr_id)
    {
        $current_kr = $this->findById($kr_id);
        if (empty($current_kr)) {
            throw new RuntimeException(__("There is no key result."));
        }
        $this->id = $kr_id;
        $this->saveField('current_value', $current_kr['KeyResult']['target_value']);
        $this->saveField('progress', 100);
        $this->saveField('completed', REQUEST_TIMESTAMP);
        return true;
    }

    function incomplete($kr_id)
    {
        $current_kr = $this->findById($kr_id);
        if (empty($current_kr)) {
            throw new RuntimeException(__("There is no key result."));
        }
        $current_kr['KeyResult']['completed'] = null;
        unset($current_kr['KeyResult']['modified']);
        //progressを元に戻し、current_valueにstart_valueをsetする
        $current_kr['KeyResult']['progress'] = 0;
        $current_kr['KeyResult']['current_value'] = $current_kr['KeyResult']['start_value'];
        $this->save($current_kr);
        return true;
    }

    function getProgress($start_val, $target_val, $current_val)
    {
        $progress = round(($current_val - $start_val) / ($target_val - $start_val), 2) * 100;
        if ($progress < 0) {
            return 0;
        }
        return $progress;
    }

    function getKrNameList($goal_id, $with_all_opt = false, $separate_progress = false)
    {
        $options = [
            'conditions' => ['goal_id' => $goal_id],
            'fields'     => ['id', 'name'],
            'order'      => ['created desc'],
        ];
        if (!$separate_progress) {
            $res = $this->find('list', $options);
            if ($with_all_opt) {
                return [null => __('All')] + $res;
            }
            return $res;
        }
        $incomplete_opt = $options;
        $incomplete_opt['conditions']['completed'] = null;
        $incomplete_krs = $this->find('list', $incomplete_opt);
        $completed_opt = $options;
        $completed_opt['conditions']['NOT']['completed'] = null;
        $completed_krs = $this->find('list', $completed_opt);
        $res = [];
        $res += $with_all_opt ? [null => __('All')] : null;
        if (!empty($incomplete_krs)) {
            $res += ['disable_value1' => '----------------------------------------------------------------------------------------'];
            $res += $incomplete_krs;
        }
        if (!empty($completed_krs)) {
            $res += ['disable_value2' => '----------------------------------------------------------------------------------------'];
            $res += $completed_krs;
        }
        return $res;
    }

    /**
     * キーリザルトが完了済みか確認
     *
     * @param $kr_id
     *
     * @return bool
     */
    public function isCompleted($kr_id)
    {
        $kr = $this->findById($kr_id);
        if (!$kr) {
            return false;
        }
        return $kr['KeyResult']['completed'] ? true : false;
    }

    /**
     * - バリデーションルールを切り替える
     * - 必須チェックを外す(オプション)
     * - バリデーションokの場合はtrueを、そうでない場合はバリデーションメッセージを返却
     *
     * @param      $data
     * @param bool $detachRequired
     *
     * @return array|true
     */
    function validateKrPOST($data, $detachRequired = false)
    {
        $validationBackup = $this->validate;
        $this->validate = am($this->validate, $this->post_validate);
        if ($detachRequired) {
            $this->validate = Hash::remove($this->validate, '{s}.{s}.required');
        }
        $this->set($data);
        if ($this->validates()) {
            $this->validate = $validationBackup;
            return true;
        }
        return $this->validationErrors;
    }

    /**
     * ゴールIDごとの件数取得
     *
     * @param $goalIds
     *
     * @return bool
     */
    public function countEachGoalId($goalIds)
    {
        $ret = $this->find('all', [
            'fields'     => ['goal_id', 'COUNT(goal_id) as cnt'],
            'conditions' => ['goal_id' => $goalIds],
            'group'      => ['goal_id'],
        ]);

        // 0件のゴールも配列要素を作り、値を0として返す
        $defaultCountEachGoalId = array_fill_keys($goalIds, 0);
        $ret = Hash::combine($ret, '{n}.KeyResult.goal_id', '{n}.0.cnt');
        return $ret + $defaultCountEachGoalId;
    }

    /**
     * 評価ページ表示用にKR一覧を取得
     *
     * @param $goalId
     *
     * @return $krs
     */
    public function getKeyResultsForEvaluation($goalId, $userId)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goalId,
                'team_id' => $this->current_team_id,
            ],
            'order'      => [
                'KeyResult.tkr_flg DESC',
                'KeyResult.priority DESC'
            ],
            'contain'    => [
                'ActionResult' => [
                    'conditions' => [
                        'user_id' => $userId
                    ]
                ]
            ]
        ];
        $res = $this->find('all', $options);

        $krs = [];
        foreach ($res as $key => $val) {
            $krs[$key] = Hash::extract($val, "KeyResult");
            $krs[$key]['ActionResult'] = Hash::extract($val, "ActionResult");
        }

        return $krs;
    }
}
