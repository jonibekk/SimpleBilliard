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
 */
class ApiGoalApprovalServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
    }

    function test_processGoalApprovalForResponse()
    {
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
