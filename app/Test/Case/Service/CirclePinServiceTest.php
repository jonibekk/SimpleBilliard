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
        $this->CirclePin = ClassRegistry::init('CirclePin');
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
        $user_id = 1;
        $team_id = 1;
        $res = $this->CirclePinService->getMyCircleSortedList($user_id, $team_id);
        $this->assertNotEmpty($res);
    }

    function test_DeleteCircleId()
    {
        $user_id = 1;
        $team_id = 1;
        $circle_orders = '1,2,3,4';
        $this->CircleMember->my_uid = $user_id;
        $this->CircleMember->current_team_id = $team_id;
        $res = $this->CirclePinService->setCircleOrders($user_id, $team_id, $circle_orders);
        $this->assertTrue($res);
        $res = $this->CirclePinService->deleteCircleId($user_id, $team_id, 20);
        $this->assertTrue($res);
        $res = $this->CirclePinService->deleteCircleId($user_id, $team_id, 3);
        $this->assertTrue($res);
        $res = $this->CirclePin->getPinData($user_id, $team_id);
        $this->assertEquals($res, '1,2,4');
    }
}
