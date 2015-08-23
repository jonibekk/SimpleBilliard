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
        'app.local_name'
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
        $types = NotifySetting::$TYPE;
        unset($types[NotifySetting::TYPE_FEED_POST]);
        unset($types[NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST]);
        unset($types[NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION]);
        foreach ($types as $type => $val) {
            $this->NotifySetting->getTitle($type, $from_user_names, $count_num, $item_name);
        }

        // from_user_name が配列以外の時
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, 'aaa', $count_num, $item_name);

        // TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST と TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION は post_user_id が必須
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST, 'aaa', $count_num,
                                       $item_name, [
                'post_user_id' => 2,
            ]);
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION, 'aaa', $count_num,
                                       $item_name, [
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
            'share_user_list'  => [1 => 1, 2 => 2],
        ]);

        // 個人共有 自分のみ
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'  => [1 => 1],
        ]);

        // 個人 + サークル
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'         => [1 => 1, 2 => 2],
            'share_circle_list'       => [1 => 1, 2 => 2],
        ]);

        // サークル共有
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'         => [1 => 1, 2 => 2],
            'share_circle_list'       => [1 => 1, 2 => 2, 3 => 3],
        ]);

        // サークル共有 サークルメンバーでない場合
        $this->NotifySetting->getTitle(NotifySetting::TYPE_FEED_POST, $from_user_names, $count_num, $item_name, [
            'share_user_list'         => [1 => 1, 2 => 2],
            'share_circle_list'       => [5 => 5],
        ]);
    }

    function testGetTitlePlain()
    {
        $from_user_names = ['aaa', 'bbb'];
        $count_num = 1;
        $item_name = json_encode(['ccc', 'ddd']);

        // HTML入り
        $html = $this->NotifySetting->getTitle(NotifySetting::TYPE_MY_MEMBER_CHANGE_GOAL, $from_user_names, $count_num, $item_name);
        $plain =  $this->NotifySetting->getTitle(NotifySetting::TYPE_MY_MEMBER_CHANGE_GOAL, $from_user_names, $count_num, $item_name, [
            'style' => 'plain'
        ]);
        $this->assertNotEquals($html, $plain);
    }
}
