<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'PostLikesPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * User: MartiFloriach
 * Date: 2018/09/10
 * Time: 12:07
 */
class PostLikesPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.post',
        'app.user',
        'app.team',
        'app.post_like'
    ];

    public function test_getPostLikes_success()
    {
        /** @var PostLikesPagingService $PostLikesPagingService */
        $PostLikesPagingService = ClassRegistry::init('PostLikesPagingService');

        $cursor = new PagingRequest();
        $cursor->setResourceId(2);
        $cursor->setCurrentUserId(1);
        $cursor->setCurrentTeamId(1);

        $result = $PostLikesPagingService->getDataWithPaging($cursor, 1);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getPostLikesWithCursor_success()
    {
        /** @var PostLikesPagingService $PostLikesPagingService  */
        $PostLikesPagingService = ClassRegistry::init('PostLikesPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(2);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $PostLikesPagingService->getDataWithPaging($pagingRequest, 1);
        $this->assertNotEmpty($result);
        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(2);
        $pagingRequest->setCurrentTeamId(1);

        $result = $PostLikesPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getPostLikesWithUserInfoExtension_success()
    {
        /** @var PostLikesPagingService $PostLikesPagingService  */
        $PostLikesPagingService = ClassRegistry::init('PostLikesPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setResourceId(2);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setCurrentTeamId(1);

        $result = $PostLikesPagingService->getDataWithPaging($pagingRequest, 1,
            [PostLikesPagingService::EXTEND_USER]);

        $data = $result['data'][0]['user'];

        $this->assertCount(1, $result['data']);
        $this->assertEqual('firstname', $data['first_name']);
    }
}
