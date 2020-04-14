<?php

App::uses('GoalousTestCase', 'Test');
App::import('Service/Paging', 'FeedPostPagingService');
App::uses('PagingRequest', 'Lib/Paging');
App::import('Service', 'TeamMemberService');

class FeedPostPagingServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.team',
        'app.team_member',
        'app.user',
        'app.post',
        'app.comment',
        'app.local_name',
        'app.post_like',
        'app.post_read',
        'app.saved_post',
        'app.comment_like',
        'app.attached_file',
        'app.post_file',
        'app.comment_file',
        'app.team_translation_status',
        'app.team_translation_language',
        'app.mst_translation_language',
        'app.translation',
        'app.post_resource',
        'app.goal',
        'app.goal_member',
        'app.action_result',
        'app.action_result_file',
        'app.key_result',
        'app.kr_progress_log',
    ];

    public function test_getActionPost_success()
    {
        /** @var FeedPostPagingService $FeedPostPagingService */
        $FeedPostPagingService = ClassRegistry::init('FeedPostPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $result = $FeedPostPagingService->getDataWithPaging($pagingRequest, 10);

        $this->assertCount(2, $result['data']);

        //Loop since not all post has circle_id
        foreach ($result['data'] as $post) {
            $this->assertNotEmpty($post['type']);
            $this->assertTrue(in_array($post['type'], [Post::TYPE_CREATE_GOAL, Post::TYPE_ACTION]));
            $this->assertNotEmpty($post['data']);
        }
    }

    public function test_getActionPostWithPaging_success()
    {
        /** @var FeedPostPagingService $FeedPostPagingService */
        $FeedPostPagingService = ClassRegistry::init('FeedPostPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $result = $FeedPostPagingService->getDataWithPaging($pagingRequest, 1);
        $this->assertNotEmpty($result['cursor']);
        $this->assertNotEmpty($result['count']);

        $pagingRequest = PagingRequest::decodeCursorToObject($result['cursor']);
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $secondResult = $FeedPostPagingService->getDataWithPaging($pagingRequest, 2);

        $this->assertCount(1, $secondResult['data']);
        $this->assertEmpty($secondResult['cursor']);
        $this->assertNotEmpty($secondResult['count']);

        foreach ($secondResult['data'] as $post) {
            $this->assertNotEmpty($post['type']);
            $this->assertTrue(in_array($post['type'], [Post::TYPE_CREATE_GOAL, Post::TYPE_ACTION]));
            $this->assertNotEmpty($post['data']);
        }
    }

    public function test_getActionPostWithExtension_success()
    {

        /** @var FeedPostPagingService $FeedPostPagingService */
        $FeedPostPagingService = ClassRegistry::init('FeedPostPagingService');

        $pagingRequest = new PagingRequest();
        $pagingRequest->setCurrentTeamId(1);
        $pagingRequest->setCurrentUserId(1);

        $result = $FeedPostPagingService->getDataWithPaging($pagingRequest, 10, [FeedPostExtender::EXTEND_ALL]);

        //Loop since not all post has circle_id
        foreach ($result['data'] as $post) {
            $this->assertNotEmpty($post['type']);
            $this->assertTrue(in_array($post['type'], [Post::TYPE_CREATE_GOAL, Post::TYPE_ACTION]));
            $this->assertNotEmpty($post['data']);
        }
    }

}
