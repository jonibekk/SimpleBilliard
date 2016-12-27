<?php
App::uses('AppModel', 'Model');

/**
 * Circle Model
 *
 * @property Team            $Team
 * @property CircleMember    $CircleMember
 * @property PostShareCircle $PostShareCircle
 */
class Circle extends AppModel
{
    /**
     * 公開タイプ
     */
    const TYPE_PUBLIC_ON = 1;
    const TYPE_PUBLIC_OFF = 0;
    static public $TYPE_PUBLIC = [self::TYPE_PUBLIC_ON => "", self::TYPE_PUBLIC_OFF => "",];

    public $add_new_member_list = [];

    /**
     * 公開タイプの名前をセット
     */
    private function _setPublicTypeName()
    {
        self::$TYPE_PUBLIC[self::TYPE_PUBLIC_ON] = __("Public");
        self::$TYPE_PUBLIC[self::TYPE_PUBLIC_OFF] = __("Secret");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setPublicTypeName();
    }

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
                'default_url' => 'no-image-circle.jpg',
                'quality'     => 100,
            ]
        ]
    ];

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
            'isString'  => [
                'rule' => ['isString',],
            ],
            'maxLength' => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'rule' => ['notBlank'],
            ],
        ],
        'description'  => [
            'isString' => [
                'rule' => ['isString',],
            ],
        ],
        'del_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'team_all_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'public_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'photo'        => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentImageType', [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_JPEG2000, IMAGETYPE_PNG]],]
        ],
        'description'  => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission']
        ]
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team'
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CircleMember'    => [
            'dependent' => true,
        ],
        'CircleAdmin'     => [
            'className'  => 'CircleMember',
            'conditions' => ['CircleAdmin.admin_flg' => true],
        ],
        'PostShareCircle' => [
            'dependent' => true,
        ],
    ];

    /**
     * 新規サークル追加(管理者として自分を登録)
     *
     * @param array   $data
     * @param boolean $show_for_all_feed_flg
     * @param boolean $get_notification_flg
     *
     * @return mixed
     */
    function add($data, $show_for_all_feed_flg = true, $get_notification_flg = true)
    {
        if (!isset($data['Circle']) || empty($data['Circle'])) {
            return false;
        }

        $data['Circle']['team_id'] = $this->current_team_id;
        $data['CircleMember'][0]['team_id'] = $this->current_team_id;
        $data['CircleMember'][0]['admin_flg'] = true;
        $data['CircleMember'][0]['user_id'] = $this->my_uid;
        $data['CircleMember'][0]['show_for_all_feed_flg'] = $show_for_all_feed_flg;
        $data['CircleMember'][0]['get_notification_flg'] = $get_notification_flg;
        if (!empty($data['Circle']['members'])) {
            $members = explode(",", $data['Circle']['members']);
            foreach ($members as $val) {
                $val = str_replace('user_', '', $val);;
                $data['CircleMember'][] = [
                    'team_id'               => $this->current_team_id,
                    'user_id'               => $val,
                    'show_for_all_feed_flg' => $show_for_all_feed_flg,
                    'get_notification_flg'  => $get_notification_flg,
                ];
                $this->add_new_member_list[] = $val;
                Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true, $val), 'user_data');
                Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true, $val), 'user_data');
                Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $val), 'user_data');
            }
        }
        //本人のキャッシュも削除
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true), 'user_data');
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');
        if ($res = $this->saveAll($data)) {
            $this->CircleMember->updateCounterCache(['circle_id' => $this->getLastInsertID()]);

            if (Hash::get($data, 'Circle.public_flg')) {
                $this->PostShareCircle->Post->createCirclePost($this->getLastInsertID(), $this->my_uid);
            }
        }
        return $res;
    }

    /**
     * サークルの基本情報を変更
     *
     * @param $data
     *
     * @return bool|mixed
     */
    function edit($data)
    {
        if (!isset($data['Circle']) || empty($data['Circle'])) {
            return false;
        }
        $members = $this->CircleMember->getMemberList($data['Circle']['id'], true);
        foreach ($members as $val) {
            Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $val), 'user_data');
        }
        if ($this->isTeamAllCircle($data['Circle']['id'])) {
            Cache::delete($this->getCacheKey(CACHE_KEY_TEAM_ALL_CIRCLE, false, null));
        }
        return $this->save($data);
    }

    /**
     * サークルにメンバーを追加する
     *
     * @param $data
     *
     * @return bool
     */
    public function addMember($data)
    {
        // 必須パラメータチェック
        if (!(isset($data['Circle']['id']) && $data['Circle']['id'] &&
            isset($data['Circle']['members']) && $data['Circle']['members'] &&
            isset($data['Circle']['team_all_flg']))
        ) {
            return false;
        }

        // チーム全体サークルは変更不可
        if ($data['Circle']['team_all_flg']) {
            return false;
        }

        // 管理者を含めたサークルメンバー全員
        $exists_member_list = $this->CircleMember->getMemberList($data['Circle']['id'], true);

        $members = explode(",", $data['Circle']['members']);
        $new_members = [];
        foreach ($members as $val) {
            $user_id = str_replace('user_', '', $val);
            if (!$user_id) {
                continue;
            }
            if (isset($exists_member_list[$user_id])) {
                continue;
            }
            $new_members[] = [
                'CircleMember' => [
                    'circle_id'             => $data['Circle']['id'],
                    'team_id'               => $this->current_team_id,
                    'user_id'               => $user_id,
                    'show_for_all_feed_flg' => false,
                    'get_notification_flg'  => false,
                ]
            ];
            $this->add_new_member_list[] = $user_id;
            Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true, $user_id), 'user_data');
            Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true, $user_id), 'user_data');
            Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true, $user_id), 'user_data');
        }

        $res = false;
        if ($new_members) {
            $res = $this->CircleMember->saveAll($new_members);
        }
        return $res;
    }

    /**
     * $keyword にマッチする公開サークル一覧を返す
     *
     * @param       $keyword
     * @param int   $limit
     * @param array $params
     *  'public_flg' 指定した場合は公開状態で絞り込む: default null
     *
     * @return array|null
     */
    public function getCirclesByKeyword($keyword, $limit = 10, array $params = [])
    {
        // オプションデフォルト
        $params = array_merge(['public_flg' => null], $params);

        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $my_circle_list = $this->CircleMember->getMyCircleList();
        $options = [
            'conditions' => [
                'id'        => $my_circle_list,
                'name LIKE' => $keyword . "%",
            ],
            'limit'      => $limit,
            'fields'     => ['name', 'id', 'photo_file_name', 'team_all_flg'],
        ];
        if ($params['public_flg'] !== null) {
            $options['conditions']['public_flg'] = $params['public_flg'];
        }
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * $keyword にマッチする公開サークル一覧を返す
     *
     * @param     $keyword
     * @param int $limit
     *
     * @return array|null
     */
    public function getPublicCirclesByKeyword($keyword, $limit = 10)
    {
        return $this->getCirclesByKeyword($keyword, $limit, ['public_flg' => 1]);
    }

    /**
     * $keyword にマッチする非公開サークル一覧を返す
     *
     * @param     $keyword
     * @param int $limit
     *
     * @return array|null
     */
    public function getSecretCirclesByKeyword($keyword, $limit = 10)
    {
        return $this->getCirclesByKeyword($keyword, $limit, ['public_flg' => 0]);
    }

    function getPublicCircles($type = 'all', $start_date = null, $end_date = null, $order = 'Circle.modified desc')
    {
        $active_user_ids = $this->Team->TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'Circle.team_id'    => $this->current_team_id,
                'Circle.public_flg' => true,
            ],
            'order'      => [$order],
            'contain'    => [
                'CircleMember' => [
                    'conditions' => [
                        'CircleMember.user_id' => $active_user_ids,
                    ],
                    'fields'     => [
                        'CircleMember.id',
                        'CircleMember.user_id'
                    ],
                ],
                'CircleAdmin'  => [
                    'conditions' => [
                        'CircleAdmin.user_id'   => $this->my_uid,
                        'CircleAdmin.admin_flg' => true
                    ],
                    'fields'     => [
                        'CircleAdmin.id'
                    ],
                ]
            ]
        ];
        if ($start_date) {
            $options['conditions']['Circle.created >='] = $start_date;
        }
        if ($end_date) {
            $options['conditions']['Circle.created <'] = $end_date;
        }
        $res = $this->find('all', $options);
        //typeに応じて絞り込み
        switch ($type) {
            //参加している
            case 'joined':
                $filter = function ($circle) {
                    foreach ($circle['CircleMember'] as $member) {
                        if ($member['user_id'] == $this->my_uid) {
                            return true;
                        }
                    }
                    return false;
                };
                break;
            //参加していない
            case 'non-joined':
                $filter = function ($circle) {
                    foreach ($circle['CircleMember'] as $member) {
                        if ($member['user_id'] == $this->my_uid) {
                            return false;
                        }
                    }
                    return true;
                };
                break;
            default :
                $filter = function () {
                    return true;
                };
        }
        $res = array_filter($res, $filter);
        return $res;
    }

    /**
     * チームに存在する全サークルのリストを返す
     *
     * @return array|null
     */
    public function getList()
    {
        $options = [
            'conditions' => [
                'Circle.team_id' => $this->current_team_id,
            ],
        ];
        return $this->find('list', $options);
    }

    /**
     * 公開サークルのリストを返す
     *
     * @return array|null
     */
    public function getPublicCircleList()
    {
        $options = [
            'conditions' => [
                'Circle.team_id'    => $this->current_team_id,
                'Circle.public_flg' => true,
            ],
        ];
        return $this->find('list', $options);
    }

    function getCirclesAndMemberById($circle_ids)
    {
        $active_user_ids = $this->Team->TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'Circle.id'      => $circle_ids,
                'Circle.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'Circle.name',
                'Circle.photo_file_name',
                'Circle.circle_member_count',
                'Circle.created',
                'Circle.modified',
                'Circle.public_flg',
                'Circle.team_all_flg',
            ],
            'contain'    => [
                'CircleMember' => [
                    'conditions' => [
                        'CircleMember.user_id' => $active_user_ids,
                    ],
                    'fields'     => [
                        'CircleMember.id'
                    ],
                    'User'       => [
                        'fields' => $this->CircleMember->User->profileFields
                    ]
                ]
            ],
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function getNameRandom($ids)
    {
        $options = [
            'conditions' => [
                'Circle.id'      => $ids,
                'Circle.team_id' => $this->current_team_id
            ],
            'fields'     => [
                'Circle.name'
            ],
            'order'      => 'rand()',
        ];
        if ($this->getDataSource()->config['datasource'] == 'Database/Sqlite') {
            $options['order'] = 'random()';
        }

        $res = $this->find('first', $options);
        if (isset($res['Circle']['name'])) {
            return $res['Circle']['name'];
        }
        return null;
    }

    function updateModified($circle_list)
    {
        if (empty($circle_list)) {
            return false;
        }
        $conditions = [
            'Circle.id' => $circle_list,
        ];

        $res = $this->updateAll(['modified' => "'" . time() . "'"], $conditions);
        return $res;
    }

    /**
     * @param $circle_id
     *
     * @return array|null
     */
    function isSecret($circle_id)
    {
        $options = [
            'conditions' => [
                'id'         => $circle_id,
                'public_flg' => false
            ]
        ];
        return $this->find('first', $options);
    }

    function isTeamAllCircle($circle_id)
    {
        $options = [
            'conditions' => [
                'id'           => $circle_id,
                'team_all_flg' => true
            ]
        ];
        $res = $this->find('first', $options);
        if (isset($res['Circle']['team_id'])) {
            return true;
        }
        return false;
    }

    /**
     * @return array|null
     */
    function getTeamAllCircle()
    {
        $model = $this;
        $res = Cache::remember($this->getCacheKey(CACHE_KEY_TEAM_ALL_CIRCLE, false, null),
            function () use ($model) {
                $options = [
                    'conditions' => [
                        'team_id'      => $this->current_team_id,
                        'team_all_flg' => true,
                    ]
                ];
                return $this->find('first', $options);

            }, 'team_info');
        return $res;
    }

    function getTeamAllCircleId()
    {
        $team_all_circle = $this->getTeamAllCircle();
        return Hash::get($team_all_circle, 'Circle.id');
    }

    /**
     * 自分が閲覧可能なサークル（公開サークル + 自分が所属している秘密サークル）の中で、
     * サークル名が $keyword にマッチするサークル一覧を返す
     *
     * @param string $keyword
     * @param int    $limit
     *
     * @return array
     */
    public function getAccessibleCirclesByKeyword($keyword, $limit = 10)
    {
        // 自分が所属しているサークル（公開 + 秘密）
        $circle_list = $this->CircleMember->getMyCircleList();

        $keyword = trim($keyword);
        $options = [
            'conditions' => [
                'OR'               => [
                    'Circle.id'         => $circle_list,
                    'Circle.public_flg' => 1,
                ],
                'Circle.name LIKE' => $keyword . '%',
                'Circle.team_id'   => $this->current_team_id,
            ],
            'limit'      => $limit,
        ];
        return $this->find('all', $options);
    }

    /**
     * 自分が閲覧可能なサークル（公開サークル + 自分が所属している秘密サークル）の select2 用データを返す
     *
     * @param string $keyword
     * @param int    $limit
     *
     * @return array
     */
    public function getAccessibleCirclesSelect2($keyword, $limit = 10)
    {
        $circles = $this->getAccessibleCirclesByKeyword($keyword, $limit);

        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $res = [];
        foreach ($circles as $val) {
            $data = [];
            $data['id'] = 'circle_' . $val['Circle']['id'];
            $data['text'] = $val['Circle']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Circle.photo', ['style' => 'small']);
            $res[] = $data;
        }
        return ['results' => $res];
    }

}
