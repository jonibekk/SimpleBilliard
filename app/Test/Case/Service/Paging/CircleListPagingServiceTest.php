<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Lib/Paging', 'PagingRequest');

class CircleListPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.circle_pin',
        'app.experiment',
        'app.post_share_circle'
    ];

    public function test_getCircleList_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(13);

        $result = $CircleListPagingService->getAllData($pagingRequest);

        $this->assertCount(3, $result['data']);
        $this->assertEmpty($result['cursor']);
        $this->assertEquals($result['count'], 3);
    }

//    public function test_getCircleListWithCursor_success()
//    {
//        /** @var CircleListPagingService $CircleListPagingService */
//        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');
//
//        $pagingRequest = new PagingRequest();
//        $pagingRequest->setCurrentUserId(13);
//        $pagingRequest->setCurrentTeamId(1);
//        $pagingRequest->addOrder('latest_post_created');
//
//        $result = $CircleListPagingService->getDataWithPaging($pagingRequest, 1);
//
//        $pagingRequest = PagingRequest::decodeCursorToObject($result['cursor']);
//        $pagingRequest->setCurrentTeamId( 1);
//        $pagingRequest->setCurrentUserId(13);
//
//        $result = $CircleListPagingService->getDataWithPaging($pagingRequest, 1);
//
//        $this->assertCount(1, $result['data']);
//        $this->assertNotEmpty($result['cursor']);
//        $this->assertNotEmpty($result['count']);
//    }
//
//    public function test_getCircleListWithMemberInfoExtension_success()
//    {
//        /** @var CircleListPagingService $CircleListPagingService */
//        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');
//
//        $cursor = new PagingRequest();
//        $cursor->setCurrentTeamId(1);
//        $cursor->setCurrentUserId(13);
//        $cursor->addOrder('latest_post_created');
//
//        $result = $CircleListPagingService->getDataWithPaging($cursor, 1,
//            [CircleExtender::EXTEND_MEMBER_INFO]);
//
//        $data = $result['data'][0];
//        $this->assertInternalType('int', $data['unread_count']);
//        $this->assertInternalType('bool', $data['admin_flg']);
//    }
//
//
//    public function test_getCircleListWithIsMemberExtension()
//    {
//        /* All joined(Default) */
//        /** @var CircleListPagingService $CircleListPagingService */
//        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');
//
//        $cursor = new PagingRequest();
//        $cursor->setCurrentTeamId(1);
//        $cursor->setCurrentUserId(13);
//        $cursor->addOrder('latest_post_created');
//
//        $result = $CircleListPagingService->getDataWithPaging($cursor, 2,
//            [CircleExtender::EXTEND_IS_MEMBER]);
//
//        $data = $result['data'];
//        $this->assertTrue($data[0]['is_member']);
//        $this->assertTrue($data[1]['is_member']);
//
//        /* All joined(Specified) */
//        $cursor = new PagingRequest();
//        $cursor->setCurrentTeamId(1);
//        $cursor->setCurrentUserId(13);
//        $cursor->addOrder('latest_post_created');
//        $cursor->addCondition(['joined' => true]);
//
//        $result = $CircleListPagingService->getDataWithPaging($cursor, 2,
//            [CircleExtender::EXTEND_IS_MEMBER]);
//
//        $data = $result['data'];
//        $this->assertTrue($data[0]['is_member']);
//        $this->assertTrue($data[1]['is_member']);
//
//        /* All not joined(Specified) */
//        $cursor = new PagingRequest();
//        $cursor->setCurrentTeamId(1);
//        $cursor->setCurrentUserId(13);
//        $cursor->addOrder('latest_post_created');
//        $cursor->addCondition(['joined' => false]);
//
//        $result = $CircleListPagingService->getDataWithPaging($cursor, 2,
//            [CircleExtender::EXTEND_IS_MEMBER]);
//
//        $data = $result['data'];
//        $this->assertFalse($data[0]['is_member']);
//        $this->assertFalse($data[1]['is_member']);
//    }
//
//    /**
//     * Currently, if user is getting joined circles, it will skip pinned circles.
//     * All of user 1's joined circles are pinned
//     */
//    public function test_getCircleListAllPinned_success()
//    {
//        /** @var CircleListPagingService $CircleListPagingService */
//        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');
//
//        $pagingRequest = new PagingRequest();
//        $pagingRequest->setCurrentTeamId(1);
//        $pagingRequest->setCurrentUserId(1);
//
//        $result = $CircleListPagingService->getDataWithPaging($pagingRequest, 2);
//
//        $this->assertCount(0, $result['data']);
//        $this->assertEmpty($result['cursor']);
//        $this->assertEquals(0, $result['count']);
//    }
}
