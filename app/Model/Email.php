<?php
App::uses('AppModel', 'Model');
App::uses('TeamMember', 'Model');

use Goalous\Enum as Enum;

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
            'maxLength'     => ['rule' => ['maxLength', 255]],
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
        'email'      => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'rule' => 'notBlank',
            ],
            'email'     => [
                'rule' => ['email'],
            ],
        ],
        'name_first' => [
            'maxLength' => ['rule' => ['maxLength', 50]],
            'notBlank'  => [
                'rule' => 'notBlank',
            ],
        ],
        'name_last'  => [
            'maxLength' => ['rule' => ['maxLength', 50]],
            'notBlank'  => [
                'rule' => 'notBlank',
            ],
        ],
        'phone'      => [
            'maxLength' => ['rule' => ['maxLength', 50]],
        ],
        'company'    => [
            'maxLength' => ['rule' => ['maxLength', 50]],
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

    /**
     * Check whether user with given email address is active in the team
     *
     * @param string $email
     * @param int    $teamId
     *
     * @return bool
     */
    public function isActiveOnTeamByEmail(string $email, int $teamId): bool
    {
        $options = [
            'conditions' => [
                'Email.email'   => $email,
                'Email.del_flg' => false,
            ],
            'fields'     => ['Email.user_id'],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = Email.user_id',
                        'TeamMember.team_id' => $teamId,
                        'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE,
                        'TeamMember.del_flg' => false
                    ]
                ]

            ]
        ];
        return (bool)$this->find('count', $options);
    }

    function getEmailsBelongTeamByEmail($emails, $teamId = null)
    {
        if (!$teamId) {
            $teamId = $this->current_team_id;
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
                        'conditions' => [
                            'TeamMember.team_id' => $teamId,
                            'TeamMember.status'  => Enum\Model\TeamMember\Status::ACTIVE
                        ],
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

    function findExistUsersByEmail(array $emails): array
    {
        $res = $this->find('all', [
            'fields'     => ['Email.email', 'Email.user_id'],
            'conditions' => [
                'Email.email'   => $emails,
                'Email.del_flg' => false,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'Email.user_id = User.id',
                        'User.del_flg' => false
                    ],
                ]
            ]
        ]);
        return Hash::extract($res, '{n}.Email');
    }

    /**
     * Get existed data by target team
     *
     * @param int   $teamId
     * @param array $emails
     *
     * @return array
     */
    function findExistByTeamId(int $teamId, array $emails): array
    {
        $res = $this->find('all', [
            'fields'     => ['Email.email', 'Email.user_id'],
            'conditions' => [
                'Email.email'   => $emails,
                'Email.del_flg' => false,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'Email.user_id = User.id',
                        'User.del_flg' => false
                    ],
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'Email.user_id = TeamMember.user_id',
                        'TeamMember.team_id' => $teamId,
                        'TeamMember.del_flg' => false
                    ],
                ]
            ]
        ]);

        return Hash::extract($res, '{n}.Email.email') ?? [];
    }

    /**
     * Find users not belong any team
     *
     * @param array $emails
     *
     * @return array
     */
    public function findNotBelongAnyTeamsByEmails(array $emails): array
    {

        $options = [
            'fields'     => [
                'Email.email'
            ],
            'conditions' => [
                'Email.email'   => $emails,
                'Email.del_flg' => false
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'Email.user_id = User.id',
                        'User.del_flg' => false,
                    ]
                ],
            ]
        ];

        /** @var DboSource $db */
        $db = $this->getDataSource();
        $subQuery = $db->buildStatement([
            'fields'     => ['TeamMember.id'],
            'table'      => 'team_members',
            'alias'      => 'TeamMember',
            'conditions' => [
                'TeamMember.user_id = User.id',
                'TeamMember.status !=' => Enum\Model\TeamMember\Status::INACTIVE
            ],
        ], $this);
        $options['conditions'][] = $db->expression("NOT EXISTS (" . $subQuery . ")");
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.Email.email');
    }

    public function findVerifiedTeamMembers(int $teamId): array
    {
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init("TeamMember");

        return $this->find('all', [
            'joins' => [
                [
                    'alias' => 'TeamMember',
                    'table' => 'team_members',
                    'conditions' => [
                        "TeamMember.user_id = Email.user_id",
                        "TeamMember.team_id" => $teamId,
                        "TeamMember.status" => TeamMember::USER_STATUS_ACTIVE
                    ]
                ],
                [
                    'alias' => 'User',
                    'table' => 'users',
                    'conditions' => [
                        "User.id = Email.user_id",
                    ]
                ],
            ],
            'conditions' => [
                'Email.email_verified' => true,
                'Email.del_flg' => false
            ],
            'fields' => [
                'Email.email'
            ],
        ]);
    }

    public function findForGroup(int $groupId): array
    {
        return $this->find('all', [
            'joins' => [
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'conditions' => [
                        "MemberGroup.user_id = Email.user_id",
                        "MemberGroup.group_id" => $groupId
                    ]
                ],
            ],
            'fields' => [
                'Email.email'
            ],
        ]);
    }

    public function findVerifiedTeamMembersByEmailAndGroup(
        int $groupId,
        int $teamId,
        array $emails
    ): array {
        /** @var TeamMember **/
        $TeamMember = ClassRegistry::init("TeamMember");

        $options = [
            'conditions' => [
                'Email.email' => $emails,
                'Email.email_verified' => true,
                'Email.del_flg' => false
            ],
            'joins' => [
                [
                    'alias' => 'TeamMember',
                    'table' => 'team_members',
                    'conditions' => [
                        "TeamMember.user_id = Email.user_id",
                        "TeamMember.team_id" => $teamId,
                        "TeamMember.status" => $TeamMember::USER_STATUS_ACTIVE
                    ]
                ],
                [
                    'alias' => 'MemberGroup',
                    'table' => 'member_groups',
                    'type' => 'LEFT',
                    'conditions' => [
                        "MemberGroup.user_id = Email.user_id",
                        "MemberGroup.group_id" => $groupId
                    ]
                ],
                [
                    'alias' => 'User',
                    'table' => 'users',
                    'conditions' => [
                        "User.id = Email.user_id",
                    ]
                ],
            ],
            'fields' => [
                'Email.user_id',
                'Email.email',
                'MemberGroup.group_id',
            ]
        ];

        return $this->find('all', $options);
    }
}
