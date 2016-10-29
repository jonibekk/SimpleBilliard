<?php
App::uses('Label', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * Label Test Case
 *
 * @property Label $Label
 */
class LabelTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.label',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Label = ClassRegistry::init('Label');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Label);

        parent::tearDown();
    }

    function testGetListWithGoalCount()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $actual = $this->Label->getListWithGoalCount();
        $this->assertEquals(['test3', 'test4', 'test2', 'test1'], Hash::extract($actual, '{n}.Label.name'));
    }

    function _setDefault()
    {
        $this->Label->current_team_id = 1;
        $this->Label->my_uid = 1;
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
