<?php
App::uses('TeamInsight', 'Model');

/**
 * TeamInsight Test Case
 *
 * @property TeamInsight $TeamInsight
 */
class TeamInsightTest extends CakeTestCase
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
    }

    function testGetTotal()
    {
        $this->TeamInsight->current_team_id = 1;
        $this->TeamInsight->my_uid = 1;

        $this->TeamInsight->create();
        $this->TeamInsight->save(
            [
                'team_id'              => 1,
                'target_date'          => '2015-01-01',
                'timezone'             => 9,
                'user_count'           => 1,
                'access_user_count'    => 1,
                'message_count'        => 1,
                'action_count'         => 1,
                'action_user_count'    => 1,
                'post_count'           => 1,
                'post_user_count'      => 1,
                'like_count'           => 1,
                'comment_count'        => 1,
                'collabo_count'        => 1,
                'collabo_action_count' => 1,
            ]);
        $this->TeamInsight->create();
        $this->TeamInsight->save(
            [
                'team_id'              => 1,
                'target_date'          => '2015-01-02',
                'timezone'             => 9,
                'user_count'           => 2,
                'access_user_count'    => 3,
                'message_count'        => 4,
                'action_count'         => 5,
                'action_user_count'    => 6,
                'post_count'           => 7,
                'post_user_count'      => 8,
                'like_count'           => 9,
                'comment_count'        => 10,
                'collabo_count'        => 11,
                'collabo_action_count' => 12,
            ]);

        $timezone = 9;
        $total = $this->TeamInsight->getTotal('2015-01-01', '2015-01-01', $timezone);
        $this->assertEquals(
            [
                'max_user_count'           => 1,
                'max_access_user_count'    => 1,
                'sum_message_count'        => 1,
                'sum_action_count'         => 1,
                'sum_action_user_count'    => 1,
                'sum_post_count'           => 1,
                'sum_post_user_count'      => 1,
                'sum_like_count'           => 1,
                'sum_comment_count'        => 1,
                'sum_collabo_count'        => 1,
                'sum_collabo_action_count' => 1,
            ], $total[0]);

        $total = $this->TeamInsight->getTotal('2015-01-01', '2015-01-02', $timezone);
        $this->assertEquals(
            [
                'max_user_count'           => 2,
                'max_access_user_count'    => 3,
                'sum_message_count'        => 5,
                'sum_action_count'         => 6,
                'sum_action_user_count'    => 7,
                'sum_post_count'           => 8,
                'sum_post_user_count'      => 9,
                'sum_like_count'           => 10,
                'sum_comment_count'        => 11,
                'sum_collabo_count'        => 12,
                'sum_collabo_action_count' => 13,
            ], $total[0]);
    }
}
