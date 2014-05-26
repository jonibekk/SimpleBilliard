<?php
App::uses('AppModel', 'Model');
/** @noinspection PhpUndefinedClassInspection */

/**
 * User Model
 *
 * @property Image          $ProfileImage
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
    static public $TYPE_GENDER = [null => "", self::TYPE_GENDER_MALE => "", self::TYPE_GENDER_FEMALE => ""];

    /**
     * 性別タイプの名前をセット
     */
    private function _setGenderTypeName()
    {
        self::$TYPE_GENDER[null] = __d('gl', "選択してください");
        self::$TYPE_GENDER[self::TYPE_GENDER_MALE] = __d('gl', "男性");
        self::$TYPE_GENDER[self::TYPE_GENDER_FEMALE] = __d('gl', "女性");
    }

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
        'hide_year_flg'     => ['boolean' => ['rule' => ['boolean'],],],
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
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'ProfileImage' => ['className' => 'Image', 'foreignKey' => 'profile_image_id',],
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
        'Image',
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

    private function _setVirtualFields()
    {
        $first_name = $this->alias . '.first_name';
        $last_name = $this->alias . '.last_name';
        $this->virtualFields = [
            'username' => 'CONCAT(UPPER(SUBSTRING(' . $first_name . ',1,1)), LOWER(SUBSTRING(' . $first_name
                . ',2)), " ", UPPER(SUBSTRING(' . $last_name . ',1,1)), LOWER(SUBSTRING(' . $last_name . ',2)))',
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
        elseif (!empty($row[$this->alias]['first_name']) && !empty($row[$this->alias]['last_name'])) {
            $display_username = ucfirst($row[$this->alias]['first_name']) . " "
                . ucfirst($row[$this->alias]['last_name']);
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
    public function userProvisionalRegistration($data = [])
    {
        if (!$data) {
            return false;
        }
        //バリデーションでエラーが発生したらreturn
        if (!$this->validateAssociated($data)) {
            return false;
        }
        //パスワードをハッシュ化
        if (isset($data['User']['password']) && !empty($data['User']['password'])) {
            $data['User']['password'] = Security::hash($data['User']['password']);
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

}
