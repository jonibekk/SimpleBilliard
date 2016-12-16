<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'KeyResultService');

/**
 * KeyResultServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property KeyResultService $KeyResultService
 */
class KeyResultServiceTest extends GoalousTestCase
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
        $this->KeyResultService = ClassRegistry::init('KeyResultService');
    }

    function test_get()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_buildKrUnitsSelectList()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processKeyResults()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processKeyResult()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_findByGoalId()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_exchangeTkr()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_formatBigFloat()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

}
