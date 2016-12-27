<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Api', 'ApiGoalService');

/**
 * ApiGoalServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property ApiGoalService $ApiGoalService
 */
class ApiGoalServiceTest extends GoalousTestCase
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
        $this->ApiGoalService = ClassRegistry::init('ApiGoalService');
    }

    function test_search()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extend()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_extractConditions()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_setPaging()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
