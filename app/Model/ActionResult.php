 <?php

 use Goalous\Enum\DataType\DataType as DataType;
 use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;

App::uses('AppModel', 'Model');
App::uses('Translation', 'Model');

/**
 * ActionResult Model
 *
 * @property Team             $Team
 * @property User             $User
 * @property Goal             $Goal
 * @property KeyResult        $KeyResult
 * @property ActionResultFile $ActionResultFile
 */
class ActionResult extends AppModel
{
    /**
     * タイプ
     */
    const TYPE_USER = 0;
    const TYPE_GOAL = 1;
    const TYPE_KR = 2;

    public $actsAs = [
        'Upload' => [
            'photo1' => [
                'styles'      => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'     => 100,
                'default_url' => 'no-image.jpg',
            ],
            'photo2' => [
                'styles'      => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'     => 100,
                'default_url' => 'no-image.jpg',
            ],
            'photo3' => [
                'styles'      => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'     => 100,
                'default_url' => 'no-image.jpg',
            ],
            'photo4' => [
                'styles'      => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'     => 100,
                'default_url' => 'no-image.jpg',
            ],
            'photo5' => [
                'styles'      => [
                    'x_small' => '128l',
                    'small'   => '460l',
                    'large'   => '2048l',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'quality'     => 100,
                'default_url' => 'no-image.jpg',
            ],
        ],
    ];

    public $uses = [
        'AttachedFile'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'photo1'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo2'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo3'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo4'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'photo5'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType',],]
        ],
        'del_flg'       => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'goal_id'       => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ]
        ],
        'key_result_id' => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'name'          => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ]
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $postValidate = [
        'goal_id'                  => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'name'                     => [
            'notBlank'  => ['rule' => 'notBlank', 'required' => true],
            'maxLength' => ['rule' => ['maxLength', 10000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission'],
        ],
        'key_result_id'            => [
            'numeric'         => [
                'rule' => ['numeric'],
            ],
            'validateExistKr' => [
                'rule'     => ['validateExistKr'],
                'required' => true
            ],
        ],
        'key_result_current_value' => [
            'notBlank'           => [
                'rule'     => 'notBlank',
                'required' => true
            ],
            'decimal'            => [
                'rule' => ['decimal'],
            ],
            'validateKrProgress' => [
                'rule' => ['validateKrProgress'],
            ],
        ],
    ];

    public $modelConversionTable = [
        'team_id'          => DataType::INT,
        'goal_id'          => DataType::INT,
    ];

    /**
     * アクションに紐付けるKRが存在するか
     *
     * @param array $val
     *
     * @return bool
     */
    function validateExistKr(array $val): bool
    {
        $krId = array_shift($val);
        if (empty($krId)) {
            return false;
        }
        $kr = $this->KeyResult->getById($krId);
        if (empty($kr)) {
            return false;
        }
        $goalId = $this->data['ActionResult']['goal_id'];
        if ($kr['goal_id'] != $goalId) {
            return false;
        }
        return true;
    }

    /**
     * KR進捗更新チェック
     *
     * @param array $val
     *
     * @return bool
     */
    function validateKrProgress(array $val): bool
    {
        $errMsg = __("Invalid Request.");
        $currentVal = array_shift($val);
        if ($currentVal === "") {
            $this->invalidate('key_result_current_value', $errMsg);
            return false;
        }

        $krId = Hash::get($this->data, 'ActionResult.key_result_id');
        $kr = $this->KeyResult->getById($krId);
        if (empty($kr)) {
            $this->invalidate('key_result_current_value', $errMsg);
            return false;
        }

        // 単位が完了/未完了の場合
        if ($kr['value_unit'] == KeyResult::UNIT_BINARY) {
            if (!in_array($currentVal, [0, 1])) {
                $this->invalidate('key_result_current_value', $errMsg);
                return false;
            }
            return true;
        }

        // それ以外の単位
        $isProgressIncrease = ($kr['target_value'] - $kr['start_value']) > 0;
        // 進捗が変わらない場合は許容
        $currentDiff = $currentVal - $kr['current_value'];
        if ($currentDiff == 0) {
            return true;
        }

        /* 現在値が減っていないか */
        if ($isProgressIncrease && $currentDiff < 0) {
            $this->invalidate('key_result_current_value', __("You can not decrease current value."));
            return false;
        }
        /* 現在値が増えていないか */
        if (!$isProgressIncrease && $currentDiff > 0) {
            $this->invalidate('key_result_current_value', __("You can not increase current value."));
            return false;
        }

        /* 目標値を超えていないか */
        if ($isProgressIncrease && $currentVal > $kr['target_value']) {
            $this->invalidate('key_result_current_value', __("Current value over target value."));
            return false;
        }
        if (!$isProgressIncrease && $currentVal < $kr['target_value']) {
            $this->invalidate('key_result_current_value', __("Current value over target value."));
            return false;
        }

        return true;
    }

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team',
        'Goal'      => [
            "counterCache" => true,
            'counterScope' => ['ActionResult.del_flg' => false]
        ],
        'KeyResult' => [
            "counterCache" => true,
            'counterScope' => ['ActionResult.del_flg' => false]
        ],
        'User',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Post' => [
            'dependent' => true,
        ],
        'ActionResultFile',
    ];

    /**
     * hasOne associations
     */
    public $hasOne = [
        'KrProgressLog',
    ];

    /**
     * アクションと添付ファイルのデータを返す
     *
     * @param $action_result_id
     *
     * @return array|null
     */
    function getWithAttachedFiles($action_result_id)
    {
        return $this->find('first', [
            'conditions' => ['ActionResult.id' => $action_result_id],
            'contain'    => [
                'ActionResultFile' => [
                    'order' => ['ActionResultFile.index_num asc'],
                    'AttachedFile',
                ]
            ]
        ]);
    }

    /**
     * アクション数のカウントを返却
     *
     * @param mixed  $userId ユーザーIDもしくは'me'を指定する。
     * @param null   $startTimestamp
     * @param null   $endTimestamp
     * @param string $dateCol
     *
     * @return int
     */
    function getCount($userId = 'me', $startTimestamp = null, $endTimestamp = null, $dateCol = 'created')
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
            ]
        ];
        // ユーザーIDに'me'が指定された場合は、自分のIDをセットする
        if ($userId == 'me') {
            $options['conditions']['user_id'] = $this->my_uid;
        } elseif ($userId) {
            $options['conditions']['user_id'] = $userId;
        }

        //期間で絞り込む
        if ($startTimestamp) {
            $options['conditions']["$dateCol >="] = $startTimestamp;
        }
        if ($endTimestamp) {
            $options['conditions']["$dateCol <="] = $endTimestamp;
        }
        $res = $this->find('count', $options);
        return $res;
    }

    /**
     * ゴールに紐づくアクション数をカウント
     *
     * @param $goal_id
     *
     * @return array|null
     */
    function getCountByGoalId($goal_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'team_id' => $this->current_team_id,
            ]
        ];
        $res = $this->find('count', $options);
        return $res;
    }

    /**
     * get Action count that depends on a KR
     *
     * @param $krId
     *
     * @return int
     */
    function getCountByKrId($krId): int
    {
        $options = [
            'conditions' => [
                'key_result_id' => $krId,
                'team_id'       => $this->current_team_id,
            ]
        ];
        $res = $this->find('count', $options);
        return (int)$res;
    }

    function getByKrId($krId,\Carbon\Carbon $periodFrom): array
    {
        $options = [
            'conditions' => [
                'key_result_id' => $krId,
                'ActionResult.created >=' => $periodFrom->getTimestamp()
            ],
            'order'      => [
                'created' => 'desc'
            ],
        ];
        return $this->useType()->find('all', $options);
    }

    function actionEdit($data)
    {
        if (empty($data)) {
            return false;
        }

        $this->begin();
        $results = [];

        // アクションデータ保存
        $results[] = $this->save($data);

        // ファイルが添付されている場合
        if ((isset($data['file_id']) && is_array($data['file_id'])) ||
            (isset($data['deleted_file_id']) && is_array($data['deleted_file_id']))
        ) {
            $results[] = $this->ActionResultFile->AttachedFile->updateRelatedFiles(
                $data['ActionResult']['id'],
                AttachedFile::TYPE_MODEL_ACTION_RESULT,
                isset($data['file_id']) ? $data['file_id'] : [],
                isset($data['deleted_file_id']) ? $data['deleted_file_id'] : []);
        }

        // どこかでエラーが発生した場合は rollback
        foreach ($results as $r) {
            if (!$r) {
                $this->rollback();
                return false;
            }
        }

        $this->commit();

        // 添付ファイルが存在する場合は一時データを削除
        if (isset($data['file_id']) && is_array($data['file_id'])) {
            $Redis = ClassRegistry::init('GlRedis');
            foreach ($data['file_id'] as $hash) {
                if (!is_numeric($hash)) {
                    $Redis->delPreUploadedFile($this->current_team_id, $this->my_uid, $hash);
                }
            }
        }

        return true;
    }

    public function addCompletedAction($data, $goal_id)
    {
        if (empty($data)) {
            return false;
        }
        $data['ActionResult']['team_id'] = $this->current_team_id;
        $data['ActionResult']['goal_id'] = $goal_id;
        $data['ActionResult']['user_id'] = $this->my_uid;
        if (isset($data['ActionResult']['key_result_id'])) {
            $data['ActionResult']['type'] = ActionResult::TYPE_KR;
        } else {
            $data['ActionResult']['type'] = ActionResult::TYPE_GOAL;
        }
        $data['ActionResult']['completed'] = REQUEST_TIMESTAMP;
        $res = $this->save($data);
        return $res;
    }

    public function releaseKr($kr_id)
    {
        $res = $this->updateAll(['ActionResult.key_result_id' => null],
            ['ActionResult.key_result_id' => $kr_id, 'ActionResult.team_id' => $this->current_team_id]);
        return $res;
    }

    public function releaseGoal($goal_id)
    {
        $res = $this->updateAll(['ActionResult.goal_id' => null, 'ActionResult.key_result_id' => null],
            ['ActionResult.goal_id' => $goal_id, 'ActionResult.team_id' => $this->current_team_id]);
        return $res;
    }

    function getActionCount($goal_ids, $user_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_ids,
                'user_id' => $user_id,
            ],
        ];
        $res = $this->find('count', $options);
        return $res;
    }

    /**
     * @param int $uerId
     *
     * @return int
     */
    function getCountByUserId(int $uerId): int
    {
        $options = [
            'conditions' => [
                'user_id' => $uerId,
            ],
        ];
        $res = $this->find('count', $options);
        return (int)$res;
    }

    /**
     * アクションを登録したユニークユーザー数を返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getUniqueUserCount($params = [])
    {
        $params = array_merge([
            'start'   => null,
            'end'     => null,
            'user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'COUNT(DISTINCT user_id) as cnt',
            ],
            'conditions' => [
                'ActionResult.team_id' => $this->current_team_id,
            ],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["ActionResult.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["ActionResult.created <="] = $params['end'];
        }
        if ($params['user_id'] !== null) {
            $options['conditions']["ActionResult.user_id"] = $params['user_id'];
        }
        $row = $this->find('first', $options);

        $count = 0;
        if (isset($row[0]['cnt'])) {
            $count = $row[0]['cnt'];
        }
        return $count;
    }

    /**
     * ゴール別のアクション数ランキングを返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getGoalRanking($params = [])
    {
        $params = array_merge([
            'limit'        => null,
            'start'        => null,
            'end'          => null,
            'goal_user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'ActionResult.goal_id',
                'COUNT(*) as cnt',
            ],
            'conditions' => [
                'ActionResult.team_id' => $this->current_team_id,
            ],
            'group'      => ['ActionResult.goal_id'],
            'order'      => ['cnt' => 'DESC'],
            'limit'      => $params['limit'],
            'contain'    => [],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["ActionResult.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["ActionResult.created <="] = $params['end'];
        }
        if ($params['goal_user_id'] !== null) {
            $options['conditions']["Goal.user_id"] = $params['goal_user_id'];
            $options['contain'][] = 'Goal';
        }
        $rows = $this->find('all', $options);
        $ranking = [];
        foreach ($rows as $v) {
            $ranking[$v['ActionResult']['goal_id']] = $v[0]['cnt'];
        }
        return $ranking;
    }

    /**
     * ユーザー別のアクション数ランキングを返す
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getUserRanking($params = [])
    {
        $params = array_merge([
            'limit'   => null,
            'start'   => null,
            'end'     => null,
            'user_id' => null,
        ], $params);

        $options = [
            'fields'     => [
                'ActionResult.user_id',
                'COUNT(*) as cnt',
            ],
            'conditions' => [
                'ActionResult.team_id' => $this->current_team_id,
            ],
            'group'      => ['ActionResult.user_id'],
            'order'      => ['cnt' => 'DESC'],
            'limit'      => $params['limit'],
        ];
        if ($params['start'] !== null) {
            $options['conditions']["ActionResult.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["ActionResult.created <="] = $params['end'];
        }
        if ($params['user_id'] !== null) {
            $options['conditions']["ActionResult.user_id"] = $params['user_id'];
        }
        $rows = $this->find('all', $options);
        $ranking = [];
        foreach ($rows as $v) {
            $ranking[$v['ActionResult']['user_id']] = $v[0]['cnt'];
        }
        return $ranking;
    }

    /**
     * 他人のゴールへのアクションのカウント数を返す
     *
     * @param array $params
     *
     * @return int
     */
    public function getCollaboGoalActionCount(array $params = [])
    {
        $params = array_merge([
            'user_id' => null,
            'start'   => null,
            'end'     => null,
        ], $params);

        $options = [
            'conditions' => [
                'ActionResult.team_id' => $this->current_team_id,
                'ActionResult.user_id <> Goal.user_id',
            ],
            'contain'    => ['Goal'],
        ];
        if ($params['user_id'] !== null) {
            $options['conditions']['ActionResult.user_id'] = $params['user_id'];
        }
        if ($params['start'] !== null) {
            $options['conditions']["ActionResult.created >="] = $params['start'];
        }
        if ($params['end'] !== null) {
            $options['conditions']["ActionResult.created <="] = $params['end'];
        }

        return $this->find('count', $options);
    }

    function getKrIdsByGoalId($goal_id, $user_id)
    {
        $options = [
            'conditions' => [
                'goal_id' => $goal_id,
                'user_id' => $user_id,
            ],
            'fields'     => [
                'key_result_id',
                'key_result_id'
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    function isPostedActionForSetupBy($user_id)
    {
        $options = [
            'conditions' => [
                'ActionResult.user_id' => $user_id,
            ],
            'fields'     => ['ActionResult.id']
        ];

        return (bool)$this->findWithoutTeamId('all', $options);
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
        $ret = Hash::combine($ret, '{n}.ActionResult.goal_id', '{n}.0.cnt');
        return $ret + $defaultCountEachGoalId;
    }

    /**
     * KRに紐づく最新のアクションを取得
     *
     * @param int $krId
     *
     * @return array|null
     */
    public function getLatestAction(int $krId): array
    {
        $options = [
            'conditions' => [
                'key_result_id' => $krId
            ],
            'order'      => 'created desc'
        ];

        $res = $this->find('first', $options);
        return $res;
    }
}
