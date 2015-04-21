<?php
App::uses('AppModel', 'Model');
App::uses('NotifySetting', 'Model');
App::uses('ConnectionManager', 'Model');

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

    private $Redis;

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->Redis = ConnectionManager::getDataSource('redis');

        $this->_setTypeDefault();
    }

    function saveNotify($data, $user_ids)
    {
        $notify = $this->getNotify($data['model_id'], $data['type']);

        if (!empty($notify)) {
            unset($notify['Notification']['modified']);
            $notify['Notification'] = array_merge($notify['Notification'], $data);
            $res = $this->save($notify);
        }
        else {
            $this->create();
            $res = $this->save($data);
        }
        //from_userを保存
        $data = [
            'notification_id' => $this->id,
            'user_id'         => $this->my_uid,
            'team_id'         => $this->current_team_id,
        ];

        $this->NotifyFromUser->create();
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

    function getNotifyFromTodayUtc($type)
    {
        $options = [
            'conditions' => [
                'Notification.team_id'    => $this->current_team_id,
                'Notification.type'       => $type,
                'Notification.modified >' => strtotime("today"),
            ],
            'contain'    => [
                'NotifyToUser'
            ]
        ];
        $res = $this->find('all', $options);
        return $res;
    }

    /**
     * @param $data
     * @param $user_ids
     *
     * @return array $saved_notify_ids
     */
    function saveNotifyOneOnOne($data, $user_ids)
    {
        //当日のutcの0:00以降の通知が既に存在する場合はupdate、存在しない場合はinsert
        $notifies = $this->getNotifyFromTodayUtc($data['type']);
        $insert_notify_uids = $user_ids;
        $saved_notify_ids = [];
        if (!empty($notifies)) {
            foreach ($notifies as $notify) {
                //存在する場合はupdate
                $insert_user_key = array_search($notify['NotifyToUser'][0]['user_id'], $insert_notify_uids);
                if ($insert_user_key !== false) {
                    //insertユーザから除外
                    unset($insert_notify_uids[$insert_user_key]);
                    //更新用のデータをマージ
                    $notify['Notification'] = array_merge($notify['Notification'], $data);
                    //更新日を更新
                    unset($notify['Notification']['modified']);
                    unset($notify['NotifyToUser'][0]['modified']);
                    $notify['NotifyToUser'][0]['unread_flg'] = true;
                    $notify['NotifyFromUser'] = [
                        [
                            'user_id' => $this->my_uid,
                            'team_id' => $this->current_team_id,
                        ]
                    ];
                    $this->saveAll($notify);
                    $saved_notify_ids[] = $notify['Notification']['id'];
                    //count_numを更新
                    $this->updateCountNum($notify['Notification']['id'], $notify['NotifyToUser'][0]['user_id']);
                }
            }
        }

        //insert処理
        if (!empty($insert_notify_uids)) {
            foreach ($insert_notify_uids as $uid) {
                $save_data = [
                    'Notification'   => $data,
                    'NotifyFromUser' => [
                        [
                            'user_id' => $this->my_uid,
                            'team_id' => $this->current_team_id,
                        ]
                    ],
                    'NotifyToUser'   => [
                        [
                            'user_id' => $uid,
                            'team_id' => $this->current_team_id,
                        ]
                    ]
                ];
                $this->create();
                $this->saveAll($save_data);
                $saved_notify_ids[] = $this->getLastInsertID();
            }
        }

        //通知未読件数を更新
        $this->Team->TeamMember->incrementNotifyUnreadCount($user_ids);

        return $saved_notify_ids;
    }

    function updateCountNum($id, $without_user_id_list)
    {
        $options = [
            'conditions' => [
                'notification_id' => $id,
            ],
            'fields'     => [
                'COUNT(DISTINCT user_id) as count',
            ]
        ];
        if (!empty($without_user_id_list)) {
            $options['conditions']['NOT']['user_id'] = $without_user_id_list;
        }
        $res = $this->NotifyFromUser->find('count', $options);
        $this->id = $id;
        $this->saveField('count_num', $res);
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

    function getTitle($type, $from_user_names, $count_num, $item_name)
    {
        $item_name = json_decode($item_name, true);
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

    /**
     * get notifications form redis.
     * return value like this.
     * $array = [
     * [
     * 'User'         => [
     * 'id'               => 1,
     * 'display_username' => 'test taro',
     * 'photo_file_name'  => null,
     * ],
     * 'Notification' => [
     * 'title'      => 'test taroさんがあなたの投稿にコメントしました。',
     * 'url'        => 'http://192.168.50.4/post_permanent/1/from_notification:1',
     * 'unread_flg' => false,
     * 'created'    => '1429643033',
     * ]
     * ],
     * [
     * 'User'         => [
     * 'id'               => 2,
     * 'display_username' => 'test jiro',
     * 'photo_file_name'  => null,
     * ],
     * 'Notification' => [
     * 'title'      => 'test jiroさんがあなたの投稿にコメントしました。',
     * 'url'        => 'http://192.168.50.4/post_permanent/2/from_notification:1',
     * 'unread_flg' => false,
     * 'created'    => '1429643033',
     * ]
     * ],
     * ];
     *
     * @param null|int $limit
     * @param null|int $page
     *
     * @return array
     */
    function getFromRedis($limit = null, $page = 1)
    {
        //$this->Redis->get();
        $data = [
            [
                'User'         => [
                    'id'               => 1,
                    'display_username' => 'test taro',
                    'photo_file_name'  => null,
                ],
                'Notification' => [
                    'title'      => 'test taroさんがあなたの投稿にコメントしました。',
                    'body'       => 'この通知機能マジ最高だね！',
                    'url'        => 'http://192.168.50.4/post_permanent/1/from_notification:1',
                    'unread_flg' => false,
                    'created'    => '1429643033',
                ]
            ],
            [
                'User'         => [
                    'id'               => 2,
                    'display_username' => 'test jiro',
                    'photo_file_name'  => null,
                ],
                'Notification' => [
                    'title'      => 'test jiroさんがあなたの投稿にコメントしました。',
                    'body'       => 'ほんと半端く良いわ！',
                    'url'        => 'http://192.168.50.4/post_permanent/2/from_notification:1',
                    'unread_flg' => false,
                    'created'    => '1429643033',
                ]
            ],
        ];

        return $data;
    }

    /**
     * set notifications
     *
     * @param array|int $to_user_ids
     * @param int       $type
     *
     * @return bool
     */
    function setNotifications($to_user_ids, $type)
    {

        return true;
    }

    /**
     * get count of new notifications from redis.
     *
     * @return int
     */
    function getCountNewFromRedis()
    {
        return 10;
    }

    /**
     * delete count of new notifications form redis.
     *
     * @return bool
     */
    function resetCountNewFromRedis()
    {
        return true;
    }

    /**
     * increment count of new notifications from redis.
     *
     * @param int $user_id
     *
     * @return bool
     */
    function incCountNewAtRedis($user_id)
    {
        return true;
    }

    /**
     * change read status of notification.
     *
     * @param int $id
     *
     * @return bool
     */
    function changeReadStatusAtRedis($id)
    {
        return true;

    }

}
