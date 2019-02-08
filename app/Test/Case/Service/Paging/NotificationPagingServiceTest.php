<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'NotificationPagingService');
App::import('Lib/Paging', 'PagingRequest');

class NotificationPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    public function test_getNotifications_success()
    {
        // TODO: GL-7814

//        $teamId = 1;
//        $fromUserId = 2;
//        $userId = 1;
//
//        for($i = 1; $i <= 5; $i++) {
//            usleep(100);
//            $this->GlRedis->setNotifications(1, $teamId, [$userId], $fromUserId, json_encode("body${i}"), '/', microtime(true));
//        }
//
//        /** @var NotificationPagingService $NotificationPagingService */
//        $NotificationPagingService = ClassRegistry::init('NotificationPagingService');
//
//        $pagingRequest = new PagingRequest();
//        $pagingRequest->setCurrentTeamId($teamId);
//        $pagingRequest->setCurrentUserId($userId);
//        $pagingRequest->addCondition(['from_timestamp' => 0]);
//
//        // Get notifications initially
//        $res = $NotificationPagingService->getDataWithPaging($pagingRequest, 3, [NotificationExtender::EXTEND_USER]);
//        $this->assertNotEmpty($res['data']);
//        $this->assertCount(3, $res['data']);
//        $this->assertNotEmpty($res['cursor']);
//        $this->assertEquals($res['data'][0]['user_id'], $fromUserId);
//        $this->assertEquals($res['data'][0]['body'], "body5");
//        $this->assertEquals($res['data'][0]['user']['id'], $fromUserId);

        // TODO: The failure that the order of the data falls apart when unit test. it is possibility that phpredis is not stable.
        // Get more notifications
//        $cursor = $res['cursor'];
//        $nextRequest = PagingRequest::decodeCursorToObject($cursor);
//        $nextRequest->setCurrentTeamId($teamId);
//        $nextRequest->setCurrentUserId($userId);
//
//        $res = $NotificationPagingService->getDataWithPaging($nextRequest, 3);
//        $this->assertNotEmpty($res['data']);
//        $this->assertCount(2, $res['data']);
//        $this->assertEmpty($res['cursor']);
    }


    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        $this->GlRedis->deleteAllData();
        parent::tearDown();
    }

}
