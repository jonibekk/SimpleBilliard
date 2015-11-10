<?php App::uses('GoalousTestCase', 'Test');
App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('NumberExHelper', 'View/Helper');

/**
 * NumberExHelper Test Case
 *
 * @property NumberExHelper $NumberEx
 */
class NumberExHelperTest extends GoalousTestCase
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
        $this->NumberEx = new NumberExHelper($View);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NumberEx);

        parent::tearDown();
    }

    /**
     * testFormatHumanReadable method
     *
     * @return void
     */
    public function testFormatHumanReadable()
    {
        $this->markTestIncomplete('testFormatHumanReadable not implemented.');
    }

}
