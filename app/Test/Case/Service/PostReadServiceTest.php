<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostReadService');
App::uses('PostRead', 'Model');

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

    public function test_addReadPost_success()
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        $postIds = ["1","2"];

        $res = $PostReadService->multipleAdd($postIds, 1, 1);

        $this->assertEquals(["1","2"], $res);
    }

    public function test_addJustOneNewReadPost_success()
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        $postIds = ["1"];
        $res = $PostReadService->multipleAdd($postIds, 1, 1);

        $postIds = ["1", "2"];
        $res = $PostReadService->multipleAdd($postIds, 1, 1);

        $this->assertEquals(["2"], $res);
    }
}