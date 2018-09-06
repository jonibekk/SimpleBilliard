<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'PostReaderPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * Created by PhpStorm.
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

        $pagingRequest = new PagingRequest();
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        // $pagingRequest->addOrder('latest_post_created');

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(2, $result['data']);
        $this->assertNotEmpty($result['paging']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getPostReaderWithCursor_success()
    {
        /** @var PostReaderPagingService $PostReaderPagingService */
        $PostReaderPagingService = ClassRegistry::init('PostReaderPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->addOrder('latest_post_created');

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1);
        $this->assertNotEmpty($result);
        $cursor = $result['paging'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->addOrder('latest_post_created');

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['paging']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getPostReadertWithUserInfoExtension_success()
    {
        /** @var PostReaderPagingService $PostReaderPagingService */
        $PostReaderPagingService = ClassRegistry::init('PostReaderPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->addOrder('latest_post_created');

        $result = $PostReaderPagingService->getDataWithPaging($pagingRequest, 1,
            [PostReaderPagingService::EXTEND_USER]);

        $data = $result['data'][0];

        $this->assertInternalType('int', $data['unread_count']);
    }
}
