<?php
App::uses('GoalousTestCase', 'Test');
App::uses('CircleMember', 'Model');
App::uses('PostRead', 'Model');
App::import('Model/Entity', 'PostReadEntity');

/**
 * PostRead Test Case
 *
 * @property  PostRead $PostRead
 */
class PostReadTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.comment',
        'app.comment_read',
        'app.post_read',
        'app.post_share_circle',
        'app.post',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->PostRead = ClassRegistry::init('PostRead');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostRead);

        parent::tearDown();
    }

    public function testRed()
    {
        $uid = '1';
        $team_id = '1';
        $post_uid = '2';
        $this->PostRead->my_uid = $uid;
        $this->PostRead->current_team_id = $team_id;
        $test_save_data = [
            'Post' => [
                'user_id' => $post_uid,
                'team_id' => $team_id,
                'body'    => 'test',
            ],
        ];
        $this->PostRead->Post->save($test_save_data);
        $post_id = $this->PostRead->Post->getLastInsertID();
        $this->PostRead->red($this->PostRead->Post->getLastInsertID(), true);
        $options = [
            'conditions' => [
                'post_id' => $this->PostRead->Post->getLastInsertID(),
                'user_id' => $uid
            ]
        ];
        $post_read = $this->PostRead->find('first', $options);
        $this->assertEquals($uid, $post_read['PostRead']['user_id']);

        $before_data = $post_read;
        $this->PostRead->red($post_id, true);
        $after_data = $this->PostRead->find('first', $options);
        $this->assertEquals($before_data, $after_data);

        $this->PostRead->Post->create();
        $this->PostRead->Post->save($test_save_data);
        $second_post_id = $this->PostRead->Post->getLastInsertID();
        $post_list = [$post_id, $second_post_id];
        $this->PostRead->red($post_list, true);
    }

    public function testRedDuplicated()
    {
        $uid = '1';
        $team_id = '1';
        $post_uid = '2';
        $this->PostRead->my_uid = $uid;
        $this->PostRead->current_team_id = $team_id;

        $this->PostRead->Post->create();
        $this->PostRead->Post->save(['user_id' => $post_uid, 'team_id' => $team_id, 'body' => 'test']);
        $last_id = $this->PostRead->Post->getLastInsertID();

        $this->PostRead->Post->create();
        $this->PostRead->Post->save(['user_id' => $post_uid, 'team_id' => $team_id, 'body' => 'test']);
        $last_id2 = $this->PostRead->Post->getLastInsertID();

        $res = $this->PostRead->red($last_id, true);
        $this->assertTrue($res);

        $PostReadMock = $this->getMockForModel('PostRead', array('pickUnMyPosts'));
        /** @noinspection PhpUndefinedMethodInspection */
        $PostReadMock->expects($this->any())
            ->method('pickUnMyPosts')
            ->will($this->returnValue([$last_id, $last_id2]));
        $PostReadMock->my_uid = $uid;
        $PostReadMock->current_team_id = $team_id;
        $this->PostRead = $PostReadMock;
        $res = $this->PostRead->red([$last_id, $last_id2], true);
        $this->assertTrue($res);

        $res = $this->PostRead->red([$last_id, $last_id2], true);
        $this->assertFalse($res);
    }

    public function testRedIfPoster()
    {
        $uid = '1';
        $team_id = '1';
        $this->PostRead->my_uid = $uid;
        $this->PostRead->current_team_id = $team_id;
        $test_save_data = [
            'Post' => [
                'user_id' => $uid,
                'team_id' => $team_id,
                'body'    => 'test',

            ],
        ];
        $this->PostRead->Post->save($test_save_data);
        $before_data = $this->PostRead->read();
        $this->PostRead->red($this->PostRead->Post->getLastInsertID(), true);
        $after_data = $this->PostRead->read();
        $this->assertEquals($before_data, $after_data);
    }

    public function testSaveAllAtOnceNoModelName()
    {
        $uid = '1';
        $team_id = '1';
        $this->PostRead->my_uid = $uid;
        $this->PostRead->current_team_id = $team_id;
        $before_count = $this->PostRead->find('count');
        $data = [
            [
                'post_id' => 1,
                'user_id' => $uid,
                'team_id' => $team_id,
            ]
        ];
        $this->PostRead->bulkInsert($data);
        $after_count = $this->PostRead->find('count');
        $this->assertEquals($before_count + 1, $after_count);
    }

    public function testSaveAllAtOnceWithModelName()
    {
        $uid = '1';
        $team_id = '1';
        $this->PostRead->my_uid = $uid;
        $this->PostRead->current_team_id = $team_id;
        $before_count = $this->PostRead->find('count');
        $data = [
            [
                'PostRead' => [
                    'post_id' => 1,
                    'user_id' => $uid,
                    'team_id' => $team_id,
                ]
            ]
        ];
        $this->PostRead->bulkInsert($data);
        $after_count = $this->PostRead->find('count');
        $this->assertEquals($before_count + 1, $after_count);
    }

    public function testSaveAllAtOnceNoData()
    {
        $uid = '1';
        $team_id = '1';
        $this->PostRead->my_uid = $uid;
        $this->PostRead->current_team_id = $team_id;
        $before_count = $this->PostRead->find('count');
        $data = [];
        $this->PostRead->bulkInsert($data);
        $after_count = $this->PostRead->find('count');
        $this->assertEquals($before_count, $after_count);
    }

    public function test_countMultiplePostReader_success()
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $this->insertNewPostRead(3111, 2, 1);
        $this->insertNewPostRead(3111, 3, 1);
        $this->insertNewPostRead(3111, 4, 1);
        $this->insertNewPostRead(3222, 4, 1);
        $this->insertNewPostRead(3222, 5, 1);

        $result = $PostRead->countPostReadersMultiplePost([3111, 3222]);

        $this->assertCount(2, $result);
        $this->assertEquals(3, $result[3111]);
        $this->assertEquals(2, $result[3222]);
    }

    public function test_updateCountMultiplePostReader_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $PostRead->updateReadersCountMultiplePost([1, 2]);

        $post = $Post->getEntity(1);
        $this->assertEquals(2, $post['post_read_count']);
        $post = $Post->getEntity(2);
        $this->assertEquals(0, $post['post_read_count']);

        $this->insertNewPostRead(1, 3, 1);
        $this->insertNewPostRead(1, 4, 1);
        $this->insertNewPostRead(2, 4, 1);
        $this->insertNewPostRead(2, 5, 1);

        $PostRead->updateReadersCountMultiplePost([1, 2]);

        $post = $Post->getEntity(1);
        $this->assertEquals(4, $post['post_read_count']);
        $post = $Post->getEntity(2);
        $this->assertEquals(2, $post['post_read_count']);
    }

    public function test_filterUnreadPost_success()
    {
        $postIds = [5, 6];
        $userId = 3;
        $circleId = 3;

        /** @var CircleMember $CircleMember */
        $CircleMember = ClassRegistry::init('CircleMember');

        $CircleMember->create();
        $CircleMember->save(['circle_id' => $circleId, 'user_id' => $userId], false);

        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $result = $PostRead->filterUnreadPost($postIds, $circleId, $userId);
        $this->assertCount(2, $result);

        $this->insertNewPostRead(5, $userId, 1);
        $result = $PostRead->filterUnreadPost($postIds, $circleId, $userId);
        $this->assertCount(1, $result);

        $result = $PostRead->filterUnreadPost($postIds, $circleId, $userId, true);
        $this->assertCount(0, $result);

        $CircleMember->updateAll(['created' => 1388603000], ['circle_id' => $circleId, 'user_id' => $userId]);
        $result = $PostRead->filterUnreadPost($postIds, $circleId, $userId, true);
        $this->assertCount(1, $result);
        $this->assertEquals([6], $result);

    }


    private function insertNewPostRead(int $postId, int $userId, int $teamId)
    {
        /** @var PostRead $PostRead */
        $PostRead = ClassRegistry::init('PostRead');

        $newData = [
            'post_id' => $postId,
            'user_id' => $userId,
            'team_id' => $teamId
        ];

        $PostRead->create();
        $PostRead->save($newData, false);
    }

}
