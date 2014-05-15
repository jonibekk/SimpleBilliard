<?php
App::uses('Notification', 'Model');

/**
 * Notification Test Case

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
        'app.user',
        'app.team',
        'app.from_user'
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

}
