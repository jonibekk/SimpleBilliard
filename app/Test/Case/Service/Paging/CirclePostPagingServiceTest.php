<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CirclePostPagingService');
App::uses('PagingCursor', 'Lib/Paging');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/20
 * Time: 11:24
 */
class CirclePostPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.team_member',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.circle_pin',
        'app.post',
        'app.post_share_circle',
        'app.post_share_user',
        'app.comment',
        'app.local_name',
        'app.experiment',
        'app.post_like',
        'app.saved_post',
    ];

    public function test_getCirclePost_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();

        $cursor = new PagingCursor(['circle_id' => 1]);

        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1);

        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['paging']['next']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCirclePostWithCursor_success()
    {
        /** @var CirclePostPagingService $CircleFeedPaging */
        $CircleFeedPaging = new CirclePostPagingService();

        $cursor = new PagingCursor(['circle_id' => 1]);
        $cursor->addOrder('id');

        $result = $CircleFeedPaging->getDataWithPaging($cursor, 1);

        $pagingCursor = PagingCursor::decodeCursorToObject($result['paging']['next']);

        $secondResult = $CircleFeedPaging->getDataWithPaging($pagingCursor, 2);

        $this->assertCount(2, $secondResult['data']);
        $this->assertNotEmpty($secondResult['paging']['next']);
        $this->assertNotEmpty($secondResult['count']);
    }

    public function test_getCirclePostWithUserExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1, 'circle_id' => 1]);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostPagingService::EXTEND_USER);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];

        $this->assertNotEmpty($postData['user']);
    }

    public function test_getCirclePostWithCircleExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1, 'circle_id' => 1]);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostPagingService::EXTEND_CIRCLE);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];

        $this->assertNotEmpty($postData['circle']);
    }

    public function test_getCirclePostWithCommentsExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1, 'circle_id' => 1]);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostPagingService::EXTEND_COMMENT);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];

        $this->assertNotEmpty($postData['comments']);
    }

    public function test_getCirclePostWithPostLikeExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1, 'circle_id' => 1]);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostPagingService::EXTEND_LIKE);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];
        $this->assertInternalType('bool', $postData['is_liked']);
    }

    public function test_getCirclePostWithPostSavedExtension_success()
    {
        /** @var CirclePostPagingService $CirclePostPagingService */
        $CirclePostPagingService = new CirclePostPagingService();
        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1, 'circle_id' => 1]);
        $result = $CirclePostPagingService->getDataWithPaging($cursor, 1, CirclePostPagingService::EXTEND_SAVED);

        $this->assertCount(1, $result['data']);

        $postData = $result['data'][0];
        $this->assertInternalType('bool', $postData['is_saved']);
    }
}