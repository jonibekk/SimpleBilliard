<?php
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
 * @property Image          $Image
 * @property Notification   $Notification
 * @property OauthToken     $OauthToken
 * @property PostLike       $PostLike
 * @property PostMention    $PostMention
 * @property PostRead       $PostRead
 * @property Post           $Post
 * @property TeamMember     $TeamMember
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
        'primary_email_id'  => ['uuid' => ['rule' => ['uuid'],],],
        'active_flg'        => ['boolean' => ['rule' => ['boolean'],],],
        'admin_flg'         => ['boolean' => ['rule' => ['boolean'],],],
        'auto_timezone_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'auto_language_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'romanize_flg'      => ['boolean' => ['rule' => ['boolean'],],],
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
            'image_max_size' => [
                'rule' => [
                    'attachmentMaxSize',
                    10485760 //10mb
                ],
            ],
        ]
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
        //TODO sessionをモデルから参照するのは良くない。要修正。
//        if ((isset($this->sessionValiable['Auth']['User']['romanize_flg'])
//                && $this->sessionValiable['Auth']['User']['romanize_flg']) || !$local_username
        if (!$local_username) {
            //ローマ字表記を指定していた場合
            $display_username = $this->_getRomanUsername($row);
        }
        elseif ($this->isNotUseLocalName($row[$this->alias]['language'])) {
            //ローカル名を使わない言語の場合
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
        if (!empty($row[$this->alias]['language']) && !empty($row[$this->alias]['local_first_name'])
            && !empty($row[$this->alias]['local_last_name'])
        ) {
            //ローカルユーザ名が存在し、言語設定がある場合は国毎の表示を設定する
            $last_first = in_array($row[$this->alias]['language'], $this->langCodeOfLastFirst);
            if ($last_first) {
                $local_username = $row[$this->alias]['local_last_name'] . " "
                    . $row[$this->alias]['local_first_name'];
            }
            else {
                $local_username = $row[$this->alias]['local_first_name'] . " "
                    . $row[$this->alias]['local_last_name'];
            }
        }
        elseif (!empty($row[$this->alias]['local_first_name']) && !empty($row[$this->alias]['local_last_name'])) {
            $local_username = $row[$this->alias]['local_first_name'] . " " . $row[$this->alias]['local_last_name'];
        }
        return $local_username;
    }

    /**
     * Goalousの全ての有効なユーザ数
     *
     * @return int
     */
    function getAllUsersCount()
    {
        $options = array(
            'conditions' => array(
                'active_flg' => true
            )
        );
        $res = $this->find('count', $options);
        return $res;
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
     * ユーザ仮登録(メール認証前)
     *
     * @param array $data
     *
     * @return bool
     */
    public function userProvisionalRegistration($data)
    {
        //バリデーションでエラーが発生したらreturn
        if (!$this->validateAssociated($data)) {
            return false;
        }
        //パスワードをハッシュ化
        if (isset($data['User']['password']) && !empty($data['User']['password'])) {
            $passwordHasher = new SimplePasswordHasher();
            $data['User']['password'] = $passwordHasher->hash($data['User']['password']);
        }
        //メールアドレスの認証トークンを発行
        $email_token = $this->generateToken();
        $data['Email'][0]['Email']['email_token'] = $email_token;
        //メールアドレスの認証トークンの期限をセット
        $data['Email'][0]['Email']['email_token_expires'] = $this->getTokenExpire();
        //データを保存
        $this->create();
        if ($this->saveAll($data, ['validate' => false])) {
            //プライマリメールアドレスを登録
            $this->save(['primary_email_id' => $this->Email->id]);
            //コントローラ側で必要になるデータをセット
            $this->Email->set('email_token', $email_token);
            $this->Email->set('email', $data['Email'][0]['Email']['email']);
            return true;
        }
        return false;
    }

    /**
     * Generate token used by the user registration system
     *
     * @param int $length Token Length
     *
     * @return string
     */
    public function generateToken($length = 22)
    {
        $possible = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = "";
        $i = 0;

        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            if (!stristr($token, $char)) {
                $token .= $char;
                $i++;
            }
        }
        return $token;
    }

    /**
     * トークンの期限を返却
     *
     * @param int $interval
     *
     * @return string
     */
    public function getTokenExpire($interval = TOKEN_EXPIRE_SEC_REGISTER)
    {
        return date('Y-m-d H:i:s', time() + $interval);
    }

    /**
     * Verifies a users email by a token that was sent to him via email and flags the user record as active
     *
     * @param string $token The token that wa sent to the user
     *
     * @throws RuntimeException
     * @return array On success it returns the user data record
     */
    public function verifyEmail($token = null)
    {
        $user = $this->Email->find('first',
                                   [
                                       'conditions' => [
                                           'Email.email_verified' => false,
                                           'Email.email_token'    => $token
                                       ],
                                   ]
        );

        if (empty($user)) {
            throw new RuntimeException(
                __d('exception', "トークンが正しくありません。送信されたメールを再度ご確認下さい。"));
        }

        $expires = strtotime($user['Email']['email_token_expires']);
        if ($expires < time()) {
            throw new RuntimeException(__d('exception', 'トークンの期限が切れています。'));
        }

        $user['User']['id'] = $user['Email']['user_id'];
        $user['User']['active_flg'] = true;
        $user['Email']['email_verified'] = true;
        $user['Email']['email_token'] = null;
        $user['Email']['email_token_expires'] = null;

        $this->Email->saveAll($user, ['validate' => false, 'callbacks' => false]);
        return $this->findById($user['User']['id']);
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
        if (isset($postData['User']['email']) && empty($postData['User']['email'])) {
            $this->invalidate('email', __d('validation', "メールアドレスを入力してください。"));
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
                'Email.email_token_expires >=' => date('Y-m-d H:i:s'),
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

        $passwordHasher = new SimplePasswordHasher();
        $user_email['User']['password'] = $passwordHasher->hash($postData['User']['password']);
        $user_email['User']['password_token'] = null;
        $user_email['User']['password_modified'] = date('Y-m-d H:i:s');
        $user_email['Email']['email_token_expires'] = null;
        return $this->Email->saveAll($user_email);
    }

}
