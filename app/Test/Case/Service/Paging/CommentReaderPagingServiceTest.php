<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CommentReaderPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * User: MartiFloriach
 * Date: 2018/09/05
 * Time: 12:07
 */
class CommentReaderPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.comment',
        'app.user',
        'app.team',
        'app.comment_read'
    ];

    public function test_getCommentReader_success()
    {
        /** @var CommentReaderPagingService $CommentReaderPagingService */
        $CommentReaderPagingService = ClassRegistry::init('CommentReaderPagingService');

        $cursor = new PagingRequest();
        $cursor->setResourceId(1);
        $cursor->setCurrentUserId(1);
        $cursor->setCurrentTeamId(1);

        $result = $CommentReaderPagingService->getDataWithPaging($cursor, 1);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCommentReaderWithCursor_success()
    {
        /** @var CommentReaderPagingService $CommentReaderPagingService  */
        $CommentReaderPagingService = ClassRegistry::init('CommentReaderPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(1);
        $pagingRequest->setCurrentUserId(2);
        $pagingRequest->setCurrentTeamId(1);

        $result = $CommentReaderPagingService->getDataWithPaging($pagingRequest, 1);
        $this->assertNotEmpty($result);
        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $CommentReaderPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCommentReadertWithUserInfoExtension_success()
    {
        /** @var CommentReaderPagingService $CommentReaderPagingService  */
        $CommentReaderPagingService = ClassRegistry::init('CommentReaderPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $CommentReaderPagingService->getDataWithPaging($pagingRequest, 1,
            [CommentReaderPagingService::EXTEND_USER]);

        $data = $result['data'][0]['user'];

        $this->assertCount(1, $result['data']);
        $this->assertEqual('firstname', $data['first_name']);
    }
}
