<?php App::uses('GoalousTestCase', 'Test');
App::uses('SendMailToUser', 'Model');

/**
 * SendMailToUser Test Case
 *
 * @property SendMailToUser $SendMailToUser
 */
class SendMailToUserTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.send_mail_to_user',
        'app.send_mail',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->SendMailToUser = ClassRegistry::init('SendMailToUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->SendMailToUser);

        parent::tearDown();
    }

    function testGetToUserList()
    {
        $this->SendMailToUser->getToUserList(1);
    }

}
