<?php
App::uses('AppModel', 'Model');

/**
 * Notification Model
 *
 * @property User          $User
 * @property Team          $Team
 * @property User          $FromUser
 * @property NotifySetting $NotifySetting
 */
class Notification extends AppModel
{
    public $uses = [
        'NotifySetting'
    ];
    /**
     * 通知タイプ
     */
    const TYPE_FEED_COMMENTED_ON_MY_POST = 1;
    const TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST = 2;
    const TYPE_CIRCLE_USER_JOIN = 3;
    const TYPE_CIRCLE_POSTED_ON_MY_CIRCLE = 4;
    const TYPE_CIRCLE_CHANGED_PRIVACY_SETTING = 5;

    static public $TYPE = [
        self::TYPE_FEED_COMMENTED_ON_MY_POST           => [],
        self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST => [],
        self::TYPE_CIRCLE_USER_JOIN                    => [],
        self::TYPE_CIRCLE_POSTED_ON_MY_CIRCLE          => [],
        self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING      => [],
    ];

    public $default_type_setting = [
        'notify_type' => null,
    ];

    public function _setTypeDefault()
    {
        foreach (self::$TYPE as $type_name => $values) {
            self::$TYPE[$type_name] = $this->default_type_setting;
        }
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_CIRCLE_USER_JOIN]['notify_type'] = NotifySetting::TYPE_CIRCLE;
        self::$TYPE[self::TYPE_CIRCLE_POSTED_ON_MY_CIRCLE]['notify_type'] = NotifySetting::TYPE_CIRCLE;
        self::$TYPE[self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING]['notify_type'] = NotifySetting::TYPE_CIRCLE;
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'type'       => ['numeric' => ['rule' => ['numeric'],],],
        'unread_flg' => ['boolean' => ['rule' => ['boolean'],],],
        'del_flg'    => ['boolean' => ['rule' => ['boolean'],],],
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

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);

        $this->_setTypeDefault();
    }

}
