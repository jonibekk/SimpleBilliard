<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'PostReadService');
App::uses('PostRead', 'Model');
App::uses('Post', 'Model');
App::uses('CircleMember', 'Model');

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
        'app.post_share_circle',
        'app.circle',
        'app.circle_member'
    );

    public function test_multipleadd_success()
    {
        $userId = 1;
        $teamId = 1;
        $circleId = 1;

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        $unreadCount = $CircleMember->getUnreadCount($circleId, $userId);

        $postsIds = ["1", "2"];

        $res = $PostReadService->multipleAdd($postsIds, $userId, $teamId);
        $this->assertEquals($postsIds, $res);

        $res = $PostRead->countPostReaders((int)$postsIds[0]);

        /** Already two readers in the fixtures*/
        $this->assertEquals(3, $res);

        $newUnreadCount = $CircleMember->getUnreadCount($circleId, $userId);

        $this->assertNotEquals($newUnreadCount, $unreadCount);
    }

    public function test_multipleadd_JustOneNewReadPost_success()
    {
        $userId = 1;
        $teamId = 1;
        $circleId = 1;

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');
        /** @var PostReadService $PostReadService */
        $PostReadService = ClassRegistry::init('PostReadService');

        $unreadCount = $CircleMember->getUnreadCount($circleId, $userId);

        $postsIds = ["2"];
        $PostReadService->multipleAdd($postsIds, $userId, $teamId);

        $postsIds = ["1", "2"];

        $res = $PostReadService->multipleAdd($postsIds, $userId, $teamId);
        $this->assertEquals(["1"], $res);
        $res = $PostRead->countPostReaders((int)$postsIds[0]);

        /** Already two readers in the fixtures*/
        $this->assertEquals(3, $res);

        $newUnreadCount = $CircleMember->getUnreadCount($circleId, $userId);

        $this->assertNotEquals($newUnreadCount, $unreadCount);
    }

}
