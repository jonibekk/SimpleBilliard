<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CircleMember', 'Model');
App::uses('Circle', 'Model');
App::uses('GlRedis', 'Model');
App::import('Service/User/Circle', 'UserCircleJoiningService');

/**
 * Class UserCircleJoiningServiceTest
 * @property CircleMember  $CircleMember
 * @property GlRedis  $GlRedis
 * @property int  $userId
 * @property int  $teamId
 * @property int  $circleId
 */
class UserCircleJoiningServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.user',
        'app.team',
        'app.circle',
        'app.circle_member',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->CircleMember->getDataSource()->truncate('circle_members');

        $this->userId = rand(1, 1000);
        $this->teamId = rand(1, 1000);
        $this->circleId = rand(1, 1000);
    }

    /**
     * @group addMember
     */
    public function testAddMember()
    {
        $service = new UserCircleJoiningService();
        $result = $service->addMember($this->userId, $this->teamId, $this->circleId);
        $resultId = Hash::get($result, 'CircleMember.id');

        $actual = $this->CircleMember->find('first', [
            'conditions' => [
                'user_id' => $this->userId,
                'team_id' => $this->teamId,
                'circle_id' => $this->circleId
            ],
            'fields' => ['id', 'user_id', 'team_id', 'circle_id']
        ]);

        $this->assertEquals($resultId, Hash::get($actual, 'CircleMember.id'));
        $this->assertEquals($this->userId, Hash::get($actual, 'CircleMember.user_id'));
        $this->assertEquals($this->teamId, Hash::get($actual, 'CircleMember.team_id'));
        $this->assertEquals($this->circleId, Hash::get($actual, 'CircleMember.circle_id'));
    }

    /**
     * @group addMember
     */
    public function testAddMemberBehaviorTest()
    {
        $resultMock = \Mockery::mock('ResultMock');
        $serviceMock = \Mockery::mock(UserCircleJoiningService::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $glRedisMock = \Mockery::mock(GlRedis::class);
        $circleMemberMock = \Mockery::mock(CircleMember::class);

        $serviceMock->shouldReceive('getCircleMemberEntity')->andReturn($circleMemberMock);
        $serviceMock->shouldReceive('getGlRedis')->andReturn($glRedisMock);

        $circleMemberMock->shouldReceive('create')->once();
        $circleMemberMock->shouldReceive('save')
            ->with(['circle_id' => $this->circleId, 'team_id' => $this->teamId, 'user_id' => $this->userId])
            ->once()
            ->andReturn($resultMock);
        $circleMemberMock->shouldReceive('updateCounterCache')
            ->with(['circle_id' => $this->circleId])
            ->once();
        $glRedisMock->shouldReceive('deleteMultiCircleMemberCount')
            ->with([$this->circleId])
            ->once();

        $expected = $resultMock;
        $actual = $serviceMock->addMember($this->userId, $this->teamId, $this->circleId);

        $this->assertSame($expected, $actual);
    }

    /**
     * @group isJoined
     * @throws Exception
     */
    public function testIsJoinedIsExist()
    {
        $this->CircleMember->create();
        $this->CircleMember->save([
            'user_id' => $this->userId,
            'team_id' => $this->teamId,
            'circle_id' => $this->circleId
        ]);

        $result = (new UserCircleJoiningService())->isJoined($this->circleId, $this->userId);
        $this->assertTrue($result);
    }

    /**
     * @group isJoined
     * @throws Exception
     */
    public function testIsJoinedIsNotExist()
    {
        $result = (new UserCircleJoiningService())->isJoined($this->circleId, $this->userId);
        $this->assertFalse($result);
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}
