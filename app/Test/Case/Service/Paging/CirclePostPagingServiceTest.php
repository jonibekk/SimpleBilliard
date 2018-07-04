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
class CirclePostPagingTest extends GoalousTestCase
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

        $result = $CircleFeedPaging->getDataWithPaging($cursor, 1);

        $pagingCursor = PagingCursor::decodeCursorToObject($result['paging']['next']);

        $secondResult = $CircleFeedPaging->getDataWithPaging($pagingCursor, 2);

        $this->assertCount(2, $secondResult['data']);
        $this->assertNotEmpty($secondResult['paging']['next']);
        $this->assertNotEmpty($secondResult['count']);
    }

}