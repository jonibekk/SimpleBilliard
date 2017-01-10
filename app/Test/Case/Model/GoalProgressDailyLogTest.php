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

    function testFindLogsSingleGoal()
    {
        $this->_setDefaultValues();
        $this->_saveDefaultData();
        $expected = [
            (int)0 => [
                'goal_id'     => '1',
                'progress'    => '20',
                'target_date' => '2016-01-02'
            ],
            (int)1 => [
                'goal_id'     => '1',
                'progress'    => '30',
                'target_date' => '2016-01-03'
            ],
            (int)2 => [
                'goal_id'     => '1',
                'progress'    => '40',
                'target_date' => '2016-01-04'
            ]
        ];
        $actual = $this->GoalProgressDailyLog->findLogs('2016-01-02', '2016-01-04', [1]);
        $this->assertEquals($expected, $actual);

    }

    function testGetLogsMultiGoals()
    {
        $this->_setDefaultValues();
        $this->_saveDefaultData();
        $expected = [
            (int)0 => [
                'goal_id'     => '1',
                'progress'    => '20',
                'target_date' => '2016-01-02'
            ],
            (int)1 => [
                'goal_id'     => '2',
                'progress'    => '20',
                'target_date' => '2016-01-02'
            ],
            (int)2 => [
                'goal_id'     => '1',
                'progress'    => '30',
                'target_date' => '2016-01-03'
            ],
            (int)3 => [
                'goal_id'     => '2',
                'progress'    => '30',
                'target_date' => '2016-01-03'
            ],
            (int)4 => [
                'goal_id'     => '1',
                'progress'    => '40',
                'target_date' => '2016-01-04'
            ],
            (int)5 => [
                'goal_id'     => '2',
                'progress'    => '40',
                'target_date' => '2016-01-04'
            ]
        ];
        $actual = $this->GoalProgressDailyLog->findLogs('2016-01-02', '2016-01-04', [1, 2]);
        $this->assertEquals($expected, $actual);
    }

    function _setDefaultValues()
    {
        $this->GoalProgressDailyLog->current_team_id = 1;
        $this->GoalProgressDailyLog->my_uid = 1;
    }

    function _saveDefaultData()
    {
        $data = [
            [
                'progress'    => 50,
                'target_date' => '2016-01-05',
                'team_id'     => 1,
                'goal_id'     => 1
            ],
            [
                'progress'    => 40,
                'target_date' => '2016-01-04',
                'team_id'     => 1,
                'goal_id'     => 1
            ],
            [
                'progress'    => 30,
                'target_date' => '2016-01-03',
                'team_id'     => 1,
                'goal_id'     => 1
            ],
            [
                'progress'    => 20,
                'target_date' => '2016-01-02',
                'team_id'     => 1,
                'goal_id'     => 1
            ],
            [
                'progress'    => 10,
                'target_date' => '2016-01-01',
                'team_id'     => 1,
                'goal_id'     => 1
            ],
            [
                'progress'    => 50,
                'target_date' => '2016-01-05',
                'team_id'     => 1,
                'goal_id'     => 2
            ],
            [
                'progress'    => 40,
                'target_date' => '2016-01-04',
                'team_id'     => 1,
                'goal_id'     => 2
            ],
            [
                'progress'    => 30,
                'target_date' => '2016-01-03',
                'team_id'     => 1,
                'goal_id'     => 2
            ],
            [
                'progress'    => 20,
                'target_date' => '2016-01-02',
                'team_id'     => 1,
                'goal_id'     => 2
            ],
            [
                'progress'    => 10,
                'target_date' => '2016-01-01',
                'team_id'     => 1,
                'goal_id'     => 2
            ],
        ];
        $this->GoalProgressDailyLog->saveAll($data);
    }

}
