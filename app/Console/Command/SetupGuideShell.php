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

        $sessionId = viaIsSet($this->params['session_id']);
        $baseUrl = viaIsSet($this->params['base_url']);

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
        $team_id = viaIsSet($this->params['team_id']);
        $force = viaIsSet($this->params['force']);

        $to_user_list = $this->User->getUsersSetupNotCompleted($team_id);

        foreach ($to_user_list as $to_user) {
            if (!$force && !$this->_isNotifySendTime($to_user['User']['timezone'])) {
                continue;
            }
            $user_id = $to_user['User']['id'];
            if ($force || $this->_isNotifyDay($user_id)) {
                $this->_sendNotify($user_id);
            }
        }

        return;
    }

    /**
     * 送信すべきnotifyの種類を確定し送付する
     * @param $user_id
     */
    function _sendNotify($user_id)
    {
        $status = $this->AppController->getStatusWithRedisSave($user_id);
        $target_key = 0;;
        foreach ($status as $key => $value) {
            if (empty($value)) {
                $target_key = $key;
                break;
            }

        }
        $notify_type = NotifySetting::TYPE_SETUP_PROFILE;

        switch ($target_key) {
            case 1:
                $notify_type = NotifySetting::TYPE_SETUP_PROFILE;
                break;
            case 2:
                $notify_type = NotifySetting::TYPE_SETUP_APP;
                break;
            case 3:
                $notify_type = NotifySetting::TYPE_SETUP_GOAL;
                break;
            case 4:
                $notify_type = NotifySetting::TYPE_SETUP_ACTION;
                break;
            case 5:
                $notify_type = NotifySetting::TYPE_SETUP_CIRCLE;
                break;
            case 6:
                $notify_type = NotifySetting::TYPE_SETUP_POST;
                break;
        }
        $this->NotifyBiz->sendNotify($notify_type,
                                     null,
                                     null,
                                     null,
                                     $user_id,
                                     null
        );
    }

    /**
     * 現在時間が最終セットアップ更新時間からSETUP_GUIDE_NOTIFY_DAYSの範囲内であればtrue
     *
     * @param $user_id
     *
     * @return bool
     */
    function _isNotifyDay($user_id)
    {
        $status = $this->AppController->getStatusWithRedisSave($user_id);
        $notify_days = explode(",", SETUP_GUIDE_NOTIFY_DAYS);
        $now = time();
        foreach ($notify_days as $notify_day) {
            $setup_last_update_time = viaIsSet($status['setup_last_update_time']);
            $from_notify_time = $setup_last_update_time + ($notify_day * 24 * 60 * 60);
            $to_notify_time = $setup_last_update_time + (($notify_day + 1) * 24 * 60 * 60);

            if (empty($setup_last_update_time) || ($from_notify_time <= $now && $to_notify_time > $now)) {
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
    function _isNotifySendTime($timezone)
    {
        $utcCurrentHour = gmdate("H");
        $now = intval($utcCurrentHour) + $timezone;
        if ($now < 0) {
            $now = 24 + $now;
        }
        if (SETUP_GUIDE_NOTIFY_HOUR == $now) {
            return true;
        }
        return false;
    }

    /**
     * get setup-guide notify target user list.
     *
     * @return null
     */
    function _getToUserList()
    {

        return null;
    }

    protected function _usageString()
    {
        return 'Usage: cake insight YYYY-MM-DD time_offset';
    }
}
