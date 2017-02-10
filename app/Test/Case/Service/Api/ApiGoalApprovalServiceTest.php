<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Api', 'ApiGoalApprovalService');

/**
 * ApiGoalApprovalServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property ApiGoalApprovalService $ApiGoalApprovalService
 * @property GoalCategory           $GoalCategory
 * @property Goal                   $Goal
 * @property User                   $User
 */
class ApiGoalApprovalServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.goal_category',
        'app.goal',
        'app.user'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApiGoalApprovalService = ClassRegistry::init('ApiGoalApprovalService');
        $this->User = ClassRegistry::init('User');
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->Goal = ClassRegistry::init('Goal');
        $this->Goal->my_uid = 1;
        $this->Goal->current_team_id = 1;
        $this->GoalCategory = ClassRegistry::init('GoalCategory');
        $this->GoalCategory->my_uid = 1;
        $this->GoalCategory->current_team_id = 1;
    }

    function test_process()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extractGoalChangeDiffColumns_existsDiff()
    {
        $expected = ['name' => 'name', 'photo_file_name' => 'photo_file_name', 'description' => 'description'];
        $res = $this->ApiGoalApprovalService->extractGoalChangeDiffColumns(
            ['name' => 'val1', 'photo_file_name' => 'test1.jpg', 'description' => 'description1'],
            ['name' => 'val2', 'photo_file_name' => 'test2.jpg', 'description' => 'description2'],
            ['name', 'photo_file_name', 'description']
        );
        $this->assertEquals($res, $expected);
    }

    function test_extractGoalChangeDiffColumns_notExistsDiff()
    {
        $expected = [];
        $res = $this->ApiGoalApprovalService->extractGoalChangeDiffColumns(
            ['name' => 'val1', 'photo_file_name' => 'test1.jpg', 'description' => 'description1'],
            ['name' => 'val1', 'photo_file_name' => 'test1.jpg', 'description' => 'description1'],
            ['name', 'photo_file_name', 'description']
        );
        $this->assertEquals($res, $expected);
    }

    function test_extractTkrChangeDiffColumns_existsDiff()
    {
        $expected = ['name' => 'name', 'start_value' => 'start_value', 'description' => 'description'];
        $res = $this->ApiGoalApprovalService->extractTkrChangeDiffColumns(
            ['name' => 'val1', 'start_value' => '10', 'description' => 'description1'],
            ['name' => 'val2', 'start_value' => '20', 'description' => 'description2'],
            ['name', 'start_value', 'description']
        );
        $this->assertEquals($res, $expected);
    }

    function test_extractTkrChangeDiffColumns_notExistsDiff()
    {
        $expected = [];
        $res = $this->ApiGoalApprovalService->extractTkrChangeDiffColumns(
            ['name' => 'val1', 'start_value' => '10', 'description' => 'description1'],
            ['name' => 'val1', 'start_value' => '10', 'description' => 'description1'],
            ['name', 'start_value', 'description']
        );
        $this->assertEquals($res, $expected);
    }

}
