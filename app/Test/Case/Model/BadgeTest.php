<?php
App::uses('Badge', 'Model');

/**
 * Badge Test Case
 *
 * @property mixed Badge
 */
class BadgeTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.badge',
        'app.user', 'app.notify_setting',
        'app.team',
        'app.image',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Badge = ClassRegistry::init('Badge');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Badge);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
