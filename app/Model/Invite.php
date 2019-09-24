<?php
App::uses('AppModel', 'Model');
App::uses('TimeExHelper', 'View/Helper');
App::uses('View', 'View');

/**
 * Invite Model
 *
 * @property User $FromUser
 * @property User $ToUser
 * @property Team $Team
 */
class Invite extends AppModel
{
    const TYPE_NORMAL = 0;
    const TYPE_BATCH = 1;

    public $tokenData = [];
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'email'          => ['email' => ['rule' => ['email']]],
        'email_verified' => ['boolean' => ['rule' => ['boolean']]],
        'del_flg'        => ['boolean' => ['rule' => ['boolean']]],
        'message'        => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
        ]

    ];

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
        'ToUser'   => ['className' => 'User', 'foreignKey' => 'to_user_id',],
        'Team',
    ];

    function saveInvite($email, $team_id, $from_uid, $message = null)
    {
        //既に招待済みの場合は古い招待メールを削除
        $exists = $this->find('first',
            [
                'conditions' => [
                    'team_id' => $team_id,
                    'email'   => $email
                ]
            ]);
        if (!empty($exists)) {
            $this->delete($exists['Invite']['id']);
        }

        $data = [];
        $data['Invite']['from_user_id'] = $from_uid;
        $data['Invite']['team_id'] = $team_id;
        $data['Invite']['email'] = $email;
        $data['Invite']['email_token'] = $this->generateToken();
        $data['Invite']['email_token_expires'] = $this->getTokenExpire(TOKEN_EXPIRE_SEC_INVITE);
        //既に登録済みのユーザの場合はuser_idをセット
        if (!empty($user_id = $this->ToUser->Email->findByEmail($email))) {
            $data['Invite']['to_user_id'] = $user_id['Email']['user_id'];
        }
        //メッセージがある場合は
        if ($message) {
            $data['Invite']['message'] = $message;
        }
        $this->create();
        $res = $this->save($data);
        return $res;
    }

    /**
     * Invite bulk
     *
     * @param int    $emails
     * @param int    $teamId
     * @param int    $fromUserId
     * @param string $msg
     *
     * @return mixed
     */
    function saveBulk(array $emails, int $teamId, int $fromUserId, string $msg = "")
    {
        // Get emails of registered users
        $registeredEmails = Hash::combine($this->ToUser->Email->findAllByEmail($emails), '{n}.Email.email',
            '{n}.Email.user_id');

        // Get invitation token expire
        $tokenExpire = $this->getTokenExpire(TOKEN_EXPIRE_SEC_INVITE);

        $insertData = [];
        foreach ($emails as $email) {
            $toUserId = !empty($registeredEmails[$email]) ? $registeredEmails[$email] : null;
                $insertData[] = [
                'from_user_id'        => $fromUserId,
                'to_user_id'          => $toUserId,
                'team_id'             => $teamId,
                'email'               => $email,
                'email_token'         => $this->generateToken(),
                'email_token_expires' => $tokenExpire,
                'message'             => $msg,
            ];
        }
        return $this->bulkInsert($insertData);
    }

    /**
     * トークンのチェック
     *
     * @param $token
     *
     * @return bool
     * @throws RuntimeException
     */
    public function confirmToken($token)
    {
        $invite = $this->getByToken($token);
        if (empty($invite)) {
            return __("The invitation token is incorrect. Check your email again.");
        }
        if ($invite['Invite']['email_verified']) {
            return __('This invitation token has already been used.');
        }
        if ($invite['Invite']['email_token_expires'] < REQUEST_TIMESTAMP) {
            return __('The invitation token is expired.');
        }
        return true;
    }

    /**
     * 招待の認証
     *
     * @param string $token The token that wa sent to the user
     * @param        $user_id
     *
     * @return array On success it returns the user data record
     */
    public function verify($token, $user_id)
    {
        $invite = $this->getByToken($token);
        $invite['Invite']['email_verified'] = true;
        $invite['Invite']['to_user_id'] = $user_id;
        $res = $this->save($invite);
        return $res;
    }

    function getByToken($token)
    {
        if (empty($this->tokenData)) {
            return $this->setInviteByToken($token);
        } else {
            return $this->tokenData;
        }
    }

    function isByBatchSetup($token)
    {
        $invite = $this->getByToken($token);
        if (!Hash::get($invite, 'Invite.email')) {
            return false;
        }

        $user = $this->FromUser->getUserByEmail($invite['Invite']['email']);
        if (Hash::get($user,
                'User') && $user['User']['active_flg'] === false && $user['User']['no_pass_flg'] === true
        ) {
            return true;
        }
        return false;
    }

    function isForMe($token, $uid)
    {
        $invite = $this->getByToken($token);
        if (isset($invite['Invite']['to_user_id']) && !empty($invite['Invite']['to_user_id'])) {
            return $invite['Invite']['to_user_id'] === $uid;
        } //招待先のメアドが既に登録済みユーザの場合で、そのユーザが自分だった場合はtrueを返す
        elseif (isset($invite['Invite']['email'])) {
            $options = [
                'conditions' => [
                    'user_id' => $uid,
                    'email'   => $invite['Invite']['email'],
                ]
            ];
            if (!empty($this->ToUser->Email->find('first', $options))) {
                return true;
            }
        }
        return false;
    }

    function setInviteByToken($token)
    {
        $options = [
            'conditions' => [
                'email_token' => $token
            ],
        ];
        $invite = $this->findWithoutTeamId('first', $options);
        $this->tokenData = $invite;
        return $this->tokenData;
    }

    function isUser($token)
    {
        $invite = $this->getByToken($token);
        if (isset($invite['Invite']['to_user_id']) && !empty($invite['Invite']['to_user_id'])) {
            return true;
        } elseif (isset($invite['Invite']['email'])) {
            $options = [
                'conditions' => [
                    'email' => $invite['Invite']['email'],
                ]
            ];
            if (!empty($this->ToUser->Email->find('first', $options))) {
                return true;
            }
        }

        return false;
    }

    function isUserPreRegistered($token)
    {
        $invite = $this->getByToken($token);
        if (!isset($invite['Invite']['email'])) {
            return false;
        }
        if (empty($email = $this->ToUser->Email->find('first', [
            'conditions' => [
                'email'          => $invite['Invite']['email'],
                'email_verified' => false
            ]
        ]))
        ) {
            return false;
        }

        if (empty($this->ToUser->find('first', [
            'conditions' => [
                'id'         => $email['Email']['user_id'],
                'active_flg' => false
            ]
        ]))
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param $invite_id
     *
     * @return null
     */
    function getInviteById($invite_id)
    {
        $options = [
            'conditions' => [
                'Invite.id' => $invite_id
            ],
            'contain'    => [
                'FromUser' => [
                    'fields' => $this->FromUser->profileFields
                ],
                'ToUser'   => [
                    'fields' => $this->FromUser->profileFields
                ],
                'Team'
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    function getInviteUserList($team_id)
    {
        $options = [
            'fields'     => [
                'Invite.email', 'Invite.created', 'Invite.id', 'Invite.del_flg', 'Invite.email_token_expires',
                'Email.user_id',
            ],
            'order'      => 'Invite.created DESC',
            'conditions' => [
                'Invite.team_id'        => $team_id,
                'Invite.email_verified' => 0,
                'Invite.del_flg'        => 0,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'emails',
                    'alias'      => 'Email',
                    'conditions' => [
                        'Invite.email = Email.email',
                        'Email.del_flg' => 0
                    ],
                ]
            ]
        ];
        $res = $this->find('all', $options);

        $time = new TimeExHelper(new View());
        foreach ($res as $key => $val) {
            // check if token expired
            $token_expired_flg = false;
            if ($res[$key]['Invite']['email_token_expires'] < REQUEST_TIMESTAMP) {
                $token_expired_flg = true;
            }
            $res[$key]['Invite']['token_expired_flg'] = $token_expired_flg;
            $res[$key]['Invite']['created'] = $time->elapsedTime(h($val['Invite']['created']));
        }
        return $res;
    }

    /**
     * Find by emails and current team
     *
     * @param array $emails
     *
     * @return array
     */
    function findByEmails(array $emails): array
    {
        $options = [
            'conditions' => [
                'team_id' => $this->current_team_id,
                'email'   => $emails
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * find unverified invites before token expired
     *
     * @param int $baseTime
     *
     * @return array
     */
    function findUnverifiedBeforeExpired(int $baseTime): array
    {
        $options = [
            'conditions' => [
                'Invite.email_verified'        => false,
                'Invite.email_token_expires >' => $baseTime
            ]
        ];
        $invites = $this->find('all', $options);
        return Hash::extract($invites, '{n}.Invite');
    }

    /**
     * find unverified Invite with Email by users.id
     * @param int $userId
     * @param int $teamId
     *
     * @return array
     */
    function getUnverifiedWithEmailByUserId(int $userId, int $teamId): array
    {
        return $this->find('first', [
            'fields' => [
                    'Invite.id', 'Invite.from_user_id', 'Invite.to_user_id', 'Invite.team_id', 'Invite.email',
                    'Invite.email_verified', 'Invite.message',
                    'Email.id', 'Email.email'
                ],
            'conditions' => [
                'Invite.team_id'        => $teamId,
                'Invite.email_verified' => 0,
                'Invite.del_flg'        => 0,
                'Email.user_id'         => $userId,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'emails',
                    'alias'      => 'Email',
                    'conditions' => [
                        'Invite.email = Email.email',
                    ],
                ]
            ]
        ]);
    }
}
