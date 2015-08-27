<?php
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('GoalHelper', 'View/Helper');

/**
 * GoalHelper Test Case
 *
 * @property GoalHelper Goal
 */
class GoalHelperTest extends CakeTestCase
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
     * testDisplayCollaboratorNameList method
     *
     * @return void
     */
    public function testDisplayCollaboratorNameList()
    {
        $this->markTestIncomplete('testDisplayCollaboratorNameList not implemented.');
    }

}
