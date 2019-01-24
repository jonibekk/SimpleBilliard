<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'CommentReadService');
App::uses('CommentRead', 'Model');
App::uses('Comment', 'Model');

/**
 * User: Marti Floriach
 * Date: 2018/09/19
 */
class CommentReadServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment_read',
        'app.comment',
        'app.user',
        'app.team',
        'app.local_name',
    );

    public function test_multipleadd_success()
    {
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        /** @var CommentReadService $CommentReadService */
        $CommentReadService = ClassRegistry::init('CommentReadService');

        $CommentsIds = ["4","5"];

        $res = $CommentReadService->multipleAdd($CommentsIds, 1, 1);
        $this->assertEquals($CommentsIds, $res);

        $res = $CommentRead->countCommentReaders((int)$CommentsIds[0]);

        /** Already two readers in the fixtures*/
        $this->assertEqual(1, $res);
    }

    public function test_multipleadd_JustOneNewReadComment_success()
    {
        /** @var CommentRead $CommentRead */
        $CommentRead = ClassRegistry::init('CommentRead');

        /** @var CommentReadService $CommentReadService */
        $CommentReadService = ClassRegistry::init('CommentReadService');

        $CommentsIds = ["4"];
        $res = $CommentReadService->multipleAdd($CommentsIds, 1, 1);

        $CommentsIds = ["4", "5"];
        $res = $CommentReadService->multipleAdd($CommentsIds, 1, 1);
        $this->assertEquals(["1"=>"5"], $res);
		$res = $CommentRead->countCommentReaders((int)$CommentsIds[0]);

		/** Already two readers in the fixtures*/
		$this->assertEqual(1, $res);
    }
  
}
