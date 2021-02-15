<?php

App::uses('GoalousTestCase', 'Test');
App::uses('Comment', 'Model');
App::uses('Post', 'Model');

App::import('Model/Entity', 'CommentEntity');

/**
 * Comment Test Case
 *
 * @property Comment $Comment
 */
class CommentTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.attached_file',
        'app.comment',
        'app.post',
        'app.user',
        'app.group',
        'app.team',
        'app.comment_like',
        'app.comment_read',
        'app.comment_file',
        'app.goal',
        'app.circle',
        'app.action_result',
        'app.key_result',
        'app.post_share_circle',
        'app.circle_member',
        'app.local_name',
        'app.post_share_user',
        'app.team_translation_language',
        'app.team_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Comment = ClassRegistry::init('Comment');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Comment);

        parent::tearDown();
    }

    function testAdd()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $data = [
            'Comment' => [
                'user_id' => 1,
                'team_id' => 1,
                'post_id' => 1,
                'body'    => 'テスト',
            ]
        ];
        $this->Comment->add($data);
    }

    function testAddWithFile()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->CommentFile->AttachedFile = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Comment->CommentFile->AttachedFile->expects($this->any())
            ->method('saveRelatedFiles')
            ->will($this->returnValue(true));
        $data = [
            'Comment' => [
                'user_id' => 1,
                'team_id' => 1,
                'post_id' => 1,
                'body'    => 'テスト',
            ],
            'file_id' => ['aaaabbbbccc'],
        ];
        $this->Comment->add($data);
    }

    function testAddError()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->Post = $this->getMockForModel('Post', array('saveField'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Comment->Post->expects($this->any())
            ->method('saveField')
            ->will($this->returnValue(false));

        $data = [
            'Comment' => [
                'user_id' => 1,
                'team_id' => 1,
                'post_id' => 1,
                'body'    => 'テスト',
            ],
        ];
        $this->Comment->add($data);
    }

    function testAddFail()
    {
        $this->Comment = $this->getMockForModel('Comment', array('save'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Comment->expects($this->any())
            ->method('save')
            ->will($this->returnValue(false));

        $res = $this->Comment->add([]);
        $this->assertFalse($res);
    }

    function testAddInvalidOgp()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $data = [
            'Comment' => [
                'user_id'    => 1,
                'post_id'    => 1,
                'team_id'    => 1,
                'body'       => 'test',
                'site_photo' => [
                    'type'     => 'binary/octet-stream',
                    'tmp_name' => "",
                ]
            ],
        ];
        $res = $this->Comment->save($data);
        $this->assertNotEmpty($res);
    }

    function testCommentEdit()
    {
        $data = [
            'photo_delete' => [1 => true],
            'Comment'      => [
                'id' => 1,
            ]
        ];
        $this->Comment->commentEdit($data);
    }

    function testGetCountCommentUniqueUser()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->getCountCommentUniqueUser(1, [1]);
    }

    function testGetCommentedUniqueUsersList()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $this->Comment->getCommentedUniqueUsersList(1);
    }

    function testGetPostsComment()
    {
        $post_id = 99;
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;
        $data = [
            'team_id' => 1,
            'post_id' => $post_id,
            'body'    => 'comment test.',
            'created' => 1000,
        ];
        $this->Comment->save($data);
        $last_id = $this->Comment->getLastInsertID();
        $res = $this->Comment->getPostsComment($post_id, null, 1, 'DESC');
        $ids = [];
        foreach ($res as $v) {
            $ids[$v['Comment']['id']] = true;
        }
        $this->assertTrue(isset($ids[$last_id]));

        $res = $this->Comment->getPostsComment($post_id, null, 1, 'DESC', ['start' => 1001]);
        $ids = [];
        foreach ($res as $v) {
            $ids[$v['Comment']['id']] = true;
        }
        $this->assertFalse(isset($ids[$last_id]));

    }

    function testGetComment()
    {
        // テスト用データ挿入Start
        $team_id = 1;
        $post_id = 2;
        $user_id = 3;

        $comment_data = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'post_id' => $post_id,
            'body'    => 'test'
        ];
        $this->Comment->save($comment_data);
        $comment_id = $this->Comment->getLastInsertID();

        $comment_like_data = [
            'comment_id' => $comment_id,
            'user_id'    => $user_id,
            'team_id'    => $team_id
        ];
        $this->Comment->CommentLike->save($comment_like_data);

        $attached_file_data = [
            'team_id'            => $team_id,
            'user_id'            => $user_id,
            'attached_file_name' => 'test_image.jpeg',
            'file_ext'           => 'jpeg'
        ];
        $this->Comment->CommentFile->AttachedFile->save($attached_file_data);
        $attached_file_id = $this->Comment->CommentFile->AttachedFile->getLastInsertID();

        $comment_file_data = [
            'comment_id'       => $comment_id,
            'attached_file_id' => $attached_file_id,
            'team_id'          => $team_id,
        ];
        $this->Comment->CommentFile->save($comment_file_data);
        // テスト用データ挿入End

        $this->Comment->current_team_id = $team_id;
        $this->Comment->my_uid = $user_id;

        $comment_info = $this->Comment->getComment($comment_id);
        $this->assertEquals($comment_id, $comment_info['Comment']['id']);
    }

    function testGetCommentCount()
    {
        $post_id = 1;
        $team_id = 2;
        $this->Comment->current_team_id = $team_id;

        $comment_data = [
            'post_id' => $post_id,
            'team_id' => $team_id,
            'body'    => 'test'
        ];
        $this->Comment->save($comment_data);
        $res = $this->Comment->getCommentCount($post_id);

        $this->assertEquals(1, $res);
    }

    function testGetCount()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;

        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 1]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 1]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 2]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 8]);

        $now = time();
        $count = $this->Comment->getCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
            ]);
        $this->assertEquals(4, $count);

        $count = $this->Comment->getCount(
            [
                'start'   => $now - HOUR,
                'end'     => $now + HOUR,
                'user_id' => 1,
            ]);
        $this->assertEquals(3, $count);

        $count = $this->Comment->getCount(
            [
                'start'   => $now - HOUR,
                'end'     => $now + HOUR,
                'post_id' => 1,
            ]);
        $this->assertEquals(2, $count);

        $count = $this->Comment->getCount(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => Post::TYPE_NORMAL,
            ]);
        $this->assertEquals(3, $count);

        $count = $this->Comment->getCount(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => Post::TYPE_ACTION,
            ]);
        $this->assertEquals(1, $count);

        $count = $this->Comment->getCount(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => [Post::TYPE_NORMAL, Post::TYPE_ACTION],
            ]);
        $this->assertEquals(4, $count);

        $count = $this->Comment->getCount(
            [
                'start'   => $now - HOUR,
                'end'     => $now + HOUR,
                'user_id' => 1,
                'post_id' => 1,
            ]);
        $this->assertEquals(1, $count);

        $count = $this->Comment->getCount(
            [
                'start' => $now + HOUR,
            ]);
        $this->assertEquals(0, $count);
    }

    function testGetUniqueUserCount()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;

        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 1]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 1]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 2]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 8]);

        $now = time();
        $count = $this->Comment->getUniqueUserCount(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
            ]);
        $this->assertEquals(2, $count);

        $count = $this->Comment->getUniqueUserCount(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => Post::TYPE_NORMAL,
            ]);
        $this->assertEquals(2, $count);

        $count = $this->Comment->getUniqueUserCount(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => Post::TYPE_ACTION,
            ]);
        $this->assertEquals(1, $count);

        $count = $this->Comment->getUniqueUserCount(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => [Post::TYPE_NORMAL, Post::TYPE_ACTION],
            ]);
        $this->assertEquals(2, $count);

        $count = $this->Comment->getUniqueUserCount(
            [
                'start'   => $now - HOUR,
                'end'     => $now + HOUR,
                'user_id' => 1,
            ]);
        $this->assertEquals(1, $count);
    }

    function testGetRanking()
    {
        $this->Comment->my_uid = 1;
        $this->Comment->current_team_id = 1;

        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 1]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 1]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 2]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => 8]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 2, 'post_id' => 8]);
        $this->Comment->create();
        $this->Comment->save(['team_id' => 1, 'user_id' => 3, 'post_id' => 8]);

        $now = time();
        $ranking = $this->Comment->getRanking(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
            ]);
        $this->assertEquals([8 => "3", 1 => "2", 2 => "1"], $ranking);
        $ranking = $this->Comment->getRanking(
            [
                'start' => $now - HOUR,
                'end'   => $now + HOUR,
                'limit' => 2,
            ]);
        $this->assertEquals([8 => "3", 1 => "2"], $ranking);

        $ranking = $this->Comment->getRanking(
            [
                'start'     => $now - HOUR,
                'end'       => $now + HOUR,
                'post_type' => Post::TYPE_NORMAL,
            ]);
        $this->assertEquals([1 => "2", 2 => "1"], $ranking);

        $ranking = $this->Comment->getRanking(
            [
                'start'        => $now - HOUR,
                'end'          => $now + HOUR,
                'post_type'    => Post::TYPE_NORMAL,
                'post_user_id' => 2,
            ]);
        $this->assertEquals([1 => "2"], $ranking);

        $ranking = $this->Comment->getRanking(
            [
                'start'           => $now - HOUR,
                'end'             => $now + HOUR,
                'post_type'       => Post::TYPE_NORMAL,
                'post_user_id'    => 2,
                'share_circle_id' => [1],
            ]);
        $this->assertEquals([1 => "2"], $ranking);

        $ranking = $this->Comment->getRanking(
            [
                'start'           => $now - HOUR,
                'end'             => $now + HOUR,
                'post_type'       => Post::TYPE_NORMAL,
                'post_user_id'    => 2,
                'share_circle_id' => [100],
            ]);
        $this->assertEquals([], $ranking);
    }

    function testAfterFind()
    {
        $this->Comment->read(null, 1);
        $this->Comment->set('body', '%%%user_1%%%');
        $this->Comment->save();
        $comment = $this->Comment->read(null, 1)['Comment'];
        $this->assertEqual($comment['body'], '%%%user_1:firstname lastname%%%');
        $this->Comment->Post->PostShareCircle->read(null, 2);
        $this->Comment->Post->PostShareCircle->set('circle_id', 4);
        $this->Comment->Post->PostShareCircle->save();
        $this->Comment->read(null, 1);
        $this->Comment->set('body', '%%%circle_4%%%');
        $this->Comment->save();
        $comment = $this->Comment->read(null, 1)['Comment'];
        $this->assertEqual($comment['body'], '%%%circle_4:秘密サークル%%%');
    }

    function testAfterFind_violated_mentioned_to_private_circle_in_public_circle()
    {
        $this->Comment->read(null, 1);
        $this->Comment->set('body', '%%%circle_4%%%');
        $this->Comment->save();
        $comment = $this->Comment->read(null, 1)['Comment'];
        $this->assertEqual($comment['body'], '%%%circle_4%%%');
    }

    function testAfterFind_violated_mentioned_to_public_circle_in_private_circle()
    {
        $this->Comment->read(null, 1);
        $this->Comment->set('body', '%%%circle_3%%%');
        $this->Comment->save();
        $this->Comment->Post->PostShareCircle->read(null, 2);
        $this->Comment->Post->PostShareCircle->set('circle_id', 4);
        $this->Comment->Post->PostShareCircle->save();
        $comment = $this->Comment->read(null, 1)['Comment'];
        $this->assertEqual($comment['body'], '%%%circle_3%%%');
    }

    function testAfterFind_violated_mentioned_to_non_member_user_in_private_circle()
    {
        $this->Comment->read(null, 1);
        $this->Comment->set('body', '%%%circle_4%%%');
        $this->Comment->save();
        $this->Comment->Post->PostShareCircle->read(null, 2);
        $this->Comment->Post->PostShareCircle->set('circle_id', 6);
        $this->Comment->Post->PostShareCircle->save();
        $comment = $this->Comment->read(null, 1)['Comment'];
        $this->assertEqual($comment['body'], '%%%circle_4%%%');
    }

    public function test_convertEntityOnFind_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $comment = $Comment->useEntity()->find('first', ['conditions' => ['id' => 1]]);

        $this->assertTrue($comment instanceof CommentEntity);
    }

    public function test_convertTypeOnFind_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $comment = $Comment->useType()->find('first', ['conditions' => ['id' => 1]])['Comment'];

        $this->assertInternalType('int', $comment['id']);
        $this->assertInternalType('int', $comment['post_id']);
        $this->assertInternalType('int', $comment['user_id']);
        $this->assertInternalType('int', $comment['team_id']);
        $this->assertInternalType('int', $comment['comment_like_count']);
        $this->assertInternalType('int', $comment['comment_read_count']);
    }

    public function test_convertTypeEntityOnFind_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $comment = $Comment->useType()->useEntity()->find('first');

        $this->assertTrue($comment instanceof CommentEntity);
        $this->assertInternalType('int', $comment['id']);
        $this->assertInternalType('int', $comment['post_id']);
        $this->assertInternalType('int', $comment['user_id']);
        $this->assertInternalType('int', $comment['team_id']);
        $this->assertInternalType('int', $comment['comment_like_count']);
        $this->assertInternalType('int', $comment['comment_read_count']);
    }

    public function test_convertEntityOnSave_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $newData = [
            'post_id' => "1",
            'user_id' => "1",
            'team_id' => "1",
            'body'    => 'test'
        ];

        $comment = $Comment->useEntity()->save($newData, false);

        $this->assertTrue($comment instanceof CommentEntity);
    }

    public function test_convertTypeOnSave_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $newData = [
            'post_id' => "1",
            'user_id' => "1",
            'team_id' => "1",
            'body'    => 'test'
        ];

        $comment = $Comment->useType()->save($newData, false)['Comment'];

        $this->assertInternalType('int', $comment['id']);
        $this->assertInternalType('int', $comment['post_id']);
        $this->assertInternalType('int', $comment['user_id']);
        $this->assertInternalType('int', $comment['team_id']);

    }

    public function test_convertTypeEntityOnSave_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $newData = [
            'post_id' => "2",
            'user_id' => "1",
            'team_id' => "1",
            'body'    => 'test'
        ];

        $comment = $Comment->useType()->useEntity()->save($newData, false);

        $this->assertTrue($comment instanceof CommentEntity);
        $this->assertInternalType('int', $comment['id']);
        $this->assertInternalType('int', $comment['post_id']);
        $this->assertInternalType('int', $comment['user_id']);
        $this->assertInternalType('int', $comment['team_id']);
    }

    public function test_updateLanguage_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $commentId = 1;

        $comment = $Comment->getById($commentId);
        $this->assertEmpty($comment['language']);

        $Comment->updateLanguage($commentId, "es");

        $comment = $Comment->getById($commentId);
        $this->assertEquals("es", $comment['language']);
    }

    public function test_clearLanguage_success()
    {
        /** @var Comment $Comment */
        $Comment = ClassRegistry::init('Comment');

        $commentId = 1;

        $comment = $Comment->getById($commentId);
        $this->assertEmpty($comment['language']);

        $Comment->updateLanguage($commentId, "es");

        $comment = $Comment->getById($commentId);
        $this->assertEquals("es", $comment['language']);

        $Comment->clearLanguage($commentId);
        $comment = $Comment->getById($commentId);
        $this->assertEmpty($comment['language']);
    }
}
