<?php
App::uses('AppModel', 'Model');
App::uses('UploadHelper', 'View/Helper');
App::import('Model/Entity', 'CircleEntity');

/**
 * Circle Model
 *
 * @property Team            $Team
 * @property CircleMember    $CircleMember
 * @property PostShareCircle $PostShareCircle
 */

use Goalous\Enum\DataType\DataType as DataType;

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
                'styles'         => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'           => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url'    => 'no-image-circle.jpg',
                's3_default_url' => 'sys/defaults/no-image-circle.svg',
                'quality'        => 100,
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
            'notBlank'  => ['rule' => 'notBlank', 'required' => true],
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
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
            'canProcessImage' => ['rule' => 'canProcessImage',],
        ],
        'description'  => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
            'isString'  => ['rule' => 'isString', 'message' => 'Invalid Submission'],
            'notBlank'  => ['rule' => 'notBlank', 'required' => true],
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

    public $modelConversionTable = [
        'team_id'             => DataType::INT,
        'public_flg'          => DataType::BOOL,
        'team_all_flg'        => DataType::BOOL,
        'circle_member_count' => DataType::INT,
        'latest_post_created' => DataType::INT
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
     * Create new circle
     *
     * @param array $data
     *
     * @return bool
     */
    function add(array $data, $userId): bool
    {
        $data['Circle']['team_id'] = $this->current_team_id;
        $data['Circle']['user_id'] = $userId;

        return (bool)$this->save($data);
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
     * Use findByKeyword method since API v2
     * @deprecated
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
                'name LIKE' => '%' . $keyword . '%',
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
     * Follow latest spec
     * $keyword にマッチする公開サークル一覧を返す
     *
     * @param string $keyword
     * @param int $limit
     * @param array $filterCircleIds
     * @param bool $publicFlg
     * @return array
     */
    public function findByKeyword(string $keyword, int $limit = 10, $filterCircleIds = [], bool $publicFlg = true) : array
    {
        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $options = [
            'conditions' => [
                'id'         => $filterCircleIds,
                'name LIKE'  => $keyword . '%',
                'public_flg' => $publicFlg
            ],
            'limit'      => $limit,
        ];
        $res = $this->useType()->find('all', $options);
        return Hash::extract($res, '{n}.Circle') ?? [];
    }

    /**
     * Use findByKeyword method since API v2
     * @deprecated
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
     * Use findByKeyword method since API v2
     * @deprecated
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
        if ($type === 'non-joined') {
            $options['conditions']['Circle.team_all_flg'] = false;
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
            'fields'     => [
                'Circle.id',
                'Circle.name',
                'Circle.photo_file_name',
                'Circle.circle_member_count',
                'Circle.created',
                'Circle.modified',
                'Circle.public_flg',
                'Circle.team_all_flg',
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

    function getNameRandom($ids, $teamId = null)
    {
        $options = [
            'conditions' => [
                'Circle.id'      => $ids,
                'Circle.team_id' => $this->current_team_id ?? $teamId,
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

    /**
     * Check whether this circle is the team's default one
     *
     * @param int $circle_id
     *
     * @return bool
     */
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
     * returning teams circles exists
     * pass $teamId if this method is called from external API, or batch shell
     *
     * @param int $teamId
     *
     * @return array|null
     */
    function getTeamAllCircle($teamId = null)
    {
        $teamId = $teamId ?? $this->current_team_id;
        $model = $this;
        $this->current_team_id = $teamId;
        $res = Cache::remember($this->getCacheKey(CACHE_KEY_TEAM_ALL_CIRCLE, false, null),
            function () use ($model, $teamId) {
                $options = [
                    'conditions' => [
                        'team_id'      => $teamId,
                        'team_all_flg' => true,
                    ]
                ];
                return $this->find('first', $options);

            }, 'team_info');
        return $res;
    }

    /**
     * Return team's all circles.id
     *
     * @param int|null $teamId
     *
     * @return string|null circle.id by string
     */
    function getTeamAllCircleId($teamId = null)
    {
        $team_all_circle = $this->getTeamAllCircle($teamId);
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
                'Circle.name LIKE' => '%' . $keyword . '%',
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

    /**
     * belong to team
     *
     * @param int $teamId
     * @param int $circleId
     *
     * @return bool
     */
    function belongToTeam(int $teamId, int $circleId): bool
    {
        $options = [
            'conditions' => [
                'id'      => $circleId,
                'team_id' => $teamId
            ],
        ];

        return (bool)$this->find('first', $options);
    }

    /**
     * Update the member count of a circle
     *
     * @param int $circleId
     *
     * @return int New member count
     */
    public function updateMemberCount(int $circleId): int
    {
        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $memberCount = $CircleMember->getMemberCount($circleId, true);

        $newData = [
            'Circle.circle_member_count' => $memberCount,
            'Circle.modified'            => GoalousDateTime::now()->getTimestamp()
        ];

        $condition = [
            'Circle.id'      => $circleId,
            'Circle.del_flg' => false
        ];

        $this->updateAll($newData, $condition);

        return $memberCount;
    }

    /**
     * Get the team ID of this circle
     *
     * @param int $circleId
     *
     * @return int Team ID of the circle
     */
    public function getTeamId(int $circleId): int
    {
        $condition = [
            'conditions' => [
                'id' => $circleId
            ],
            'fields'     => [
                'team_id'
            ]
        ];
        $teamId = Hash::extract($this->useType()->find('first', $condition), '{*}.team_id');
        return $teamId[0];

    }

    /**
     * Follow latest spec
     * Get the team ID of this circle
     *
     * @param int $postId
     * @return int Team ID of the circle
     */
    public function getSharedSecretCircleByPostId(int $postId): array
    {
        $conditions = [
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'post_share_circles',
                    'alias'      => 'PostShareCircle',
                    'field'      => 'PostShareCircle.circle_id',
                    'conditions' => [
                        'Circle.id = PostShareCircle.circle_id',
                        'PostShareCircle.post_id' => $postId,
                        'Circle.public_flg' => false,
                        'PostShareCircle.del_flg' => false,
                    ],
                ]
            ]
        ];

        $res = $this->useType()->find('first', $conditions);
        return Hash::get($res, 'Circle') ?? [];
    }

    /**
     * Update the latest_post_created
     *
     * @param int $circleId
     * @param int $time
     *
     * @return bool
     */
    public function updateLatestPosted(int $circleId, int $time = null)
    {
        return $this->updateLatestPostedInCircles([$circleId], $time);
    }

    /**
     * Update the latest_post_created column of circles
     *
     * @param array $circleIds
     * @param int   $time
     *
     * @return bool
     */
    public function updateLatestPostedInCircles(array $circleIds, int $time = null): bool
    {
        if (empty($time)) {
            $time = GoalousDateTime::now()->getTimestamp();
        }

        $newData = [
            'Circle.latest_post_created' => $time,
            'Circle.modified'            => $time
        ];

        $condition = [
            'Circle.id' => $circleIds
        ];

        return $this->updateAll($newData, $condition);
    }
}
