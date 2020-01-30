<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'UnreadCircleListPagingService');
App::import('Lib/Paging', 'PagingRequest');

class UnreadCircleListPagingServiceTest extends GoalousTestCase
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

        /** @var UnreadCircleListPagingService $UnreadCircleListPagingService */
        $UnreadCircleListPagingService = ClassRegistry::init('UnreadCircleListPagingService');

        $res = $UnreadCircleListPagingService->getDataWithPaging($pagingRequest, 1, [CircleExtender::EXTEND_MEMBER_INFO]);
        $this->assertCount(1, $res['data']);
        $this->assertEquals(2, $res['data'][0]['id']);

        $pagingRequest = PagingRequest::decodeCursorToObject($res['cursor']);
        $pagingRequest->setCurrentTeamId( 1);
        $pagingRequest->setCurrentUserId(1);

        $res = $UnreadCircleListPagingService->getDataWithPaging($pagingRequest, 1, [CircleExtender::EXTEND_MEMBER_INFO]);

        $this->assertCount(1, $res['data']);
        $this->assertEquals(1, $res['data'][0]['id']);
        $this->assertEmpty($res['cursor']);
    }
}
