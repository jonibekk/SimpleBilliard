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
        'photo1'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo2'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo3'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo4'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'photo5'  => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'del_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'name'    => [
            'isString' => ['rule' => 'isString', 'message' => 'Invalid Submission']
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
     * アクション数のカウントを返却
     *
     * @param mixed $user_id ユーザーIDもしくは'me'を指定する。
     * @param null  $start_date
     * @param null  $end_date
     *
     * @return int
     */
    function getCount($user_id = 'me', $start_date = null, $end_date = null)
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
            ]
        ];
        // ユーザーIDに'me'が指定された場合は、自分のIDをセットする
        if ($user_id == 'me') {
            $options['conditions']['user_id'] = $this->my_uid;
        }
        elseif (is_numeric($user_id)) {
            $options['conditions']['user_id'] = $user_id;
        }

        //期間で絞り込む
        if ($start_date) {
            $options['conditions']['modified >'] = $start_date;
        }
        if ($end_date) {
            $options['conditions']['modified <'] = $end_date;
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
        if (isset($data['photo_delete']) && !empty($data['photo_delete'])) {
            foreach ($data['photo_delete'] as $index => $val) {
                if ($val) {
                    $data['ActionResult']['photo' . $index] = null;
                }
            }
        }
        return $this->save($data);
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
        }
        else {
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

    function getActionIdsByKrId($key_result_id)
    {
        $options = [
            'conditions' => [
                'key_result_id' => $key_result_id,
            ],
            'fields'     => ['id', 'id']
        ];
        $res = $this->find('list', $options);
        return $res;
    }

}
