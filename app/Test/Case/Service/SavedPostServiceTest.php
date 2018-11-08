<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'SavedPostService');
App::uses('PostLike', 'Model');

/**
 * User: Marti Floriach
 * Date: 2018/09/14
 */
class SavedPostServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.saved_post',
        'app.post',
        'app.user',
        'app.team',
        'app.local_name',
        'app.post_share_circle'
    );

    public function test_savedPost_success()
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        $postId = 2;

        $SavedPostService->add($postId, 1, 1);
        $result = $SavedPost->getUserSavedPost($postId, 1);
        $this->assertEquals(2, $result[0]["post_id"]);

        $SavedPostService->add($postId, 2, 1);
        $result = $SavedPost->getUserSavedPost($postId, 2);
        $this->assertEquals(2, $result[0]["post_id"]);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousConflictException
     */
    public function test_savedPostRepeat_failure()
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        $postId = 2;

        $SavedPostService->add($postId, 1, 1);

        /** GoalousConflictException */
        $SavedPostService->add($postId, 1, 1);
    }

    public function test_deleteSavedPost_success()
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        $postId = 3;

        $SavedPostService->add($postId, 1, 1);

        $SavedPostService->delete($postId, 1);
        $result = $SavedPost->getUserSavedPost($postId, 1);
        $this->assertCount(0, $result);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_deleteSavedPostNotSaved_failure()
    {
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        $postId = 3;

        $SavedPostService->add($postId, 1, 1);

        $SavedPostService->delete($postId, 1);
        /** GoalousConflictException */
        $SavedPostService->delete($postId, 1);
    }

    public function test_deleteAllInCircle_success()
    {
        $userId = 1;
        $teamId = 1;
        $circleId = 3;

        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');

        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        $newData = [
            'post_id' => 5,
            'user_id' => 1,
            'team_id' => 1
        ];
        $newData2 = [
            'post_id' => 6,
            'user_id' => 1,
            'team_id' => 1
        ];

        $SavedPost->create();
        $SavedPost->save($newData);
        $SavedPost->create();
        $SavedPost->save($newData2);

        $this->assertCount(2, $SavedPostService->findAllInCircle($userId, $teamId, $circleId));
        $SavedPostService->deleteAllInCircle($userId, $teamId, $circleId);
        $this->assertEmpty($SavedPostService->findAllInCircle($userId, $teamId, $circleId));
    }
}
