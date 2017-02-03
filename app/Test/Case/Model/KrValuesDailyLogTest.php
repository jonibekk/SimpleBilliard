<?php
App::uses('KrValuesDailyLog', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * KrValuesDailyLog Test Case
 *
 * @property KrValuesDailyLog $KrValuesDailyLog
 */
class KrValuesDailyLogTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.kr_values_daily_log',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->KrValuesDailyLog = ClassRegistry::init('KrValuesDailyLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->KrValuesDailyLog);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
