<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'GroupService');

/**
 * GroupServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property GroupService $GroupService
 */
class GroupServiceTest extends GoalousTestCase
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
        $this->GroupService = ClassRegistry::init('GroupService');
    }

    function test_isGroupMember()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_findAllGroupsWithMemberCount()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
