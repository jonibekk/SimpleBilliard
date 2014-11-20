<?php
App::uses('SendMail', 'Model');

/**
 * SendMail Test Case
 *
 * @property SendMail $SendMail
 */
class SendMailTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.send_mail',
        'app.send_mail_to_user',
        'app.notify_from_user',
        'app.team',
        'app.image',
        'app.user',
        'app.notify_setting',
        'app.email',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.comment_mention',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.images_post',
        'app.comment_read',
        'app.notification',
        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.invite',
        'app.thread',
        'app.message'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->SendMail = ClassRegistry::init('SendMail');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SendMail);

        parent::tearDown();
    }

    public function testGetDetail()
    {
        $id = "1";
        $this->SendMail->getDetail($id, null, true, 999999999999);
    }

    public function testGetDetailWithLang()
    {
        $id = "1";
        $res = $this->SendMail->getDetail($id, "jpn");
        $from = "from@email.com";
        $this->assertEquals($from, $res['FromUser']['PrimaryEmail']['email'], "送信元メールアドレスが取得できている");
    }

    public function testIsNotifySentBefore()
    {
        $this->SendMail->isNotifySentBefore(999);
        $notify = [
            'model_id' => 1,
            'type'     => 1,
            'team_id'  => 1
        ];
        $this->SendMail->Notification->save($notify);
        $send_mail = [
            'template_type'   => 1,
            'notification_id' => $this->SendMail->Notification->getLastInsertID()
        ];
        $this->SendMail->save($send_mail);
        $this->SendMail->isNotifySentBefore($this->SendMail->Notification->getLastInsertID());
    }
}
