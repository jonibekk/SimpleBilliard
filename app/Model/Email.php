<?php
App::uses('AppModel', 'Model');

/**
 * Email Model
 *
 * @property User $User
 */
class Email extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'        => [
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'email'          => [
            'maxLength'     => ['rule' => ['maxLength', 200]],
            'notBlank'      => [
                'rule' => 'notBlank',
            ],
            'email'         => [
                'rule' => ['email'],
            ],
            'emailIsUnique' => [
                'rule' => ['isUnique'],
            ]
        ],
        'email_verified' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
    ];

    public $contact_validate = [
        'want'    => [
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'email'   => [
            'maxLength' => ['rule' => ['maxLength', 200]],
            'notBlank'  => [
                'rule' => 'notBlank',
            ],
            'email'     => [
                'rule' => ['email'],
            ],
        ],
        'company' => [
            'isString'  => ['rule' => 'isString',],
            'maxLength' => ['rule' => ['maxLength', 50],],
        ],
        'name'    => [
            'notBlank'  => ['rule' => 'notBlank',],
            'maxLength' => ['rule' => ['maxLength', 50]],
        ],
        'message' => [
            'notBlank'  => ['rule' => 'notBlank',],
            'maxLength' => ['rule' => ['maxLength', 3000]],
        ],
        'need'    => [
            'rule'     => ['multiple', ['min' => 1]],
            'required' => true,
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];

    public function isAllVerified($uid)
    {
        $options = [
            'conditions' => [
                'user_id'        => $uid,
                'email_verified' => false,
            ]
        ];
        $res = $this->find('first', $options);
        if (empty($res)) {
            return true;
        }
        return false;
    }

    /**
     * まだ認証が終わっていないメールを返却
     *
     * @param $uid
     *
     * @return array
     */
    public function getNotVerifiedEmail($uid)
    {
        $options = [
            'conditions' => [
                'user_id'        => $uid,
                'email_verified' => false,
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    function isActiveOnTeamByEmail($email, $team_id)
    {
        $options = [
            'conditions' => [
                'Email.email' => $email,
            ],
            'fields'     => ['user_id'],
            'contain'    => [
                'User' => [
                    'TeamMember' => [
                        'conditions' => ['TeamMember.team_id' => $team_id],
                        'fields'     => ['id', 'active_flg']
                    ]
                ]
            ]
        ];
        $res = $this->find('first', $options);
        if (isset($res['User']['TeamMember'][0]['active_flg']) && $res['User']['TeamMember'][0]['active_flg']) {
            return true;
        }
        return false;
    }

    function getEmailsBelongTeamByEmail($emails, $team_id = null)
    {
        if (!$team_id) {
            $team_id = $this->current_team_id;
        }
        $options = [
            'conditions' => [
                'Email.email'          => $emails,
                'Email.email_verified' => 1,
            ],
            'fields'     => ['user_id', 'email'],
            'contain'    => [
                'User' => [
                    'TeamMember' => [
                        'conditions' => ['TeamMember.team_id' => $team_id, 'TeamMember.active_flg' => 1],
                        'fields'     => ['id']
                    ]
                ]
            ]
        ];
        $email_team_members = $this->find('all', $options);

        foreach ($email_team_members as $k => $v) {
            if (!Hash::get($v, 'User.TeamMember')) {
                unset($email_team_members[$k]);
            }
        }
        return $email_team_members;
    }

    function isVerified($email)
    {
        $options = [
            'conditions' => [
                'email'          => $email,
                'email_verified' => true,
            ]
        ];
        if ($this->find('first', $options)) {
            return true;
        }
        return false;
    }
}
