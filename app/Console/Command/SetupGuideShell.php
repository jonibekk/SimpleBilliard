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
 *
 * # 通知タイミング
 *  - 以下で定義
 *  app/Config/extra_defines.php
 *   SETUP_GUIDE_NOTIFY_DAYSに会員登録してから送信するまでの日数を,区切りで指定。
 *   通知対象者が、バッチ起動時に、指定された日数、会員登録から経過している場合、通知を行う
 *
 * ex) SETUP_GUIDE_NOTIFY_DAYS=2,5,10
 *      会員登録してから、2日目、5日目、10日目になってもセットアップガイドが完了していなければ通知する
 *
 * TBD:タイムゾーンを意識するか？しないか？ する場合、バッチ起動時にタイムゾーンも指定し、該当するタイムゾーンのユーザーのみ対象にする
 *
 * # 通知対象ユーザー
 *  バッチ起動タイミングで、セットアップガイドが完了していないユーザー
 *  users.setup_complete_flg != true
 *
 * タイムゾーンが指定されている場合には、指定されたタイムゾーンのユーザー
 *  users.timezone = '指定されたタイムゾーン' (数字 JSTの場合:9)
 *
 * チームIDが指定されている場合には、指定されたチームのみ対象とする
 *
 * # 通知内容
 *
 *
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
            'team_id'      => ['short' => 'o', 'help' => 'チームID', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        echo "MAIN\n";

        //FURU:NOTIFY:1,63,,,11,1
//        $to_user_list = null;
//        if (isset($this->params['user_list'])) {
//            $to_user_list = json_decode(base64_decode($this->params['user_list']), true);
//        }

        $team_id = viaIsSet($this->params['team_id']);
        echo("team_id:".$team_id."\n");

        $timezone = viaIsSet($this->params['timezone']);
        echo("TIMEZONE:".$timezone);
        $to_user_list = $this->User->getUsersSetupNotCompleted($team_id);
        echo("USER:".print_r($to_user_list,true)."\n");
        echo("USER:".count($to_user_list)."\n");
        $this->NotifyBiz->sendNotify(1,     // 1
                                     66,    // 63
                                     null,  // null
                                     null,  // null
                                     3,     // 11
                                     1      // 1
        );
    }

    /**
     * get setup-guide notify target user list.
     * @return null
     */
    function _getToUserList(){

        return null;
    }

    protected function _usageString()
    {
        echo "USE\n";
        return 'Usage: cake insight YYYY-MM-DD time_offset';
    }
}
