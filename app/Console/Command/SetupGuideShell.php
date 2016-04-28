<?php
App::uses('Controller', 'Core');
App::uses('AppController', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('SessionComponent', 'Controller/Component');
App::uses('NotifyBizComponent', 'Controller/Component');

/**
 * セットアップガイドバッチ
 * 対象者にセットアップを促す通知を送信する。
 * # 方法
 *  - mail
 *  - push notify
 *  - feed
 * # 通知タイミング
 *  - 以下で定義
 *  app/Config/extra_defines.php
 *   SETUP_GUIDE_NOTIFY_DAYSに会員登録してから送信するまでの日数を,区切りで指定。
 *   通知対象者が、バッチ起動時に、指定された日数、前回のセットアップガイド項目完了から経過している場合、通知を行う
 *       -> 前回のセットアップガイド項目完了 = redisの該当アイテム更新日時
 * ex) SETUP_GUIDE_NOTIFY_DAYS=2,5,10
 *      会員登録以降、セットアップガイド完了まで、前回のセットアップガイドの項目完了から、
 *      2日目、5日目、10日目になってもセットアップガイドが完了していなければ通知する
 * TBD:タイムゾーンを意識するか？しないか？ する場合、バッチ起動時にタイムゾーンも指定し、該当するタイムゾーンのユーザーのみ対象にする
 * # 通知対象ユーザー
 *  バッチ起動タイミングで、セットアップガイドが完了していないユーザー
 *  users.setup_complete_flg != true
 * タイムゾーンが指定されている場合には、指定されたタイムゾーンのユーザー
 *  users.timezone = '指定されたタイムゾーン' (数字 JSTの場合:9)
 * チームIDが指定されている場合には、指定されたチームのみ対象とする
 * # 通知内容
 * 以下の優先順位で残ったセットアップガイドに関する通知を行う
 * - プロフィール
 * - APP
 * - ゴール
 * - サークル
 * - POST
 * - Action
 * # Usage
 * Console/cake setupGuide -t timezone -o team_id
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
        echo "START!\n";

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
            'timezone' => ['short' => 't', 'help' => 'タイムゾーン', 'required' => false,],
            'team_id'  => ['short' => 'o', 'help' => 'チームID', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    /**
     * 1.セットアップ通知対象のユーザーを取得する
     * 2.ユーザーごとに、送信すべき通知の種類を確定させる
     * 3.通知する
     */
    public function main()
    {
        echo "MAIN\n";

        //FURU:NOTIFY:1,63,,,11,1
//        $to_user_list = null;
//        if (isset($this->params['user_list'])) {
//            $to_user_list = json_decode(base64_decode($this->params['user_list']), true);
//        }

        $team_id = viaIsSet($this->params['team_id']);
        $timezone = viaIsSet($this->params['timezone']);

        $to_user_list = $this->User->getUsersSetupNotCompleted($team_id);

        foreach ($to_user_list as $to_user) {
            echo $to_user['User']['id'] . "\n";
            $user_id = $to_user['User']['id'];
            $status = $this->AppController->getStatusWithRedisSave($user_id);
            echo print_r($status, true) . "\n";
        }

//        $this->NotifyBiz->sendNotify(1,     // 1
//                                     66,    // 63
//                                     null,  // null
//                                     null,  // null
//                                     3,     // 11
//                                     1      // 1
//        );
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
        echo "USE\n";
        return 'Usage: cake insight YYYY-MM-DD time_offset';
    }
}
