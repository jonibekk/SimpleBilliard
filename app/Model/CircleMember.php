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

}
