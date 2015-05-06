<?php
App::uses('AppModel', 'Model');

/**
 * NotifySetting Model
 *
 * @property User $User
 */
class NotifySetting extends AppModel
{
    /**
     * 通知設定タイプ
     */
    const TYPE_FEED_POST = 1;
    const TYPE_FEED_COMMENTED_ON_MY_POST = 2;
    const TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST = 3;
    const TYPE_CIRCLE_USER_JOIN = 4;
    const TYPE_CIRCLE_CHANGED_PRIVACY_SETTING = 5;
    const TYPE_CIRCLE_ADD_USER = 6;

    static public $TYPE = [
        self::TYPE_FEED_POST                           => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_post',
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_POST           => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_commented_on_my_post',
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_commented_on_my_commented_post',
        ],
        self::TYPE_CIRCLE_USER_JOIN                    => [
            'mail_template'   => "notify_not_use_body",
            'field_real_name' => null,
            'field_prefix'    => 'circle_user_join',
        ],
        self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING      => [
            'mail_template'   => "notify_not_use_body",
            'field_real_name' => null,
            'field_prefix'    => 'circle_changed_privacy_setting',
        ],
        self::TYPE_CIRCLE_ADD_USER                     => [
            'mail_template'   => "notify_not_use_body",
            'field_real_name' => null,
            'field_prefix'    => 'circle_add_user',
        ],
    ];

    public function _setFieldRealName()
    {
        self::$TYPE[self::TYPE_FEED_POST]['field_real_name'] = __d('gl', "自分が閲覧可能な投稿があったとき");
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_POST]['field_real_name'] = __d('gl', "自分の投稿に「コメント」されたとき");
        self::$TYPE[self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST]['field_real_name'] = __d('gl',
                                                                                             "自分のコメントした投稿に「コメント」されたとき");
        self::$TYPE[self::TYPE_CIRCLE_USER_JOIN]['field_real_name'] = __d('gl', "自分が管理者の公開サークルに誰かが参加したとき");
        self::$TYPE[self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING]['field_real_name'] = __d('gl',
                                                                                        "自分が所属するサークルのプライバシー設定が変更になったとき");
        self::$TYPE[self::TYPE_CIRCLE_ADD_USER]['field_real_name'] = __d('gl', "自分が新たにサークルメンバーに追加させたとき");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setFieldRealName();
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'feed_post_app_flg'                             => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'feed_post_email_flg'                           => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'feed_commented_on_my_post_app_flg'             => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'feed_commented_on_my_post_email_flg'           => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'feed_commented_on_my_commented_post_app_flg'   => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'feed_commented_on_my_commented_post_email_flg' => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'circle_user_join_app_flg'                      => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'circle_user_join_email_flg'                    => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'circle_changed_privacy_setting_app_flg'        => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'circle_changed_privacy_setting_email_flg'      => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'circle_add_user_app_flg'                       => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'circle_add_user_email_flg'                     => [
            'boolean' => ['rule' => ['boolean'], 'allowEmpty' => true,],
        ],
        'del_flg'                                       => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User'
    ];

    /**
     * 指定タイプのアプリ、メールの通知設定を返却
     * ユーザ指定は単数、複数の両方対応
     * 返却値は[uid=>['app'=>true,'email'=>true],,,,]
     *
     * @param $user_ids
     * @param $type
     *
     * @return array
     */
    function getAppEmailNotifySetting($user_ids, $type)
    {
        if (!is_array($user_ids)) {
            $user_ids = [$user_ids];
        }
        $default_data = [
            'app'   => true,
            'email' => true,
        ];
        $options = array(
            'conditions' => array(
                'user_id' => $user_ids,
                'NOT'     => ['user_id' => $this->my_uid]
            )
        );
        $result = $this->find('all', $options);
        $res_data = [];
        $field_prefix = self::$TYPE[$type]['field_prefix'];
        if (!empty($result)) {
            foreach ($result as $val) {
                $res_data[$val['NotifySetting']['user_id']] = $default_data;
                if (!$val['NotifySetting'][$field_prefix . '_app_flg']) {
                    //アプリがoff
                    $res_data[$val['NotifySetting']['user_id']]['app'] = false;
                }
                if (!$val['NotifySetting'][$field_prefix . '_email_flg']) {
                    //メールがoff
                    $res_data[$val['NotifySetting']['user_id']]['email'] = false;
                }
                //引数のユーザリストから除去
                if (($array_key = array_search($val['NotifySetting']['user_id'], $user_ids)) !== false) {
                    unset($user_ids[$array_key]);
                }
            }
        }
        //設定なしユーザはデフォルトを適用
        if (!empty($user_ids)) {
            foreach ($user_ids as $uid) {
                $res_data[$uid] = $default_data;
            }
        }
        return $res_data;
    }

}
