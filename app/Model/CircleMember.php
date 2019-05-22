<?php
App::uses('AppModel', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Circle', 'Model');

App::import('Service', 'CirclePinService');
App::import('Model/Entity', 'CircleMemberEntity');

use Goalous\Enum as Enum;

/**
 * CircleMember Model
 *
 * @property Circle $Circle
 * @property Team   $Team
 * @property User   $User
 */

use Goalous\Enum\DataType\DataType as DataType;

class CircleMember extends AppModel
{
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'               => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => ['rule' => 'notBlank'],
        ],
        'del_flg'               => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'admin_flg'             => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'show_for_all_feed_flg' => [
            'rule'    => ['boolean'],
            'message' => 'Invalid Status'
        ]
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Circle' => [
            "counterCache" => true,
            'counterScope' => ['CircleMember.del_flg' => false]
        ],
        'Team',
        'User',
    ];

    public $modelConversionTable = [
        'circle_id'             => DataType::INT,
        'team_id'               => DataType::INT,
        'user_id'               => DataType::INT,
        'admin_flg'             => DataType::BOOL,
        'unread_count'          => DataType::INT,
        'show_for_all_feed_flg' => DataType::BOOL,
        'get_notification_flg'  => DataType::BOOL,
        'last_posted'           => DataType::INT
    ];

    public function getMyCircleList($check_hide_status = null, $userId = null, $teamId = null)
    {
        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;

        if (!is_null($check_hide_status)) {
            $options = [
                'conditions' => [
                    'user_id'               => $userId,
                    'team_id'               => $teamId,
                    'show_for_all_feed_flg' => $check_hide_status
                ],
                'fields'     => ['circle_id'],
            ];
        } else {
            $options = [
                'conditions' => [
                    'user_id' => $userId,
                    'team_id' => $teamId,
                ],
                'fields'     => ['circle_id'],
            ];
        }
        $cache_key_name = $this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true);
        if ($check_hide_status === true) {
            $cache_key_name = $this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true);
        }

        $model = $this;
        $res = Cache::remember($cache_key_name,
            function () use ($model, $options) {
                return $this->find('list', $options);
            }, 'user_data');
        return $res;
    }

    /**
     * 自分が所属しているサークルを返す
     *
     * @param array $params
     *
     * @return array|null
     */
    public function getMyCircle(
        $params = []
    )
    {
        ClassRegistry::init('Circle');
        $is_default = false;
        if (empty($params)) {
            $is_default = true;
            $circle_list = Cache::read($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');
            if ($circle_list !== false) {
                return $circle_list;
            }
        }
        $params = array_merge([
            'circle_created_start' => null,
            'circle_created_end'   => null,
            'order'                => [
                'Circle.team_all_flg desc',
                'Circle.modified desc'
            ],
        ],
            $params);

        $options = [
            'conditions' => [
                'CircleMember.user_id' => $this->my_uid,
                'CircleMember.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'CircleMember.id',
                'CircleMember.circle_id',
                'CircleMember.admin_flg',
                'CircleMember.unread_count',
            ],
            'order'      => $params['order'],
            'contain'    => [
                'Circle' => [
                    'fields' => [
                        'Circle.id',
                        'Circle.name',
                        'Circle.description',
                        'Circle.public_flg',
                        'Circle.photo_file_name',
                        'Circle.team_all_flg',
                        'Circle.created',
                        'Circle.modified',
                    ]
                ]
            ]
        ];
        if ($params['circle_created_start'] !== null) {
            $options['conditions']['Circle.created >='] = $params['circle_created_start'];
        }
        if ($params['circle_created_end'] !== null) {
            $options['conditions']['Circle.created <'] = $params['circle_created_end'];
        }
        $res = $this->find('all', $options);
        if ($is_default) {
            //TODO: このキャッシュは任意のタイミングでリセットするのが難しい(サークルに投稿がある度にリセットだとあまり効果ない)ので有効期限を特別にセット
            Cache::set('duration', 60 * 1, 'user_data');//1 minute
            Cache::write($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), $res, 'user_data');
        }
        return $res;
    }

    public function getAdminMemberList(
        $circle_id,
        $with_me = false
    )
    {
        $primary_backup = $this->primaryKey;
        $this->primaryKey = 'user_id';
        $options = [
            'conditions' => [
                'circle_id' => $circle_id,
                'admin_flg' => true,
            ],
            'fields'     => ['user_id']
        ];
        if (!$with_me) {
            $options['conditions']['NOT']['user_id'] = $this->my_uid;
        }
        $res = $this->find('list', $options);
        $this->primaryKey = $primary_backup;
        return $res;
    }

    public function getCircleInitMemberSelect2(
        $circle_id,
        $with_admin = false
    )
    {
        $users = $this->getMembers($circle_id, $with_admin);
        $user_res = $this->User->makeSelect2UserList($users);
        return ['results' => $user_res];
    }

    public function getMembers(
        $circle_id,
        $with_admin = false,
        $order = 'CircleMember.modified',
        $order_direction = "desc"
    )
    {
        $active_user_ids = $this->User->TeamMember->getActiveTeamMembersList();

        $options = [
            'conditions' => [
                'CircleMember.circle_id' => $circle_id,
                'CircleMember.team_id'   => $this->current_team_id,
                'CircleMember.admin_flg' => false,
                'CircleMember.user_id'   => $active_user_ids
            ],
            'order'      => [$order => $order_direction],
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ]
            ]
        ];
        if ($with_admin) {
            unset($options['conditions']['CircleMember.admin_flg']);
        }
        $users = $this->find('all', $options);
        return $users;
    }

    /**
     * サークルメンバーでないユーザーのリストを select2 用のデータ形式で返す
     *
     * @param     $circle_id
     * @param     $keyword
     * @param int $limit
     * @param     $with_group
     *
     * @return array
     */
    public function getNonCircleMemberSelect2(
        $circle_id,
        $keyword,
        $limit = 10,
        $with_group = false
    )
    {
        $member_list = $this->getMemberList($circle_id, true);

        $keyword = trim($keyword);
        $keyword_conditions = $this->User->makeUserNameConditions($keyword);
        $options = [
            'conditions' => [
                'TeamMember.team_id' => $this->current_team_id,
                'TeamMember.status'  => TeamMember::USER_STATUS_ACTIVE,
                'NOT'                => [
                    'TeamMember.user_id' => $member_list
                ],
                'OR'                 => $keyword_conditions,
            ],
            'limit'      => $limit,
            'contain'    => [
                'User' => [
                    'fields' => $this->User->profileFields
                ]
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'local_names',
                    'alias'      => 'SearchLocalName',
                    'conditions' => [
                        'SearchLocalName.user_id = User.id',
                    ],
                ]
            ]
        ];
        $users = $this->User->TeamMember->find('all', $options);
        $user_res = $this->User->makeSelect2UserList($users);

        // グループを結果に含める場合
        // 既にサークルメンバーになっているユーザーを除外してから返却データに追加
        if ($with_group) {
            $group_res = $this->User->getGroupsSelect2($keyword, $limit);
            $user_res = array_merge($user_res,
                $this->User->excludeGroupMemberSelect2($group_res['results'], $member_list));
        }

        return ['results' => $user_res];
    }

    public function getMemberList(
        $circle_id,
        $with_admin = false,
        $with_me = true,
        array $usersToExclude = []
    )
    {
        $primary_backup = $this->primaryKey;
        $this->primaryKey = 'user_id';
        $options = [
            'conditions' => [
                'circle_id' => $circle_id,
                'admin_flg' => false,
            ],
            'fields'     => ['user_id']
        ];
        if ($with_admin) {
            unset($options['conditions']['admin_flg']);
        }
        if (!$with_me) {
            if (empty($usersToExclude)) {
                $options['conditions']['NOT']['user_id'] = $this->my_uid;
            } else {
                $options['conditions']['NOT']['user_id'] = $usersToExclude;
            }
        }
        $res = $this->find('list', $options);

        // fetching active members list
        $active_user_ids = $this->User->TeamMember->getActiveTeamMembersList();
        // only active circle members list
        $res = array_intersect($active_user_ids, $res);
        $this->primaryKey = $primary_backup;
        return $res;
    }

    /**
     * Check whether the user is an admin in the given circle
     *
     * @param int $user_id
     * @param int $circle_id
     *
     * @return bool
     */
    public function isAdmin(int $user_id, int $circle_id): bool
    {
        $options = [
            'conditions' => [
                'circle_id' => $circle_id,
                'user_id'   => $user_id,
                'admin_flg' => true,
            ],
            'fields'     => [
                'id'
            ]
        ];
        return (bool)$this->find('count', $options);
    }

    /**
     * Increment unread count in circle_members by 1
     *
     * @param int|int[] $circle_list
     * @param bool      $withoutMe
     * @param int       $teamId
     * @param int       $userId
     *
     * @return bool
     */
    public function incrementUnreadCount($circle_list, $withoutMe = true, $teamId = 0, $userId = 0)
    {
        if (empty($circle_list)) {
            return false;
        }
        $conditions = [
            'CircleMember.circle_id' => $circle_list,
            'CircleMember.team_id'   => $teamId ?: $this->current_team_id,
        ];
        if ($withoutMe) {
            $conditions['NOT']['CircleMember.user_id'] = $userId ?: $this->my_uid;
        }
        $res = $this->updateAll(['CircleMember.unread_count' => 'CircleMember.unread_count + 1'], $conditions);
        return $res;
    }

    /**
     * Update unread count
     *
     * @param int      $circleId
     * @param int      $newUnreadCount
     * @param int|null $userId
     * @param int|null $teamId
     *
     * @return bool
     */
    public function updateUnreadCount(int $circleId, $newUnreadCount = 0, int $userId = null, int $teamId = null)
    {
        $userId = $userId ?: $this->my_uid;
        $teamId = $teamId ?: $this->current_team_id;

        $conditions = [
            'CircleMember.circle_id' => $circleId,
            'CircleMember.user_id'   => $userId,
            'CircleMember.team_id'   => $teamId,
        ];
        $res = $this->updateAll(['CircleMember.unread_count' => $newUnreadCount], $conditions);
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');
        return $res;
    }

    /**
     * join Circle
     *
     * @param int     $circleId
     * @param int     $userId
     * @param boolean $showForAllFeedFlg
     * @param boolean $getNotificationFlg
     *
     * @return mixed
     */
    function join(
        int $circleId,
        int $userId,
        bool $showForAllFeedFlg = true,
        bool $getNotificationFlg = true,
        bool $isAdmin = false
    ): bool
    {
        if (!empty($this->isBelong($circleId, $userId))) {
            return false;
        }

        $options = [
            'CircleMember' => [
                'circle_id'             => $circleId,
                'team_id'               => $this->current_team_id,
                'user_id'               => $userId,
                'admin_flg'             => $isAdmin,
                'show_for_all_feed_flg' => $showForAllFeedFlg,
                'get_notification_flg'  => $getNotificationFlg,
            ]
        ];
        $this->create();
        return (bool)$this->save($options);
    }

    function isBelong($circleId, $userId = null, $teamId = null)
    {
        $teamId = !empty($teamId) ? $teamId : $this->current_team_id;
        if (!$userId) {
            $userId = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'user_id'   => $userId,
                'circle_id' => $circleId,
                'team_id'   => $teamId,
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * Leave circle
     * - Delete circle member record
     * - Update counter cache per circle
     *
     * @param  int | array $circleId
     * @param  int         $userId
     *
     * @return bool
     */
    public function remove($circleId, int $userId): bool
    {
        $conditions = [
            'CircleMember.circle_id' => $circleId,
            'CircleMember.user_id'   => $userId,
            'CircleMember.team_id'   => $this->current_team_id,
        ];

        if (!$this->deleteAll($conditions)) {
            return false;
        }
        /** @var CirclePinService $CirclePinService */
        $CirclePinService = ClassRegistry::init('CirclePinService');
        if (!$CirclePinService->deleteCircleId($userId, $this->current_team_id, $circleId)) {
            return false;
        }
        $this->updateCounterCache(['circle_id' => $circleId]);
        return true;
    }

    function updateModified($circle_list, $team_id = null)
    {
        if (empty($circle_list)) {
            return false;
        }
        $conditions = [
            'CircleMember.circle_id' => $circle_list,
            'CircleMember.team_id'   => $team_id ?? $this->current_team_id,
            'CircleMember.user_id'   => $this->my_uid,
        ];

        $res = $this->updateAll(['modified' => "'" . time() . "'"], $conditions);
        return $res;
    }

    function unjoinMember($circle_id, $user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->my_uid;
        }
        if (empty($this->User->CircleMember->isBelong($circle_id, $user_id))) {
            return;
        }
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true), 'user_data');
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');
        return $this->deleteAll(
            [
                'CircleMember.circle_id' => $circle_id,
                'CircleMember.user_id'   => $user_id,
                'CircleMember.team_id'   => $this->current_team_id,
            ]
        );
    }

    function getShowHideStatus($userid, $circle_id)
    {
        $options = [
            'conditions' => [
                'CircleMember.user_id'   => $userid,
                'CircleMember.circle_id' => $circle_id
            ]
        ];
        $res = $this->find('first', $options);
        return Hash::get($res, 'CircleMember.show_for_all_feed_flg');
    }

    function circleStatusToggle($circle_id, $status)
    {
        $conditions = [
            'CircleMember.circle_id' => $circle_id,
            'CircleMember.team_id'   => $this->current_team_id,
            'CircleMember.user_id'   => $this->my_uid
        ];

        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');
        $res = $this->updateAll(['CircleMember.show_for_all_feed_flg' => $status], $conditions);
        return $res;
    }

    /**
     * 管理者フラグを変更する
     *
     * @param $circle_id
     * @param $user_id
     * @param $admin_status
     *
     * @return bool
     */
    function editAdminStatus($circle_id, $user_id, $admin_status)
    {
        $conditions = [
            'CircleMember.circle_id' => $circle_id,
            'CircleMember.team_id'   => $this->current_team_id,
            'CircleMember.user_id'   => $user_id,
        ];

        return $this->updateAll(['CircleMember.admin_flg' => $admin_status], $conditions);
    }

    /**
     * サークル設定を更新する
     *
     * @param $circle_id
     * @param $user_id
     * @param $data
     *
     * @return bool
     */
    function editCircleSetting($circle_id, $user_id, $data)
    {
        // 更新するデータのキー
        $setting_keys = ['show_for_all_feed_flg', 'get_notification_flg'];
        $update_data = [];
        foreach ($setting_keys as $k) {
            if (isset($data['CircleMember'][$k])) {
                $update_data[$k] = $data['CircleMember'][$k];
            }
        }
        if (!$update_data) {
            return false;
        }

        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');
        $conditions = [
            'CircleMember.team_id'   => $this->current_team_id,
            'CircleMember.circle_id' => $circle_id,
            'CircleMember.user_id'   => $user_id,
        ];
        return $this->updateAll($update_data, $conditions);
    }

    /**
     * 指定サークルの中で１つでも通知設定をオンにしているユーザーのリストを返す
     *
     * @param $circle_id
     *
     * @return array
     */
    function getNotificationEnableUserList($circle_id)
    {
        $options = [
            'conditions' => [
                'CircleMember.circle_id'            => $circle_id,
                'CircleMember.get_notification_flg' => 1,
            ],
            'fields'     => [
                'CircleMember.user_id',
                'CircleMember.user_id',
            ],
            'group'      => ['CircleMember.user_id']
        ];
        return $this->find('list', $options);
    }

    /**
     * return active member count of circle
     *
     * @param           $circle_id
     * @param bool|true $use_cache
     *
     * @return int
     */
    function getActiveMemberCount($circle_id, $use_cache = true): int
    {
        $active_team_members_list = $this->Team->TeamMember->getActiveTeamMembersList($use_cache);
        $options = [
            'conditions' => [
                'circle_id' => $circle_id,
                'user_id'   => $active_team_members_list,
            ],
            'fields'     => [
                'id'
            ]
        ];
        $res = (int)$this->find('count', $options);
        return $res;
    }

    /**
     * Get member count each circle
     * array key: circle id, value: member count
     * e.g. [1 => 3, 10 => 100]
     *
     * @param array $circleIds
     *
     * @return array
     */
    function countEachCircle(array $circleIds): array
    {
        $options = [
            'conditions' => [
                'CircleMember.circle_id' => $circleIds,
                'CircleMember.del_flg'   => false
            ],
            'fields'     => [
                'CircleMember.circle_id',
                'COUNT(CircleMember.circle_id) as count'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.team_id = CircleMember.team_id',
                        'TeamMember.user_id = CircleMember.user_id',
                        'TeamMember.del_flg' => false,
                        'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE,
                    ]
                ]
            ],
            'group'      => 'CircleMember.circle_id'
        ];
        $res = $this->find('all', $options);
        $res = Hash::combine($res, '{n}.CircleMember.circle_id', '{n}.0.count');
        foreach ($circleIds as $circleId) {
            $res[$circleId] = array_key_exists($circleId, $res) ? (int)$res[$circleId] : 0;
        }
        return $res;
    }

    /**
     * 複数サークルのアクティブメンバー数をまとめて返す
     *
     * @param $circle_ids
     *
     * @return array|null
     */
    function getActiveMemberCountList($circle_ids)
    {
        $active_team_members_list = $this->Team->TeamMember->getActiveTeamMembersList();
        $options = [
            'fields'     => [
                'CircleMember.circle_id',
                'COUNT(*) as cnt',
            ],
            'conditions' => [
                'circle_id' => $circle_ids,
                'user_id'   => $active_team_members_list,
            ],
            'group'      => 'CircleMember.circle_id',
        ];
        $rows = $this->find('all', $options);

        $count_list = [];
        foreach ($rows as $row) {
            $count_list[$row['CircleMember']['circle_id']] = $row[0]['cnt'];
        }
        foreach ($circle_ids as $id) {
            if (!isset($count_list[$id])) {
                $count_list[$id] = 0;
            }
        }
        return $count_list;
    }

    function isJoinedForSetupBy($user_id)
    {
        $options = [
            'conditions' => [
                'user_id' => $user_id
            ],
            'fields'     => ['CircleMember.id'],
            'contain'    => [
                'Circle' => [
                    'conditions' => [
                        'Circle.team_all_flg' => false
                    ],
                    'fields'     => ['Circle.id']
                ]
            ]
        ];

        $circles = $this->findWithoutTeamId('all', $options);

        $is_joined_circle = false;
        foreach ($circles as $circle) {
            if (Hash::get($circle, 'Circle.id')) {
                $is_joined_circle = true;
                break;
            }
        }

        return $is_joined_circle;
    }

    /**
     * already joined or not
     *
     * @param int $circleId
     * @param int $userId
     *
     * @return bool
     */
    function isJoined(int $circleId, int $userId): bool
    {
        $options = [
            'conditions' => [
                'user_id'   => $userId,
                'circle_id' => $circleId
            ],
            'fields'     => [
                'id'
            ]
        ];

        return (bool)$this->find('first', $options) ?? false;
    }

    /**
     * Get circle member information of an user in a circle
     *
     * @param int $circleId
     * @param int $userId
     *
     * @return array
     */
    public function getCircleMember(int $circleId, int $userId): array
    {

        $options = [
            'conditions' => [
                'circle_id' => $circleId,
                'user_id'   => $userId
            ],
        ];

        return Hash::get($this->find('first', $options), 'CircleMember') ?? [];
    }

    /**
     * Get circle member information of users in a circle
     *
     * @param int  $circleId
     * @param bool $getNotificationFlg
     *
     * @return CircleMemberEntity[]
     */
    public function getMembersWithNotificationFlg(int $circleId, bool $getNotificationFlg): array
    {

        $options = [
            'conditions' => [
                'circle_id'            => $circleId,
                'get_notification_flg' => $getNotificationFlg,
                'del_flg'              => false
            ],
        ];

        $result = $this->useType()->useEntity()->find('all', $options);

        return $result;
    }

    /**
     * Get circle members information of an user
     *
     * @param int  $userId
     * @param bool $getNotificationFlg
     *
     * @return CircleMemberEntity[]
     */
    public function getCirclesWithNotificationFlg(int $userId, bool $getNotificationFlg): array
    {

        $options = [
            'conditions' => [
                'user_id'              => $userId,
                'get_notification_flg' => $getNotificationFlg,
                'del_flg'              => false
            ],
        ];

        $result = $this->useType()->useEntity()->find('all', $options);

        return $result;
    }

    /**
     * Count number of members in a circle
     *
     * @param int  $circleId
     * @param bool $activeOnly
     *
     * @return int
     */
    public function getMemberCount(int $circleId, bool $activeOnly = true): int
    {
        $conditions = [
            'conditions' => [
                'CircleMember.circle_id' => $circleId,
                'CircleMember.del_flg'   => false
            ],
        ];

        if ($activeOnly) {
            /** @var Circle $Circle */
            $Circle = ClassRegistry::init('Circle');

            /** @var TeamMember $TeamMember */
            $TeamMember = ClassRegistry::init('TeamMember');

            $userList = $TeamMember->getMemberList($Circle->getTeamId($circleId),
                Goalous\Enum\Model\TeamMember\Status::ACTIVE());

            $conditions['conditions']['CircleMember.user_id'] = Hash::extract($userList, '{n}.{*}.user_id');
        }

        $count = array_keys($this->find('all', $conditions));
        return count($count);
    }

    /**
     * Get post unread count in a circle for an user
     *
     * @param int $circleId
     * @param int $userId
     *
     * @return int
     */
    public function getUnreadCount(int $circleId, int $userId): int
    {
        $condition = [
            'conditions' => [
                'circle_id' => $circleId,
                'user_id'   => $userId,
                'del_flg'   => false
            ],
            'fields'     => [
                'unread_count'
            ]
        ];

        $res = $this->useType()->find('first', $condition);

        if (empty($res['CircleMember']['unread_count'])) {
            return 0;
        }

        return $res['CircleMember']['unread_count'];
    }

    /**
     * Get notification flg of an user in a circle
     *
     * @param int $circleId
     * @param int $userId
     *
     * @return bool
     */
    public function getNotificationFlg(int $circleId, int $userId): bool
    {
        $condition = [
            'conditions' => [
                'circle_id' => $circleId,
                'user_id'   => $userId,
                'del_flg'   => false
            ],
            'fields'     => [
                'get_notification_flg'
            ]
        ];

        $res = $this->useType()->find('first', $condition);

        if (empty($res['CircleMember']['get_notification_flg'])) {
            return false;
        }

        return $res['CircleMember']['get_notification_flg'];
    }

    /**
     * Get all unread count
     *
     * @param int  $userId
     * @param bool $checkNotifSetting
     *
     * @return array
     */
    public function getAllUnread(int $userId, bool $checkNotifSetting = false): array
    {
        $condition = [
            'conditions' => [
                'user_id'        => $userId,
                'unread_count >' => 0,
                'del_flg'        => false,
            ]
            ,
            'fields'     => [
                'circle_id',
                'unread_count'
            ]
        ];

        if ($checkNotifSetting) {
            $condition ['conditions']['get_notification_flg'] = true;
        }

        $res = $this->useType()->find('all', $condition);

        return Hash::extract($res, '{n}.CircleMember', []);
    }

    /**
     * Get specific user_id in a circle
     *
     * @param int   $circle_id
     * @param array $user_id
     *
     * @return array
     */
    public function getSpecificMember(
        $circle_id,
        $user_id,
        $team_id
    ): array
    {
        $options = [
            'conditions' => [
                'CircleMember.circle_id' => $circle_id,
                'CircleMember.team_id'   => $team_id,
                'CircleMember.user_id'   => $user_id
            ],
            'fields'     => [
                'CircleMember.user_id'
            ]
        ];

        $users = $this->find('list', $options);

        return array_values($users);
    }
}
