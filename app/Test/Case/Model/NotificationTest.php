<?php
App::uses('Notification', 'Model');

/**
 * Notification Test Case
 *
 * @property mixed Notification
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
        'app.user', 'app.notify_setting',
        'app.team',
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

    //ダミーテスト
    function testDummy()
    {
    }

}
