<?php
App::uses('GoalousTestCase', 'Test');
App::uses('GoalProgressDailyLog', 'Model');

/**
 * GoalProgressDailyLog Test Case
 *
 * @property GoalProgressDailyLog $GoalProgressDailyLog
 */
class GoalProgressDailyLogTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.goal_progress_daily_log',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalProgressDailyLog = ClassRegistry::init('GoalProgressDailyLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalProgressDailyLog);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
