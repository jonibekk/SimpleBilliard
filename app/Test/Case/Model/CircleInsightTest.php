<?php App::uses('GoalousTestCase', 'Test');
App::uses('CircleInsight', 'Model');

/**
 * CircleInsight Test Case
 *
 * @property CircleInsight $CircleInsight
 */
class CircleInsightTest extends GoalousTestCase
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
                'team_id'     => 1,
                'target_date' => '2015-01-01',
                'timezone'    => 9,
                'circle_id'   => 3,
                'user_count'  => 1,
            ]);
        $this->CircleInsight->create();
        $this->CircleInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-02',
                'timezone'    => 9,
                'circle_id'   => 3,
                'user_count'  => 2,
            ]);
        $this->CircleInsight->create();
        $this->CircleInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-01',
                'timezone'    => 9,
                'circle_id'   => 1,
                'user_count'  => 10,
            ]);

        $start_date = '2015-01-01';
        $end_date = '2015-01-02';
        $timezone = 9;
        $total = $this->CircleInsight->getTotal(1, $start_date, $end_date, $timezone);
        $this->assertEquals(10, $total[0]['max_user_count']);
        $total = $this->CircleInsight->getTotal(3, $start_date, $end_date, $timezone);
        $this->assertEquals(2, $total[0]['max_user_count']);
    }

}
