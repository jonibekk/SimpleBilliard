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
        'Circle',
        'Team',
        'User',
    ];

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
            'order'      => ['Circle.name asc'],
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

    public function getMemberList($circle_id, $with_admin = false)
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

}
