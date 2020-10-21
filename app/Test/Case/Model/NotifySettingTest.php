<?php App::uses('GoalousTestCase', 'Test');
App::uses('NotifySetting', 'Model');

/**
 * NotifySetting Test Case
 *
 * @property NotifySetting $NotifySetting
 */
class NotifySettingTest extends GoalousTestCase
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
        'app.circle',
        'app.circle_member',
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
        $res = $this->NotifySetting->getUserNotifySetting($uid, NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid => [
                'app'    => true,
                'email'  => in_array('all', NotifySetting::$TYPE[NotifySetting::TYPE_FEED_POST]['groups']),
                'mobile' => in_array('all', NotifySetting::$TYPE[NotifySetting::TYPE_FEED_POST]['groups']),
                'desktop' => in_array('all', NotifySetting::$TYPE[NotifySetting::TYPE_FEED_POST]['groups'])
            ]
        ];
        $this->assertEquals($expected, $res, "通知設定なし");
        $res = $this->NotifySetting->getUserNotifySetting($uid, NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST);
        $expected = [
            $uid => [
                'app'    => true,
                'email'  => in_array('all',
                    NotifySetting::$TYPE[NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST]['groups']),
                'mobile' => in_array('all',
                    NotifySetting::$TYPE[NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST]['groups']),
                'desktop' => in_array('all',
                    NotifySetting::$TYPE[NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST]['groups'])
            ]
        ];
        $this->assertEquals($expected, $res, "通知設定なし 2");
        $data = [
            'feed_post_app_flg'    => false,
            'feed_post_email_flg'  => false,
            'feed_post_mobile_flg' => true,
            'user_id'              => $uid
        ];
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->getUserNotifySetting($uid, NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid => ['app' => false, 'email' => false, 'mobile' => true, 'desktop' => true]
        ];
        $this->assertEquals($expected, $res, "通知設定あり、off");
    }

    function testIsOnNotify2()
    {
        $uid = 1000000;
        $uid2 = 9999999;

        $data = [
            'user_id'              => $uid,
            'feed_post_app_flg'    => true,
            'feed_post_email_flg'  => true,
            'feed_post_mobile_flg' => false
        ];
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->getUserNotifySetting($uid, NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid => ['app' => true, 'email' => true, 'mobile' => false, 'desktop' => true]
        ];
        $this->assertEquals($expected, $res, "通知設定あり、on");
        $res = $this->NotifySetting->getUserNotifySetting([$uid, $uid2], NotifySetting::TYPE_FEED_POST);
        $expected = [
            $uid  => ['app' => true, 'email' => true, 'mobile' => false, 'desktop' => true],
            $uid2 => [
                'app'    => true,
                'email'  => in_array('all', NotifySetting::$TYPE[NotifySetting::TYPE_FEED_POST]['groups']),
                'mobile' => in_array('all', NotifySetting::$TYPE[NotifySetting::TYPE_FEED_POST]['groups']),
                'desktop' => in_array('all', NotifySetting::$TYPE[NotifySetting::TYPE_FEED_POST]['groups'])
            ]
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
            NotifySetting::TYPE_MESSAGE,
        ];
        foreach ($types as $type) {
            $title = $this->NotifySetting->getTitle($type, $from_user_names, $count_num, $item_name);
            $this->assertNotEmpty($title);
            // from_user_name が配列以外の時
            $title = $this->NotifySetting->getTitle($type, 'aaa', $count_num, $item_name);
            $this->assertNotEmpty($title);
        }
    }

    function testGetTileComment()
    {
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

        // サークル共有 サークルメンバーでなく、存在しないサークルがある場合
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_circle_list' => [5 => 5, 1000 => 1000],
        ]);

        // サークル共有 サークルメンバーでなく、存在しないサークルしかない場合
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_circle_list' => [1000 => 1000, 1001 => 1001],
        ]);

    }

    function testGetTitlePlain()
    {
        $from_user_names = ['aaa', 'bbb'];
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);

        // HTML入り
        $html = $this->NotifySetting->getTitle(NotifySetting::TYPE_MESSAGE, $from_user_names, $count_num,
            $item_name);
        $plain = $this->NotifySetting->getTitle(NotifySetting::TYPE_MESSAGE, $from_user_names, $count_num,
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
            NotifySetting::TYPE_COACHEE_CREATE_GOAL,
            NotifySetting::TYPE_COACHEE_COLLABORATE_GOAL,
            NotifySetting::TYPE_COACHEE_CHANGE_GOAL,
            NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
        ];
        foreach ($types as $type) {
            $title = $this->NotifySetting->getTitle($type, 'aaa', $count_num, $item_name, [
                'goal_id' => 1,
            ]);
            $this->assertNotEmpty($title);
        }
    }

    function testGetSettingValues()
    {
        $settings = $this->NotifySetting->getSettingValues('app', 'all');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_app_flg$/', $k));
            $this->assertTrue($v);
        }
        $settings = $this->NotifySetting->getSettingValues('email', 'all');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_email_flg$/', $k));
            $this->assertTrue($v);
        }
        $settings = $this->NotifySetting->getSettingValues('mobile', 'all');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_mobile_flg$/', $k));
            $this->assertTrue($v);
        }

        $primary_data = [];
        foreach (NotifySetting::$TYPE as $k => $v) {
            $is_primary = in_array('primary', $v['groups']);
            $primary_data[$v['field_prefix'] . '_app_flg'] = $is_primary;
            $primary_data[$v['field_prefix'] . '_email_flg'] = $is_primary;
            $primary_data[$v['field_prefix'] . '_mobile_flg'] = $is_primary;
        }

        $settings = $this->NotifySetting->getSettingValues('app', 'primary');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_app_flg$/', $k));
            $this->assertEquals($primary_data[$k], $v);
        }

        $settings = $this->NotifySetting->getSettingValues('email', 'primary');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_email_flg$/', $k));
            $this->assertEquals($primary_data[$k], $v);
        }

        $settings = $this->NotifySetting->getSettingValues('mobile', 'primary');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_mobile_flg$/', $k));
            $this->assertEquals($primary_data[$k], $v);
        }

        $settings = $this->NotifySetting->getSettingValues('app', 'none');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_app_flg$/', $k));
            $this->assertFalse($v);
        }
        $settings = $this->NotifySetting->getSettingValues('email', 'none');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_email_flg$/', $k));
            $this->assertFalse($v);
        }
        $settings = $this->NotifySetting->getSettingValues('mobile', 'none');
        foreach ($settings as $k => $v) {
            $this->assertNotEmpty(preg_match('/_mobile_flg$/', $k));
            $this->assertFalse($v);
        }
    }

    function testGetMySettingNoData()
    {
        $this->NotifySetting->my_uid = 1;
        $res_1 = $this->NotifySetting->getMySettings();
        $this->assertEmpty($res_1['id']);
    }

    function testGetMySettingExistData()
    {
        $this->NotifySetting->my_uid = 1;
        $this->NotifySetting->save(['user_id' => 1]);
        $res_2 = $this->NotifySetting->getMySettings();
        $this->assertNotNull($res_2['id']);
    }

    function testGetFlagPrefixByType()
    {
        $res1 = $this->NotifySetting->getFlagPrefixByType(1);
        $this->assertNotNull($res1);
        $res2 = $this->NotifySetting->getFlagPrefixByType(1111);
        $this->assertNull($res2);
    }

}
