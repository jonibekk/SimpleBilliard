<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'RecentCircleListPagingService');
App::import('Lib/Paging', 'PagingRequest');

class RecentCircleListPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
    ];

    public function test_getCircleList_success()
    {
        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        /** @var RecentCircleListPagingService $RecentCircleListPagingService */
        $RecentCircleListPagingService = ClassRegistry::init('RecentCircleListPagingService');

        $res = $RecentCircleListPagingService->getDataWithPaging($pagingRequest, 1, [CircleExtender::EXTEND_MEMBER_INFO]);
        $this->assertCount(1, $res['data']);
        $this->assertEquals(4, $res['data'][0]['id']);

        $pagingRequest = PagingRequest::decodeCursorToObject($res['cursor']);
        $pagingRequest->setCurrentTeamId( 1);
        $pagingRequest->setCurrentUserId(1);

        $res = $RecentCircleListPagingService->getDataWithPaging($pagingRequest, 1, [CircleExtender::EXTEND_MEMBER_INFO]);

        $this->assertCount(1, $res['data']);
        $this->assertEquals(3, $res['data'][0]['id']);
        $this->assertNotEmpty($res['cursor']);
    }
}
