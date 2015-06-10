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
        }
        else {
            $options = [
                'conditions' => [
                    'user_id' => $this->my_uid,
                    'team_id' => $this->current_team_id,
                ],
                'fields'     => ['circle_id'],
            ];
        }
        $res = $this->find('list', $options);
        return $res;
    }

    public function getMyCircle()
    {
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
            'order'      => ['Circle.modified desc'],
            'contain'    => [
                'Circle' => [
                    'fields' => [
                        'Circle.id',
                        'Circle.name',
                        'Circle.description',
                        'Circle.public_flg',
                        'Circle.photo_file_name',
                    ]
                ]
            ]
        ];
        $res = $this->find('all', $options);
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

    public function getMembers($circle_id, $with_admin = false, $order = 'CircleMember.modified', $order_direction = "desc")
    {
        $options = [
            'conditions' => [
                'CircleMember.circle_id' => $circle_id,
                'CircleMember.team_id'   => $this->current_team_id,
                'CircleMember.admin_flg' => false,
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
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $users = $this->getMembers($circle_id, $with_admin);
        $user_res = [];
        foreach ($users as $val) {
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['display_username'] . " (" . $val['User']['roman_username'] . ")";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $user_res[] = $data;
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

    function isBelong($circle_id, $user_id = null)
    {
        if (!$user_id) {
            $user_id = $this->my_uid;
        }
        $options = [
            'conditions' => [
                'user_id'   => $user_id,
                'circle_id' => $circle_id,
                'team_id'   => $this->current_team_id,
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
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
        return $res;
    }

    function joinCircle($postData)
    {
        if (!isset($postData['Circle']) || empty($postData['Circle'])) {
            return false;
        }
        //自分の所属しているサークルを取得
        $my_circles = $this->getMyCircle();
        $un_join_circles = [];
        $join_circles = [];
        foreach ($postData['Circle'] as $val) {
            $joined = false;
            foreach ($my_circles as $my_circle) {
                if ($val['circle_id'] == $my_circle['CircleMember']['circle_id']) {
                    $joined = true;
                    break;
                }
            }
            if ($val['join']) {
                //既に参加しているサークル以外を追加
                if (!$joined) {
                    $join_circles[] = $val['circle_id'];
                }
            }
            else {
                //既に参加しているサークルを追加
                if ($joined) {
                    $un_join_circles[] = $val['circle_id'];
                }
            }
        }
        //offのサークルを削除
        if (!empty($un_join_circles)) {
            $conditions = [
                'CircleMember.circle_id' => $un_join_circles,
                'CircleMember.user_id'   => $this->my_uid,
                'CircleMember.team_id'   => $this->current_team_id,
            ];
            $this->deleteAll($conditions);
            foreach ($un_join_circles as $val) {
                $this->updateCounterCache(['circle_id' => $val]);
            }
        }
        //onサークルを追加
        if (!empty($join_circles)) {
            $this->new_joined_circle_list = $join_circles;
            $data = [];
            foreach ($join_circles as $circle) {
                $data[] = [
                    'circle_id' => $circle,
                    'user_id'   => $this->my_uid,
                    'team_id'   => $this->current_team_id,
                ];
            }
            $this->saveAll($data);
            foreach ($join_circles as $val) {
                $this->updateCounterCache(['circle_id' => $val]);
            }
        }
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

    function joinNewMember($circle_id)
    {
        if (!empty($this->isBelong($circle_id))) {
            return;
        }
        $options = [
            'CircleMember' => [
                'circle_id' => $circle_id,
                'team_id'   => $this->current_team_id,
                'user_id'   => $this->my_uid,
            ]
        ];
        $this->create();
        return $this->save($options);
    }

    function unjoinMember($circle_id)
    {
        if (empty($this->User->CircleMember->isBelong($circle_id))) {
            return;
        }
        $this->deleteAll(
            [
                'CircleMember.circle_id' => $circle_id,
                'CircleMember.user_id'   => $this->my_uid,
                'CircleMember.team_id'   => $this->current_team_id,
            ]
        );
        return;
    }

    function show_hide_stats($userid, $circle_id)
    {
        $options = [
            'conditions' => [
                'CircleMember.user_id'   => $userid,
                'CircleMember.circle_id' => $circle_id
            ]
        ];
        $res = $this->find('first', $options);
        return viaIsSet($res['CircleMember']['show_for_all_feed_flg']);
    }

    function circle_status_toggle($circle_id, $status)
    {
        $conditions = [
            'CircleMember.circle_id' => $circle_id,
            'CircleMember.team_id'   => $this->current_team_id,
            'CircleMember.user_id'   => $this->my_uid
        ];

        $res = $this->updateAll(['CircleMember.show_for_all_feed_flg' => $status], $conditions);
        return $res;
    }
}
