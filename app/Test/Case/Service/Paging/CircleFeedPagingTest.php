<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CircleFeedPaging', 'Service/Paging');
App::uses('PagingCursor', 'Lib/Paging');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/14
 * Time: 16:27
 */
class CircleFeedPagingTest extends GoalousTestCase
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

    public function test_getCircleFeed_success()
    {
        /** @var CircleFeedPaging $CircleFeedPaging */
        $CircleFeedPaging = new CircleFeedPaging();

        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1]);

        $result = $CircleFeedPaging->getDataWithPaging($cursor, 1);

        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['paging']['next']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCircleFeedWithCursor_success()
    {
        /** @var CircleFeedPaging $CircleFeedPaging */
        $CircleFeedPaging = new CircleFeedPaging();

        $cursor = new PagingCursor(['user_id' => 1, 'team_id' => 1]);

        $result = $CircleFeedPaging->getDataWithPaging($cursor, 1);

        $pagingCursor = PagingCursor::decodeCursorToObject($result['paging']['next']);

        $secondResult = $CircleFeedPaging->getDataWithPaging($pagingCursor, 2);

        $this->assertCount(2, $secondResult['data']);
        $this->assertNotEmpty($secondResult['paging']['next']);
        $this->assertNotEmpty($secondResult['count']);
    }
}