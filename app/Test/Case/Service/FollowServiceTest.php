<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'FollowService');

/**
 * FollowServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property FollowService $FollowService
 */
class FollowServiceTest extends GoalousTestCase
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
        $this->FollowService = ClassRegistry::init('FollowService');
    }

    function testAdd()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testDelete()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testGetUnique()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

}
