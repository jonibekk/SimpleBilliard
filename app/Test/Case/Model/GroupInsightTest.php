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
                'team_id'              => 1,
                'group_id'             => 1,
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
        $this->GroupInsight->create();
        $this->GroupInsight->save(
            [
                'team_id'              => 1,
                'group_id'             => 1,
                'target_date'          => '2015-01-01',
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
        $this->GroupInsight->create();
        $this->GroupInsight->save(
            [
                'team_id'              => 1,
                'group_id'             => 2,
                'target_date'          => '2015-01-01',
                'timezone'             => 9,
                'user_count'           => 100,
                'access_user_count'    => 110,
                'message_count'        => 120,
                'action_count'         => 130,
                'action_user_count'    => 140,
                'post_count'           => 150,
                'post_user_count'      => 160,
                'like_count'           => 170,
                'comment_count'        => 180,
                'collabo_count'        => 190,
                'collabo_action_count' => 200,
            ]);

        $start_date = '2015-01-01';
        $end_date = '2015-01-01';
        $timezone = 9;
        $total = $this->GroupInsight->getTotal(1, $start_date, $end_date, $timezone);
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

        $total = $this->GroupInsight->getTotal(2, $start_date, $end_date, $timezone);
        $this->assertEquals(
            [
                'max_user_count'           => 100,
                'max_access_user_count'    => 110,
                'sum_message_count'        => 120,
                'sum_action_count'         => 130,
                'sum_action_user_count'    => 140,
                'sum_post_count'           => 150,
                'sum_post_user_count'      => 160,
                'sum_like_count'           => 170,
                'sum_comment_count'        => 180,
                'sum_collabo_count'        => 190,
                'sum_collabo_action_count' => 200,
            ], $total[0]);
    }
}
