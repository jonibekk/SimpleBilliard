<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'GoalApprovalService');

/**
 * GoalApprovalServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property GoalApprovalService $GoalApprovalService
 */
class GoalApprovalServiceTest extends GoalousTestCase
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
        $this->GoalApprovalService = ClassRegistry::init('GoalApprovalService');
    }

    function testCountUnapprovedGoal()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testFindHistories()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testHaveAccessAuthoriyOnApproval()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testDeleteUnapprovedCountCache()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testSaveApproval()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testValidateApprovalPost()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGenerateApprovalSaveData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGenerateWithdrawSaveData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGenerateCommentSaveData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGetActionStatusByApprovalType()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testIsApprovable()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testSaveSnapshotForApproval()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
