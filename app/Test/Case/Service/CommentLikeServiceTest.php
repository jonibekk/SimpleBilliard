<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CommentLike', 'Model');
App::import('Service', 'CommentLikeService');
App::import('Model/Entity', 'CommentLikeEntity');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/07/23
 * Time: 10:45
 */
class CommentLikeServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment',
        'app.post',
        'app.user',
        'app.team',
        'app.comment_like',
        'app.local_name',
    );

    public function test_addCommentLike_success()
    {
        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init("CommentLike");

        /** @var CommentLikeService $CommentLikeService */
        $CommentLikeService = ClassRegistry::init("CommentLikeService");

        $commentId = 1;

        $initialCount = $CommentLike->updateCommentLikeCount(1);

        $CommentLikeService->add($commentId, 1, 1);
        $this->assertEquals(++$initialCount, $CommentLike->updateCommentLikeCount($commentId));

        $CommentLikeService->add($commentId, 2, 1);
        $this->assertEquals(++$initialCount, $CommentLike->updateCommentLikeCount($commentId));

        $CommentLikeService->add($commentId, 1, 1);
        $this->assertEquals($initialCount, $CommentLike->updateCommentLikeCount($commentId));
    }

    public function test_removeCommentLike_success()
    {
        /** @var CommentLike $CommentLike */
        $CommentLike = ClassRegistry::init("CommentLike");

        /** @var CommentLikeService $CommentLikeService */
        $CommentLikeService = ClassRegistry::init("CommentLikeService");

        $commentId = 1;

        $initialCount = $CommentLike->updateCommentLikeCount(1);

        $CommentLikeService->add($commentId, 1, 1);
        $CommentLikeService->add($commentId, 2, 1);

        $CommentLikeService->delete($commentId, 1, 1);
        $this->assertEquals(++$initialCount, $CommentLike->updateCommentLikeCount($commentId));

        $CommentLikeService->delete($commentId, 1, 1);
        $this->assertEquals($initialCount, $CommentLike->updateCommentLikeCount($commentId));

    }
}