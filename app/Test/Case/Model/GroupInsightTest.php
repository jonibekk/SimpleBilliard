<?php
App::uses('GroupInsight', 'Model');

/**
 * GroupInsight Test Case
 *
 * @property GroupInsight $GroupInsight
 */
class GroupInsightTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.group_insight',
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
        $this->GroupInsight = ClassRegistry::init('GroupInsight');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupInsight);

        parent::tearDown();
    }

    function testGetTotal()
    {
        $this->GroupInsight->current_team_id = 1;
        $this->GroupInsight->my_uid = 1;


        $this->GroupInsight->create();
        $this->GroupInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-01',
                'timezone'    => 9,
                'group_id'   => 3,
                'user_count'  => 1,
            ]);
        $this->GroupInsight->create();
        $this->GroupInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-02',
                'timezone'    => 9,
                'group_id'   => 3,
                'user_count'  => 2,
            ]);
        $this->GroupInsight->create();
        $this->GroupInsight->save(
            [
                'team_id'     => 1,
                'target_date' => '2015-01-01',
                'timezone'    => 9,
                'group_id'   => 1,
                'user_count'  => 10,
            ]);

        $start_date = '2015-01-01';
        $end_date = '2015-01-02';
        $timezone = 9;
        $total = $this->GroupInsight->getTotal(1, $start_date, $end_date, $timezone);
        $this->assertEquals(10, $total[0]['max_user_count']);
        $total = $this->GroupInsight->getTotal(3, $start_date, $end_date, $timezone);
        $this->assertEquals(2, $total[0]['max_user_count']);
    }
}
