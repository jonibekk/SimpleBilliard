<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
App::uses('AppModel', 'Model');
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
 * @property Collaborator   $Collaborator
 * @property MemberGroup    $MemberGroup
 * @property RecoveryCode   $RecoveryCode
 */
class User extends AppModel
{
    /**
     * 性別タイプ
     */
    const TYPE_GENDER_MALE = 1;
    const TYPE_GENDER_FEMALE = 2;
    const TYPE_GENDER_NEITHER = 3;
    static public $TYPE_GENDER = [self::TYPE_GENDER_MALE => "", self::TYPE_GENDER_FEMALE => "", self::TYPE_GENDER_NEITHER => ""];

    /**
     * 性別タイプの名前をセット
     */
    private function _setGenderTypeName()
    {
        self::$TYPE_GENDER[self::TYPE_GENDER_MALE] = __d('gl', "男性");
        self::$TYPE_GENDER[self::TYPE_GENDER_FEMALE] = __d('gl', "女性");
        self::$TYPE_GENDER[self::TYPE_GENDER_NEITHER] = __d('gl', "どちらでもない");
    }

    public $actsAs = [
        'Upload' => [
            'photo' => [
                'styles'      => [
                    'small'        => '32x32',
                    'medium'       => '48x48',
                    'medium_large' => '96x96',
                    'large'        => '128x128',
                    'x_large'      => '256x256',
                ],
                'path'        => ":webroot/upload/:model/:id/:hash_:style.:extension",
                'default_url' => 'no-image-user.jpg',
                'quality'     => 100,
            ]
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
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'first_name'        => [
            'notEmpty'       => ['rule' => 'notEmpty'],
            'isAlphabetOnly' => ['rule' => 'isAlphabetOnly'],
        ],
        'last_name'         => [
            'notEmpty'       => ['rule' => 'notEmpty'],
            'isAlphabetOnly' => ['rule' => 'isAlphabetOnly'],
        ],
        'hide_year_flg'     => [
            'boolean' => [
                'rule'       => ['boolean',],
                'allowEmpty' => true,
            ],
        ],
        'no_pass_flg'       => ['boolean' => ['rule' => ['boolean'],],],
        'active_flg'        => ['boolean' => ['rule' => ['boolean'],],],
        'admin_flg'         => ['boolean' => ['rule' => ['boolean'],],],
        'auto_timezone_flg' => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
        'auto_language_flg' => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
        'romanize_flg'      => ['boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],],
        'update_email_flg'  => [
            'boolean' => [
                'rule'       => ['boolean',],
                'allowEmpty' => true,
            ],
        ],
        'agree_tos'         => [
            'notBlankCheckbox' => [
                'rule' => ['custom', '[1]'],
            ]
        ],
        'del_flg'           => ['boolean' => ['rule' => ['boolean'],],],
        'old_password'      => [
            'notEmpty'  => [
                'rule' => 'notEmpty',
            ],
            'minLength' => [
                'rule' => ['minLength', 8],
            ]
        ],
        'password_request'  => [
            'notEmpty'      => [
                'rule' => 'notEmpty',
            ],
            'minLength'     => [
                'rule' => ['minLength', 8],
            ],
            'passwordCheck' => [
                'rule' => ['passwordCheck', 'password_request'],
            ]
        ],
        'password_request2' => [
            'notEmpty'      => [
                'rule' => 'notEmpty',
            ],
            'minLength'     => [
                'rule' => ['minLength', 8],
            ],
            'passwordCheck' => [
                'rule' => ['passwordCheck', 'password_request2'],
            ]
        ],
        'password'          => [
            'notEmpty'  => [
                'rule' => 'notEmpty',
            ],
            'minLength' => [
                'rule' => ['minLength', 8],
            ]
        ],
        'password_confirm'  => [
            'notEmpty'          => [
                'rule' => 'notEmpty',
            ],
            'passwordSameCheck' => [
                'rule' => ['passwordSameCheck', 'password'],
            ],
        ],
        'photo'             => [
            'image_max_size' => ['rule' => ['attachmentMaxSize', 10485760],], //10mb
            'image_type'     => ['rule' => ['attachmentContentType', ['image/jpeg', 'image/gif', 'image/png']],]
        ],
        'hometown'          => [
            'isString' => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
        ]
    ];

    public $profileFields = [
        'id',
        'first_name',
        'last_name',
        'photo_file_name',
        'language',
        'auto_language_flg',
        'romanize_flg',
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'DefaultTeam'  => ['className' => 'Team', 'foreignKey' => 'default_team_id',],
        'PrimaryEmail' => ['className' => 'Email', 'foreignKey' => 'primary_email_id', 'dependent' => true],
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
        'Purpose',
        'Badge',
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
        'Email',
        'GivenBadge',
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
        'Collaborator',
        'Evaluator',
        'RecoveryCode',
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
            !empty($this->data[$this->alias]['first_name'])
        ) {
            $this->data[$this->alias]['first_name'] = ucfirst($this->data[$this->alias]['first_name']);
            $this->data[$this->alias]['last_name'] = ucfirst($this->data[$this->alias]['last_name']);
        }
        return true;
    }

    /**
     * afterFind callback
     *
     * @param array $results Result data
     * @param mixed $primary Primary query
     *
     * @return array
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
        $this->local_names = $this->local_names +
            $this->LocalName->getNames($this->uids, $this->me['language']);
        //データにLocalName付与する
        /** @noinspection PhpUnusedParameterInspection */
        $this
            ->dataIter($results,
                function (&$entity, &$model) {
                    $entity = $this->setUsername($entity);
                });
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
        }
        else {
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
        }
        else {
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
     * @param $lung
     *
     * @return bool
     */
    public function isNotUseLocalName($lung)
    {
        return in_array($lung, $this->langCodeOfNotLocalName);
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
    public function userRegistration($data, $provisional = true)
    {
        //バリデーションでエラーが発生したらreturn
        if (!$this->validateAssociated($data)) {
            return false;
        }
        //パスワードをハッシュ化
        if (isset($data['User']['password']) && !empty($data['User']['password'])) {
            $data['User']['password'] = $this->generateHash($data['User']['password']);
        }
        $email_token = null;
        //仮登録なら
        if ($provisional) {
            //メールアドレスの認証トークンを発行
            $email_token = $this->generateToken();
            $data['Email'][0]['Email']['email_token'] = $email_token;
            //メールアドレスの認証トークンの期限をセット
            $data['Email'][0]['Email']['email_token_expires'] = $this->getTokenExpire(3600);
        }
        //本登録なら
        else {
            $data['Email'][0]['Email']['email_verified'] = true;
            $data['User']['active_flg'] = true;
        }
        //データを保存
        $this->create();
        if ($this->saveAll($data, ['validate' => false])) {
            //プライマリメールアドレスを登録
            $this->save(['primary_email_id' => $this->Email->id]);
            //コントローラ側で必要になるデータをセット
            if ($provisional) {
                $this->Email->set('email_token', $email_token);
            }
            $this->Email->set('email', $data['Email'][0]['Email']['email']);
        }
        return true;
    }

    /**
     * @param $data
     *
     * @return bool
     * @throws RuntimeException
     */
    public function changePassword($data)
    {
        if (!$this->validateAssociated($data)) {
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
            throw new RuntimeException(__d('validate', "現在のパスワードが間違っています。"));
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
        }
        else {
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
                __d('exception', "トークンが正しくありません。送信されたメールを再度ご確認下さい。"));
        }
        if ($user['Email']['email_token_expires'] < REQUEST_TIMESTAMP) {
            throw new RuntimeException(__d('exception', 'トークンの期限が切れています。'));
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
            $this->invalidate('email', __d('validate', "メールアドレスを入力してください。"));
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
            $this->invalidate('email', __d('validate', "このメールアドレスはGoalousに登録されていません。"));
            return false;
        }
        //メールアドレスの認証が終わっていない場合
        if (!$user['Email']['email_verified']) {
            $this->invalidate('email', __d('validate', "このメールアドレスは認証が完了しておりません。Goalousから以前に送信されたメールをご確認ください。"));
            return false;
        }
        //ユーザがアクティブではない場合
        if (!$user['User']['active_flg']) {
            $this->invalidate('email', __d('validate', "ユーザアカウントが有効ではありません。"));
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
        $this->set($postData);
        if (!$this->validates()) {
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
        $passwordHasher = new SimplePasswordHasher();
        return $passwordHasher->hash($str);
    }

    public function addEmail($postData, $uid)
    {
        if (!isset($postData['User']['email'])) {
            throw new RuntimeException(__d('validate', "メールアドレスが入力されていません"));
        }

        $this->id = $uid;
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
            throw new RuntimeException(__d('validate', "現在、他のメールアドレスの認証待ちの為、メールアドレス変更はできません。"));
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
        $currentPassword = $this->field('password', ['User.id' => $this->id]);
        $hashed_old_password = $this->generateHash($value[$field_name]);
        if ($currentPassword !== $hashed_old_password) {
            return false;
        }
        return true;
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
            $this->id = $uid ? $uid : $this->my_uid;
            $this->saveField('default_team_id', $team_id);
            return true;
        }
        return false;
    }

    public function getUsersByKeyword($keyword, $limit = 10, $not_me = true)
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
        if ($not_me) {
            $options['conditions']['NOT']['User.id'] = $this->my_uid;
        }
        $res = $this->find('all', $options);

        return $res;
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

    public function getUsersSelect2($keyword, $limit = 10, $with_group = false)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $users = $this->getUsersByKeyword($keyword, $limit);
        $user_res = [];
        foreach ($users as $val) {
            $data = [];
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['display_username'] . " (" . $val['User']['roman_username'] . ")";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $user_res[] = $data;
        }
        // グループを結果に含める場合
        if ($with_group) {
            $group_res = $this->getGroupsSelect2($keyword, $limit);
            $user_res = array_merge($user_res, $group_res['results']);
        }
        return ['results' => $user_res];
    }

    public function getUsersSelectOnly($keyword, $limit = 10, $post_id, $with_group = false)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $users = $this->getNewUsersByKeywordNotSharedOnPost($keyword, $limit, true, $post_id);
        $user_res = [];
        foreach ($users as $val) {
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['display_username'] . " (" . $val['User']['roman_username'] . ")";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $user_res[] = $data;
        }

        // グループを結果に含める場合
        // 既にメッセージメンバーになっているユーザーを除外してから返却データに追加
        if ($with_group) {
            $shared_user_list = $this->Post->PostShareUser->getShareUserListByPost($post_id);
            $post = $this->Post->findById($post_id);
            if ($post) {
                $shared_user_list[$post['Post']['user_id']] = $post['Post']['user_id'];
            }
            $group_res = $this->getGroupsSelect2($keyword, $limit);
            $user_res = array_merge($user_res, $this->excludeGroupMemberSelect2($group_res['results'], $shared_user_list));
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
        foreach ($users as $val) {
            $data = [];
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['display_username'] . " (" . $val['User']['roman_username'] . ")";
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $res[] = $data;
        }

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
                    'text'  => __d('gl', "チーム全体"),
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
            $members = $this->MemberGroup->getGroupMember($group['Group']['id']);
            $users = [];
            foreach ($members as $member) {
                if ($this->my_uid != $member['User']['id']) {
                    $users[] = [
                        'id'   => 'user_' . $member['User']['id'],
                        'text' => $member['User']['display_username'] . " (" . $member['User']['roman_username'] . ")",
                        'image' => $Upload->uploadUrl($member, 'User.photo', ['style' => 'small']),
                    ];
                }
            }
            if (!$users) {
                continue;
            }
            $res[] = [
                'id'    => 'group_' . $group['Group']['id'],
                'text'  => $group['Group']['name'] . ' (' . strval(__d('gl', '%1$s人のメンバー', count($users))) . ')',
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
        $collaboList = $this->Goal->Collaborator->getCollaboGoalList($this->my_uid, true);
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
        if (strpos($keyword, ' ') !== false || strpos($keyword, __d('gl', '　')) !== false) {
            $keyword = str_replace(__d('gl', '　'), ' ', $keyword);
            $keyword_conditions = [
                'CONCAT(`User.first_name`," ",`User.last_name`) Like'                       => $keyword . "%",
                'CONCAT(`SearchLocalName.first_name`," ",`SearchLocalName.last_name`) Like' => $keyword . "%",
            ];
        }
        else {
            $keyword_conditions = [
                'User.first_name LIKE'            => $keyword . "%",
                'User.last_name LIKE'             => $keyword . "%",
                'SearchLocalName.first_name LIKE' => $keyword . "%",
                'SearchLocalName.last_name LIKE'  => $keyword . "%",
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
    public function excludeGroupMemberSelect2($select2_results, $exclude_member_list) {
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
}
