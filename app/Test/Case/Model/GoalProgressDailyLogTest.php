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

    function testProgressValidationNumBetween()
    {
        $this->GoalProgressDailyLog->data = ['GoalProgressDailyLog' => ['progress' => 0]];
        $this->assertTrue($this->GoalProgressDailyLog->validates(['fieldList' => ['progress']]));

        $this->GoalProgressDailyLog->data = ['GoalProgressDailyLog' => ['progress' => 100]];
        $this->assertTrue($this->GoalProgressDailyLog->validates(['fieldList' => ['progress']]));

        $this->GoalProgressDailyLog->data = ['GoalProgressDailyLog' => ['progress' => -1]];
        $this->assertFalse($this->GoalProgressDailyLog->validates(['fieldList' => ['progress']]));

        $this->GoalProgressDailyLog->data = ['GoalProgressDailyLog' => ['progress' => 101]];
        $this->assertFalse($this->GoalProgressDailyLog->validates(['fieldList' => ['progress']]));

        $this->GoalProgressDailyLog->data = ['GoalProgressDailyLog' => ['progress' => 'aaa']];
        $this->assertFalse($this->GoalProgressDailyLog->validates(['fieldList' => ['progress']]));

    }

}
