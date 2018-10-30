<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CircleMemberPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/08/23
 * Time: 15:08
 */
class CircleMemberPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.experiment',
        'app.post_share_circle',
        'app.team_member',
    ];

    public function test_getCircleMembers_success()
    {
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertNotEmpty($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
        $this->assertNotEmpty($result['data'][0]['user_id']);
        $this->assertNotEmpty($result['data'][0]['last_posted']);
    }

    public function test_getCircleMembersWithCursor_success()
    {
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $cursor = $result['cursor'];

        $nextRequest = PagingRequest::decodeCursorToObject($cursor);
        $nextRequest->setCurrentTeamId(1);
        $nextRequest->setCurrentUserId(1);
        $nextRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($nextRequest, 1);

        $this->assertNotEmpty($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
        $this->assertNotEmpty($result['data'][0]['user_id']);
        $this->assertNotEmpty($result['data'][0]['last_posted']);
    }

    public function test_getCircleMembersWithUserExtension_success()
    {
        /** @var CircleMemberPagingService $CircleMemberPagingService */
        $CircleMemberPagingService = ClassRegistry::init('CircleMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->setResourceId(1);

        $result = $CircleMemberPagingService->getDataWithPaging($pagingRequest, 1,
            CircleMemberPagingService::EXTEND_USER);

        $this->assertNotEmpty($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
        $this->assertNotEmpty($result['data'][0]['user_id']);
        $this->assertNotEmpty($result['data'][0]['last_posted']);
        $this->assertNotEmpty($result['data'][0]['user']);
        $this->assertNotEmpty($result['data'][0]['user']['id']);
    }
}
