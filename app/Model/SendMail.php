<?php
App::uses('AppModel', 'Model');
/** @noinspection PhpDocSignatureInspection */

/**
 * SendMail Model
 *
 * @property User           $FromUser
 * @property User           $ToUser
 * @property Team           $Team
 * @property Notification   $Notification
 * @property SendMailToUser $SendMailToUser
 */
class SendMail extends AppModel
{
    /**
     * メールテンプレタイプ
     */
    const TYPE_TMPL_ACCOUNT_VERIFY = 1;
    const TYPE_TMPL_PASSWORD_RESET = 2;
    const TYPE_TMPL_PASSWORD_RESET_COMPLETE = 3;
    const TYPE_TMPL_TOKEN_RESEND = 4;
    const TYPE_TMPL_CHANGE_EMAIL_VERIFY = 5;
    const TYPE_TMPL_INVITE = 6;
    const TYPE_TMPL_NOTIFY = 7;

    static public $TYPE_TMPL = [
        self::TYPE_TMPL_ACCOUNT_VERIFY          => [
            'subject'  => null,
            'template' => 'account_verification',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_PASSWORD_RESET          => [
            'subject'  => null,
            'template' => 'password_reset',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_PASSWORD_RESET_COMPLETE => [
            'subject'  => null,
            'template' => 'password_reset_complete',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_TOKEN_RESEND            => [
            'subject'  => null,
            'template' => 'token_resend',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_CHANGE_EMAIL_VERIFY     => [
            'subject'  => null,
            'template' => 'change_email',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_INVITE                  => [
            'subject'  => null,
            'template' => 'invite',
            'layout'   => 'default',
        ],
    ];

    private function _setTemplateSubject()
    {
        self::$TYPE_TMPL[self::TYPE_TMPL_ACCOUNT_VERIFY]['subject'] = __d('mail', "アカウントの仮登録が完了しました");
        self::$TYPE_TMPL[self::TYPE_TMPL_PASSWORD_RESET]['subject'] = __d('mail', "パスワードの再設定");
        self::$TYPE_TMPL[self::TYPE_TMPL_PASSWORD_RESET_COMPLETE]['subject'] = __d('mail', "パスワードの再設定が完了しました");
        self::$TYPE_TMPL[self::TYPE_TMPL_TOKEN_RESEND]['subject'] = __d('mail', "メールアドレス認証");
        self::$TYPE_TMPL[self::TYPE_TMPL_CHANGE_EMAIL_VERIFY]['subject'] = __d('mail', "メールアドレス変更に伴う認証");
        self::$TYPE_TMPL[self::TYPE_TMPL_INVITE]['subject'] = __d('mail', "Goalousのチームへ招待");
    }

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->_setTemplateSubject();
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'template_type' => [
            'numeric' => ['rule' => ['numeric'],],
        ],
        'del_flg'       => [
            'boolean' => ['rule' => ['boolean'],],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'FromUser' => ['className' => 'User', 'foreignKey' => 'from_user_id',],
        'ToUser'   => ['className' => 'User', 'foreignKey' => 'to_user_id',],
        'Team',
        'Notification',
    ];

    public $hasMany = [
        'SendMailToUser'
    ];

    /**
     * 送信メールデータ作成
     *
     * @param       $to_uid
     * @param       $tmpl_type
     * @param array $item
     * @param null  $from_uid
     * @param null  $team_id
     *
     * @return bool
     */
    public function saveMailData($to_uid = null, $tmpl_type, $item = [], $from_uid = null, $team_id = null)
    {
        $data = [
            'template_type' => $tmpl_type,
            'item'          => (!empty($item)) ? json_encode($item) : null,
            'from_user_id'  => $from_uid,
            'team_id'       => $team_id,
        ];
        $this->create();
        $res = $this->save($data);
        if ($to_uid) {
            if (!is_array($to_uid)) {
                $to_uid = [$to_uid];
            }
            $send_to_users = [];
            foreach ($to_uid as $val) {
                $send_to_users[] = [
                    'user_id'      => $val,
                    'send_mail_id' => $this->id,
                    'team_id'      => $this->SendMailToUser->current_team_id,
                ];
            }
            $this->SendMailToUser->saveAll($send_to_users);
        }
        return $res;
    }

    /**
     * 詳細なユーザ名等を含んだデータを返す
     * $to_user_idは送信先ユーザID。このIDが指定された場合はNotifyFromUserから送信先ユーザを除外する
     *
     * @param      $id
     * @param null $lang
     * @param bool $with_notify_from_user
     * @param null $to_user_id
     *
     * @return array|null
     */
    public function getDetail($id, $lang = null, $with_notify_from_user = false, $to_user_id = null)
    {
        $lang_backup = null;
        if ($lang) {
            $lang_backup = isset($this->me['language']) ? $this->me['language'] : null;
            $this->FromUser->me['language'] = $lang;
            $this->SendMailToUser->User->me['language'] = $lang;
        }
        $options = [
            'conditions' => ['SendMail.id' => $id],
            'contain'    => [
                'FromUser' => [
                    'fields' => $this->SendMailToUser->User->profileFields,
                    'PrimaryEmail'
                ],
                'Team',
            ]
        ];
        $res = $this->find('first', $options);

        if ($lang) {
            $this->me['language'] = $lang_backup;
            $this->SendMailToUser->User->me['language'] = $lang_backup;
        }
        return $res;
    }

    public function isNotifySentBefore($notification_id, $before_hours = 3)
    {
        $options = [
            'conditions' => [
                'notification_id' => $notification_id,
                'modified >'      => REQUEST_TIMESTAMP - (60 * 60 * $before_hours),
            ],
        ];
        $res = $this->find('first', $options);
        //指定時刻以内の送信履歴あり
        if (!empty($res)) {
            return true;
        }
        //指定時刻以内の送信履歴なし
        return false;
    }
}
