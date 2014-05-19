<?php
App::uses('AppModel', 'Model');

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
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'first_name'        => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'last_name'         => ['notEmpty' => ['rule' => ['notEmpty'],],],
        'hide_year_flg'     => ['boolean' => ['rule' => ['boolean'],],],
        'no_pass_flg'       => ['boolean' => ['rule' => ['boolean'],],],
        'primary_email_id'  => ['uuid' => ['rule' => ['uuid'],],],
        'active_flg'        => ['boolean' => ['rule' => ['boolean'],],],
        'admin_flg'         => ['boolean' => ['rule' => ['boolean'],],],
        'auto_timezone_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'auto_language_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'romanize_flg'      => ['boolean' => ['rule' => ['boolean'],],],
        'update_email_flg'  => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'           => ['boolean' => ['rule' => ['boolean'],],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'ProfileImage' => ['className' => 'Image', 'foreignKey' => 'profile_image_id',],
        'PrimaryEmail' => ['className' => 'Email', 'foreignKey' => 'primary_email_id',],
        'DefaultTeam'  => ['className' => 'Team', 'foreignKey' => 'default_team_id',],
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

    function __construct()
    {
        parent::__construct();
        $this->_setGenderTypeName();
    }

    /**
     * @param $id
     *
     * @return array|null
     */
    function getUser($id)
    {
        if (!$id) {
            return null;
        }
        return $this->find('first', $id);
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
}
