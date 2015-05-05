<?php
App::uses('AppModel', 'Model');
App::uses('NotifySetting', 'Model');

/**
 * Notification Model
 *
 * @property User              $User
 * @property Team              $Team
 * @property User              $FromUser
 * @property NotifySetting     $NotifySetting
 * @property NotifyToUser      $NotifyToUser
 * @property NotifyFromUser    $NotifyFromUser
 */
class Notification extends AppModel
{
    public $uses = [
        'NotifySetting',
    ];
    /**
     * 通知タイプ
     */
    const TYPE_FEED_POST = 1;
    const TYPE_FEED_COMMENTED_ON_MY_POST = 2;
    const TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST = 3;
    const TYPE_CIRCLE_USER_JOIN = 4;
    const TYPE_CIRCLE_CHANGED_PRIVACY_SETTING = 5;
    const TYPE_CIRCLE_ADD_USER = 6;

    static public $TYPE = [
        self::TYPE_FEED_POST                           => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_POST           => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_CIRCLE_USER_JOIN                    => [
            'mail_template' => "notify_not_use_body",
        ],
        self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING      => [
            'mail_template' => "notify_not_use_body",
        ],
        self::TYPE_CIRCLE_ADD_USER                     => [
            'mail_template' => "notify_not_use_body",
        ],
    ];

    public function _setTypeDefault()
    {
        self::$TYPE[self::TYPE_FEED_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_CIRCLE_USER_JOIN]['notify_type'] = NotifySetting::TYPE_CIRCLE;
        self::$TYPE[self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING]['notify_type'] = NotifySetting::TYPE_CIRCLE;
        self::$TYPE[self::TYPE_CIRCLE_ADD_USER]['notify_type'] = NotifySetting::TYPE_CIRCLE;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'    => ['numeric' => ['rule' => ['numeric'],],],
        'del_flg' => ['boolean' => ['rule' => ['boolean'],],],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
        'Team',
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
    ];

    public $hasMany = [
        'NotifyToUser',
        'NotifyFromUser',
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTypeDefault();
    }

    function getTitle($type, $from_user_names, $count_num, $item_name)
    {
        json_decode($item_name, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            $item_name = json_decode($item_name, true);
        }
        $title = null;
        $user_text = null;
        //カウント数はユーザ名リストを引いた数
        $count_num -= count($from_user_names);
        if (!is_array($from_user_names)) {
            $from_user_names = [$from_user_names];
        }
        foreach ($from_user_names as $key => $name) {
            if ($key !== 0) {
                $user_text .= __d('gl', "、");
            }
            $user_text .= __d('gl', '%sさん', $name);
        }
        switch ($type) {
            case self::TYPE_FEED_POST:
                $title = __d('gl', '%1$s%2$sが投稿しました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_POST:
                $title = __d('gl', '%1$s%2$sがあなたの投稿にコメントしました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $title = __d('gl', '%1$s%2$sも投稿にコメントしました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_CIRCLE_USER_JOIN:
                $title = __d('gl', '%1$s%2$sがサークル「%3$s」に参加しました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null,
                             $item_name[0]);
                break;
            case self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                $title = __d('gl', '%1$sがサークル「%2$s」のプライバシー設定を「%3$s」に変更しました。', $user_text,
                             $item_name[0], $item_name[1]);
                break;
            case self::TYPE_CIRCLE_ADD_USER:
                $title = __d('gl', '%1$sがサークル「%2$s」にあなたを追加しました。', $user_text,
                             $item_name[0]);
                break;
            default:
                break;
        }
        return $title;
    }

}
