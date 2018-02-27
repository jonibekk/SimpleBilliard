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
    const TYPE_MY_GOAL_FOLLOW = 7;
    const TYPE_MY_GOAL_COLLABORATE = 8;
    const TYPE_MY_GOAL_CHANGED_BY_LEADER = 9;
    const TYPE_MY_GOAL_TARGET_FOR_EVALUATION = 10;
    const TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE = 11;
    const TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION = 12;
    const TYPE_COACHEE_CREATE_GOAL = 13;
    const TYPE_COACHEE_COLLABORATE_GOAL = 14;
    const TYPE_COACHEE_CHANGE_GOAL = 15;
    const TYPE_EVALUATION_START = 16;
    const TYPE_EVALUATION_FREEZE = 17;
    const TYPE_EVALUATION_START_CAN_ONESELF = 18;
    const TYPE_EVALUATION_CAN_AS_EVALUATOR = 19;
    const TYPE_EVALUATION_DONE_FINAL = 20;
    const TYPE_FEED_COMMENTED_ON_MY_ACTION = 21;
    const TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION = 22;
    const TYPE_FEED_CAN_SEE_ACTION = 23;
    const TYPE_USER_JOINED_TO_INVITED_TEAM = 24;
    const TYPE_MESSAGE = 25;
    const TYPE_SETUP_GUIDE = 26;
    const TYPE_COACHEE_CHANGE_ROLE = 27;
    const TYPE_COACHEE_WITHDRAW_APPROVAL = 28;
    const TYPE_APPROVAL_COMMENT = 29;
    const TYPE_COACHEE_EXCHANGE_TKR = 30;
    const TYPE_TKR_EXCHANGED_BY_LEADER = 31;
    const TYPE_EXCHANGED_LEADER = 32;
    const TYPE_MEMBER_CHANGE_KR = 33;
    const TYPE_MY_GOAL_CHANGED_NEXT_TO_CURRENT_BY_LEADER = 34;
    const TYPE_COACHEE_CHANGE_GOAL_NEXT_TO_CURRENT = 35;
    const TYPE_CHANGED_TEAM_BASIC_SETTING = 36;
    const TYPE_CHANGED_TERM_SETTING = 37;
    const TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED = 38;
    const TYPE_TRANSCODE_FAILED = 39;

    /**
     * @var array
     *
     * @key mail_template string
     *      Mail template name on app\View\Emails\text\*.ctp
     *
     * @key field_real_name null
     *      Not using currently.
     *
     * @key field_prefix string
     *      string
     *          Prefix name of DB table columns of
     *          notify_settings.*_app_flg
     *          notify_settings.*_email_flg
     *          notify_settings.*_mobile_flg
     *
     * @key icon_class string
     *      The Font-Awesome icon show in the web notification.
     *
     * @key groups string[] 'all' || 'primary' || 'none'
     *      'all': notify all
     *      'primary': notify important event
     *      'none': no notify
     *
     * @key force_notify bool
     *      This is optional
     *      force notify to user or not
     *      true: notify to user every time
     *      false: never notifying
     */
    static public $TYPE = [
        self::TYPE_FEED_POST                                 => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_post',
            'icon_class'      => 'fa-comment-o',
            'groups'          => ['all'],
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_POST                 => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_commented_on_my_post',
            'icon_class'      => 'fa-comment-o',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_commented_on_my_commented_post',
            'icon_class'      => 'fa-comment-o',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_CIRCLE_USER_JOIN                          => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'circle_user_join',
            'icon_class'      => 'fa-circle-o',
            'groups'          => ['all'],
        ],
        self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING            => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'circle_changed_privacy_setting',
            'icon_class'      => 'fa-circle-o',
            'groups'          => ['all'],
        ],
        self::TYPE_CIRCLE_ADD_USER                           => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'circle_add_user',
            'icon_class'      => 'fa-circle-o',
            'groups'          => ['all'],
        ],
        self::TYPE_MY_GOAL_FOLLOW                            => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_follow',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all'],
        ],
        self::TYPE_MY_GOAL_COLLABORATE                       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_collaborate',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MEMBER_CHANGE_KR                          => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_changed_by_leader',
            'icon_class'      => 'fa-key',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MY_GOAL_CHANGED_BY_LEADER                 => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_changed_by_leader',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MY_GOAL_TARGET_FOR_EVALUATION             => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_target_for_evaluation',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_as_leader_request_to_change',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION         => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_goal_not_target_for_evaluation',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_COACHEE_CREATE_GOAL                       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all'],
        ],
        self::TYPE_COACHEE_COLLABORATE_GOAL                  => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_collaborate_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all'],
        ],
        self::TYPE_COACHEE_CHANGE_ROLE                       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_collaborate_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all'],
        ],
        self::TYPE_COACHEE_CHANGE_GOAL                       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_change_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_EVALUATION_START                          => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'start_evaluation',
            'icon_class'      => 'fa-paw',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_EVALUATION_FREEZE                         => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'fleeze_evaluation',
            'icon_class'      => 'fa-paw',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_EVALUATION_START_CAN_ONESELF              => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'start_can_oneself_evaluation',
            'icon_class'      => 'fa-paw',
            'groups'          => ['all'],
        ],
        self::TYPE_EVALUATION_CAN_AS_EVALUATOR               => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'start_can_evaluate_as_evaluator',
            'icon_class'      => 'fa-paw',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_EVALUATION_DONE_FINAL                     => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'final_evaluation_is_done',
            'icon_class'      => 'fa-paw',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_ACTION               => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_commented_on_my_action',
            'icon_class'      => 'fa-check-circle',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION     => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_commented_on_my_commented_action',
            'icon_class'      => 'fa-check-circle',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_FEED_CAN_SEE_ACTION                       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_action',
            'icon_class'      => 'fa-check-circle',
            'groups'          => ['all'],
        ],
        self::TYPE_USER_JOINED_TO_INVITED_TEAM               => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'user_joined_to_invited_team',
            'icon_class'      => 'fa-users',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MESSAGE                                   => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'feed_message',
            'icon_class'      => 'fa-paper-plane-o',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_SETUP_GUIDE                               => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'setup_guide',
            'icon_class'      => 'fa-book',
            'groups'          => ['all'],
        ],
        self::TYPE_COACHEE_WITHDRAW_APPROVAL                 => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            //TODO 現在、この通知用のカラムが存在しないため、ゴール作成の通知と同じカラム名にしておく。通知設定を細分化しなければ新たに用意する必要なし
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_APPROVAL_COMMENT                          => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-comment-o',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_COACHEE_EXCHANGE_TKR                      => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_TKR_EXCHANGED_BY_LEADER                   => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_EXCHANGED_LEADER                          => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_MY_GOAL_CHANGED_NEXT_TO_CURRENT_BY_LEADER => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_COACHEE_CHANGE_GOAL_NEXT_TO_CURRENT       => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => 'my_member_create_goal',
            'icon_class'      => 'fa-flag',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_CHANGED_TEAM_BASIC_SETTING                => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            // TODO: using start_evaluation notify setting because same as what to do.
            //       but it's not good. Should improve it's architecture.
            'field_prefix'    => 'start_evaluation',
            'icon_class'      => 'fa-users',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_CHANGED_TERM_SETTING                => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            // TODO: using start_evaluation notify setting because same as what to do.
            //       but it's not good. Should improve it's architecture.
            'field_prefix'    => 'start_evaluation',
            'icon_class'      => 'fa-users',
            'groups'          => ['all', 'primary'],
        ],
        self::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => '',
            'icon_class'      => 'fa-video-camera',
            'groups'          => ['all'],
            'force_notify'    => true,
        ],
        self::TYPE_TRANSCODE_FAILED => [
            'mail_template'   => "notify_basic",
            'field_real_name' => null,
            'field_prefix'    => '',
            'icon_class'      => 'fa-video-camera',
            'groups'          => ['all'],
            'force_notify'    => true,
        ],
    ];

    static public $TYPE_GROUP = [
        'all'     => null,
        'primary' => null,
        'none'    => null,
    ];

    /**
     * Return generator of notification type of user can set
     *
     * @return Generator
     *      self::TYPE_* => []
     */
    public static function getUserSettableNotifyTypeGroups(): Generator
    {
        foreach (static::$TYPE as $typeKey => $typeValue) {
            if (isset($typeValue['force_notify']) && is_bool($typeValue['force_notify'])) {
                continue;
            }
            if (is_string($typeValue['field_prefix'])) {
                yield $typeKey => $typeValue;
            }
        }
    }

    public function _setFieldRealName()
    {
        self::$TYPE_GROUP['all'] = __("All");
        self::$TYPE_GROUP['primary'] = __("Important ones");
        self::$TYPE_GROUP['none'] = __("Off");
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
        'email'                                         => [
            'isString' => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
        ],
        'mobile'                                        => [
            'isString' => [
                'rule'       => ['isString',],
                'allowEmpty' => true,
            ],
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
     * 指定タイプのアプリ、メール、モバイルの通知設定を返却
     * ユーザ指定は単数、複数の両方対応
     * 返却値は[uid=>['app'=>true,'email'=>true,'mobile'=>true],,,,]
     *
     * @param $user_ids
     * @param $type
     *
     * @return array
     */
    function getUserNotifySetting($user_ids, $type)
    {
        if (!is_array($user_ids)) {
            $user_ids = [$user_ids];
        }
        // email と mobile のデフォルトは「すべて」
        $default_data = [
            'app'    => true,
            'email'  => in_array('all', self::$TYPE[$type]['groups']),
            'mobile' => in_array('all', self::$TYPE[$type]['groups']),
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
        if (isset(self::$TYPE[$type]['force_notify']) && is_bool(self::$TYPE[$type]['force_notify'])) {
            return $this->createSettingOfForceNotify($user_ids, self::$TYPE[$type]['force_notify'], $default_data);
        }
        if (!empty($result)) {
            foreach ($result as $val) {
                // アプリ
                $res_data[$val['NotifySetting']['user_id']]['app'] =
                    $val['NotifySetting'][$field_prefix . '_app_flg'] ? true : false;
                // メール
                $res_data[$val['NotifySetting']['user_id']]['email'] =
                    $val['NotifySetting'][$field_prefix . '_email_flg'] ? true : false;
                // モバイル
                $res_data[$val['NotifySetting']['user_id']]['mobile'] =
                    $val['NotifySetting'][$field_prefix . '_mobile_flg'] ? true : false;
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

    private function createSettingOfForceNotify(array $userIds, bool $bool, array $defaultData): array
    {
        $r = [];
        foreach ($userIds as $userId) {
            $r[$userId] = $bool ? $defaultData : [
                'app'    => false,
                'email'  => false,
                'mobile' => false,
            ];
        }
        return $r;
    }

    /**
     * Notification のタイトルを返す
     * 戻り値はデフォルト HTML 形式なので注意。
     * $options['style'] = 'plain' を指定するとテキスト形式で返す。
     *
     * @param       $type
     * @param       $from_user_names
     * @param       $count_num
     * @param       $item_name
     * @param array $options
     *                 style: 'html' or 'plain'
     *
     * @return null|string
     */
    function getTitle($type, $from_user_names, $count_num, $item_name, $options = [])
    {
        $options = array_merge(
            [
                'style' => 'html',
            ], $options);
        $is_plain_mode = $options['style'] === 'plain';

        if ($item_name && !is_array($item_name)) {
            $item_name = json_decode($item_name, true);
        }
        $title = null;
        $user_text = null;
        //カウント数はユーザ名リストを引いた数
        if ($from_user_names) {
            $count_num -= count($from_user_names);
            if (!is_array($from_user_names)) {
                $from_user_names = [$from_user_names];
            }
            foreach ($from_user_names as $key => $name) {
                if ($key !== 0) {
                    $user_text .= __(",");
                }
                $user_text .= $name . ' ';
            }
        }
        $title = null;

        // getting goalName.
        $goalName = null;
        if(Hash::get($options,'goal_id')){
            $goal = $this->User->Goal->findById($options['goal_id']);
            $goalName = Hash::get($goal,'Goal.name');
        }

        switch ($type) {
            case self::TYPE_FEED_POST:
                // 共有先ユーザー名 + サークル名
                $targets = [];

                // 共有先に個人ユーザーが含まれている場合
                if (isset($options['share_user_list']) && $options['share_user_list']) {
                    $share_user_count = count($options['share_user_list']);

                    // 共有先ユーザーが自分１人のみの場合
                    if (isset($options['share_user_list'][$this->my_uid]) && $share_user_count == 1) {
                        $targets[] = __('You');
                    } // 自分以外の人が個人として共有されている場合はその人の名前を表示
                    else {
                        $user_name = "";
                        foreach ($options['share_user_list'] as $uid) {
                            if ($uid != $this->my_uid) {
                                $other_user = $this->User->findById($uid);
                                $user_name = $other_user['User']['display_username'];
                                break;
                            }
                        }
                        if ($share_user_count >= 2) {
                            $user_name .= __('Other %d members', $share_user_count - 1);
                        }
                        $targets[] = $user_name;
                    }
                }

                // サークルに共有されている場合
                if (isset($options['share_circle_list']) && !empty($options['share_circle_list'])) {
                    $circleMember = $this->User->CircleMember->isBelong(
                        $options['share_circle_list'],
                        $this->my_uid);

                    // 共有先サークルのメンバーの場合
                    if ($circleMember) {
                        $circle = $this->User->CircleMember->Circle->findById($circleMember['CircleMember']['circle_id']);
                    } // 共有先サークルのメンバーでない場合
                    else {
                        // サークルを正常に取得できないケースがあるので、
                        // 取得できるまで回す
                        foreach ($options['share_circle_list'] as $circle_id) {
                            if ($circle = $this->User->CircleMember->Circle->findById($circle_id)) {
                                break;
                            }
                        }
                    }

                    if (isset($circle['Circle']['name'])) {
                        $circle_name = $circle['Circle']['name'];
                        $circle_count = count($options['share_circle_list']);
                        if ($circle_count >= 2) {
                            $circle_name .= __('Other %d circles', $circle_count - 1);
                        }
                        $targets[] = $circle_name;
                    }
                }

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s%2$s</span> posted in <span class="notify-card-head-target">%3$s</span>.',
                        $user_text,
                        ($count_num > 0) ? __("and %s others", $count_num) : null,
                        implode(__(","), $targets));
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s%2$s</span> posted in <span class="notify-card-head-target">%3$s</span>.',
                        h($user_text),
                        ($count_num > 0) ? h(__("and %s others", $count_num)) : null,
                        h(implode(__(","), $targets)));
                }
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_POST:
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s%2$s</span> commented on <span class="notify-card-head-target">your </span>post.',
                        $user_text,
                        ($count_num > 0) ? __("and %s others", $count_num) : null);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s%2$s</span> commented on <span class="notify-card-head-target">your </span>post.',
                        h($user_text),
                        ($count_num > 0) ? h(__("and %s others", $count_num)) : null);
                }
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                // この通知で必要なオプション値
                //   - from_user_id: コメントを書いたユーザーのID
                //   - post_user_id: コメントが書かれた投稿の投稿者ID

                // 投稿者の表示名をセット
                // 自分の投稿へのコメントの場合は、表示名を「自分」にする
                $target_user_name = __("his/her");
                if ($options['from_user_id'] != $options['post_user_id']) {
                    $user = $this->User->findById($options['post_user_id']);
                    $target_user_name = $user['User']['display_username'];
                }
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s%2$s</span> also commented on <span class="notify-card-head-target">%3$s</span>\'s post.',
                        $user_text,
                        ($count_num > 0) ? __("and %s others", $count_num) : null,
                        $target_user_name);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s%2$s</span> also commented on <span class="notify-card-head-target">%3$s</span>\'s post.',
                        h($user_text),
                        ($count_num > 0) ? h(__("and %s others", $count_num)) : null,
                        h($target_user_name));
                }
                break;
            case self::TYPE_CIRCLE_USER_JOIN:
                if ($is_plain_mode) {
                    $title = __('<span class="notify-card-head-target">%1$s%2$s</span> joined the circle.',
                        $user_text,
                        ($count_num > 0) ? (__("and %s others", $count_num)) : null);
                } else {
                    $title = __('<span class="notify-card-head-target">%1$s%2$s</span> joined the circle.',
                        h($user_text),
                        ($count_num > 0) ? h(__("and %s others", $count_num)) : null);
                }
                break;
            case self::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                // ToDo - 大樹さん、すでにサークルのプライバシー設定変更はできなくなっていると思うので削除よろしくお願いします。
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span>がサークルのプライバシー設定を「<span class="notify-card-head-target">%2$s</span>」に変更しました。',
                        $user_text, $item_name[1]);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span>がサークルのプライバシー設定を「<span class="notify-card-head-target">%2$s</span>」に変更しました。',
                        h($user_text), h($item_name[1]));
                }
                break;
            case self::TYPE_CIRCLE_ADD_USER:
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> added <span class="notify-card-head-target">you </span>to the circle.',
                        $user_text);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> added <span class="notify-card-head-target">you </span>to the circle.',
                        h($user_text));
                }
                break;
            case self::TYPE_MY_GOAL_FOLLOW:
                // この通知で必要なオプション値
                //   - goal_id: フォローしたゴールID
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has followed <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has followed <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_MY_GOAL_COLLABORATE:
                // この通知で必要なオプション値
                //   - goal_id: コラボしたゴールID
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has collaborate with <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has collaborate with <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_MY_GOAL_CHANGED_BY_LEADER:
                // この通知で必要なオプション値
                //   - goal_id: 内容を変更したゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed information on <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed information on <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_MEMBER_CHANGE_KR:
                // この通知で必要なオプション値
                //   - kr_id: 内容を変更したゴールID
                $kr = $this->User->Goal->KeyResult->getById($options['kr_id']);
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed information on <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $kr['name']);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed information on <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($kr['name']));
                }
                break;
            case self::TYPE_MY_GOAL_TARGET_FOR_EVALUATION:
                // この通知で必要なオプション値
                //   - goal_id: 評価対象にしたゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has evaluated <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has evaluated <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE:
                // この通知で必要なオプション値
                //   - goal_id: 修正依頼をしたゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> requested <span class="notify-card-head-target">%2$s</span> to modify.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> requested <span class="notify-card-head-target">%2$s</span> to modify.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION:
                // この通知で必要なオプション値
                //   - goal_id: 評価対象外にしたゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has not evaluated <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has not evaluated <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_COACHEE_CREATE_GOAL:
                // この通知で必要なオプション値
                //   - goal_id: 新しく作成したゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> created <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> created <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_COACHEE_COLLABORATE_GOAL:
                // この通知で必要なオプション値
                //   - goal_id: コラボしたゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has collaborate with <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has collaborate with <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_COACHEE_CHANGE_ROLE:
                // この通知で必要なオプション値
                //   - goal_id: コラボしたゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has reapplied <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has reapplied <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_COACHEE_CHANGE_GOAL:
                // この通知で必要なオプション値
                //   - goal_id: 内容を修正したゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has reapplied <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has reapplied <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_COACHEE_WITHDRAW_APPROVAL:
                // この通知で必要なオプション値
                //   - goal_id: 評価対象にしたゴールID

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has withdrawn <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has withdrawn <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_EVALUATION_START:
                $title = __('Begin evaluation term.');
                break;
            case self::TYPE_EVALUATION_FREEZE:
                $title = __('Fix evaluation.');
                break;
            case self::TYPE_EVALUATION_START_CAN_ONESELF:
                $title = __('Evaluate yourself.');
                break;
            case self::TYPE_EVALUATION_CAN_AS_EVALUATOR:
                $title = __('Set the Evaluatees score.');
                break;
            case self::TYPE_EVALUATION_DONE_FINAL:
                $title = __('Last evaluator finished evaluation.');
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_ACTION:
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> commented on <span class="notify-card-head-target">your </span>action.',
                        $user_text);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> commented on <span class="notify-card-head-target">your </span>action.',
                        h($user_text));
                }
                break;
            case self::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION:
                // この通知で必要なオプション値
                //   - from_user_id: コメントを書いたユーザーのID
                //   - post_user_id: コメントが書かれた投稿の投稿者ID

                // 投稿者の表示名をセット
                // 自分の投稿へのコメントの場合は、表示名を「自分」にする
                $target_user_name = __("his/her");
                if ($options['from_user_id'] != $options['post_user_id']) {
                    $user = $this->User->findById($options['post_user_id']);
                    $target_user_name = $user['User']['display_username'];
                }
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> also commented on <span class="notify-card-head-target">%2$s</span>\'s action',
                        $user_text,
                        $target_user_name);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> also commented on <span class="notify-card-head-target">%2$s</span>\'s action',
                        h($user_text),
                        h($target_user_name));
                }

                break;
            case self::TYPE_FEED_CAN_SEE_ACTION:
                // この通知で必要なオプション値
                //   - goal_id: アクションしたゴール

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> added an action on<span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> added an action on<span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }

                break;
            case self::TYPE_USER_JOINED_TO_INVITED_TEAM:
                if ($is_plain_mode) {
                    $title = __('<span class="notify-card-head-target">%1$s</span> joined this team.', $user_text);
                } else {
                    $title = __('<span class="notify-card-head-target">%1$s</span> joined this team.', h($user_text));
                }
                break;
            case self::TYPE_MESSAGE:
                if ($is_plain_mode) {
                    $title = __('<span class="notify-card-head-target">%1$s%2$s</span>',
                        $user_text,
                        ($count_num > 0) ? __(" +%s", $count_num) : null);
                } else {
                    $title = __('<span class="notify-card-head-target">%1$s%2$s</span>',
                        h($user_text),
                        ($count_num > 0) ? h(__(" +%s", $count_num)) : null);
                }
                break;
            case self::TYPE_APPROVAL_COMMENT:

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> commented on <span class="notify-card-head-target">%2$s</span>.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> commented on <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_COACHEE_EXCHANGE_TKR:
            case self::TYPE_TKR_EXCHANGED_BY_LEADER:
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> changed Top KR to another KR.',
                        $user_text);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> changed Top KR to another KR.',
                        h($user_text));
                }
                break;
            case self::TYPE_EXCHANGED_LEADER:
                $goalMember = $this->User->Goal->GoalMember->getActiveLeader($options['goal_id']);
                $leaderName = $goalMember['User']['display_username'];
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed the leader to <span class="notify-card-head-target">%2$s</span>.',
                        $user_text, $leaderName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed the leader to <span class="notify-card-head-target">%2$s</span>.',
                        h($user_text), h($leaderName));
                }
                break;
            case self::TYPE_COACHEE_CHANGE_GOAL_NEXT_TO_CURRENT:
            case self::TYPE_MY_GOAL_CHANGED_NEXT_TO_CURRENT_BY_LEADER:

                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed the term of <span class="notify-card-head-target">%2$s</span> from the next term to this term.',
                        $user_text,
                        $goalName);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> has changed the term of <span class="notify-card-head-target">%2$s</span> from the next term to this term.',
                        h($user_text),
                        h($goalName));
                }
                break;
            case self::TYPE_CHANGED_TEAM_BASIC_SETTING:
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> changed team basic setting.',
                        $user_text);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> changed team basic setting.',
                        h($user_text));
                }
                break;
            case self::TYPE_CHANGED_TERM_SETTING:
                if ($is_plain_mode) {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> changed term setting.',
                        $user_text);
                } else {
                    $title = __(
                        '<span class="notify-card-head-target">%1$s</span> changed term setting.',
                        h($user_text));
                }
                break;
            case self::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED:
                $title = __('Video processing completed and published post.');
                break;
            case self::TYPE_TRANSCODE_FAILED:
                $title = __('Video processing failed.');
                break;
        }

        if ($options['style'] == 'plain') {
            $title = strip_tags($title);
        }
        return $title;
    }

    /**
     * 通知先とグループに応じて DB 登録用の キー/値 の配列を作成して返す
     *
     * @param string $notify_target 通知先 ('app' or 'email' or 'mobile')
     * @param string $type_group    通知タイプのグループ ('all' or 'primary' or 'none')
     *
     * @return array
     */
    public function getSettingValues($notify_target, $type_group)
    {
        $values = [];
        foreach (NotifySetting::getUserSettableNotifyTypeGroups() as $k => $v) {
            $values["{$v['field_prefix']}_{$notify_target}_flg"] = in_array($type_group, $v['groups']);
        }
        return $values;
    }

    public function getMySettings()
    {
        $model = $this;
        $notify_setting = Cache::remember($this->getCacheKey(CACHE_KEY_MY_NOTIFY_SETTING, true, null, false),
            function () use ($model) {
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $res = $model->findByUserId($model->my_uid);
                $res = Hash::extract($res, 'NotifySetting');
                if (!empty($res)) {
                    return $res;
                }
                $schema = $model->schema();
                foreach ($schema as $k => $v) {
                    $res[$k] = $v['default'];
                }
                return $res;
            }, 'user_data');

        return $notify_setting;
    }

    function getFlagPrefixByType($type)
    {
        if (!isset(self::$TYPE[$type]['field_prefix'])) {
            return null;
        }
        return self::$TYPE[$type]['field_prefix'];
    }
}
