<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CircleService');
App::uses('Circle', 'Model');
App::uses('CircleMember', 'Model');
App::uses('GlRedis', 'Model');
App::import('Service/Request/Resource', 'CircleResourceRequest');

/**
 * CircleService Test Case
 *
 * @property ActionService $ActionService
 * @property CircleService $CircleService
 * @property Circle        $Circle
 * @property CircleMember  $CircleMember
 * @property User          $User
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
        'app.circle_pin',
        'app.post',
        'app.post_share_circle',
        'app.local_name',
        'app.experiment',
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
        $this->User = ClassRegistry::init('User');
        $this->GlRedis = ClassRegistry::init('GlRedis');
    }

    public function test_validateCreate_validate()
    {
        $circle = [
            'Circle' => [
                'name'        => 'Kohei',
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
                'name'        => 'Kohei',
                'description' => ''
            ]
        ];
        $res = $this->CircleService->validateCreate($circle, 1);
        $this->assertTrue($res !== true);

        $circle = [
            'Circle' => [
                'name'        => '',
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
                'name'        => 'test circle',
                'description' => 'desc',
                'public_flg'  => true
            ]
        ];

        $myUserId = $this->createActiveUser(1);
        $userIds = [$this->createActiveUser(1), $this->createActiveUser(1)];

        $res = $this->CircleService->create($circle, $myUserId, $userIds);
        $this->assertTrue($res);

        $insertedCircle = $this->Circle->findByName($circle['Circle']['name']);
        $this->assertNotEmpty($insertedCircle);

        // check member
        $memberCount = $this->CircleMember->find('count',
            ['conditions' => ['circle_id' => $insertedCircle['Circle']['id']]]);
        $this->assertEquals($memberCount, 3);

        // check post
        $post = $this->Circle->PostShareCircle->Post->find('first',
            ['conditions' => ['circle_id' => $insertedCircle['Circle']['id'], 'type' => 7]]);
        $this->assertNotEmpty($post);
    }

    function test_create_private()
    {
        $this->_setModelProperties();
        $circle = [
            'Circle' => [
                'name'        => 'test circle',
                'description' => 'desc',
                'public_flg'  => false
            ]
        ];
        $myUserId = $this->createActiveUser(1);
        $userIds = [$this->createActiveUser(1), $this->createActiveUser(1)];
        $res = $this->CircleService->create($circle, $myUserId, $userIds);
        $this->assertTrue($res);

        $insertedCircle = $this->Circle->findByName($circle['Circle']['name']);
        $this->assertNotEmpty($insertedCircle);

        // check post
        $post = $this->Circle->PostShareCircle->Post->find('first',
            ['conditions' => ['circle_id' => $insertedCircle['Circle']['id'], 'type' => 7]]);
        $this->assertEmpty($post);
    }

    function test_create_error()
    {
        $this->_setModelProperties();
        $circle = [
            'Circle' => [
                'name'        => '',
                'description' => 'desc',
                'public_flg'  => true
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
                'name'        => 'test circle',
                'description' => 'desc',
                'public_flg'  => true
            ]
        ];

        $myUserId = $this->createActiveUser(1);
        $res = $this->CircleService->create($circle, $myUserId);
        $insertedCircle = $this->Circle->findByName($circle['Circle']['name']);
        $circleId = $insertedCircle['Circle']['id'];

        $this->assertTrue($this->CircleService->join($circleId, 2));
        $this->assertNotEmpty($this->CircleMember->find('first',
            ['conditions' => ['circle_id' => $circleId, 'user_id' => 2]]));
    }

    function test_removeCircleMember_success()
    {
        $teamId = 1;
        $circleId = 1;
        $userId = 1;
        $userId2 = 2;
        $this->Team->current_team_id = $teamId;
        $this->Circle->current_team_id = $teamId;
        $this->CircleMember->current_team_id = $teamId;
        $this->CircleMember->my_uid = $userId;
        $this->User->current_team_id = $teamId;
        $circleData = [
            'Circle' => [
                'name'        => 'サークル1',
                'public_flg'  => 1,
                'description' => "説明しちゃうよ",
                'photo'       => [
                    'name'     => '',
                    'type'     => '',
                    'tmp_name' => '',
                    'error'    => 4,
                    'size'     => 0
                ]

            ]

        ];
        // Create cache for testing
        Cache::write($this->CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true), true, 'user_data');
        Cache::write($this->CircleMember->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), true, 'user_data');
        Cache::write($this->CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), true, 'user_data');
        $this->CircleService->create($circleData, $userId, [$userId2]);
        $res = $this->CircleService->removeCircleMember($teamId, $circleId, $userId2);
        $this->assertTrue($res);
        $this->assertFalse($this->CircleMember->isJoined($circleId, $userId2));
        $this->assertTrue($this->CircleMember->isJoined($circleId, $userId));

        $res = Cache::read($this->CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_ALL, true), 'user_data');
        $this->assertFalse($res);
        $res = Cache::read($this->CircleMember->getCacheKey(CACHE_KEY_MY_CIRCLE_LIST, true), 'user_data');
        $this->assertFalse($res);
        $res = Cache::read($this->CircleMember->getCacheKey(CACHE_KEY_CHANNEL_CIRCLES_NOT_HIDE, true), 'user_data');
        $this->assertFalse($res);

        // TODO: Check whether removed saved posts. But sql which removing saved posts is plain sql. so  The SQlite error occurred for not supported sql syntax.
    }

    function test_removeCircleMember_failed()
    {
        $teamId = 1;
        $circleId = 1000;
        $userId = 1;
        $res = $this->CircleService->removeCircleMember($teamId, $circleId, $userId);
        $this->assertFalse($res);
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

    function test_validateJoin()
    {
        $teamId = $this->createTeam();
        $circleId = $this->createCircle(['team_id' => $teamId]);
        $userId = 1;
        $this->setDefaultTeamIdAndUid($userId, $teamId);
        // valid
        $this->assertTrue($this->CircleService->validateJoin($teamId, $circleId, $userId));
        // invalid (circle not belong team)
        $this->assertFalse($this->CircleService->validateJoin($teamId + 1, $circleId, $userId));
        // invalid (circle member already exist)
        $this->CircleMember->save(['team_id' => $teamId, 'circle_id' => $circleId, 'user_id' => $userId]);
        $this->assertFalse($this->CircleService->validateJoin($teamId, $circleId, $userId));
    }

    function test_getMemberCountEachCircle()
    {
        $this->GlRedis->deleteMultiCircleMemberCount([1, 2, 3, 4]);

        /* all cache doesn't exist */
        $expect = [1 => 3];
        $circleIds = array_keys($expect);
        $res = $this->CircleService->getMemberCountEachCircle($circleIds);
        $this->assertEquals($res, $expect);
        // confirm whether cache was saved
        $res = $this->GlRedis->getMultiCircleMemberCount($circleIds);
        $this->assertEquals($res, $expect);

        /* some cache doesn't exist */
        $expect = [1 => 3, 3 => 2, 4 => 2];
        $circleIds = array_keys($expect);
        $res = $this->CircleService->getMemberCountEachCircle($circleIds);
        $this->assertEquals($res, $expect);
        // confirm whether cache was saved
        $res = $this->GlRedis->getMultiCircleMemberCount($circleIds);
        $this->assertEquals($res, $expect);

        /* all cache exist */
        $res = $this->CircleService->getMemberCountEachCircle($circleIds);
        $this->assertEquals($res, $expect);
    }

    public function test_getCircle_success()
    {
        $circleId = 1;

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        $circleRequestResource = new CircleResourceRequest($circleId, 1, 1);
        $circle = $CircleService->get($circleRequestResource);

        $this->assertEquals($circleId, $circle['id']);
        $this->assertNotEmpty($circle['img_url']);
        $this->assertNotEmpty($circle['is_member']);
        $this->assertArrayHasKey('get_notification_flg', $circle);
        $this->assertArrayHasKey('admin_flg', $circle);
    }


}
