<?php App::uses('GoalousTestCase', 'Test');
App::uses('PostShareCircle', 'Model');

/**
 * PostShareCircle Test Case
 *
 * @property PostShareCircle $PostShareCircle
 */
class PostShareCircleTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_share_circle',
        'app.team',
        'app.team_member',
        'app.post',
        'app.user',
        'app.circle_member',
        'app.circle',
        'app.post_like',
        'app.comment',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostShareCircle = ClassRegistry::init('PostShareCircle');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostShareCircle);

        parent::tearDown();
    }

    public function testAdd()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->add(1, []);
        $this->PostShareCircle->add(1, [1]);
    }

    public function testGetShareCircleMemberList()
    {
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->getShareCircleMemberList(1);
    }

    public function testIsMyCirclePost()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->Circle->CircleMember->my_uid = 1;
        $this->PostShareCircle->Circle->CircleMember->current_team_id = 1;
        $this->PostShareCircle->isMyCirclePost(1);
    }

    public function testIsShareWithPublicCircle()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $res = $this->PostShareCircle->isShareWithPublicCircle(5);
        $this->assertTrue($res);
        $res = $this->PostShareCircle->isShareWithPublicCircle(7);
        $this->assertFalse($res);
    }

    public function testGetPostCountByCircleId()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;

        $now = time();
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 1, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 2, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 1, 'circle_id' => 2]);
        $count = $this->PostShareCircle->getPostCountByCircleId(1, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals(2, $count);
        $count = $this->PostShareCircle->getPostCountByCircleId(2, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals(1, $count);
    }

    public function testGetTotalPostReadCountByCircleId()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;

        $now = time();
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 1, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 2, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 12, 'circle_id' => 2]);
        $count = $this->PostShareCircle->getTotalPostReadCountByCircleId(1, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals(2, $count);

        $count = $this->PostShareCircle->getTotalPostReadCountByCircleId(2, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals(13, $count);
    }

    public function testGetLikeUserListByCircleId()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->Post->PostLike->my_uid = 1;
        $this->PostShareCircle->Post->PostLike->current_team_id = 1;

        $now = time();
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 100, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 200, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 300, 'circle_id' => 2]);
        $list = $this->PostShareCircle->getLikeUserListByCircleId(1, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals([1 => "1", 2 => "2"], $list);

        $list = $this->PostShareCircle->getLikeUserListByCircleId(2, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals([3 => "3"], $list);

        $list = $this->PostShareCircle->getLikeUserListByCircleId(1, [
            'start'        => $now - HOUR,
            'end'          => $now + HOUR,
            'like_user_id' => [2],
        ]);
        $this->assertEquals([2 => "2"], $list);
    }

    public function testGetCommentUserListByCircleId()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->Post->Comment->my_uid = 1;
        $this->PostShareCircle->Post->Comment->current_team_id = 1;

        $now = time();
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 100, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 200, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 300, 'circle_id' => 2]);
        $list = $this->PostShareCircle->getCommentUserListByCircleId(1, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals([1 => "1", 2 => "2", 3 => "3"], $list);

        $list = $this->PostShareCircle->getCommentUserListByCircleId(2, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals([4 => "4"], $list);

        $list = $this->PostShareCircle->getCommentUserListByCircleId(1, [
            'start'           => $now - HOUR,
            'end'             => $now + HOUR,
            'comment_user_id' => [2, 3],
        ]);
        $this->assertEquals([2 => "2", 3 => "3"], $list);
    }

    public function testGetTotalPostLikeCountByCircleId()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;

        $now = time();
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 1, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 2, 'circle_id' => 1]);
        $this->PostShareCircle->create();
        $this->PostShareCircle->save(['team_id' => 1, 'post_id' => 12, 'circle_id' => 2]);
        $count = $this->PostShareCircle->getTotalPostLikeCountByCircleId(1, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals(2, $count);

        $count = $this->PostShareCircle->getTotalPostLikeCountByCircleId(2, [
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
        ]);
        $this->assertEquals(12, $count);
    }

    public function test_getPostInCircles_success()
    {
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');

        $circleIds = [1, 2, 3];

        $result = $PostShareCircle->getListOfPost($circleIds);

        $this->assertCount(2, $result);
        $this->assertContains(1, $result[1]);
        $this->assertContains(101, $result[1]);
        $this->assertContains(5, $result[3]);
        $this->assertContains(6, $result[3]);
        $this->assertContains(24, $result[3]);

        $this->insertNewData(999, 1, 1);
        $this->insertNewData(999, 2, 1);

        $result = $PostShareCircle->getListOfPost($circleIds);
        $this->assertCount(3, $result);
        $this->assertContains(999, $result[1]);
        $this->assertContains(999, $result[2]);
    }

    public function test_getShareCirclesAndMembers_success()
    {
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        $PostShareCircle->current_team_id = 1;
        $PostShareCircle->Circle->current_team_id = 1;
        $PostShareCircle->Circle->Team->TeamMember->current_team_id = 1;

        $postId = 1;

        $result = $PostShareCircle->getShareCirclesAndMembers($postId);

        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result[0]['Circle']);
        $this->assertNotEmpty($result[0]['CircleMember']);
        $this->assertCount(3, $result[0]['CircleMember']);
        $this->assertNotEmpty($result[0]['CircleMember'][0]['User']);
    }

    public function test_getFirstSharedCircleId_success()
    {
        $circleId = 124;
        $teamId = 125;
        $postId = 9393;

        $this->insertNewData($postId, $circleId, $teamId);

        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');
        $returnedCircleId = $PostShareCircle->getFirstSharedCircleId($postId);

        $this->assertTextEquals($circleId, $returnedCircleId);
    }


    private function insertNewData(int $postId, int $circleId, int $teamId)
    {
        /** @var PostShareCircle $PostShareCircle */
        $PostShareCircle = ClassRegistry::init('PostShareCircle');

        $newData = [
            'post_id'   => $postId,
            'circle_id' => $circleId,
            'team_id'   => $teamId
        ];

        $PostShareCircle->create();
        $PostShareCircle->save($newData, false);
    }
}
