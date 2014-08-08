<?php
App::uses('AppModel', 'Model');
/** @noinspection PhpUndefinedClassInspection */

/**
 * User Model
 *
 * @property Email            $PrimaryEmail
 * @property Team             $DefaultTeam
 * @property Badge            $Badge
 * @property CommentLike      $CommentLike
 * @property CommentMention   $CommentMention
 * @property CommentRead      $CommentRead
 * @property Comment          $Comment
 * @property Email            $Email
 * @property GivenBadge       $GivenBadge
 * @property Notification     $Notification
 * @property OauthToken       $OauthToken
 * @property PostLike         $PostLike
 * @property PostMention      $PostMention
 * @property PostRead         $PostRead
 * @property Post             $Post
 * @property TeamMember       $TeamMember
 * @property CircleMember     $CircleMember
 * @property LocalName        $LocalName
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
                'default_url' => 'no-image.jpg',
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

    public $displayField = 'username';

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
        'username',
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
    ];

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = [
        'Badge',
        'CommentLike',
        'CommentMention',
        'CommentRead',
        'Comment',
        'Email',
        'GivenBadge',
        'Notification',
        'OauthToken',
        'PostLike',
        'PostMention',
        'PostRead',
        'Post',
        'TeamMember',
        'LocalName',
        'CircleMember',
    ];

    /**
     * ローカル名を使わない言語のリスト
     */
    public $langCodeOfNotLocalName = [
        'eng',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setGenderTypeName();
        $this->_setVirtualFields();
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

    private function _setVirtualFields()
    {
        $first_name = $this->alias . '.first_name';
        $last_name = $this->alias . '.last_name';
        $this->virtualFields = [
            'username' => 'CONCAT(' . $first_name . ', " ", ' . $last_name . ')'
        ];
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
        //TODO php5.3だとクロージャで$thisが使えないため、$thisを変数に格納して、useで渡す。php5.4なら簡潔に書ける
        //TODO これなおす！
        $self = $this;
        /** @noinspection PhpUnusedParameterInspection */
        $this
            ->dataIter($results,
                function (&$entity, &$model) use ($self) {
                    $entity = $self->setUsername($entity);
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
        $recursive = $this->recursive;
        $this->recursive = 0;
        $res = $this->findById($id);
        $this->recursive = $recursive;
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
        $local_username = null;
        //ローカルユーザ名の設定
        $local_username = $this->_getLocalUsername($row);
        if (!$local_username) {
            //ローカル名が存在しない場合はローマ字で
            $display_username = $this->_getRomanUsername($row);
        }
        else {
            //それ以外は
            $display_username = $local_username;
        }

        $row[$this->alias]['display_username'] = $display_username;
        $row[$this->alias]['local_username'] = $local_username;

        //姓名の並び順の場合フラグをセット
        if (isset($row[$this->alias]['language'])) {
            $last_first = in_array($row[$this->alias]['language'], $this->langCodeOfLastFirst);
            $row[$this->alias]['last_first'] = $last_first;
        }
        return $row;
    }

    private function _getRomanUsername($row)
    {
        $display_username = null;
        if (!empty($row[$this->alias]['username'])) {
            $display_username = $row[$this->alias]['username'];
        }
        return $display_username;
    }

    private function _getLocalUsername($row)
    {
        $local_username = null;
        if (!isset($this->me['language']) || empty($this->me['language'])) {
            return null;
        }
        //ローカル名を取得
        $options = [
            'conditions' => [
                'user_id'  => $row[$this->alias]['id'],
                'language' => $this->me['language'],
            ]
        ];
        $res = $this->LocalName->find('first', $options);
        if (empty($res)) {
            return null;
        }
        //ローカルユーザ名が存在し、言語設定がある場合は国毎の表示を設定する
        $last_first = in_array($this->me['language'], $this->langCodeOfLastFirst);
        if ($last_first) {
            $local_username = $res['LocalName']['last_name'] . " "
                . $res['LocalName']['first_name'];
        }
        else {
            $local_username = $res['LocalName']['first_name'] . " "
                . $res['LocalName']['last_name'];
        }
        return $local_username;
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
            $data['Email'][0]['Email']['email_token_expires'] = $this->getTokenExpire();
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
        $this->saveField('password_modified', time());
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
        if ($user['Email']['email_token_expires'] < time()) {
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
                'Email.email_token_expires >=' => time(),
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
        if (!isset($user_email['User']) || !isset($user_email['Email']) || !$postData['User']) {
            return false;
        }
        $this->id = $user_email['User']['id'];
        $this->set($postData);
        if (!$this->validates()) {
            return false;
        }

        $user_email['User']['password'] = $this->generateHash($postData['User']['password']);
        $user_email['User']['password_token'] = null;
        $user_email['User']['password_modified'] = time();
        $user_email['Email']['email_token_expires'] = null;
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
     * 未設定の場合のみ
     *
     * @param      $team_id
     * @param bool $force
     *
     * @return bool
     */
    public function updateDefaultTeam($team_id, $force = false)
    {
        if (!$this->me['default_team_id'] || $force) {
            $this->id = $this->me['id'];
            $this->saveField('default_team_id', $team_id);
            return true;
        }
        return false;
    }

    public function getUsersByKeyword($keyword, $limit = 10, $not_me = true)
    {
        $user_list = $this->TeamMember->getAllMemberUserIdList();
        $options = [
            'conditions' => [
                'User.id'              => $user_list,
                'User.active_flg'      => true,
                'User.username Like ?' => "%" . $keyword . "%",
            ],
            'limit'      => $limit,
            'fields'     => $this->profileFields,
        ];
        if ($not_me) {
            $options['conditions']['NOT']['User.id'] = $this->me['id'];
        }
        $res = $this->find('all', $options);
        return $res;
    }

    public function getUsersSelect2($keyword, $limit = 10)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());
        $users = $this->getUsersByKeyword($keyword, $limit);
        $user_res = [];
        foreach ($users as $val) {
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['username'];
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $user_res[] = $data;
        }
        return ['results' => $user_res];
    }

    public function getUsersCirclesSelect2($keyword, $limit = 10)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $circles = $this->CircleMember->Circle->getCirclesByKeyword($keyword, $limit);
        $circle_res = [];
        foreach ($circles as $val) {
            $data['id'] = 'circle_' . $val['Circle']['id'];
            $data['text'] = $val['Circle']['name'];
            $data['image'] = $Upload->uploadUrl($val, 'Circle.photo', ['style' => 'small']);
            $circle_res[] = $data;
        }

        $users = $this->getUsersByKeyword($keyword, $limit);
        $user_res = [];
        foreach ($users as $val) {
            $data['id'] = 'user_' . $val['User']['id'];
            $data['text'] = $val['User']['username'];
            $data['image'] = $Upload->uploadUrl($val, 'User.photo', ['style' => 'small']);
            $user_res[] = $data;
        }
        $res = array_merge($circle_res, $user_res);
        return ['results' => $res];
    }
}
