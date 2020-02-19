<?php

App::uses('GoalousTestCase', 'Test');
App::uses('UnreadCirclePost', 'Model');
App::import('Service', 'UnreadCirclePostService');

class UnreadCirclePostServiceTest extends GoalousTestCase
{
    public $fixtures = [
        'app.cache_unread_circle_post',
        'app.circle_member',
        'app.team',
        'app.team_member',
        'app.user'
    ];

    public function test_addUnread_success()
    {
        $teamId = 1;
        $postId = 1;
        $circleId = 1;
        $authorUserId = 1;

        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');

        $UnreadCirclePostService->addUnread($teamId, $circleId, $postId, $authorUserId);

        $count = $UnreadCirclePost->countPostUnread($circleId, $postId);
        $this->assertEquals(2, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle($circleId, $authorUserId);
        $this->assertEquals(0, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle($circleId, 2);
        $this->assertEquals(1, $count);
    }

    public function test_deleteUserCacheInTeam_success()
    {
        $teamId = 1;
        $postId = 1;
        $circleId = 1;
        $authorUserId = 1;

        /** @var UnreadCirclePost $UnreadCirclePost */
        $UnreadCirclePost = ClassRegistry::init('UnreadCirclePost');

        /** @var UnreadCirclePostService $UnreadCirclePostService */
        $UnreadCirclePostService = ClassRegistry::init('UnreadCirclePostService');

        $UnreadCirclePostService->addUnread($teamId, $circleId, $postId, $authorUserId);

        $UnreadCirclePostService->deleteUserCacheInTeam($teamId, 12);

        $count = $UnreadCirclePost->countUserUnreadInCircle($circleId, 2);
        $this->assertEquals(1, $count);

        $count = $UnreadCirclePost->countUserUnreadInCircle($circleId, 12);
        $this->assertEquals(0, $count);
    }

}
