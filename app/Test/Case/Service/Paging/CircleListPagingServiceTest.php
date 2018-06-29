<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Lib/Paging', 'PagingCursor');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/28
 * Time: 12:07
 */
class CircleListPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.experiment',
        'app.post_share_circle'
    ];

    public function test_getCircleList_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $cursor = new PagingCursor(['team_id' => 1, 'user_id' => 1]);

        $result = $CircleListPagingService->getDataWithPaging($cursor, 2);

        $this->assertCount(2, $result['data']);
        $this->assertNotEmpty($result['paging']['next']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCircleListWithCursor_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $cursor = new PagingCursor(['team_id' => 1, 'user_id' => 1]);
        $cursor->addOrder('latest_post_created');

        $result = $CircleListPagingService->getDataWithPaging($cursor, 1);

        $pagingCursor = PagingCursor::decodeCursorToObject($result['paging']['next']);

        $result = $CircleListPagingService->getDataWithPaging($pagingCursor, 1);

        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['paging']['next']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCircleListWithMemberInfoExtension_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $cursor = new PagingCursor(['team_id' => 1, 'user_id' => 1]);
        $cursor->addOrder('latest_post_created');

        $result = $CircleListPagingService->getDataWithPaging($cursor, 1,
            [CircleListPagingService::EXTEND_MEMBER_INFO]);

        $data = $result['data'][0];
        
        $this->assertInternalType('int', $data['unread_count']);
        $this->assertInternalType('bool', $data['admin_flg']);
    }
}