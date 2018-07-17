<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CircleListPagingService');
App::import('Lib/Paging', 'PagingRequest');

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

        $pagingRequest = new PagingRequest();
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->addResource('current_team_id', 1);
        $pagingRequest->addOrder('latest_post_created');

        $result = $CircleListPagingService->getDataWithPaging($pagingRequest, 2);

        $this->assertCount(2, $result['data']);
        $this->assertNotEmpty($result['paging']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCircleListWithCursor_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->addResource('res_id', 1);
        $pagingRequest->addResource('current_team_id', 1);
        $pagingRequest->addOrder('latest_post_created');

        $result = $CircleListPagingService->getDataWithPaging($pagingRequest, 1);

        $pagingRequest = PagingRequest::decodeCursorToObject($result['paging']);
        $pagingRequest->addResource('current_team_id', 1);
        $pagingRequest->addResource('res_id', 1);

        $result = $CircleListPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['paging']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getCircleListWithMemberInfoExtension_success()
    {
        /** @var CircleListPagingService $CircleListPagingService */
        $CircleListPagingService = ClassRegistry::init('CircleListPagingService');

        $cursor = new PagingRequest();
        $cursor->addResource('current_team_id', 1);
        $cursor->addResource('res_id', 1);
        $cursor->addOrder('latest_post_created');

        $result = $CircleListPagingService->getDataWithPaging($cursor, 1,
            [CircleListPagingService::EXTEND_MEMBER_INFO]);

        $data = $result['data'][0];
        
        $this->assertInternalType('int', $data['unread_count']);
        $this->assertInternalType('bool', $data['admin_flg']);
    }
}