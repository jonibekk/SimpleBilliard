<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'GoalService');

/**
 * GoalServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property GoalService $GoalService
 */
class GoalServiceTest extends GoalousTestCase
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
        $this->GoalService = ClassRegistry::init('GoalService');
    }

    function testGoalValidateFields()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testCacheList()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGet()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testExtend()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testUpdate()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testCreate()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testBuildUpdateGoalData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testBuildUpdateTkrData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_validateSave()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_isGoalAfterCurrentTerm()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_processGoals()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getProgress()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extendTermType()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_getTermType()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_canExchangeTkr()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

}
