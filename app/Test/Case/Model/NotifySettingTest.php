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
        'app.notification',
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

    function testIsOnNotify()
    {
        $uid = 1;
        $res = $this->NotifySetting->isOnNotify($uid, NotifySetting::TYPE_FEED_APP);
        $this->assertTrue($res, "通知設定なし");
        $data = ['feed_app_flg' => false, 'user_id' => $uid];
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->isOnNotify($uid, NotifySetting::TYPE_FEED_APP);
        $this->assertFalse($res, "通知設定あり、off");
        $data = ['feed_app_flg' => true, 'user_id' => $uid];
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->isOnNotify($uid, NotifySetting::TYPE_FEED_APP);
        $this->assertTrue($res, "通知設定あり、on");
    }
}
