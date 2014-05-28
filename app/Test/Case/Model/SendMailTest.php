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
        'app.send_mail',
        'app.user',
        'app.team',
        'app.image',
        'app.user',
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
        $id = "537f7709-e6a8-4861-801f-0915ac11b50b";
        $res = $this->SendMail->getDetail($id);
        $from = "from@email.com";
        $to = "to@email.com";
        $this->assertEquals($from, $res['FromUser']['PrimaryEmail']['email'], "送信元メールアドレスが取得できている");
        $this->assertEquals($to, $res['ToUser']['PrimaryEmail']['email'], "送信元メールアドレスが取得できている");
    }

}
