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
        $res = $this->ApiGoalApprovalService->process([],1);
        $this->assertEmpty($res);
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processChangeLog()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processChangeGoalLog()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processChangeTkrLog()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
