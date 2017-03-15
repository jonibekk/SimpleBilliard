<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CircleService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');


/**
 * CircleService Test Case
 *
 * @property ActionService $ActionService
 */
class CircleServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.team',
        'app.team_member',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.post',
        'app.post_share_circle',
        'app.local_name'
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
        $this->CircleService = ClassRegistry::init('CircleService');
        $this->Circle = ClassRegistry::init('Circle');
        $this->CircleMember = ClassRegistry::init('CircleMember');
    }
    public function test_validateCreate_validate()
    {
        $circle = [
            'Circle' => [
                'name' => 'Kohei',
                'description' => 'I am Kohei'
            ]
        ];
        $circleMembers = [1, 2, 3];
        $res = $this->CircleService->validateCreate($circle, 1);
        $this->assertTrue($res);
    }

    public function test_validateCreate_validateError()
    {
        $circle = [
            'Circle' => [
                'name' => 'Kohei',
                'description' => ''
            ]
        ];
        $res = $this->CircleService->validateCreate($circle, 1);
        $this->assertTrue($res !== true);

        $circle = [
            'Circle' => [
                'name' => '',
                'description' => 'I am Kohei'
            ]
        ];
        $res = $this->CircleService->validateCreate($circle, 1);
        $this->assertTrue($res !== true);
    }

    function test_create_public()
    {
        $this->_setModelProperties();
        $circle = [
            'Circle' => [
                'name' => 'test circle',
                'description' => 'desc',
                'public_flg' => true
            ]
        ];

        $myUserId = $this->createActiveUser(1);
        $userIds = [$this->createActiveUser(1), $this->createActiveUser(1)];

        $res = $this->CircleService->create($circle, $myUserId, $userIds);
        $this->assertTrue($res);

        $insertedCircle = $this->Circle->findByName($circle['Circle']['name']);
        $this->assertNotEmpty($insertedCircle);

        // check member
        $memberCount = $this->CircleMember->find('count', ['conditions' => ['circle_id' => $insertedCircle['Circle']['id']]]);
        $this->assertEquals($memberCount, 3);

        // check post
        $post = $this->Circle->PostShareCircle->Post->find('first', ['conditions' => ['circle_id' => $insertedCircle['Circle']['id'], 'type' => 7]]);
        $this->assertNotEmpty($post);
    }

    function test_create_private()
    {
        $this->_setModelProperties();
        $circle = [
            'Circle' => [
                'name' => 'test circle',
                'description' => 'desc',
                'public_flg' => false
            ]
        ];
        $myUserId = $this->createActiveUser(1);
        $userIds = [$this->createActiveUser(1), $this->createActiveUser(1)];
        $res = $this->CircleService->create($circle, $myUserId, $userIds);
        $this->assertTrue($res);

        $insertedCircle = $this->Circle->findByName($circle['Circle']['name']);
        $this->assertNotEmpty($insertedCircle);

        // check post
        $post = $this->Circle->PostShareCircle->Post->find('first', ['conditions' => ['circle_id' => $insertedCircle['Circle']['id'], 'type' => 7]]);
        $this->assertEmpty($post);
    }

    function test_create_error()
    {
        $this->_setModelProperties();
        $circle = [
            'Circle' => [
                'name' => '',
                'description' => 'desc',
                'public_flg' => true
            ]
        ];
        $myUserId = $this->createActiveUser(1);
        $res = $this->CircleService->create($circle, $myUserId);
        $this->assertFalse($res);
    }

    function test_join()
    {
        $this->_setModelProperties();
        $circle = [
            'Circle' => [
                'name' => 'test circle',
                'description' => 'desc',
                'public_flg' => true
            ]
        ];

        $myUserId = $this->createActiveUser(1);
        $res = $this->CircleService->create($circle, $myUserId);
        $insertedCircle = $this->Circle->findByName($circle['Circle']['name']);
        $circleId = $insertedCircle['Circle']['id'];

        $this->assertTrue($this->CircleService->join($circleId, 2));
        $this->assertNotEmpty($this->CircleMember->find('first', ['conditions' => ['circle_id' => $circleId, 'user_id' => 2]]));
    }
    //余裕あれば
    function test_leave()
    {
        $this->markTestSkipped();
    }

    //余裕あれば
    function test_validateAddMember()
    {
        $this->markTestSkipped();
    }

    //余裕あれば
    function test_addMembers()
    {
        $this->markTestSkipped();
    }

    //余裕あれば
    function test_joinMultiple()
    {
        $this->markTestSkipped();
    }

    function _setModelProperties()
    {
        $teamId = 1;
        $this->Circle->current_team_id = $teamId;
        $this->Circle->PostShareCircle->current_team_id = $teamId;
        $this->Circle->PostShareCircle->Post->current_team_id = $teamId;
        $this->CircleMember->current_team_id = $teamId;
        $this->CircleMember->User->current_team_id = $teamId;
    }
}
