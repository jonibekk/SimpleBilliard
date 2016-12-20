<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Api', 'ApiService');

/**
 * ApiServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property ApiService $ApiService
 */
class ApiServiceTest extends GoalousTestCase
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
        $this->ApiService = ClassRegistry::init('ApiService');
    }

    function test_checkMaxLimit()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_formatResponseData()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
