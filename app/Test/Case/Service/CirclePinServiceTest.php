<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CirclePinService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('CirclePin', 'Model');

/**
 * CirclePinService Test Case
 *
 * @property ActionService $ActionService
 * @property CirclePinService $CirclePinService
 * @property Circle $Circle
 * @property CircleMember $CircleMember
 * @property User $User
 */
class CirclePinsServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.circle_pin',
        'app.circle',
        'app.circle_member',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setDefaultTeamIdAndUid();
        $this->CirclePinService = ClassRegistry::init('CirclePinService');
        $this->Circle = ClassRegistry::init('Circle');
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->User = ClassRegistry::init('User');
    }

    public function test_SetCircleOrders()
    {
        $user_id = 1;
        $team_id = 1;
        $circle_orders = '1,2,3,4';
        $this->CircleMember->my_uid = $user_id;
        $this->CircleMember->current_team_id = $team_id;
        $res = $this->CirclePinService->setCircleOrders($user_id, $team_id, $circle_orders);
        $this->assertTrue($res);
    }

    public function test_GetMyCircleSortedList()
    {
        $user_id = 2;
        $team_id = 2;
        $res = $this->CirclePinService->getMyCircleSortedList($user_id, $team_id);
        $this->assertNotEmpty($res);
    }

    function test_DeleteCircleId()
    {
        $uid = 3;
        $team_id = 3;
        $res = $this->CirclePinService->deleteCircleId($uid, $team_id, 20);
        $this->assertTrue($res);
    }
}
