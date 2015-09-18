<?php
App::uses('CircleInsight', 'Model');

/**
 * CircleInsight Test Case
 *
 * @property CircleInsight $CircleInsight
 */
class CircleInsightTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.circle_insight',
        'app.team',
        'app.circle'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->CircleInsight = ClassRegistry::init('CircleInsight');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CircleInsight);

        parent::tearDown();
    }

    function testGetTotal()
    {
        $this->CircleInsight->current_team_id = 1;
        $this->CircleInsight->my_uid = 1;

        $this->CircleInsight->create();
        $this->CircleInsight->save(
            [
                'team_id'         => 1,
                'target_date'     => '2015-01-01',
                'timezone'        => 9,
                'circle_id'       => 3,
                'member_count'    => 1,
                'post_count'      => 1,
                'post_read_count' => 1,
                'post_like_count' => 1,
                'comment_count'   => 1,
            ]);
        $this->CircleInsight->create();
        $this->CircleInsight->save(
            [
                'team_id'         => 1,
                'target_date'     => '2015-01-02',
                'timezone'        => 9,
                'circle_id'       => 3,
                'member_count'    => 2,
                'post_count'      => 3,
                'post_read_count' => 4,
                'post_like_count' => 5,
                'comment_count'   => 6,
            ]);
        $this->CircleInsight->create();
        $this->CircleInsight->save(
            [
                'team_id'         => 1,
                'target_date'     => '2015-01-01',
                'timezone'        => 9,
                'circle_id'       => 1,
                'member_count'    => 10,
                'post_count'      => 11,
                'post_read_count' => 12,
                'post_like_count' => 13,
                'comment_count'   => 14,
            ]);

        $start_date = '2015-01-01';
        $end_date = '2015-01-02';
        $timezone = 9;
        $total = $this->CircleInsight->getTotal($start_date, $end_date, $timezone);
        $this->assertCount(2, $total);
        $this->assertEquals(
            [
                'max_member_count'    => 10,
                'sum_post_count'      => 11,
                'sum_post_read_count' => 12,
                'sum_post_like_count' => 13,
                'sum_comment_count'   => 14,
            ], $total[0][0]);
        $this->assertEquals(
            [
                'max_member_count'    => 2,
                'sum_post_count'      => 4,
                'sum_post_read_count' => 5,
                'sum_post_like_count' => 6,
                'sum_comment_count'   => 7,
            ], $total[1][0]);
    }

}
