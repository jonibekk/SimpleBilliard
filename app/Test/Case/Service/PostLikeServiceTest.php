<?php
App::import('Service', 'PostLikeService');
App::uses('PostLike', 'Model');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/20
 * Time: 12:29
 */
class PostLikeServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_like',
        'app.post',
        'app.user',
        'app.team',
        'app.local_name',
        'app.post_share_circle'
    );

    public function test_addLike_success()
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');
        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        $postId = 2;

        $initialCount = $PostLike->updateLikeCount($postId);

        try {
            $PostLikeService->addPostLike($postId, 1, 1);
            $this->assertEquals(++$initialCount, $PostLike->updateLikeCount($postId));

            $PostLikeService->addPostLike($postId, 2, 1);
            $this->assertEquals(++$initialCount, $PostLike->updateLikeCount($postId));

            $PostLikeService->addPostLike($postId, 1, 1);
            $this->assertEquals($initialCount, $PostLike->updateLikeCount($postId));
        } catch (Exception $e) {
            $this->fail();
        }

    }

    public function test_deleteLike_success()
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');
        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        $postId = 3;

        $initialCount = $PostLike->updateLikeCount($postId);

        try {
            $PostLikeService->addPostLike($postId, 1, 1);
            $PostLikeService->addPostLike($postId, 2, 1);

            $PostLikeService->deletePostLike($postId, 1);
            $this->assertEquals(++$initialCount, $PostLike->updateLikeCount($postId));

            $PostLikeService->deletePostLike($postId, 1);
            $this->assertEquals($initialCount, $PostLike->updateLikeCount($postId));
        } catch (Exception $e) {
            $this->fail();
        }
    }
}