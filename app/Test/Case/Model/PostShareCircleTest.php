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
        'app.user', 'app.notify_setting',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.comment_read',
        'app.comment_mention',
        'app.given_badge',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',

        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token',
        'app.local_name',
        'app.circle_member',
        'app.circle'
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
        $res = $this->PostShareCircle->getAccessibleCirclePostList(strtotime("2014-01-01"), strtotime("2014-01-31"), "PostShareCircle.modified", 'desc', 1000, [
            'user_id' => $user_id,
        ]);
        $this->assertNotEmpty($res);

        // 投稿者IDで絞り込めているか確認
        $posts = $this->PostShareCircle->Post->find('all', [
            'fields' => [
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

}
