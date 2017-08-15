<?php
App::uses('Controller', 'Core');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('SessionComponent', 'Controller/Component');
App::uses('NotifyBizComponent', 'Controller/Component');

/**
 * # セットアップガイドバッチ
 * 対象者にセットアップを促す通知を送信する。
 * ## 通知方法
 * - mail
 * - push notify
 * ## 通知タイミング
 * - 以下で定義
 *    app/Config/extra_defines.php
 *   SETUP_GUIDE_NOTIFY_DAYS
 *    会員登録,または前回のセットアップガイド項目完了から送信するまでの日数を,区切りで指定。
 *      ex) SETUP_GUIDE_NOTIFY_DAYS=2,5,10
 *   SETUP_GUIDE_NOTIFY_HOUR
 *    通知される時間を指定。対象ユーザーのTIMEZONE上で該当時間の場合のみ送信する。
 *      ex) SETUP_GUIDE_HOUR=11
 *    ※ バッチは1時間に1度起動される想定
 * ## 通知対象ユーザー
 *  バッチ起動タイミングで、セットアップガイドが完了していないユーザー
 *  users.setup_complete_flg != true
 * ## 通知内容
 * 以下の優先順位で残ったセットアップガイドに関する通知を行う
 * - プロフィール
 * - APP
 * - ゴール
 * - サークル
 * - POST
 * - Action
 * ## その他
 * o teamid :チームIDが指定されている場合には、指定されたチームのみ対象とする
 * f 強制。時間に関係なく即座に通知を行う
 * ## Usage
 * Console/cake setupGuide -o team_id -f true
 *
 * @property Team          $Team
 * @property TeamMember    $TeamMember
 * @property MemberGroup   $MemberGroup
 * @property Circle        $Circle
 * @property CircleMember  $CircleMember
 * @property TeamInsight   $TeamInsight
 * @property GroupInsight  $GroupInsight
 * @property CircleInsight $CircleInsight
 * @property GlRedis       $GlRedis
 * @property AccessUser    $AccessUser
 */
class SetupGuideShell extends AppShell
{
    public $uses = array(
        'Team',
        'TeamMember',
        'MemberGroup',
        'Circle',
        'CircleMember',
        'TeamInsight',
        'GroupInsight',
        'CircleInsight',
        'AccessUser',
        'GlRedis',
        'SendMail',
        'User',
    );

    public $components;
    /**
     * @var AppController
     */
    public $AppController;

    public function startup()
    {
        parent::startup();
        $sessionId = Hash::get($this->params, 'session_id');
        $baseUrl = Hash::get($this->params, 'base_url');

        if ($sessionId) {
            CakeSession::id($sessionId);
            CakeSession::start();
        }
        if ($baseUrl) {
            Router::fullBaseUrl($baseUrl);
        }
        $this->components = new ComponentCollection();
        $this->AppController = new AppController();
        $this->NotifyBiz = new NotifyBizComponent($this->components);
        $this->components->disable('Security');
        $this->NotifyBiz->startup($this->AppController);
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            'force'   => ['short' => 'f', 'help' => '強制', 'required' => false,],
            'team_id' => ['short' => 'o', 'help' => 'チームID', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * - セットアップが完了していないユーザーを取得する
     *  ※ team_idが指定されている場合は、指定されているteamのみ
     * - ユーザー毎にループ
     * - もしforceが指定されていなければ、ユーザーのtimezoneを取得し、ユーザーにとっての現在時間が
     *   SETUP_GUIDE_NOTIFY_HOURで指定された時間と一致していなければスキップする
     * - セットアップの最終更新時間から現在までの日数が、SETUP_GUIDE_NOTIFY_DAYSに指定された日数と一致しなければスキップする
     * - ユーザーごとに、優先順位に従って送信すべき通知の種類を確定させる
     * - 通知する
     */
    public function main()
    {
        $team_id = Hash::get($this->params, 'team_id');
        $force = Hash::get($this->params, 'force');

        $to_user_list = $this->User->getUsersSetupNotCompleted($team_id);
        $utcCurrentHour = gmdate("H");

        foreach ($to_user_list as $to_user) {
            $user_id = $to_user['User']['id'];
            $user_language = $to_user['User']['language'];
            $user_time_zone = $to_user['User']['timezone'];
            $user_singup_time = $to_user['User']['created'];

            $now = intval($utcCurrentHour) + $user_time_zone;
            $is_timing_to_send = $this->_isNotifyDay($user_id, $user_singup_time) && $this->_isNotifySendTime($now);
            if ($force || $is_timing_to_send) {
                Configure::write('Config.language', $user_language);
                $this->_sendNotify($user_id);
            }
        }

        return;
    }

    /**
     * 送信すべきnotifyの種類を確定し送付する
     *
     * @param $user_id
     */
    function _sendNotify($user_id)
    {
        $status = $this->AppController->getStatusWithRedisSave($user_id);
        $target_key = 0;
        foreach ($status as $key => $value) {
            if (empty($value)) {
                $target_key = $key;
                break;
            }
        }
        $notify_data = $this->_getNotifyDataByTargetKey($target_key);

        $this->NotifyBiz->sendSetupNotify(
            $user_id,
            $messages = $notify_data['messages'],
            $urls = $notify_data['urls']
        );
    }

    /**
     * 現在時間が最終セットアップ更新時間からSETUP_GUIDE_NOTIFY_DAYSの範囲内であればtrue
     *
     * @param $user_id
     *
     * @return bool
     */
    function _isNotifyDay($user_id, $user_singup_time)
    {
        $status = $this->AppController->getAllSetupDataFromRedis($user_id);
        if($status === null) {
            return false;
        }
        $setup_update_time = Hash::get($status, 'setup_last_update_time');
        // remove last update time for calc rest count
        unset($status[GlRedis::FIELD_SETUP_LAST_UPDATE_TIME]);

        // define base time for notify
        $setup_rest_count = $this->AppController->calcSetupRestCount($status);
        if ($user_do_nothing_for_setup = $setup_rest_count == count(User::$TYPE_SETUP_GUIDE)) {
            $base_update_time = $user_singup_time;
        } else {
            $base_update_time = $setup_update_time;
        }

        // check can notify compare with base time
        $notify_days = explode(",", SETUP_GUIDE_NOTIFY_DAYS);
        $now = time();
        foreach ($notify_days as $notify_day) {
            $from_notify_time = $base_update_time + ($notify_day * 24 * 60 * 60);
            $to_notify_time = $base_update_time + (($notify_day + 1) * 24 * 60 * 60);

            if ($from_notify_time <= $now && $to_notify_time > $now) {
                return true;
            }
        }
        return false;
    }

    /**
     * 今がtimezone(+9とか)を加味してSETUP_GUIDE_NOTIFY_HOURに指定されているpush通知を送る時間であればtrueを返す
     *
     * @param $timezone
     *
     * @return bool
     */
    function _isNotifySendTime($now)
    {
        if ($now < 0) {
            $now = 24 + $now;
        }
        if (SETUP_GUIDE_NOTIFY_HOUR == $now) {
            return true;
        }
        return false;
    }

    function _getNotifyDataByTargetKey($target_key)
    {
        switch ($target_key) {
            case 1:
                $message = __("Please input your profile to Goalous.");
                $url = "/setup/profile/image";
                break;
            case 2:
                $message = __("Please install the Goalous mobile application and login.");
                $url = "/setup/app/image";
                break;
            case 3:
                $message = __("Please create your Goal.");
                $url = "/setup/goal/image";
                break;
            case 4:
                $message = __("Please action on Goalous.");
                $url = "/setup/action/image";
                break;
            case 5:
                $message = __("Please join a circle.");
                $url = "/setup/circle/image";
                break;
            case 6:
                $message = __("Please post on Goalous.");
                $url = "/setup/post/image";
                break;
            default:
                return;
        }

        $url = SETUP_GUIDE_NOTIFY_URL . $url;
        $mail_url = $url . '/?from=email';
        $push_url = $url . '/?from=pushnotifi';

        $mail_message = $push_message = $message;
        $mail_message .= "\n\n";
        $mail_message .= __('Click the below link to setup Goalous.');
        $mail_message .= "\n";
        $mail_message .= $mail_url;

        return [
            'messages' => ['mail' => $mail_message, 'push' => $push_message],
            'urls'     => ['mail' => $mail_url, 'push' => $push_url]
        ];
    }

    protected function _usageString()
    {
        return 'Usage: cake insight YYYY-MM-DD time_offset';
    }
}
