<?php
App::uses('GoalLabel', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * GoalLabel Test Case
 *
 * @property GoalLabel $GoalLabel
 */
class GoalLabelTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_label',
        'app.label'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GoalLabel = ClassRegistry::init('GoalLabel');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalLabel);

        parent::tearDown();
    }

    function testAAA()
    {

    }

    function _setDefault()
    {
        $this->GoalLabel->current_team_id = 1;
        $this->GoalLabel->my_uid = 1;
        $this->GoalLabel->Label->current_team_id = 1;
        $this->GoalLabel->Label->my_uid = 1;
    }

    function _saveDefaultData()
    {
        $fixture_data = [
            [
                'name'             => 'test1',
                'team_id'          => 1,
                'goal_label_count' => '1',
            ],
            [
                'name'             => 'test2',
                'team_id'          => 1,
                'goal_label_count' => '2',
            ],
            [
                'name'             => 'test3',
                'team_id'          => 1,
                'goal_label_count' => '4',
            ],
            [
                'name'             => 'test4',
                'team_id'          => 1,
                'goal_label_count' => '3',
            ],
        ];
        $this->Label->saveAll($fixture_data);
    }

}
