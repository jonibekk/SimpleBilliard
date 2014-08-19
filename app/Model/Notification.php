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
        'NotifySetting'
    ];
    /**
     * 通知タイプ
     */
    const TYPE_FEED_POST = 1;
    const TYPE_FEED_COMMENTED_ON_MY_POST = 2;
    const TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST = 3;
    const TYPE_CIRCLE_USER_JOIN = 4;
    const TYPE_CIRCLE_CHANGED_PRIVACY_SETTING = 5;

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
            'mail_template' => "notify_basic",
        ],
        self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING      => [
            'mail_template' => "notify_basic",
        ],
    ];

    public function _setTypeDefault()
    {
        self::$TYPE[self::TYPE_FEED_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST]['notify_type'] = NotifySetting::TYPE_FEED;
        self::$TYPE[self::TYPE_CIRCLE_USER_JOIN]['notify_type'] = NotifySetting::TYPE_CIRCLE;
        self::$TYPE[self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING]['notify_type'] = NotifySetting::TYPE_CIRCLE;
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

    function saveNotify($data, $user_ids)
    {
        $notify = $this->getNotify($data['model_id'], $data['type']);

        //既に存在する通知リストを取得
        $this->create();
        if (!empty($notify)) {
            unset($notify['Notification']['modified']);
            $notify['Notification'] = array_merge($notify['Notification'], $data);
            $res = $this->save($notify);
        }
        else {
            $res = $this->save($data);
        }
        //from_userを保存
        $data = [
            'notification_id' => $this->id,
            'user_id'         => $this->me['id'],
            'team_id'         => $this->current_team_id,
        ];
        $this->NotifyFromUser->save($data);

        //$this->No
        //関連する通知ユーザを削除
        $this->NotifyToUser->deleteAll(['NotifyToUser.notification_id' => $this->id]);
        //保存データ
        $notify_user_data = [];
        foreach ($user_ids as $uid) {
            $notify_user_data[] = [
                'notification_id' => $this->id,
                'user_id'         => $uid,
                'team_id'         => $this->current_team_id
            ];
        }
        $this->NotifyToUser->create();
        $this->NotifyToUser->saveAll($notify_user_data);
        $this->Team->TeamMember->incrementNotifyUnreadCount($user_ids);
        return $res;
    }

    function getNotify($model_id, $type)
    {
        $option = [
            'conditions' => [
                'model_id' => $model_id,
                'type'     => $type,
                'team_id'  => $this->current_team_id
            ]
        ];
        if (!$model_id) {
            unset($option['conditions']['model_id']);
        }
        $res = $this->find('first', $option);
        return $res;
    }

    function getTitle($type, $from_user_names, $count_num, $item_name_1 = null, $item_name_2 = null)
    {
        $title = null;
        $user_text = null;
        foreach ($from_user_names as $key => $name) {
            if ($key !== 0) {
                $user_text .= __d('gl', "、");
            }
            $user_text .= __d('gl', '%sさん', $name);
        }
        switch ($type) {
            case self::TYPE_FEED_POST:
                $title = __d('gl', '%1$sさんが投稿しました。', $user_text);
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_POST:
                $title = __d('gl', '%1$sさん%2$sがあなたの投稿にコメントしました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $title = __d('gl', '%1$sさん%2$sがあなたのコメントした投稿にコメントしました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null);
                break;
            case self::TYPE_CIRCLE_USER_JOIN:
                $title = __d('gl', '%1$sさん%2$sがサークル「%3$s」に参加しました。', $user_text,
                             ($count_num > 0) ? __d('gl', "と他%s人", $count_num) : null,
                             $item_name_1);
                break;
            case self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                $title = __d('gl', '%1$sさんがサークル「%1$s」のプライバシー設定を%2$sに変更しました。', $user_text,
                             $item_name_1, $item_name_2);
                break;
            default:
                break;
        }
        return $title;
    }

}
