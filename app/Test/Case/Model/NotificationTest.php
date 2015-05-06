<?php
App::uses('Notification', 'Model');

/**
 * Notification Test Case
 *
 * @property  Notification $Notification
 */
class NotificationTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.notification',
        'app.member_type',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.team_member',
        'app.notify_from_user',
        'app.notify_to_user',
        'app.group',
        'app.job_category',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Notification = ClassRegistry::init('Notification');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Notification);

        parent::tearDown();
    }

    function testDummy()
    {

    }
}
