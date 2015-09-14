<?php
App::uses('NotifySetting', 'Model');

/**
 * NotifySetting Test Case
 *
 * @property NotifySetting $NotifySetting
 */
class NotifySettingTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.notify_setting',
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token',
        'app.local_name',
        'app.goal',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->NotifySetting = ClassRegistry::init('NotifySetting');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NotifySetting);

        parent::tearDown();
    }

    function testIsOnNotify1()
    {
        $uid = 1000000;
        $this->NotifySetting->my_uid = 1;
        $res = $this->NotifySetting->getAppEmailNotifySetting($uid, NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid => ['app' => true, 'email' => false]
        ];
        $this->assertEquals($expected, $res, "通知設定なし");
        $data = ['feed_post_app_flg' => false, 'feed_post_email_flg' => false, 'user_id' => $uid];
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->getAppEmailNotifySetting($uid, NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid => ['app' => false, 'email' => false]
        ];
        $this->assertEquals($expected, $res, "通知設定あり、off");
    }

    function testIsOnNotify2()
    {
        $uid = 1000000;
        $uid2 = 9999999;

        $data = ['user_id' => $uid, 'feed_post_app_flg' => true, 'feed_post_email_flg' => true];
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->getAppEmailNotifySetting($uid, NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid => ['app' => true, 'email' => true]
        ];
        $this->assertEquals($expected, $res, "通知設定あり、on");
        $res = $this->NotifySetting->getAppEmailNotifySetting([$uid, $uid2], NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid  => ['app' => true, 'email' => true],
            $uid2 => ['app' => true, 'email' => false]
        ];
        $this->assertEquals($expected, $res, "通知設定ありなし混在。複数ユーザ");

    }
    
    function testGetTitle()
    {
        $from_user_names = ['aaa', 'bbb'];
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);
        
        // 特殊な処理の入らないタイトル
        $types = [
            NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST,
            NotifySetting::TYPE_CIRCLE_USER_JOIN,
            NotifySetting::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING,
            NotifySetting::TYPE_CIRCLE_ADD_USER,
            NotifySetting::TYPE_EVALUATION_START,
            NotifySetting::TYPE_EVALUATION_FREEZE,
            NotifySetting::TYPE_EVALUATION_START_CAN_ONESELF,
            NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR,
            NotifySetting::TYPE_EVALUATION_DONE_FINAL,
            NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION,
            NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM,
            NotifySetting::TYPE_FEED_MESSAGE,
        ];
        foreach ($types as $type) {
            $title = $this->NotifySetting->getTitle($type, $from_user_names, $count_num, $item_name);
            $this->assertNotEmpty($title);
            // from_user_name が配列以外の時
            $title = $this->NotifySetting->getTitle($type, 'aaa', $count_num, $item_name);
            $this->assertNotEmpty($title);
        }
    }

    function testGetTileComment() {
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);
        
        // TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST と TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION は
        // post_user_id、from_user_id が必須
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST, 'aaa', $count_num,
                                       $item_name,
                                       [
                                           'from_user_id' => 1,
                                           'post_user_id' => 2,
                                       ]);
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION, 'aaa', $count_num,
                                       $item_name,
                                       [
                                           'from_user_id' => 1,
                                           'post_user_id' => 2,
                                       ]);
    }
    
    function testGetTitleFeedPost()
    {
        $from_user_names = ['aaa', 'bbb'];
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);

        $this->NotifySetting->my_uid = 1;
        $this->NotifySetting->current_team_id = 1;
        $this->NotifySetting->User->CircleMember->my_uid = 1;
        $this->NotifySetting->User->CircleMember->current_team_id = 1;

        // 個人共有 複数
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list' => [1 => 1, 2 => 2],
        ]);

        // 個人共有 自分のみ
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list' => [1 => 1],
        ]);

        // 個人 + サークル
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'   => [1 => 1, 2 => 2],
            'share_circle_list' => [1 => 1, 2 => 2],
        ]);

        // サークル共有
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'   => [1 => 1, 2 => 2],
            'share_circle_list' => [1 => 1, 2 => 2, 3 => 3],
        ]);

        // サークル共有 サークルメンバーでない場合
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'   => [1 => 1, 2 => 2],
            'share_circle_list' => [5 => 5],
        ]);
    }

    function testGetTitlePlain()
    {
        $from_user_names = ['aaa', 'bbb'];
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);

        // HTML入り
        $html = $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_MESSAGE, $from_user_names, $count_num,
                                               $item_name);
        $plain = $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_MESSAGE, $from_user_names, $count_num,
                                                $item_name, [
                'style' => 'plain'
            ]);
        $this->assertNotEquals($html, $plain);
    }


    function testGetTitleRelateGoal()
    {
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);
        
        $types = [
            NotifySetting::TYPE_MY_GOAL_FOLLOW,
            NotifySetting::TYPE_MY_GOAL_COLLABORATE,
            NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER,
            NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION,
            NotifySetting::TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE,
            NotifySetting::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION,
            NotifySetting::TYPE_MY_MEMBER_CREATE_GOAL,
            NotifySetting::TYPE_MY_MEMBER_COLLABORATE_GOAL,
            NotifySetting::TYPE_MY_MEMBER_CHANGE_GOAL,
            NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
        ];
        foreach ($types as $type) {
            $title = $this->NotifySetting->getTitle($type, 'aaa', $count_num, $item_name, [
                'goal_id' => 1,
            ]);
            $this->assertNotEmpty($title);
        }
    }
}
