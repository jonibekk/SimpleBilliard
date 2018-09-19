<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CommentLikesPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * User: MartiFloriach
 * Date: 2018/09/10
 * Time: 12:07
 */
class CommentLikesPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.post',
        'app.user',
        'app.team',
        'app.comment_like'
    ];

    public function test_getCommentLikes_success()
    {
        /** @var CommentLikesPagingService $CommentLikesPagingService */
        $CommentLikesPagingService = ClassRegistry::init('CommentLikesPagingService');

        $cursor = new PagingRequest();
        $cursor->setResourceId(123);
        $cursor->setCurrentUserId(1);
        $cursor->setCurrentTeamId(1);

        $result = $CommentLikesPagingService->getDataWithPaging($cursor, 1);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertEqual(2, $result['count']);
    }

    public function test_getCommentLikesWithCursor_success()
    {
        /** @var CommentLikesPagingService $CommentLikesPagingService  */
        $CommentLikesPagingService = ClassRegistry::init('CommentLikesPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(123);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $CommentLikesPagingService->getDataWithPaging($pagingRequest, 1);
        $this->assertNotEmpty($result);
        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(123);
        $pagingRequest->setCurrentTeamId(1);

        $result = $CommentLikesPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEmpty($result['cursor']);
        $this->assertEqual(2, $result['count']);
    }

    public function test_getCommentLikesWithUserInfoExtension_success()
    {
        /** @var CommentLikesPagingService $CommentLikesPagingService  */
        $CommentLikesPagingService = ClassRegistry::init('CommentLikesPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(123);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $CommentLikesPagingService->getDataWithPaging($pagingRequest, 1,
            [CommentLikesPagingService::EXTEND_USER]);

        $data = $result['data'][0]['user'];

        $this->assertCount(1, $result['data']);
        $this->assertEqual('firstname', $data['first_name']);
    }
}
