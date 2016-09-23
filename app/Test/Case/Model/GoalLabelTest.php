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

    function testAttachLabels()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $this->_clearCache();

        $bf_all_label_count = count(array_unique($this->GoalLabel->Label->find('list')));
        $bf_goal_label_list = $this->GoalLabel->getLabelList(2);
        $bf_label_1_g_count = $this->_getGoalCount('test2');
        $bf_label_2_g_count = $this->_getGoalCount('test3');
        $this->GoalLabel->attachLabels(2, ['test2', 'test3', 'test4', 'test5']);
        $this->_clearCache();
        $af_all_label_count = count(array_unique($this->GoalLabel->Label->find('list')));
        $af_goal_label_list = $this->GoalLabel->getLabelList(2);
        $af_label_1_g_count = $this->_getGoalCount('test2');
        $af_label_2_g_count = $this->_getGoalCount('test3');
        $this->assertEquals($bf_all_label_count + 2, $af_all_label_count);
        $this->assertEquals(count($bf_goal_label_list) + 4, count($af_goal_label_list));
        $this->assertEquals($bf_label_1_g_count + 1, $af_label_1_g_count);
        $this->assertEquals($bf_label_2_g_count + 1, $af_label_2_g_count);

    }

    function testDetachLabels()
    {
        $this->_setDefault();
        $this->_saveDefaultData();
        $this->_clearCache();

        $before_goal_label_list = $this->GoalLabel->getLabelList(1);
        $before_label_1_g_count = $this->_getGoalCount('test1');
        $before_label_2_g_count = $this->_getGoalCount('test2');
        $this->GoalLabel->detachLabels(1, ['1' => null, '2' => null]);
        $after_goal_label_list = $this->GoalLabel->getLabelList(1);
        $after_label_1_g_count = $this->_getGoalCount('test1');
        $after_label_2_g_count = $this->_getGoalCount('test2');
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

    function _getGoalCount($name)
    {
        return Hash::get($this->GoalLabel->Label->findByName($name), 'Label.goal_label_count');
    }

}
