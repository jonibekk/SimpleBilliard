<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'GoalMemberService');

/**
 * GoalMemberServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property GoalMemberService $GoalMemberService
 */
class GoalMemberServiceTest extends GoalousTestCase
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
        $this->GoalMemberService = ClassRegistry::init('GoalMemberService');
    }

    function testGet()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testExtend()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testIsApprovableGoalMember()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testIsApprovableByGoalId()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function testIsLeader()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
