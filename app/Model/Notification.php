<?php
App::uses('AppModel', 'Model');
App::uses('NotifySetting', 'Model');

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
        self::TYPE_FEED_COMMENTED_ON_MY_POST           => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_CIRCLE_USER_JOIN                    => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_CIRCLE_POSTED_ON_MY_CIRCLE          => [
            'mail_template' => "notify_basic",
        ],
        self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING      => [
            'mail_template' => "notify_basic",
        ],
    ];

    public function _setTypeDefault()
    {
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

    function saveNotify($data)
    {
        $option = [
            'conditions' => [
                'model_id' => $data['model_id'],
                'user_id'  => $data['user_id'],
                'type'     => $data['type'],
            ]
        ];
        $notify = $this->find('first', $option);
        $this->create();
        if (!empty($notify)) {
            unset($notify['Notification']['modified']);
            $notify['Notification'] = array_merge($notify['Notification'], $data);
            $res = $this->save($notify);
        }
        else {
            $res = $this->save($data);
        }
        if ($data['enable_flg']) {
            $this->Team->TeamMember->incrementNotifyUnreadCount($res['Notification']['user_id']);
        }
        return $res;
    }

    function getTitle($type, $user_name, $count_num, $item_name_1 = null, $item_name_2 = null)
    {
        $title = null;
        switch ($type) {
            case self::TYPE_FEED_COMMENTED_ON_MY_POST:
                $title = __d('gl', '%1$sさん%2$sがあなたの投稿にコメントしました。', $user_name,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $title = __d('gl', '%1$sさん%2$sがあなたのコメントした投稿にコメントしました。', $user_name,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_CIRCLE_USER_JOIN:
                $title = __d('gl', '%1$sさん%2$sが%2$sに参加しました。', $user_name,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null,
                             $item_name_1);
                break;
            case self::TYPE_CIRCLE_POSTED_ON_MY_CIRCLE:
                $title = __d('gl', '%1$sさん%2$sが%2$sに投稿しました。', $user_name,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null,
                             $item_name_1);
                break;
            case self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                $title = __d('gl', '%1$sさんが%1$sのプライバシー設定を%2$sに変更しました。', $user_name,
                             $item_name_1, $item_name_2);
                break;
            default:
                break;
        }
        return $title;
    }

}
