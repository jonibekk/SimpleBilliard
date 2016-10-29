<?php App::uses('GoalousTestCase', 'Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('GoalHelper', 'View/Helper');

/**
 * GoalHelper Test Case
 *
 * @property GoalHelper Goal
 */
class GoalHelperTest extends GoalousTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $View = new View();
        $this->Goal = new GoalHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Goal);

        parent::tearDown();
    }

    /**
     * testGetFollowOption method
     *
     * @return void
     */
    public function testGetFollowOption()
    {
        $this->markTestIncomplete('testGetFollowOption not implemented.');
    }

    /**
     * testGetCollaboOption method
     *
     * @return void
     */
    public function testGetCollaboOption()
    {
        $this->markTestIncomplete('testGetCollaboOption not implemented.');
    }

    /**
     * testDisplayGoalMemberNameList method
     *
     * @return void
     */
    public function testDisplayGoalMemberNameList()
    {
        $this->markTestIncomplete('testDisplayGoalMemberNameList not implemented.');
    }

}
