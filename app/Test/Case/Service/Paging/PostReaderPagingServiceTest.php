<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'PostReaderPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * User: MartiFloriach
 * Date: 2018/09/05
 * Time: 12:07
 */
class PostReaderPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.post',
        'app.user',
        'app.team',
        'app.post_read'
    ];

    public function test_getPostReader_success()
    {
        /** @var PostReaderPagingService $PostReaderPagingService */
        $PostReaderPagingService = ClassRegistry::init('PostReaderPagingService');

        $cursor = new PagingRequest();
        $cursor->setResourceId(1);
        $cursor->setCurrentUserId(1);
        $cursor->setCurrentTeamId(1);

        $result = $PostReaderPagingService->getDataWithPaging($cursor, 1);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getPostReaderWithCursor_success()
    {
        /** @var PostReaderPagingService $PostReaderPagingService  */
        $PostReaderPagingService = ClassRegistry::init('PostReaderPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(1);
        $pagingRequest->setCurrentUserId(2);
        $pagingRequest->setCurrentTeamId(1);

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1);
        $this->assertNotEmpty($result);
        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getPostReadertWithUserInfoExtension_success()
    {
        /** @var PostReaderPagingService $PostReaderPagingService  */
        $PostReaderPagingService = ClassRegistry::init('PostReaderPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1,
            [PostReaderPagingService::EXTEND_USER]);

        $data = $result['data'][0]['user'];

        $this->assertCount(1, $result['data']);
        $this->assertEqual('firstname', $data['first_name']);
    }
}
