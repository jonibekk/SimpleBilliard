<?php App::uses('GoalousTestCase', 'Test');
App::uses('GivenBadge', 'Model');

/**
 * GivenBadge Test Case
 *
 * @property mixed GivenBadge
 */
class GivenBadgeTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.given_badge',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GivenBadge = ClassRegistry::init('GivenBadge');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GivenBadge);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
