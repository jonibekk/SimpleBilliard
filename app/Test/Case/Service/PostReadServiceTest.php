<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostReadService');
App::uses('PostRead', 'Model');
App::uses('Post', 'Model');

/**
 * User: Marti Floriach
 * Date: 2018/09/19
 */
class PostReadServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_read',
        'app.post',
        'app.user',
        'app.team',
        'app.local_name',
        'app.post_share_circle'
    );

    public function test_multipleadd_success()
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        $postsIds = ["1","2"];

        $res = $PostReadService->multipleAdd($postsIds, 1, 1);
        $this->assertEquals($postsIds, $res);

        $res = $PostRead->countPostReaders((int)$postsIds[0]);

        /** Already two readers in the fixtures*/
        $this->assertEqual(3, $res);
    }

    public function test_multipleadd_JustOneNewReadPost_success()
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        $postsIds = ["2"];
        $res = $PostReadService->multipleAdd($postsIds, 1, 1);

        $postsIds = ["1", "2"];

        $res = $PostReadService->multipleAdd($postsIds, 1, 1);
        $this->assertEquals(["1"], $res);
		$res = $PostRead->countPostReaders((int)$postsIds[0]);

		/** Already two readers in the fixtures*/
		$this->assertEqual(3, $res);
    }
  
}
