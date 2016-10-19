<?php
App::uses('AppModel', 'Model');

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
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
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
            'notEmpty' => [
                'rule' => 'notEmpty',
            ]
        ],
        'key_result_id' => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'name'          => [
            'maxLength' => ['rule' => ['maxLength', 10000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ]
    ];

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
     * @param mixed  $user_id ユーザーIDもしくは'me'を指定する。
     * @param null   $start_date
     * @param null   $end_date
     * @param string $date_col
     *
     * @return int
     */
    function getCount($user_id = 'me', $start_date = null, $end_date = null, $date_col = 'modified')
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
            ]
        ];
        // ユーザーIDに'me'が指定された場合は、自分のIDをセットする
        if ($user_id == 'me') {
            $options['conditions']['user_id'] = $this->my_uid;
        } elseif ($user_id) {
            $options['conditions']['user_id'] = $user_id;
        }

        //期間で絞り込む
        if ($start_date) {
            $options['conditions']["$date_col >="] = $start_date;
        }
        if ($end_date) {
            $options['conditions']["$date_col <="] = $end_date;
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
                'ActionResult.user_id'    => $user_id,
                'ActionResult.created >=' => $this->Team->EvaluateTerm->getPreviousTermData()['start_date'],
                'ActionResult.created <=' => $this->Team->EvaluateTerm->getCurrentTermData()['end_date'],
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
            'fields'=> ['goal_id', 'COUNT(goal_id) as cnt'],
            'conditions' => ['goal_id' => $goalIds],
            'group' => ['goal_id'],
        ]);
        // 0件のゴールも配列要素を作り、値を0として返す
        $defaultCountEachGoalId = array_fill_keys($goalIds, 0);
        $ret = Hash::combine($ret, '{n}.ActionResult.goal_id', '{n}.0.cnt');
        return $ret + $defaultCountEachGoalId;
    }

}
