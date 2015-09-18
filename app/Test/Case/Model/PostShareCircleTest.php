<?php
App::uses('PostShareCircle', 'Model');

/**
 * PostShareCircle Test Case
 *
 * @property PostShareCircle $PostShareCircle
 */
class PostShareCircleTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_share_circle',
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

    public function testGetAccessibleCirclePostList()
    {
        $this->PostShareCircle->my_uid = 1;
        $this->PostShareCircle->current_team_id = 1;
        $this->PostShareCircle->Circle->CircleMember->my_uid = 1;
        $this->PostShareCircle->Circle->CircleMember->current_team_id = 1;
        $this->PostShareCircle->Post->my_uid = 1;
        $this->PostShareCircle->Post->current_team_id = 1;

        // 閲覧可能なサークルの投稿
        $res = $this->PostShareCircle->getAccessibleCirclePostList(strtotime("2014-01-01"), strtotime("2014-01-31"));
        $this->assertNotEmpty($res);

        // 閲覧可能なサークルの投稿（投稿者IDで絞り込み）
        $user_id = 103;
        $res = $this->PostShareCircle->getAccessibleCirclePostList(strtotime("2014-01-01"), strtotime("2014-01-31"),
                                                                   "PostShareCircle.modified", 'desc', 1000, [
                'user_id' => $user_id,
            ]);
        $this->assertNotEmpty($res);

        // 投稿者IDで絞り込めているか確認
        $posts = $this->PostShareCircle->Post->find('all', [
            'fields'     => [
                'Post.user_id'
            ],
            'conditions' => [
                'Post.id' => $res,
            ],
        ]);
        foreach ($posts as $post) {
            $this->assertEquals($user_id, $post['Post']['user_id']);
        }
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
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
            'like_user_id'   => [2],
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
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
            'comment_user_id'   => [2, 3],
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
}
