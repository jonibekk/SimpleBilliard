<?php
App::uses('AppModel', 'Model');
/** @noinspection PhpDocSignatureInspection */

/**
 * SendMail Model
 *
 * @property User           $FromUser
 * @property User           $ToUser
 * @property Team           $Team
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
    const TYPE_TMPL_SETUP = 8;
    const TYPE_TMPL_SEND_EMAIL_VERIFY_DIGIT_CODE = 9;
    const TYPE_TMPL_EXPIRE_ALERT_FREE_TRIAL = 10;
    const TYPE_TMPL_EXPIRE_ALERT_READ_ONLY = 11;
    const TYPE_TMPL_EXPIRE_ALERT_CANNOT_USE = 12;
    const TYPE_TMPL_EXPIRE_ALERT_CREDIT_CARD = 13;
    const TYPE_TMPL_CREDIT_STATUS_APPROVED = 14;
    const TYPE_TMPL_CREDIT_STATUS_DENIED = 15;
    const TYPE_TMPL_REGISTER_INVOICE_PAID_PLAN = 16;
    const TYPE_TMPL_REGISTER_CREDIT_CARD_PAID_PLAN = 17;
    const TYPE_TMPL_MOVE_READ_ONLY_FOR_CHARGE_FAILURE = 18;
    const TYPE_TMPL_ALERT_CHARGE_FAILURE = 19;
    const TYPE_TMPL_RECHARGE = 20;
    const TYPE_TMPL_DELETE_TEAM_MANUAL = 21;
    const TYPE_TMPL_DELETE_TEAM_AUTO = 22;
    const TYPE_TMPL_TRANSLATION_LIMIT_REACHED = 23;
    const TYPE_TMPL_TRANSLATION_LIMIT_CLOSING = 24;
    const TYPE_TMPL_TEAM_MEMBER_BULK_REGISTRATION = 25;

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
        self::TYPE_TMPL_TOKEN_RESEND                   => [
            'subject'  => null,
            'template' => 'token_resend',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_CHANGE_EMAIL_VERIFY            => [
            'subject'  => null,
            'template' => 'change_email',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_INVITE                         => [
            'subject'  => null,
            'template' => 'invite',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_SETUP                          => [
            'subject'  => null,
            'template' => 'setup',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_SEND_EMAIL_VERIFY_DIGIT_CODE   => [
            'subject'  => null,
            'template' => 'email_verify_digit_code',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_EXPIRE_ALERT_FREE_TRIAL        => [
            'subject'  => null,
            'template' => 'expire_alert_free_trial',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_EXPIRE_ALERT_READ_ONLY         => [
            'subject'  => null,
            'template' => 'expire_alert_read_only',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_EXPIRE_ALERT_CANNOT_USE        => [
            'subject'  => null,
            'template' => 'expire_alert_cannot_use',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_EXPIRE_ALERT_CREDIT_CARD       => [
            'subject'  => null,
            'template' => 'expire_alert_credit_card',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_CREDIT_STATUS_APPROVED         => [
            'subject'  => null,
            'template' => 'credit_approved_notification',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_CREDIT_STATUS_DENIED           => [
            'subject'  => null,
            'template' => 'credit_denied_notification',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_REGISTER_INVOICE_PAID_PLAN     => [
            'subject'  => null,
            'template' => 'register_invoice_paid_plan',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_REGISTER_CREDIT_CARD_PAID_PLAN    => [
            'subject'  => null,
            'template' => 'register_credit_card_paid_plan',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_MOVE_READ_ONLY_FOR_CHARGE_FAILURE => [
            'subject'  => null,
            'template' => 'move_read_only_for_charge_failure',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_ALERT_CHARGE_FAILURE    => [
            'subject'  => null,
            'template' => 'alert_charge_failure',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_RECHARGE                => [
            'subject'  => null,
            'template' => 'recharge',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_DELETE_TEAM_MANUAL      => [
            'subject'  => null,
            'template' => 'delete_team_manual',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_DELETE_TEAM_AUTO        => [
            'subject'  => null,
            'template' => 'delete_team_auto',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_TRANSLATION_LIMIT_REACHED => [
            'subject'  => null,
            'template' => 'translation_limit_reached',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_TRANSLATION_LIMIT_CLOSING => [
            'subject'  => null,
            'template' => 'translation_limit_closing',
            'layout'   => 'default',
        ],
        self::TYPE_TMPL_TEAM_MEMBER_BULK_REGISTRATION => [
            'subject'  => null,
            'template' => 'team_member_bulk_registration',
            'layout'   => 'default',
        ]
    ];

    public function _setTemplateSubject()
    {
        self::$TYPE_TMPL[self::TYPE_TMPL_ACCOUNT_VERIFY]['subject'] = __("Registered temporarily");
        self::$TYPE_TMPL[self::TYPE_TMPL_PASSWORD_RESET]['subject'] = __("Reset Password");
        self::$TYPE_TMPL[self::TYPE_TMPL_PASSWORD_RESET_COMPLETE]['subject'] = __("Succeeded to reset password");
        self::$TYPE_TMPL[self::TYPE_TMPL_TOKEN_RESEND]['subject'] = __("Authentication email address");
        self::$TYPE_TMPL[self::TYPE_TMPL_CHANGE_EMAIL_VERIFY]['subject'] = __("Authentication for changing email address");
        self::$TYPE_TMPL[self::TYPE_TMPL_INVITE]['subject'] = __("Invitation for team");
        self::$TYPE_TMPL[self::TYPE_TMPL_SETUP]['subject'] = __("Could you setup Goalous?");
        self::$TYPE_TMPL[self::TYPE_TMPL_EXPIRE_ALERT_FREE_TRIAL]['subject'] = __("The time limit for Free Trial is approaching");
        self::$TYPE_TMPL[self::TYPE_TMPL_EXPIRE_ALERT_READ_ONLY]['subject'] = __("The time limit for Free Trial has expired");
        self::$TYPE_TMPL[self::TYPE_TMPL_EXPIRE_ALERT_CANNOT_USE]['subject'] = __("To continue using Goalous, please apply for the Paid Plan");
        self::$TYPE_TMPL[self::TYPE_TMPL_EXPIRE_ALERT_CREDIT_CARD]['subject'] = __("[Important]Credit card expiring soon");
        self::$TYPE_TMPL[self::TYPE_TMPL_CREDIT_STATUS_APPROVED]['subject'] = __("Notice of credit check result: You passed the credit check");
        self::$TYPE_TMPL[self::TYPE_TMPL_CREDIT_STATUS_DENIED]['subject'] = __("Notice of credit check result: You didn't pass the credit check");
        self::$TYPE_TMPL[self::TYPE_TMPL_REGISTER_INVOICE_PAID_PLAN]['subject'] = __("Registration to the paid plan is complete");
        self::$TYPE_TMPL[self::TYPE_TMPL_REGISTER_CREDIT_CARD_PAID_PLAN]['subject'] = __("Registration to the paid plan is complete");
        self::$TYPE_TMPL[self::TYPE_TMPL_ALERT_CHARGE_FAILURE]['subject'] = __("We had a problem billing your team");
        self::$TYPE_TMPL[self::TYPE_TMPL_MOVE_READ_ONLY_FOR_CHARGE_FAILURE]['subject'] = __("We are downgrading your team to read-only");
        self::$TYPE_TMPL[self::TYPE_TMPL_RECHARGE]['subject'] = __("We recharged your team");
        self::$TYPE_TMPL[self::TYPE_TMPL_DELETE_TEAM_MANUAL]['subject'] = __("The team has been deleted");
        self::$TYPE_TMPL[self::TYPE_TMPL_DELETE_TEAM_AUTO]['subject'] = __("The team has been deleted");
        self::$TYPE_TMPL[self::TYPE_TMPL_TRANSLATION_LIMIT_REACHED]['subject'] = __("Pause translation for your team");
        self::$TYPE_TMPL[self::TYPE_TMPL_TRANSLATION_LIMIT_CLOSING]['subject'] = __("Approaching the limit of translatable number.");
        self::$TYPE_TMPL[self::TYPE_TMPL_TEAM_MEMBER_BULK_REGISTRATION]['subject'] = __("Invitation for team");
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
        $res = $this->findWithoutTeamId('first', $options);

        if ($lang) {
            $this->me['language'] = $lang_backup;
            $this->SendMailToUser->User->me['language'] = $lang_backup;
        }
        return $res;
    }

}
