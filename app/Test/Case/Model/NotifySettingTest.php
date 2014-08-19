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
        $uid = 1000000;
        $uid2 = 9999999;
        $this->NotifySetting->me['id'] = 1;
        $res = $this->NotifySetting->getAppEmailNotifySetting($uid, NotifySetting::TYPE_FEED);
        $expected = [
            $uid => ['app' => true, 'email' => true]
        ];
        $this->assertEquals($expected, $res, "通知設定なし");
        $data = ['feed_app_flg' => false, 'feed_email_flg' => false, 'user_id' => $uid];
        $this->NotifySetting->save($data);
        $notifi_setting_id = $this->NotifySetting->getLastInsertID();
        $res = $this->NotifySetting->getAppEmailNotifySetting($uid, NotifySetting::TYPE_FEED);
        $expected = [
            $uid => ['app' => false, 'email' => false]
        ];
        $this->assertEquals($expected, $res, "通知設定あり、off");
        $data = ['id' => $notifi_setting_id, 'feed_app_flg' => true, 'feed_email_flg' => true];
        $this->NotifySetting->create();
        $this->NotifySetting->save($data);
        $res = $this->NotifySetting->getAppEmailNotifySetting($uid, NotifySetting::TYPE_FEED);
        $expected = [
            $uid => ['app' => true, 'email' => true]
        ];
        $this->assertEquals($expected, $res, "通知設定あり、on");
        $res = $this->NotifySetting->getAppEmailNotifySetting([$uid, $uid2], NotifySetting::TYPE_FEED);
        $expected = [
            $uid  => ['app' => true, 'email' => true],
            $uid2 => ['app' => true, 'email' => true]
        ];
        $this->assertEquals($expected, $res, "通知設定ありなし混在。複数ユーザ");
    }
}
