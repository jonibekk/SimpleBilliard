<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'CommentPagingService');
App::import('Lib/Paging', 'PagingRequest');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/13
 * Time: 12:11
 */
class CommentPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.user',
        'app.post',
        'app.comment',
        'app.local_name',
        'app.experiment',
        'app.post_like',
        'app.comment_like',
        'app.attached_file',
        'app.post_file',
        'app.comment_file',
        'app.saved_post',
    ];

    public function test_getComments_success()
    {
        /** @var CommentPagingService $CommentPagingService */
        $CommentPagingService = ClassRegistry::init("CommentPagingService");

        $request = new PagingRequest();
        $request->addResource('res_id', 1);
        $request->addResource('current_user_id', 1);
        $request->addResource('current_team_id', 1);

        $result = $CommentPagingService->getDataWithPaging($request, 1);

        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result['paging']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals(15, $result['data'][0]['id']);
    }

    public function test_getCommentsByCursor_success()
    {
        /** @var CommentPagingService $CommentPagingService */
        $CommentPagingService = ClassRegistry::init("CommentPagingService");

        $request = new PagingRequest();
        $request->addResource('res_id', 1);
        $request->addResource('current_user_id', 1);
        $request->addResource('current_team_id', 1);

        $result = $CommentPagingService->getDataWithPaging($request, 1);

        $this->assertNotEmpty($result);

        $cursor = $result['paging'];

        $request1 = PagingRequest::decodeCursorToObject($cursor);
        $request1->addResource('res_id', 1);
        $request1->addResource('current_user_id', 1);
        $request1->addResource('current_team_id', 1);

        $result1 = $CommentPagingService->getDataWithPaging($request1, 1);

        $this->assertNotEmpty($result1);
        $this->assertEmpty($result1['paging']);
        $this->assertCount(1, $result1['data']);
        $this->assertEquals(16, $result1['data'][0]['id']);
    }

    public function test_getCommentWithUserExtension_success()
    {
        /** @var CommentPagingService $CommentPagingService */
        $CommentPagingService = ClassRegistry::init("CommentPagingService");

        $request = new PagingRequest();
        $request->addResource('res_id', 1);
        $request->addResource('current_user_id', 1);
        $request->addResource('current_team_id', 1);

        $result = $CommentPagingService->getDataWithPaging($request, 1, CommentPagingService::EXTEND_USER);

        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result['paging']);
        $this->assertCount(1, $result['data']);
        $this->assertNotEmpty($result['data'][0]['user']);
        $this->assertNotEmpty($result['data'][0]['user']['id']);
    }

}