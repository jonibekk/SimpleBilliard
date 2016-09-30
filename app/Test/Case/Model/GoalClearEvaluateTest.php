<?php
App::uses('GoalClearEvaluate', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * GoalClearEvaluate Test Case
 *
 * @property GoalClearEvaluate $GoalClearEvaluate
 */
class GoalClearEvaluateTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_clear_evaluate',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalClearEvaluate = ClassRegistry::init('GoalClearEvaluate');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalClearEvaluate);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
