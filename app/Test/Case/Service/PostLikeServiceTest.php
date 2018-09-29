<?php
App::uses('GoalousTestCase', 'Test');
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

        $PostLikeService->add($postId, 1, 1);
        $this->assertEquals(++$initialCount, $PostLike->updateLikeCount($postId));

        $PostLikeService->add($postId, 2, 1);
        $this->assertEquals(++$initialCount, $PostLike->updateLikeCount($postId));
    }

    /**
     * @expectedException \Goalous\Exception\GoalousConflictException
     */
    public function test_addLikeMany_failure()
    {
        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        $postId = 3;

        $PostLikeService->add($postId, 1, 1);
        $PostLikeService->add($postId, 1, 1);
    }

    public function test_deleteLike_success()
    {
        /** @var PostLike $PostLike */
        $PostLike = ClassRegistry::init('PostLike');
        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        $postId = 3;

        $initialCount = $PostLike->updateLikeCount($postId);

        $PostLikeService->add($postId, 1, 1);
        $PostLikeService->add($postId, 2, 1);

        $PostLikeService->delete($postId, 1);
        $this->assertEquals(++$initialCount, $PostLike->updateLikeCount($postId));
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_deleteLikeNotExist_failure()
    {
        /** @var PostLikeService $PostLikeService */
        $PostLikeService = ClassRegistry::init('PostLikeService');

        $PostLikeService->delete(9090909, 1, 1);
    }
}