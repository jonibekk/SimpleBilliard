<?php
App::uses('AppModel', 'Model');

/**
 * CircleMember Model
 *
 * @property Circle $Circle
 * @property Team   $Team
 * @property User   $User
 */
class CircleMember extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
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

    public $new_joined_circle_list = [];

    public function getMyCircleList($check_hide_status = null)
    {
        if (!is_null($check_hide_status)) {
            $options = [
                'conditions' => [
                    'user_id'               => $this->my_uid,
                    'team_id'               => $this->current_team_id,
                    'show_for_all_feed_flg' => $check_hide_status
                ],
                'fields'     => ['circle_id'],
            ];
        } else {
            $options = [
                'conditions' => [
                    'user_id' => $this->my_uid,
                    'team_id' => $this->current_team_id,
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
    public function getMyCircle($params = [])
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

    public function getMemberList($circle_id, $with_admin = false, $with_me = true)
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
            $options['conditions']['NOT']['user_id'] = $this->my_uid;
        }
        $res = $this->find('list', $options);

        // fetching active members list
        $active_user_ids = $this->User->TeamMember->getActiveTeamMembersList();
        // only active circle members list
        $res = array_intersect($active_user_ids, $res);
        $this->primaryKey = $primary_backup;
        return $res;
    }

    public function getAdminMemberList($circle_id, $with_me = false)
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

    public function getMembers(
        $circle_id,
        $with_admin = false,
        $order = 'CircleMember.modified',
        $order_direction = "desc"
    ) {
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

    public function getCircleInitMemberSelect2($circle_id, $with_admin = false)
    {
        $users = $this->getMembers($circle_id, $with_admin);
        $user_res = $this->User->makeSelect2UserList($users);
        return ['results' => $user_res];
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
    public function getNonCircleMemberSelect2($circle_id, $keyword, $limit = 10, $with_group = false)
    {
        $member_list = $this->getMemberList($circle_id, true);

        $keyword = trim($keyword);
        $keyword_conditions = $this->User->makeUserNameConditions($keyword);
        $options = [
            'conditions' => [
                'TeamMember.team_id'    => $this->current_team_id,
                'TeamMember.active_flg' => true,
                'NOT'                   => [
                    'TeamMember.user_id' => $member_list
                ],
                'OR'                    => $keyword_conditions,
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

    function isAdmin($user_id, $circle_id)
    {
        $options = [
            'conditions' => [
                'circle_id' => $circle_id,
                'user_id'   => $user_id,
                'admin_flg' => true,
            ]
        ];
        return $this->find('first', $options);
    }

    function isBelong($circleId, $userId = null): bool
    {
        if (!$userId) {
            $userId = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'user_id'   => $userId,
                'circle_id' => $circleId,
                'team_id'   => $this->current_team_id,
            ],
            'fields'     => ['id']
        ];
        $res = $this->find('first', $options);
        return (bool)$res;
    }

    function incrementUnreadCount($circle_list, $without_me = true)
    {
        if (empty($circle_list)) {
            return false;
        }
        $conditions = [
            'CircleMember.circle_id' => $circle_list,
            'CircleMember.team_id'   => $this->current_team_id,
        ];
        if ($without_me) {
            $conditions['NOT']['CircleMember.user_id'] = $this->my_uid;
        }
        $res = $this->updateAll(['CircleMember.unread_count' => 'CircleMember.unread_count + 1'], $conditions);
        return $res;
    }

    function updateUnreadCount($circle_id, $set_count = 0)
    {
        $conditions = [
            'CircleMember.circle_id' => $circle_id,
            'CircleMember.user_id'   => $this->my_uid,
            'CircleMember.team_id'   => $this->current_team_id,
        ];
        $res = $this->updateAll(['CircleMember.unread_count' => $set_count], $conditions);
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');
        return $res;
    }

    /**
     * joinCircles
     *
     * @param int     $circleId
     * @param int     $userId
     * @param boolean $showForAllFeedFlg
     * @param boolean $getNotificationFlg
     *
     * @return mixed
     */
    function join(int $circleId, int $userId, bool $showForAllFeedFlg = true, bool $getNotificationFlg = true, bool $isAdmin = false): bool
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

    /**
     * Leave circle
     * - Delete circle member record
     * - Update counter cache per circle
     *
     * @param  array $circleId
     * @param  int   $userId
     *
     * @return bool
     */
    function leave(int $circleId, int $userId) :bool
    {
        $conditions = [
            'CircleMember.circle_id' => $circleId,
            'CircleMember.user_id'   => $userId,
            'CircleMember.team_id'   => $this->current_team_id,
        ];

        $this->deleteAll($conditions);
        $this->updateCounterCache(['circle_id' => $circleId]);
        return true;
    }

    function updateModified($circle_list)
    {
        if (empty($circle_list)) {
            return false;
        }
        $conditions = [
            'CircleMember.circle_id' => $circle_list,
            'CircleMember.team_id'   => $this->current_team_id,
            'CircleMember.user_id'   => $this->my_uid,
        ];

        $res = $this->updateAll(['modified' => "'" . time() . "'"], $conditions);
        return $res;
    }

    /**
     * @param         $circle_id
     * @param boolean $show_for_all_feed_flg
     * @param boolean $get_notification_flg
     *
     * @return mixed
     */
    function joinNewMember($circle_id, $show_for_all_feed_flg = true, $get_notification_flg = true)
    {
        if (!empty($this->isBelong($circle_id))) {
            return false;
        }
        $options = [
            'CircleMember' => [
                'circle_id'             => $circle_id,
                'team_id'               => $this->current_team_id,
                'user_id'               => $this->my_uid,
                'show_for_all_feed_flg' => $show_for_all_feed_flg,
                'get_notification_flg'  => $get_notification_flg,
            ]
        ];
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true), 'user_data');
        Cache::delete($this->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');
        Cache::delete($this->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');

        $this->create();
        return $this->save($options);
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
     * @return array|null
     */
    function getActiveMemberCount($circle_id, $use_cache = true)
    {
        $active_team_members_list = $this->Team->TeamMember->getActiveTeamMembersList($use_cache);
        $options = [
            'conditions' => [
                'circle_id' => $circle_id,
                'user_id'   => $active_team_members_list,
            ]
        ];
        $res = $this->find('count', $options);
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
}
