<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Api', 'ApiApprovalHistoryService');

/**
 * ApiApprovalHistoryServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property ApiApprovalHistoryService $ApiApprovalHistoryService
 */
class ApiApprovalHistoryServiceTest extends GoalousTestCase
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
        $this->ApiApprovalHistoryService = ClassRegistry::init('ApiApprovalHistoryService');
    }

    function test_processApprovalHistories()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getClearImportantWord()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getLatestCoachActionStatement()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
