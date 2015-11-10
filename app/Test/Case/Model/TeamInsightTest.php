<?php App::uses('GoalousTestCase', 'Test');
App::uses('TeamInsight', 'Model');

/**
 * TeamInsight Test Case
 *
 * @property TeamInsight $TeamInsight
 */
class TeamInsightTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_insight',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamInsight = ClassRegistry::init('TeamInsight');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TeamInsight);

        parent::tearDown();
    }

    function testGetWeekRangeDate()
    {
        $case = [
            '2015-09-07',
            '2015-09-08',
            '2015-09-09',
            '2015-09-10',
            '2015-09-11',
            '2015-09-12',
            '2015-09-13',
        ];
        foreach ($case as $date) {
            $res = $this->TeamInsight->getWeekRangeDate($date);
            $this->assertEquals(['start' => '2015-09-07', 'end' => '2015-09-13'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => 1]);
            $this->assertEquals(['start' => '2015-09-14', 'end' => '2015-09-20'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => 2]);
            $this->assertEquals(['start' => '2015-09-21', 'end' => '2015-09-27'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => -1]);
            $this->assertEquals(['start' => '2015-08-31', 'end' => '2015-09-06'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => -2]);
            $this->assertEquals(['start' => '2015-08-24', 'end' => '2015-08-30'], $res);
        }

        $case = [
            '2015-09-14',
            '2015-09-15',
            '2015-09-16',
            '2015-09-17',
            '2015-09-18',
            '2015-09-19',
            '2015-09-20',
        ];
        foreach ($case as $date) {
            $res = $this->TeamInsight->getWeekRangeDate($date);
            $this->assertEquals(['start' => '2015-09-14', 'end' => '2015-09-20'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => 1]);
            $this->assertEquals(['start' => '2015-09-21', 'end' => '2015-09-27'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => 2]);
            $this->assertEquals(['start' => '2015-09-28', 'end' => '2015-10-04'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => -1]);
            $this->assertEquals(['start' => '2015-09-07', 'end' => '2015-09-13'], $res);
            $res = $this->TeamInsight->getWeekRangeDate($date, ['offset' => -2]);
            $this->assertEquals(['start' => '2015-08-31', 'end' => '2015-09-06'], $res);
        }

        $res = $this->TeamInsight->getWeekRangeDate('abcde');
        $this->assertFalse($res);
    }

    function testGetMonthRangeDate()
    {
        $case = [
            '2015-09-01',
            '2015-09-08',
            '2015-09-20',
            '2015-09-30',
        ];
        foreach ($case as $date) {
            $res = $this->TeamInsight->getMonthRangeDate($date);
            $this->assertEquals(['start' => '2015-09-01', 'end' => '2015-09-30'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => 1]);
            $this->assertEquals(['start' => '2015-10-01', 'end' => '2015-10-31'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => 2]);
            $this->assertEquals(['start' => '2015-11-01', 'end' => '2015-11-30'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => -1]);
            $this->assertEquals(['start' => '2015-08-01', 'end' => '2015-08-31'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => -2]);
            $this->assertEquals(['start' => '2015-07-01', 'end' => '2015-07-31'], $res);
        }

        $case = [
            '2015-10-01',
            '2015-10-08',
            '2015-10-20',
            '2015-10-31',
        ];
        foreach ($case as $date) {
            $res = $this->TeamInsight->getMonthRangeDate($date);
            $this->assertEquals(['start' => '2015-10-01', 'end' => '2015-10-31'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => 1]);
            $this->assertEquals(['start' => '2015-11-01', 'end' => '2015-11-30'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => 2]);
            $this->assertEquals(['start' => '2015-12-01', 'end' => '2015-12-31'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => 3]);
            $this->assertEquals(['start' => '2016-01-01', 'end' => '2016-01-31'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => -9]);
            $this->assertEquals(['start' => '2015-01-01', 'end' => '2015-01-31'], $res);
            $res = $this->TeamInsight->getMonthRangeDate($date, ['offset' => -10]);
            $this->assertEquals(['start' => '2014-12-01', 'end' => '2014-12-31'], $res);
        }
        $res = $this->TeamInsight->getMonthRangeDate('abcde');
        $this->assertFalse($res);
    }

    function testGetTotal()
    {
        $this->TeamInsight->current_team_id = 1;
        $this->TeamInsight->my_uid = 1;

        $this->TeamInsight->create();
        $this->TeamInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-01',
                'timezone'    => 9,
                'user_count'  => 1,
            ]);
        $this->TeamInsight->create();
        $this->TeamInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-02',
                'timezone'    => 9,
                'user_count'  => 10,
            ]);
        $this->TeamInsight->create();
        $this->TeamInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-01',
                'timezone'    => 9,
                'user_count'  => 2,
            ]);

        $start_date = '2015-01-01';
        $end_date = '2015-01-02';
        $timezone = 9;
        $total = $this->TeamInsight->getTotal($start_date, $start_date, $timezone);
        $this->assertEquals(2, $total[0]['max_user_count']);
        $total = $this->TeamInsight->getTotal($start_date, $end_date, $timezone);
        $this->assertEquals(10, $total[0]['max_user_count']);

    }
}
