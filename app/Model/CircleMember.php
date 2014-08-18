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
        'del_flg'   => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
        'admin_flg' => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
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

    public function getMyCircleList()
    {
        $options = [
            'conditions' => [
                'user_id' => $this->me['id'],
                'team_id' => $this->current_team_id,
            ],
            'fields'     => ['circle_id'],
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    public function getMyCircle()
    {
        $options = [
            'conditions' => [
                'CircleMember.user_id' => $this->me['id'],
                'CircleMember.team_id' => $this->current_team_id,
            ],
            'fields'     => [
                'CircleMember.id',
                'CircleMember.circle_id',
                'CircleMember.admin_flg',
                'CircleMember.unread_count',
            ],
            'order'      => ['CircleMember.unread_count desc', 'Circle.name asc'],
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
            $options['conditions']['NOT']['user_id'] = $this->me['id'];
        }
        return $this->find('list', $options);
    }

    public function getCircleInitMemberSelect2($circle_id, $with_admin = false)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $options = [
            'conditions' => [
                'CircleMember.circle_id' => $circle_id,
                'CircleMember.team_id'   => $this->current_team_id,
                'CircleMember.admin_flg' => false,
            ],
            'contain'    => [
                'User'
            ]
        ];
        if ($with_admin) {
            unset($options['conditions']['admin_flg']);
        }
        $users = $this->find('all', $options);
        $user_res = [];
        foreach ($users as $val) {
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['username'];
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
            $user_id = $this->me['id'];
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
            $conditions['NOT']['CircleMember.user_id'] = $this->me['id'];
        }

        $res = $this->updateAll(['CircleMember.unread_count' => 'CircleMember.unread_count + 1'], $conditions);
        return $res;
    }

    function updateUnreadCount($circle_id, $set_count = 0)
    {
        $conditions = [
            'CircleMember.circle_id' => $circle_id,
            'CircleMember.user_id'   => $this->me['id'],
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
                'CircleMember.user_id'   => $this->me['id'],
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
                    'user_id'   => $this->me['id'],
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

}
