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
     * @expectedException \Goalous\Exception\GoalousConflictException
     */
    public function test_deleteSavedPostNotSaved_failure()
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init('SavedPost');
        /** @var SavedPostService $SavedPostService */
        $SavedPostService = ClassRegistry::init('SavedPostService');

        $postId = 3;

        $SavedPostService->add($postId, 1, 1);

        $SavedPostService->delete($postId, 1);
        /** GoalousConflictException */
        $SavedPostService->delete($postId, 1);
    }
}
