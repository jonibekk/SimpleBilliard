<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('AppModel', 'Model');
App::uses('AppUtil', 'Util');
App::uses('Email', 'Model');
App::import('Model/Entity', 'UserEntity');

use Goalous\Enum as Enum;
use Goalous\Enum\DataType\DataType as DataType;

/** @noinspection PhpUndefinedClassInspection */

/**
 * User Model
 *
 * @property Email          $PrimaryEmail
 * @property Team           $DefaultTeam
 * @property Badge          $Badge
 * @property CommentLike    $CommentLike
 * @property CommentMention $CommentMention
 * @property CommentRead    $CommentRead
 * @property Comment        $Comment
 * @property Email          $Email
 * @property GivenBadge     $GivenBadge
 * @property NotifySetting  $NotifySetting
 * @property OauthToken     $OauthToken
 * @property PostLike       $PostLike
 * @property PostMention    $PostMention
 * @property PostRead       $PostRead
 * @property Post           $Post
 * @property Goal           $Goal
 * @property TeamMember     $TeamMember
 * @property CircleMember   $CircleMember
 * @property LocalName      $LocalName
 * @property GoalMember     $GoalMember
 * @property MemberGroup    $MemberGroup
 * @property RecoveryCode   $RecoveryCode
 * @property Device         $Device
 * @property TermsOfService $TermsOfService
 */
class User extends AppModel
{
    /**
     * 性別タイプ
     */
    const TYPE_GENDER_MALE = '1';
    const TYPE_GENDER_FEMALE = '2';
    const TYPE_GENDER_NEITHER = '3';
    static public $TYPE_GENDER = [
        self::TYPE_GENDER_MALE    => "",
        self::TYPE_GENDER_FEMALE  => "",
        self::TYPE_GENDER_NEITHER => ""
    ];

    const USER_NAME_REGEX = '^[a-zA-Z\p{Latin} \‘’’]+$';
    const USER_NAME_REGEX_JAVASCRIPT = '^[a-zA-Z\u00C0-\u017F \'‘’]+$';
    const USER_PASSWORD_REGEX = '/\A(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}\z/i';

    /**
     * 性別タイプの名前をセット
     */
    private function _setGenderTypeName()
    {
        self::$TYPE_GENDER[self::TYPE_GENDER_MALE] = __("Male");
        self::$TYPE_GENDER[self::TYPE_GENDER_FEMALE] = __("Female");
        self::$TYPE_GENDER[self::TYPE_GENDER_NEITHER] = __("Neither");
    }

    /**
     * STATUS_TYPE_OF_SETUP_GUIDE
     */
    const SETUP_PROFILE = 1;
    const SETUP_MOBILE_APP = 2;
    const SETUP_GOAL_CREATED = 3;
    const SETUP_ACTION_POSTED = 4;
    const SETUP_CIRCLE_JOINED_OR_CREATED = 5;
    const SETUP_CIRCLE_POSTED = 6;

    static public $TYPE_SETUP_GUIDE = [
        self::SETUP_PROFILE                  => "",
        self::SETUP_MOBILE_APP               => "",
    ];

    const SETUP_GUIDE_IS_NOT_COMPLETED = 0;
    const SETUP_GUIDE_IS_COMPLETED = 1;

    public $actsAs = [
        'Upload' => [
            'photo'       => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-user.jpg',
                's3_default_url' => 'sys/defaults/no-image-user.svg',
                'quality'     => 100,
            ],
            'cover_photo' => [
                'styles'      => [
                    'small'  => 'f[254x142]',
                    'medium' => 'f[672x378]',
                    'large'  => 'f[2048x1152]',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-cover.jpg',
                's3_default_url' => 'sys/defaults/no-image-cover.svg',
                'quality'     => 100,
            ],
        ]
    ];
    /**
     * ユーザ名の表記が姓名の順である言語のリスト
     */
    public $langCodeOfLastFirst = [
        //日本
        'jpn',
        //韓国
        'kor',
        //中国
        'chi',
        //ハンガリー
        'hun',
    ];

    /**
     * ローカル名の一時格納用
     *
     * @var array
     */
    protected $local_names = [];

    /**
     * ユーザIDの一時格納用
     *
     * @var array
     */
    protected $uids = [];

    /**
     * Type conversion table for User model
     *
     * @var array
     */
    protected $modelConversionTable = [
        'default_team_id' => DataType::INT,
        'timezone' => DataType::INT
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'team_id'            => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'first_name'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'     => ['rule' => 'notBlank'],
            'userNameChar' => ['rule' => ['userNameChar']],
        ],
        'last_name'          => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'     => ['rule' => 'notBlank'],
            'userNameChar' => ['rule' => ['userNameChar']],
        ],
        'gender_type'        => [
            'isString' => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
        ],
        'birth_day'          => [
            'rule'       => ['date', 'ymd'],
            'allowEmpty' => true
        ],
        'hide_year_flg'      => [
            'boolean' => [
                'rule'       => ['boolean',],
                'allowEmpty' => true,
            ],
        ],
        'no_pass_flg'        => ['boolean' => ['rule' => ['boolean'],],],
        'active_flg'         => ['boolean' => ['rule' => ['boolean'],],],
        'admin_flg'          => ['boolean' => ['rule' => ['boolean'],],],
        'auto_timezone_flg'  => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
        'auto_language_flg'  => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
        'romanize_flg'       => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
        'update_email_flg'   => [
            'boolean' => [
                'rule'       => ['boolean',],
                'allowEmpty' => true,
            ],
        ],
        'language'           => [
            'isString' => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
        ],
        'timezone'           => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'default_team_id'    => [
            'numeric' => [
                'rule'       => ['numeric'],
                'allowEmpty' => true,
            ],
        ],
        'agree_tos'          => [
            'notBlankCheckbox' => [
                'rule'       => ['custom', '[1]'],
                'allowEmpty' => true,
            ]
        ],
        'del_flg'            => ['boolean' => ['rule' => ['boolean'],],],
        'old_password'       => [
            'notBlank'  => [
                'rule' => 'notBlank',
            ],
            'minLength' => [
                'rule' => ['minLength', 8],
            ]
        ],
        'photo'              => [
            'canProcessImage' => ['rule' => 'canProcessImage',],
            'image_max_size'  => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'      => ['rule' => ['attachmentImageType',],],
        ],
        'hometown'           => [
            'maxLength' => ['rule' => ['maxLength', 128]],
            'isString'  => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
        ],
        'cover_photo'        => [
            'canProcessImage'     => ['rule' => 'canProcessImage'],
            'image_max_size'      => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'          => ['rule' => ['attachmentImageType',],],
            'imageMinWidthHeight' => ['rule' => ['minWidthHeight', 672, 378]],
        ],
        'comment'            => [
            'maxLength' => ['rule' => ['maxLength', 2000]],
        ],
        'phone_no'           => [
            'maxLength' => ['rule' => ['maxLength', 20]],
        ],
        'setup_complete_flg' => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
    ];

    public $validatePassword = [
        'password_request'  => [
            'maxLength'     => ['rule' => ['maxLength', 50]],
            'notBlank'      => [
                'rule' => 'notBlank',
            ],
            'minLength'     => [
                'rule' => ['minLength', 8],
            ],
            'passwordCheck' => [
                'rule' => ['passwordCheck', 'password_request'],
            ]
        ],
        'password_request2' => [
            'maxLength'     => ['rule' => ['maxLength', 50]],
            'notBlank'      => [
                'rule' => 'notBlank',
            ],
            'minLength'     => [
                'rule' => ['minLength', 8],
            ],
            'passwordCheck' => [
                'rule' => ['passwordCheck', 'password_request2'],
            ]
        ],
        'password'          => [
            'maxLength'      => ['rule' => ['maxLength', 50]],
            'notBlank'       => [
                'rule' => 'notBlank',
            ],
            'minLength'      => [
                'rule' => ['minLength', 8],
            ],
            'passwordPolicy' => [
                'rule' => [
                    'custom',
                    self::USER_PASSWORD_REGEX,
                ]
            ]
        ],
        'password_confirm'  => [
            'notBlank'          => [
                'rule' => 'notBlank',
            ],
            'passwordSameCheck' => [
                'rule' => ['passwordSameCheck', 'password'],
            ],
        ],
    ];

    public $profileFields = [
        'id',
        'first_name',
        'last_name',
        'photo_file_name',
        'cover_photo_file_name',
        'language',
        'auto_language_flg',
        'romanize_flg',
    ];

    /**
     * User fields to be returned on user login
     *
     * @var array
     */
    public $loginUserFields = [
        'id',
        'photo_file_name',
        'cover_photo_file_name',
        'first_name',
        'last_name',
        'middle_name',
        'language',
        'active_flg',
        'last_login',
        'admin_flg',
        'default_team_id',
        'timezone',
        'language',
        'romanize_flg',
        'update_email_flg',
        'agreed_terms_of_service_id',
        'gender_type',
        'birth_day',
        'hide_year_flg',
        'phone_no',
        'hometown',
        'primary_email_id',
        'setup_complete_flg',
        'created'
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'DefaultTeam'    => ['className' => 'Team', 'foreignKey' => 'default_team_id',],
        'PrimaryEmail'   => ['className' => 'Email', 'foreignKey' => 'primary_email_id', 'dependent' => true],
        'TermsOfService' => [
            'className'  => 'TermsOfService',
            'foreignKey' => 'agreed_terms_of_service_id'
        ]
    ];

    public $hasOne = [
        'NotifySetting',
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
        'Email',
        'OauthToken',
        'PostLike',
        'PostMention',
        'PostRead',
        'Post',
        'TeamMember',
        'LocalName',
        'CircleMember',
        'Goal',
        'MemberGroup',
        'GoalMember',
        'RecoveryCode',
        'Device',
        'TopicMember'
    ];

    /**
     * ローカル名を使わない言語のリスト
     */
    public $langCodeOfNotLocalName = [
        'eng',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        /** @noinspection PhpUndefinedClassInspection */
        parent::__construct($id, $table, $ds);
        $this->_setGenderTypeName();
    }

    public function resetLocalNames()
    {
        $this->local_names = [];
        $this->uids = [];
    }

    public function beforeSave($options = [])
    {
        //英名、英性の頭文字を大文字に変更
        if (!empty($this->data[$this->alias]['first_name']) &&
            !empty($this->data[$this->alias]['last_name'])
        ) {
            $this->data[$this->alias]['first_name'] = ucfirst($this->data[$this->alias]['first_name']);
            $this->data[$this->alias]['last_name'] = ucfirst($this->data[$this->alias]['last_name']);
        }

        // If default_team_id is null, not working fine at all.
        if (isset($this->data[$this->alias]['default_team_id']) && !$this->data[$this->alias]['default_team_id']) {
            CakeLog::emergency(sprintf("Attempting to save null to default_team_id! saveData: %s, trace: %s",
                AppUtil::jsonOneLine($this->data[$this->alias]),
                Debugger::trace()
            ));
        }
        return true;
    }

    /**
     * afterFind callback
     *
     * @param array $results Result data
     * @param mixed $primary Primary query
     *
     * @return mixed
     */
    public function afterFind($results, $primary = false)
    {
        if (empty($results)) {
            return $results;
        }
        $this->uids = [];
        /** @noinspection PhpUnusedParameterInspection */
        //ユーザIDのみを抽出(データのゆらぎを吸収する)
        $this
            ->dataIter($results,
                function (&$entity, &$model) {
                    if (isset($entity[$this->alias]['id'])) {
                        array_push($this->uids, $entity[$this->alias]['id']);
                    }
                });

        //LocalName取得(まだ取得していないIDのみ)
        foreach ($this->uids as $k => $v) {
            if (array_key_exists($v, $this->local_names)) {
                unset($this->uids[$k]);
            }
        }
        // shellによる通知orメール送信時は言語設定がセットされていない時があるが、その場合はローカル名の取得をしない
        if ($this->me['language']) {
            $localNames = $this->LocalName->getNames($this->uids, $this->me['language']);
            if (!empty($localNames)) {
                $this->local_names += $localNames;
            }
        }
        //データにLocalName付与する
        /** @noinspection PhpUnusedParameterInspection */
        $this
            ->dataIter($results,
                function (&$entity, &$model) {
                    $entity = $this->setUsername($entity);
                });

        $this->dataIter($results,
            function (&$data, &$model) {
                $data = $this->attachImageUrl($data);
            });


        $results = parent::afterFind($results, $primary);

        return $results;
    }

    /**
     * @param $email
     *
     * @return array
     */
    public function getUserByEmail($email)
    {
        $options = [
            'conditions' => [
                'Email.email' => $email,
            ],
            'contain'    => ['User']
        ];
        return $this->Email->find('first', $options);
    }

    public function getUsersSetupNotCompleted($team_id = false)
    {
        $options = [
            'conditions' => [
                'User.setup_complete_flg' => false,
                'User.active_flg'         => true,
            ],
            'contain'    => [
                'TeamMember' => [
                    'conditions' => [
                        'TeamMember.status' => TeamMember::USER_STATUS_ACTIVE
                    ],
                    'fields'     => [
                        'TeamMember.id',
                        'TeamMember.team_id'
                    ],
                    'Team'       => [
                        'conditions' => [
                            'Team.del_flg' => false
                        ],
                        'fields'     => [
                            'Team.id'
                        ]
                    ]
                ]
            ]
        ];

        if ($team_id) {
            $options['joins'][] = [
                'type'       => 'INNER',
                'table'      => 'team_members',
                'alias'      => 'TeamMember',
                'conditions' => [
                    'TeamMember.user_id = User.id',
                    'TeamMember.team_id' => $team_id,
                ]
            ];
        }

        $all_users_contain_team_is_inactive = $this->find('all', $options);
        $active_users_only = [];

        foreach ($all_users_contain_team_is_inactive as $user) {
            // Checking belongs to any teams.
            if (count($team_member_list = $user['TeamMember']) === 0) {
                continue;
            }

            // Checking teams that belongs to is active
            foreach ($team_member_list as $team_member) {
                if (Hash::get($team_member, 'Team.id')) {
                    $active_users_only[] = $user;
                    break;
                }
            }
        }

        return $active_users_only;
    }

    public function getDetail($id)
    {
        $options = [
            'conditions' => [
                'User.id' => $id,
            ],
            'contain'    => [
                'TeamMember' => [
                    'conditions' => [
                        'TeamMember.team_id' => $this->current_team_id,
                    ]
                ],
                'PrimaryEmail',
                'NotifySetting',
                'DefaultTeam',
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * 表示用ユーザ名と、ローカルユーザ名をセット
     *
     * @param array $row
     *
     * @return string
     */
    public function setUsername($row)
    {
        if (!isset($row[$this->alias]['first_name']) || !isset($row[$this->alias]['last_name'])) {
            return $row;
        }
        $display_username = null;
        $local_name = null;
        //姓名別の表示名のセット
        $row[$this->alias]['display_first_name'] = $row[$this->alias]['first_name'];
        $row[$this->alias]['display_last_name'] = $row[$this->alias]['last_name'];
        //ローカルユーザ名の設定
        $local_names = $this->_getLocalUsername($row);
        if (!$local_names) {
            //ローカル名が存在しない場合はローマ字で
            $display_username = $row[$this->alias]['first_name'] . " " . $row[$this->alias]['last_name'];
        } else {
            //それ以外は
            $local_name = $local_names['local_username'];
            $row[$this->alias]['display_first_name'] = $local_names['first_name'];
            $row[$this->alias]['display_last_name'] = $local_names['last_name'];
            $display_username = $local_name;
        }

        $row[$this->alias]['display_username'] = $display_username;
        $row[$this->alias]['local_username'] = $local_name;
        $row[$this->alias]['roman_username'] = $row[$this->alias]['first_name'] . " " . $row[$this->alias]['last_name'];

        //姓名の並び順の場合フラグをセット
        if (isset($row[$this->alias]['language'])) {
            $last_first = in_array($row[$this->alias]['language'], $this->langCodeOfLastFirst);
            $row[$this->alias]['last_first'] = $last_first;
        }
        return $row;
    }

    /**
     * @param $row
     *
     * @return null|array
     */
    private function _getLocalUsername($row)
    {
        $local_username = null;
        if (!isset($this->me['language']) || empty($this->me['language'])) {
            return null;
        }

        if (array_key_exists($row[$this->alias]['id'], $this->local_names)
            && !empty($this->local_names[$row[$this->alias]['id']])
        ) {
            $res = $this->local_names[$row[$this->alias]['id']];
            if (empty($res['first_name']) || empty($res['last_name'])) {
                return null;
            }
        } else {
            return null;
        }
        //ローカルユーザ名が存在し、言語設定がある場合は国毎の表示を設定する
        $local_names = [
            'first_name'     => $res['first_name'],
            'last_name'      => $res['last_name'],
            'local_username' => $this->buildLocalUserName($this->me['language'], $res['first_name'], $res['last_name']),
        ];
        return $local_names;
    }

    /**
     * 言語ごとの姓名並び順を考慮したローカル名を作成して返す
     *
     * @param $language
     * @param $first_name
     * @param $last_name
     *
     * @return string
     */
    public function buildLocalUserName($language, $first_name, $last_name)
    {
        return in_array($language, $this->langCodeOfLastFirst) ?
            $last_name . " " . $first_name :
            $first_name . " " . $last_name;
    }

    /**
     * ローカル名を利用しないか判定
     *
     * @param string $lang
     *
     * @return bool
     */
    public function isNotUseLocalName(string $lang)
    {
        return in_array($lang, $this->langCodeOfNotLocalName);
    }

    /**
     * ユーザ登録
     * 仮登録 or 本登録
     *
     * @param array $data
     * @param bool  $provisional
     *
     * @return bool
     */
    public function userRegistration($data)
    {
        // password validation
        if (!$this->validatePassword($data)) {
            return false;
        }

        //バリデーションでエラーが発生したらreturn
        if (!$this->validateAssociated($data)) {
            return false;
        }
        //パスワードをハッシュ化
        if (isset($data['User']['password']) && !empty($data['User']['password'])) {
            $data['User']['password'] = $this->generateHash($data['User']['password']);
        }
        $email_token = null;
        $data['Email'][0]['Email']['email_verified'] = true;
        $data['User']['active_flg'] = true;

        $termsOfService = $this->TermsOfService->getCurrent();
        $data['User']['agreed_terms_of_service_id'] = $termsOfService['id'];

        //データを保存
        if (!Hash::get($data, 'Email.0.Email.email_verified') && !Hash::get($data, 'User.id')) {
            $this->create();
        }
        if ($this->saveAll($data, ['validate' => false])) {
            //プライマリメールアドレスを登録
            $this->save(['primary_email_id' => $this->Email->id]);
            $this->Email->set('email', $data['Email'][0]['Email']['email']);
        }
        return true;
    }

    public function userRegistrationNewForm($data)
    {
        $data['User']['password'] = $this->generateHash($data['User']['password']);
        $data['User']['password_token'] = null;
        $data['User']['active_flg'] = true;
        $data['Email']['email_verified'] = true;
        $data['Email']['email_token'] = null;
        $data['Email']['email_token_expires'] = null;

        $termsOfService = $this->TermsOfService->getCurrent();
        $data['User']['agreed_terms_of_service_id'] = $termsOfService['id'];

        ///user with email and local_name
        ////if data exists, update them
        if ($email = $this->Email->findByEmail($data['Email']['email'])) {
            //updating Email
            $data['Email']['id'] = $email['Email']['id'];
            $this->Email->create();
            if (!$this->Email->save($data['Email'])) {
                throw New RuntimeException(__('Saving Email failed'));
            }

            $user_id = $email['Email']['user_id'];
            //Updating User
            $data['User']['id'] = $user_id;
            $data['User']['primary_email_id'] = $email['Email']['id'];
            $this->create();
            unset($this->validate['password']);
            if (!$this->save($data['User'])) {
                throw New RuntimeException(__('Saving User failed'));
            }

            //Saving LocalName
            if (isset($data['LocalName'])) {
                if ($local_name = $this->LocalName->findByUserId($user_id)) {
                    //Updating Local Name
                    $data['LocalName']['id'] = $local_name['LocalName']['id'];
                    $this->LocalName->create();
                    if (!$this->save($this->LocalName->save($data['LocalName']))) {
                        throw New RuntimeException(__('Saving LocalName failed'));
                    }
                } else {
                    //Saving new local name
                    $data['LocalName']['user_id'] = $user_id;
                    $this->LocalName->create();
                    if (!$this->save($this->LocalName->save($data['LocalName']))) {
                        throw New RuntimeException(__('Saving LocalName failed'));
                    }
                }
            }
        } else {
            //Saving User
            $this->create();
            unset($this->validate['password']);
            if (!$this->save($data['User'])) {
                throw New RuntimeException(__('Saving User failed'));
            }
            $user_id = $this->getLastInsertID();
            //Saving Email
            $data['Email']['user_id'] = $user_id;
            $this->Email->create();
            if (!$this->Email->save($data['Email'])) {
                throw New RuntimeException(__('Saving Email failed'));
            }

            //updating primary email
            $this->id = $user_id;
            $this->saveField('primary_email_id', $this->Email->getLastInsertID());

            //Saving LocalName
            if (isset($data['LocalName'])) {
                $data['LocalName']['user_id'] = $user_id;
                $this->LocalName->create();
                if (!$this->save($this->LocalName->save($data['LocalName']))) {
                    throw New RuntimeException(__('Saving LocalName failed'));
                }
            }
        }
        return true;
    }

    /**
     * Password validation for user input datas
     *
     * @param array $data
     *
     * @return bool
     */
    public function validatePassword(array $data): bool
    {
        $validateBackup = $this->validate;
        $this->validate = $this->validatePassword;
        $this->set($data);
        $res = $this->validates();
        $this->validate = $validateBackup;
        return $res;
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws RuntimeException
     */
    public function changePassword($data)
    {
        if (!$this->validatePassword($data)) {
            $msg = null;
            foreach ($this->validationErrors as $val) {
                $msg = $val[0];
                break;
            }
            throw new RuntimeException($msg);
        }
        $currentPassword = $this->field('password', ['User.id' => $data['User']['id']]);
        $hashed_old_password = $this->generateHash($data['User']['old_password']);
        if ($currentPassword !== $hashed_old_password) {
            throw new RuntimeException(__("Current Password is incorrect."));
        }

        $this->id = $data['User']['id'];
        $this->saveField('password', $this->generateHash($data['User']['password']));
        $this->saveField('password_modified', REQUEST_TIMESTAMP);
        return true;
    }

    /**
     * トークンの発行
     *
     * @param string $email
     *
     * @return mixed
     */
    function saveEmailToken($email)
    {
        $default = ['Email' => ['user_id' => null]];
        $email_user = $this->getUserByEmail($email);
        $email_user = array_merge($default, $email_user);
        $email_user['Email']['email_token'] = $this->generateToken();
        $email_user['Email']['email_token_expires'] = $this->getTokenExpire();
        if ($this->Email->saveAll($email_user)) {
            return $email_user;
        } else {
            return false;
        }
    }

    /**
     * Verifies a users email by a token that was sent to him via email and flags the user record as active
     *
     * @param string $token The token that wa sent to the user
     * @param null   $uid
     *
     * @throws RuntimeException
     * @return array On success it returns the user data record
     */
    public function verifyEmail($token, $uid = null)
    {
        $options = [
            'conditions' => [
                'Email.email_verified' => false,
                'Email.email_token'    => $token
            ],
        ];
        if ($uid) {
            $options['conditions']['Email.user_id'] = $uid;
        }
        $user = $this->Email->find('first', $options);

        if (empty($user)) {
            throw new RuntimeException(
                __("Invitation token is incorrect. Check your email again."));
        }
        if ($user['Email']['email_token_expires'] < REQUEST_TIMESTAMP) {
            throw new RuntimeException(__('Invitation token is expired.'));
        }

        $user['User']['id'] = $user['Email']['user_id'];
        $user['User']['active_flg'] = true;
        $user['Email']['email_verified'] = true;
        $user['Email']['email_token'] = null;
        $user['Email']['email_token_expires'] = null;

        $this->Email->saveAll($user, ['validate' => false, 'callbacks' => false]);

        $res = $this->findById($user['User']['id']);
        return array_merge($user, $res);
    }

    /**
     * Checks if an email is in the system, validated and if the user is active so that the user is allowed to reste his password
     *
     * @param array $postData post data from controller
     *
     * @return mixed False or user data as array on success
     */
    public function passwordResetPre($postData)
    {
        //メールアドレスが空で送信されてきた場合はエラーで返却
        if (!isset($postData['User']['email']) || empty($postData['User']['email'])) {
            $this->invalidate('email', __("Enter your email address."));
            return false;
        }
        $options = [
            'conditions' => [
                'Email.email' => $postData['User']['email'],
            ],
            'contain'    => ['User']
        ];
        $user = $this->Email->find('first', $options);
        //メールアドレスが存在しない場合もしくはユーザが存在しない場合はエラーで返却
        if (empty($user) || !$user['User']['id']) {
            $this->invalidate('email', __("This email address isn't registered at Goalous."));
            return false;
        }
        //メールアドレスの認証が終わっていない場合
        if (!$user['Email']['email_verified']) {
            $this->invalidate('email', __("This email should be authenticated. Check your email box."));
            return false;
        }
        //ユーザがアクティブではない場合
        if (!$user['User']['active_flg']) {
            $this->invalidate('email', __("The user account is invalid."));
            return false;
        }
        //ここから認証データ登録
        $user['User']['password_token'] = $this->generateToken();
        $user['Email']['email_token_expires'] = $this->getTokenExpire(60 * 60); //一時間
        $this->Email->saveAssociated($user, ['validate' => false]);
        $this->data = $user;
        return $user;
    }

    /**
     * Checks the token for a password change
     *
     * @param string $token Token
     *
     * @return mixed False or user data as array
     */
    public function checkPasswordToken($token)
    {
        $options = [
            'conditions' => [
                'User.password_token'          => $token,
                'Email.email_token_expires >=' => REQUEST_TIMESTAMP,
                'User.active_flg'              => true,
            ],
            'contain'    => ['User']
        ];
        $user = $this->Email->find('first', $options);
        if (empty($user)) {
            return false;
        }
        return $user;
    }

    /**
     * パスワードリセット
     *
     * @param       $user_email
     * @param array $postData
     *
     * @return bool
     */
    public function passwordReset($user_email, $postData)
    {
        if (!isset($user_email['User']) || !isset($user_email['Email']) || !isset($postData['User'])) {
            return false;
        }
        $this->id = $user_email['User']['id'];
        if (!$this->validatePassword($postData)) {
            return false;
        }

        $user_email['User']['password'] = $this->generateHash($postData['User']['password']);
        $user_email['User']['password_token'] = null;
        $user_email['User']['no_pass_flg'] = false;
        $user_email['User']['active_flg'] = true;
        $user_email['User']['password_modified'] = REQUEST_TIMESTAMP;
        $user_email['Email']['email_token_expires'] = null;
        $user_email['Email']['email_verified'] = true;
        $res = $this->Email->saveAll($user_email);
        return $res;
    }

    public function generateHash($str)
    {
        $passwordHasher = new SimplePasswordHasher(['hashType' => 'sha256']);
        return $passwordHasher->hash($str);
    }

    public function addEmail($postData, $uid)
    {
        if (!isset($postData['User']['email'])) {
            throw new RuntimeException(__("Email address is empty."));
        }

        if (!$this->validatePassword($postData)) {
            $msg = null;
            foreach ($this->validationErrors as $val) {
                $msg = $val[0];
                break;
            }
            throw new RuntimeException($msg);
        }

        $this->set($postData);
        if (!$this->validates()) {
            $msg = null;
            foreach ($this->validationErrors as $val) {
                $msg = $val[0];
                break;
            }
            throw new RuntimeException($msg);
        }

        $email = $postData['User']['email'];

        //現在メール認証中の場合は拒否
        if (!$this->Email->isAllVerified($uid)) {
            throw new RuntimeException(__("Current email address is not authenticated. So, you can't change email address."));
        }
        //メールアドレスの認証トークンを発行
        $data = [];
        $data['Email']['user_id'] = $uid;
        $data['Email']['email'] = $email;
        $data['Email']['email_token'] = $this->generateToken();
        $data['Email']['email_token_expires'] = $this->getTokenExpire();
        //データを保存
        $res = $this->Email->save($data);
        if ($this->Email->validationErrors) {
            $msg = null;
            foreach ($this->Email->validationErrors as $val) {
                $msg = $val[0];
                break;
            }
            throw new RuntimeException($msg);
        }

        return $res;
    }

    /**
     * 通常使うメールアドレスの変更（今まで使っていたメールアドレスを削除）
     *
     * @param      $uid
     * @param      $email_id
     * @param bool $old_delete
     *
     * @return bool
     */
    public function changePrimaryEmail($uid, $email_id, $old_delete = true)
    {
        if ($old_delete) {
            $user = $this->find('first', ['conditions' => ['id' => $uid]]);
            $this->Email->delete($user['User']['primary_email_id']);
        }
        $this->id = $uid;
        return $this->saveField('primary_email_id', $email_id);
    }

    /**
     * パスワードチェックをするバリデーションルール
     *
     * @param $value
     * @param $field_name
     *
     * @return bool
     */
    public function passwordCheck($value, $field_name)
    {
        if (empty($value) || !isset($value[$field_name])) {
            return false;
        }
        $currentPassword = $this->field('password', ['User.id' => $this->my_uid]);
        $inputHashedPassword = $this->generateHash($value[$field_name]);
        if ($currentPassword !== $inputHashedPassword) {
            return false;
        }
        return true;
    }

    public function updateAgreedTermsOfServiceId(int $userId, int $agreedTermsOfServiceId)
    {
        /** @var User $User */
        $User = ClassRegistry::init("User");
        $user = $User->find('first', ['conditions' => ['id' => $userId]]);
        $user['User']['agreed_terms_of_service_id'] = $agreedTermsOfServiceId;
        $User->save($user);
    }

    /**
     * デフォルトチームを更新
     * 未設定の場合のみ(強制的に変更可)
     *
     * @param      $team_id
     * @param bool $force
     * @param null $uid
     *
     * @return bool
     */
    public function updateDefaultTeam($team_id, $force = false, $uid = null)
    {
        if (!$this->me['default_team_id'] || $force) {
            $this->clear();
            $this->id = $uid ? $uid : $this->my_uid;
            $this->saveField('default_team_id', $team_id);
            return true;
        }
        return false;
    }

    /**
     * Use findByKeywordRangeCircle method since API v2
     * @deprecated
     * @param       $keyword
     * @param int   $limit
     * @param bool  $excludeAuthUser If set to true, auth user in php session will be excluded from result.
     * @param array $excludeUserIds
     *
     * @return array|null
     */
    public function getUsersByKeyword($keyword, $limit = 10, $excludeAuthUser = true, array $excludeUserIds = [])
    {
        $user_list = $this->TeamMember->getAllMemberUserIdList();

        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $keyword_conditions = $this->makeUserNameConditions($keyword);
        $options = [
            'conditions' => [
                'User.id'         => $user_list,
                'User.active_flg' => true,
                'OR'              => $keyword_conditions,
            ],
            'limit'      => $limit,
            'fields'     => $this->profileFields,
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'local_names',
                    'alias'      => 'SearchLocalName',
                    'conditions' => [
                        'SearchLocalName.user_id=User.id',
                        'SearchLocalName.language' => $this->me['language'],
                    ],
                ]
            ]
        ];
        if ($excludeAuthUser && !in_array($this->my_uid, $excludeUserIds)) {
            $excludeUserIds[] = $this->my_uid;
        }
        if (count($excludeUserIds) > 0) {
            $options['conditions']['NOT']['User.id'] = $excludeUserIds;
        }

        $res = $this->find('all', $options);

        return $res;
    }

    /**
     * Follow latest spec
     * @param string $keyword
     * @param int $teamId
     * @param int $userId
     * @param int $limit
     * @param bool $excludeAuthUser If set to true, auth user in php session will be excluded from result.
     *
     * @param null $circleId
     * @return array|null
     */
    public function findByKeywordRangeCircle(
        string $keyword,
        int $teamId,
        int $userId,
        $limit = 10,
        $excludeAuthUser = true,
        $circleId = null
    ): array
    {
        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $keywordConditions = $this->makeUserNameConditions($keyword);
        $options = [
            'conditions' => [
                'User.active_flg' => true,
                'OR'              => $keywordConditions,
            ],
            'limit'      => $limit,
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'local_names',
                    'alias'      => 'SearchLocalName',
                    'conditions' => [
                        'SearchLocalName.user_id = User.id',
                    ],
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = User.id',
                        'TeamMember.team_id' => $teamId,
                        'TeamMember.status' => Enum\Model\TeamMember\Status::ACTIVE,
                        'TeamMember.del_flg' => false,
                    ],
                ]
            ]
        ];
        if ($excludeAuthUser) {
            $options['conditions']['User.id !='] = $userId;
        }
        if (!empty($circleId)) {
            $options['joins'][] = [
                'type'       => 'INNER',
                'table'      => 'circle_members',
                'alias'      => 'CircleMember',
                'conditions' => [
                    'CircleMember.user_id = User.id',
                    'CircleMember.circle_id' => $circleId,
                    'CircleMember.del_flg' => false,
                ],

            ];
        }
        $res = $this->useType()->find('all', $options);
        return Hash::extract($res, '{n}.User') ?? [];
    }

    public function getNewUsersByKeywordNotSharedOnPost($keyword, $limit = 10, $not_me = true, $post_id)
    {
        $NoneUser_list = $this->Post->PostShareUser->getShareUserListByPost($post_id);

        $post = $this->Post->findById($post_id);
        if ($post) {
            $NoneUser_list[] = $post['Post']['user_id'];
        }

        $user_list = $this->TeamMember->getAllMemberUserIdList(true, true, false);

        $new_user_list = array_diff($user_list, $NoneUser_list);

        $keyword = trim($keyword);
        if (strlen($keyword) == 0) {
            return [];
        }
        $keyword_conditions = $this->makeUserNameConditions($keyword);
        $options = [
            'conditions' => [
                'User.id'         => $new_user_list,
                'User.active_flg' => true,
                'OR'              => $keyword_conditions,
            ],
            'limit'      => $limit,
            'fields'     => $this->profileFields,
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'local_names',
                    'alias'      => 'SearchLocalName',
                    'conditions' => [
                        'SearchLocalName.user_id=User.id',
                        'SearchLocalName.language' => $this->me['language'],
                    ],
                ]
            ]
        ];
        if ($not_me) {
            $options['conditions']['NOT']['User.id'] = $this->my_uid;
        }
        $res = $this->find('all', $options);

        return $res;
    }

    /**
     * Return the array for called from ajax via Select2 (jQuery based plugin)
     *
     * @see https://select2.github.io/select2/ (v 3.5.x)
     *
     * @param       $keyword       User typed string in input type=text
     * @param int   $limit
     * @param bool  $with_group
     * @param bool  $with_self     Include authorized user in the result.
     * @param array $excludedUsers User IDs to be removed from the query
     *
     * @return array
     */
    public function getUsersSelect2($keyword, $limit = 10, $with_group = false, $with_self = false, $excludedUsers = [])
    {
        $exclude_auth_user = !$with_self;
        $users = $this->getUsersByKeyword($keyword, $limit, $exclude_auth_user, $excludedUsers);
        $user_res = $this->makeSelect2UserList($users);

        // グループを結果に含める場合
        if ($with_group) {
            $group_res = $this->getGroupsSelect2($keyword, $limit);
            $user_res = array_merge($user_res, $group_res['results']);
        }
        return ['results' => $user_res];
    }

    public function getUsersSelectOnly($keyword, $limit = 10, $post_id, $with_group = false)
    {
        $users = $this->getNewUsersByKeywordNotSharedOnPost($keyword, $limit, true, $post_id);
        $user_res = $this->makeSelect2UserList($users);

        // グループを結果に含める場合
        // 既にメッセージメンバーになっているユーザーを除外してから返却データに追加
        if ($with_group) {
            $shared_user_list = $this->Post->PostShareUser->getShareUserListByPost($post_id);
            $post = $this->Post->findById($post_id);
            if ($post) {
                $shared_user_list[$post['Post']['user_id']] = $post['Post']['user_id'];
            }
            $group_res = $this->getGroupsSelect2($keyword, $limit);
            $user_res = array_merge($user_res,
                $this->excludeGroupMemberSelect2($group_res['results'], $shared_user_list));
        }
        return ['results' => $user_res];
    }

    public function getUsersCirclesSelect2($keyword, $limit = 10, $circle_type = "all", $with_group = false)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $circles = [];
        switch ($circle_type) {
            case "all":
                $circles = $this->CircleMember->Circle->getCirclesByKeyword($keyword, $limit);
                break;

            case "public":
                $circles = $this->CircleMember->Circle->getPublicCirclesByKeyword($keyword, $limit);
                break;
//            case "private":
//                break;
        }
        $res = [];
        foreach ($circles as $val) {
            $data = [];
            $data['id'] = $val['Circle']['team_all_flg'] ? 'public' : 'circle_' . $val['Circle']['id'];
            $data['text'] = $val['Circle']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Circle.photo', ['style' => 'small']);
            $res[] = $data;
        }

        $users = $this->getUsersByKeyword($keyword, $limit);
        $user_res = $this->makeSelect2UserList($users);
        $res = array_merge($res, $user_res);

        // グループを結果に含める場合
        if ($with_group) {
            $group_res = $this->getGroupsSelect2($keyword, $limit);
            $res = array_merge($res, $group_res['results']);
        }

        return ['results' => $res];
    }

    /**
     * 非公開のサークル一覧を select2 用のデータ形式で返す
     *
     * @param     $keyword
     * @param int $limit
     *
     * @return array
     */
    public function getSecretCirclesSelect2($keyword, $limit = 10)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $circles = $this->CircleMember->Circle->getSecretCirclesByKeyword($keyword, $limit);
        $circle_res = [];
        foreach ($circles as $val) {
            $data = [];
            $data['id'] = $val['Circle']['team_all_flg'] ? 'public' : 'circle_' . $val['Circle']['id'];
            $data['text'] = $val['Circle']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Circle.photo', ['style' => 'small']);
            $circle_res[] = $data;
        }

        return ['results' => $circle_res];
    }

    /**
     * feedのselect2で使うデフォルトデータリスト
     *
     * @return array
     */
    function getAllUsersCirclesSelect2()
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $circles = $this->CircleMember->getMyCircle();
        $circle_res = [];
        foreach ($circles as $val) {
            $data['id'] = 'circle_' . $val['Circle']['id'];
            $data['text'] = mb_strimwidth(trim($val['Circle']['name']), 0, 35, "...");
            $data['image'] = $Upload->uploadUrl($val, 'Circle.photo', ['style' => 'small']);
            $circle_res[] = $data;
        }

        $users = $this->getAllMember(false);
        $user_res = [];
        foreach ($users as $val) {
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['roman_username'] . " ( " . $val['User']['display_username'] . " )";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $user_res[] = $data;
        }
        $team_res = [];
        $team = $this->TeamMember->Team->findById($this->current_team_id);
        if (!empty($team)) {
            $team_res = [
                [
                    'id'    => "public",
                    'text'  => __("All Team"),
                    'image' => $Upload->uploadUrl($team, 'Team.photo', ['style' => 'small']),
                ]
            ];
        }

        $res = array_merge($team_res, $circle_res, $user_res);

        return $res;
    }

    /**
     * キーワードにマッチするグループの select2 用データを返す
     *
     * @param     $keyword
     * @param int $limit
     *
     * @return array
     */
    public function getGroupsSelect2($keyword, $limit = 10)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $res = [];

        // キーワードにマッチするグループ
        $groups = $this->MemberGroup->Group->getGroupsByKeyword($keyword, $limit);
        foreach ($groups as $group) {
            // グループに属するメンバーの情報を追加
            $members = $this->MemberGroup->getGroupMember($group['Group']['id']);
            $users = [];
            foreach ($members as $member) {
                if ($this->my_uid != $member['User']['id']) {
                    $users[] = [
                        'id'    => 'user_' . $member['User']['id'],
                        'text'  => $member['User']['display_username'] . " (" . $member['User']['roman_username'] . ")",
                        'image' => $Upload->uploadUrl($member, 'User.photo', ['style' => 'small']),
                    ];
                }
            }
            // 所属するメンバーが一人もいない場合は結果に含めない
            if (!$users) {
                continue;
            }
            $res[] = [
                'id'    => 'group_' . $group['Group']['id'],
                'text'  => $group['Group']['name'] . ' (' . strval(__('%1$s member', count($users))) . ')',
                'users' => $users,
            ];
        }
        return ['results' => $res];
    }

    public function getAllMember($with_me = true)
    {
        $uid_list = $this->TeamMember->getAllMemberUserIdList($with_me);

        $options = [
            'conditions' => [
                'id' => $uid_list,
            ],
            'order'      => ['first_name'],
            'fields'     => $this->profileFields,
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function getProfileAndEmail($uid, $lang = null)
    {
        $backup_lang = null;
        if ($lang) {
            $backup_lang = $this->me['language'];
            $this->me['language'] = $lang;
        }

        $options = [
            'conditions' => [
                'User.id' => $uid,
            ],
            'fields'     => $this->profileFields,
            'contain'    => [
                'PrimaryEmail' => [
                    'fields' => [
                        'PrimaryEmail.email'
                    ]
                ]
            ]
        ];
        $res = $this->find('first', $options);
        if (isset($res['PrimaryEmail'])) {
            $res['User']['PrimaryEmail'] = $res['PrimaryEmail'];
            unset($res['PrimaryEmail']);
        }

        if ($backup_lang) {
            $this->me['language'] = $backup_lang;
        }
        return $res;
    }

    function getNameRandom($uid)
    {
        $options = [
            'conditions' => [
                'User.id' => $uid,
            ],
            'fields'     => $this->profileFields,
            'order'      => 'rand()',
        ];
        if ($this->getDataSource()->config['datasource'] == 'Database/Sqlite') {
            $options['order'] = 'random()';
        }

        $res = $this->find('first', $options);
        if (isset($res['User']['display_username'])) {
            return $res['User']['display_username'];
        }
        return null;
    }

    function getMyChannelsJson($check_hide_status = null)
    {
        $my_channels = [];
        $my_channels[] = 'team_all_' . $this->current_team_id;
        $my_channels[] = 'user_' . $this->my_uid . '_team_' . $this->current_team_id;
        // サークル
        $my_circles = $this->CircleMember->getMyCircleList($check_hide_status);
        foreach ($my_circles as $val) {
            $my_channels[] = 'circle_' . $val . '_team_' . $this->current_team_id;
        }
        // ゴール
        $followList = $this->Goal->Follower->getFollowList($this->my_uid);
        $collaboList = $this->Goal->GoalMember->getCollaboGoalList($this->my_uid, true);
        $goals = array_unique(array_merge($followList, $collaboList));
        foreach ($goals as $val) {
            $my_channels[] = 'goal_' . $val . '_team_' . $this->current_team_id;
        }

        return json_encode($my_channels);
    }

    function getUsersProf($uids)
    {
        $options = [
            'conditions' => [
                'id' => $uids
            ],
            'fields'     => $this->profileFields
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    function getMyProf()
    {
        $model = $this;
        $res = Cache::remember($this->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false),
            function () use ($model) {
                $options = [
                    'conditions' => [
                        'id' => $model->my_uid
                    ],
                    'fields'     => $model->profileFields
                ];
                $res = $model->find('first', $options);
                return $res;
            }, 'user_data');

        return $res;
    }

    /**
     * ユーザー名検索時の条件配列を作成する
     * codeclimate 対策
     * CircleMember でも使ってるので注意
     *
     * @param $keyword
     *
     * @return array
     */
    public function makeUserNameConditions($keyword)
    {
        // $keyword にスペースが入っていればフルネーム検索
        // 入っていない場合は姓名それぞれを検索
        // クオートの中には半角スペースか全角スペースが入っているので注意
        if (strpos($keyword, ' ') !== false || strpos($keyword, '　') !== false) {
            $keyword = str_replace('　', ' ', $keyword);
            $keyword_conditions = [
                'CONCAT(`User.first_name`," ",`User.last_name`) Like'                       => $keyword . '%',
                'CONCAT(`SearchLocalName.first_name`," ",`SearchLocalName.last_name`) Like' => $keyword . '%',
            ];
        } else {
            $keyword_conditions = [
                'User.first_name LIKE'            => $keyword . '%',
                'User.last_name LIKE'             => $keyword . '%',
                'SearchLocalName.first_name LIKE' => $keyword . '%',
                'SearchLocalName.last_name LIKE'  => $keyword . '%',
            ];
        }
        return $keyword_conditions;
    }

    /**
     * default_team_id == $team_id のレコードの default_team_id を null に更新する
     *
     * @param $team_id
     *
     * @return bool
     */
    public function clearDefaultTeamId($team_id)
    {
        return $this->updateAll(['default_team_id' => null], ['default_team_id' => $team_id]);
    }

    /**
     * select2 用のグループ検索結果のユーザーリストから
     * $exclude_member_list のユーザーを除外する
     *
     * @param array $select2_results
     * @param array $exclude_member_list user_id をキーに持つリスト
     *
     * @return array
     */
    public function excludeGroupMemberSelect2($select2_results, $exclude_member_list)
    {
        foreach ($select2_results as $k => $v) {
            $users = [];
            foreach ($v['users'] as $k2 => $v2) {
                if (!isset($exclude_member_list[str_replace('user_', '', $v2['id'])])) {
                    $users[] = $v2;
                }
            }
            $select2_results[$k]['users'] = $users;

            // users リストが空になった場合はグループ自体を除外
            if (!$users) {
                unset($select2_results[$k]);
            }
        }
        // 連想配列にならないようにする
        return array_values($select2_results);
    }

    /**
     * select2 用のユーザーリスト配列を返す
     *
     * @param array $users
     *
     * @return array
     */
    public function makeSelect2UserList(array $users)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $res = [];
        foreach ($users as $val) {
            $data = [];
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['display_username'] . " (" . $val['User']['roman_username'] . ")";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $res[] = $data;
        }
        return $res;
    }

    /**
     * select2 用のユーザー配列を返す
     * TODO:makeSelect2UserList/makeSelect2Userの処理をControllerもしくはComponentに移行
     *
     * @param array $user
     *
     * @return array
     */
    public function makeSelect2User(array $user)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $res = [];
        $res['id'] = 'user_' . $user['User']['id'];
        $res['text'] = $user['User']['display_username'] . " (" . $user['User']['roman_username'] . ")";
        $res['image'] = $Upload->uploadUrl($user, 'User.photo', ['style' => 'small']);
        return $res;
    }

    /**
     * check registered profile for setup-guide
     *
     * @return boolean isCompleted
     */
    function isCompletedProfileForSetup($user_id)
    {
        $options = [
            'conditions' => [
                'User.id' => $user_id,
            ],
            'contain'    => [
                'TeamMember' => [
                    'fields' => [
                        'TeamMember.id',
                        'TeamMember.comment'
                    ]
                ]
            ]
        ];
        $res = $this->findWithoutTeamId('first', $options);

        $profile_photo_is_registered = (bool)Hash::get($res, 'User.photo_file_name');
        $comment_is_registered = false;

        if (!isset($res['TeamMember'])) {
            return false;
        }

        // Because profile comment stride mult team, should check all comment of each teams.
        foreach ($res['TeamMember'] as $team_member) {
            if ($team_member['comment']) {
                $comment_is_registered = true;
                break;
            }
        }

        return $profile_photo_is_registered && $comment_is_registered;
    }

    function generateSetupGuideStatusDict($user_id)
    {
        return [
            self::SETUP_PROFILE                  => $this->isCompletedProfileForSetup($user_id),
            self::SETUP_MOBILE_APP               => $this->Device->isInstalledMobileApp($user_id),
        ];
    }

    function completeSetupGuide($user_id)
    {
        $this->id = $user_id;
        return $this->saveField('setup_complete_flg', self::SETUP_GUIDE_IS_COMPLETED);
    }

    function filterActiveUserList($uids)
    {
        $options = [
            'conditions' => [
                'id'         => $uids,
                'active_flg' => true,
            ],
            'fields'     => [
                'id',
                'id'
            ]
        ];
        $res = $this->find('list', $options);
        return $res;
    }

    /**
     * Check users are active
     *
     * @param  array $userIds
     *
     * @return bool
     */
    function isActiveUsers(array $userIds): bool
    {
        $options = [
            'conditions' => [
                'User.id'            => $userIds,
                'User.active_flg'    => true,
                'TeamMember.team_id' => $this->current_team_id,
                'TeamMember.status'  => TeamMember::USER_STATUS_ACTIVE,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = User.id',
                        'TeamMember.del_flg' => false
                    ]
                ]
            ]
        ];

        $res = $this->find('count', $options);

        return $res === count($userIds);
    }

    /**
     * getting user profiles by user id list
     *
     * @param array $userIds e.g. [1,2,3]
     *
     * @return array
     */
    function findProfilesByIds(array $userIds): array
    {
        $options = [
            'conditions' => ['id' => $userIds],
            'fields'     => $this->profileFields,
        ];
        $users = $this->find('all', $options);
        $ret = Hash::extract($users, '{n}.User');
        return $ret;
    }

    /**
     * Find users not belong to team
     * filter: emails
     *
     * @param int   $teamId
     * @param array $emails
     *
     * @return array
     */
    function findNotBelongToTeamByEmail(int $teamId, array $emails): array
    {
        $options = [
            'fields'     => [
                'User.id',

            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'emails',
                    'alias'      => 'Email',
                    'conditions' => [
                        'Email.user_id = User.id',
                        'Email.email'   => $emails,
                        'Email.del_flg' => false,
                    ]

                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = User.id',
                        'TeamMember.team_id' => $teamId,
                        'TeamMember.del_flg' => false,
                    ]

                ],
            ],
            'conditions' => [
                'TeamMember.id' => null,
                'User.del_flg'  => false,
            ],
        ];
        $res = $this->find('list', $options);
        return array_values($res);
    }

    /**
     * Check preRegistered(invited but not accept any team's invitation)
     *
     * @param string $email
     *
     * @return bool
     */
    function isPreRegisteredByInvitationToken(string $email): bool
    {
        $options = [
            'fields'     => [
                'User.id',
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'emails',
                    'alias'      => 'Email',
                    'conditions' => [
                        'Email.user_id = User.id',
                        'Email.email'   => $email,
                        'Email.del_flg' => false,
                    ]
                ],
            ],
            'conditions' => [
                'User.active_flg' => false,
                'User.del_flg'    => false,
            ],
        ];
        $res = $this->find('count', $options);
        return $res == 1;
    }

    /**
     * Function for filter user ids based on their activity status in a team
     *
     * @param int @teamId
     *          Team ID of the users
     * @param array $userIds
     *          User IDs to be filtered
     * @param bool  $activeFlag
     *          Whether the user is active in the team
     *
     * @return array | null Array of inactive users
     */
    public function filterUsersOnTeamActivity(int $teamId, array $userIds, bool $activeFlag = false): array
    {

        $options = [
            'conditions' => [
                'User.id'            => $userIds,
                'User.active_flg'    => true,
                'TeamMember.team_id' => $teamId,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = User.id'
                    ]
                ]
            ]
        ];

        if ($activeFlag) {
            $options['conditions']['TeamMember.status'] = Enum\Model\TeamMember\Status::ACTIVE;
        } else {
            $options['conditions']['TeamMember.status'] = Enum\Model\TeamMember\Status::INACTIVE;
        }

        $res = $this->find('all', $options);

        return $res;
    }

    /**
     * @param $userId
     *
     * @return bool Validation result; true for successful validation
     */
    public function validateUserId($userId): bool
    {
        return !empty($userId) && AppUtil::isInt($userId) && $userId != 0;
    }

    /**
     * Get user object from user's primary email address
     *
     * @param string $email
     *
     * @return UserEntity
     */
    public function findUserByEmail(string $email)
    {
        $condition = [
            'conditions' => [
                'User.del_flg'    => false,
                'User.active_flg' => true,
            ],
            'fields'     => [
                'User.id',
                'User.password',
                'User.default_team_id'
            ],
            'joins'      => [
                [
                    'table'      => 'emails',
                    'alias'      => 'Email',
                    'type'       => 'INNER',
                    'conditions' => [
                        'User.primary_email_id = Email.id',
                        'Email.del_flg' => false,
                        'Email.email'   => $email,
                    ]
                ]
            ]
        ];

        /** @var UserEntity $user */
        $user = $this->useType()->useEntity()->find('first', $condition);

        return $user;
    }

    /**
     * Get user with fields for login response
     *
     * @param int $userId
     *
     * @return UserEntity
     */
    public function getUserForLoginResponse(int $userId)
    {
        $conditions = [
            'conditions' => [
                'id' => $userId
            ],
            'fields'     => $this->loginUserFields,
        ];

       /** @var UserEntity $res */
        $res =  $this->useType()->useEntity()->find('first', $conditions);

        return $res;
    }

    /**
     * Get photo file names from the user data array, and turn them into URLs
     *
     * @param array $data
     *
     * @return array
     */
    private function attachImageUrl(array $data): array
    {
//TODO GL-7111
        $coverFileName = Hash::get($data, 'cover_photo_file_name');
        $photoFileName = Hash::get($data, 'photo_file_name');

        if (isset($coverFileName)) {
            $data['cover_img_url'] = '';
            unset($data['cover_photo_file_name']);
        }

        if (isset($photoFileName)) {
            $data['photo_img_url'] = '';
            unset($data['photo_file_name']);
        }

        return $data;
    }
}
