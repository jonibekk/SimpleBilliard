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
    /** 性別フラグ */
    const MALE = 1;
    const FEMALE = 2;
    /**
     * 性別タイプ
     * @var array
     */
    static public $GENDER_TYPE = [];

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'first_name'        => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'last_name'         => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
            ),
        ),
        'hide_year_flg'     => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'no_pass_flg'       => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'primary_email_id'  => array(
            'uuid' => array(
                'rule' => array('uuid'),
            ),
        ),
        'active_flg'        => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'admin_flg'         => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'auto_timezone_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'auto_language_flg' => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'romanize_flg'      => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'update_email_flg'  => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
        'del_flg'           => array(
            'boolean' => array(
                'rule' => array('boolean'),
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'ProfileImage' => array(
            'className'  => 'Image',
            'foreignKey' => 'profile_image_id',
        ),
        'PrimaryEmail' => array(
            'className'  => 'Email',
            'foreignKey' => 'primary_email_id',
        ),
        'DefaultTeam'  => array(
            'className'  => 'Team',
            'foreignKey' => 'default_team_id',
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Badge'          => array(
            'className'  => 'Badge',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'CommentLike'    => array(
            'className'  => 'CommentLike',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'CommentMention' => array(
            'className'  => 'CommentMention',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'CommentRead'    => array(
            'className'  => 'CommentRead',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'Comment'        => array(
            'className'  => 'Comment',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'Email'          => array(
            'className'  => 'Email',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'GivenBadge'     => array(
            'className'  => 'GivenBadge',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'Image'          => array(
            'className'  => 'Image',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'Notification'   => array(
            'className'  => 'Notification',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'OauthToken'     => array(
            'className'  => 'OauthToken',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'PostLike'       => array(
            'className'  => 'PostLike',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'PostMention'    => array(
            'className'  => 'PostMention',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'PostRead'       => array(
            'className'  => 'PostRead',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'Post'           => array(
            'className'  => 'Post',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        ),
        'TeamMember'     => array(
            'className'  => 'TeamMember',
            'foreignKey' => 'user_id',
            'dependent'  => false,
        )
    );

    function __construct()
    {
        parent::__construct();
        $this->_setGenderTypeName();
    }

    /**
     * 性別タイプの名前をセット
     */
    private function _setGenderTypeName()
    {
        self::$GENDER_TYPE[null] = __d('gl', "選択してください");
        self::$GENDER_TYPE[self::MALE] = __d('gl', "男性");
        self::$GENDER_TYPE[self::FEMALE] = __d('gl', "女性");
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
}
