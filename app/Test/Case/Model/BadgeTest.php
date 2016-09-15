<?php App::uses('GoalousTestCase', 'Test');
App::uses('Badge', 'Model');

/**
 * Badge Test Case
 *
 * @property mixed Badge
 */
class BadgeTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.badge',
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
