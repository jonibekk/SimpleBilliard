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
        'app.goal',
        'app.goal_label',
        'app.label',
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

    function testGetLabelList()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $this->_clearCache();
        $this->assertCount(3, $this->GoalLabel->getLabelList(1));
        $this->assertCount(1, $this->GoalLabel->getLabelList(2));

    }

    function testSaveNewLabelsAttachGoal()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $this->_clearCache();
        $before_label_count = $this->GoalLabel->Label->find('count');
        $before_goal_label_list = $this->GoalLabel->getLabelList(2);
        $this->GoalLabel->saveNewLabelsAttachGoal(2, ['aaa', 'bbb']);
        $after_label_count = $this->GoalLabel->Label->find('count');
        $after_goal_label_list = $this->GoalLabel->getLabelList(2);
        $this->assertEquals(count($before_goal_label_list) + 2, count($after_goal_label_list));
        $this->assertEquals($before_label_count + 2, $after_label_count);
    }

    function testAttachLabels()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $this->_clearCache();

        $before_goal_label_list = $this->GoalLabel->getLabelList(2);
        $before_label_1_g_count = $this->_getGoalCount(2);
        $before_label_2_g_count = $this->_getGoalCount(3);
        $this->GoalLabel->attachLabels(2, ['2' => null, '3' => null]);
        $after_goal_label_list = $this->GoalLabel->getLabelList(2);
        $after_label_1_g_count = $this->_getGoalCount(2);
        $after_label_2_g_count = $this->_getGoalCount(3);
        $this->assertEquals(count($before_goal_label_list) + 2, count($after_goal_label_list));
        $this->assertEquals($before_label_1_g_count + 1, $after_label_1_g_count);
        $this->assertEquals($before_label_2_g_count + 1, $after_label_2_g_count);

    }

    function testDetachLabels()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $this->_clearCache();

        $before_goal_label_list = $this->GoalLabel->getLabelList(1);
        $before_label_1_g_count = $this->_getGoalCount(1);
        $before_label_2_g_count = $this->_getGoalCount(2);
        $this->GoalLabel->detachLabels(1, ['1' => null, '2' => null]);
        $after_goal_label_list = $this->GoalLabel->getLabelList(1);
        $after_label_1_g_count = $this->_getGoalCount(1);
        $after_label_2_g_count = $this->_getGoalCount(2);
        $this->assertEquals(count($before_goal_label_list) - 2, count($after_goal_label_list));
        $this->assertEquals($before_label_1_g_count - 1, $after_label_1_g_count);
        $this->assertEquals($before_label_2_g_count - 1, $after_label_2_g_count);
    }

    function _setDefault()
    {
        $this->GoalLabel->current_team_id = 1;
        $this->GoalLabel->my_uid = 1;
        $this->GoalLabel->Label->current_team_id = 1;
        $this->GoalLabel->Label->my_uid = 1;
        $this->GoalLabel->Goal->current_team_id = 1;
        $this->GoalLabel->Goal->my_uid = 1;
    }

    function _saveDefaultData()
    {
        $labels = [
            [
                'id'      => 1,
                'name'    => 'test1',
                'team_id' => 1,
            ],
            [
                'id'      => 2,
                'name'    => 'test2',
                'team_id' => 1,
            ],
            [
                'id'      => 3,
                'name'    => 'test3',
                'team_id' => 1,
            ],
        ];
        $this->GoalLabel->Label->saveAll($labels);

        $goal_labels = [
            [
                'goal_id'  => 1,
                'label_id' => 1,
                'team_id'  => 1,
            ],
            [
                'goal_id'  => 1,
                'label_id' => 2,
                'team_id'  => 1,
            ],
            [
                'goal_id'  => 1,
                'label_id' => 3,
                'team_id'  => 1,
            ],
            [
                'goal_id'  => 2,
                'label_id' => 1,
                'team_id'  => 1,
            ],
        ];
        $this->GoalLabel->saveAll($goal_labels);

    }

    function _getGoalCount($label_id)
    {
        return Hash::get($this->GoalLabel->Label->findById($label_id), 'Label.goal_label_count');
    }

}
