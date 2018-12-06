<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'TeamMemberPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * Created by PhpStorm.
 * User: stephen
 * Date: 18/11/29
 * Time: 11:32
 */
class TeamMemberPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.team_member',
        'app.local_name',
        'app.user',
        'app.experiment',
    ];

    public function test_getMemberList_success()
    {
        /** @var TeamMemberPagingService $TeamMemberPagingService */
        $TeamMemberPagingService = ClassRegistry::init('TeamMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->addCondition(["keyword" => 'f']);
        $pagingRequest->addCondition(["lang" => 'jpn']);

        $result = $TeamMemberPagingService->getDataWithPaging($pagingRequest, 2);

        $this->assertCount(2, $result['data']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }

    public function test_getMemberListWithCursor_success()
    {
        /** @var TeamMemberPagingService $TeamMemberPagingService */
        $TeamMemberPagingService = ClassRegistry::init('TeamMemberPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);
        $pagingRequest->addCondition(["keyword" => 'f']);
        $pagingRequest->addCondition(["lang" => 'jpn']);

        $result = $TeamMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertNotEmpty($result['cursor']);
        $this->assertEquals(13, $result['data'][0]['id']);

        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $result = $TeamMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEquals(4, $result['data'][0]['id']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);

        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $result = $TeamMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEquals(3, $result['data'][0]['id']);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);

        $cursor = $result['cursor'];

        $pagingRequest = PagingRequest::decodeCursorToObject($cursor);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $result = $TeamMemberPagingService->getDataWithPaging($pagingRequest, 1);

        $this->assertCount(1, $result['data']);
        $this->assertEquals(2, $result['data'][0]['id']);
        $this->assertEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);
    }


}