<?php App::uses('GoalousTestCase', 'Test');
App::uses('Post', 'Model');
App::uses('TeamTranslationStatus', 'Model');
App::uses('Translation', 'Model');

/**
 * Post Test Case
 *
 * @property Post $Post
 */

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;

class PostTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post_file',
        'app.comment_file',
        'app.action_result_file',
        'app.attached_file',
        'app.action_result',
        'app.key_result',
        'app.post',
        'app.user',
        'app.group',
        'app.team',
        'app.invite',
        'app.post_mention',
        'app.evaluator',
        'app.evaluation',
        'app.evaluation_setting',
        'app.circle_insight',
        'app.goal_label',
        'app.member_group',
        'app.message_file',
        'app.device',
        'app.approval_history',
        'app.member_type',
        'app.goal_category',
        'app.notify_setting',
        'app.oauth_token',
        'app.terms_of_service',
        'app.access_user',
        'app.email',
        'app.payment_setting',
        'app.given_badge',
        'app.group_insight',
        'app.group_vision',
        'app.job_category',
        'app.team_vision',
        'app.team_insight',
        'app.recovery_code',
        'app.topic_member',
        'app.comment_mention',
        'app.goal',
        'app.local_name',
        'app.follower',
        'app.goal_member',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.post_like',
        'app.post_read',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.team_member',
        'app.term',
        'app.post_resource',
        'app.kr_progress_log',
        'app.saved_post',
        'app.team_translation_status',
        'app.team_translation_language',
        'app.mst_translation_language',
        'app.translation',
        'app.experiment'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Post = ClassRegistry::init('Post');
        $this->User = ClassRegistry::init('User');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Post);

        parent::tearDown();
    }

    public function testAddInvalidOgp()
    {
        $this->Post->my_uid = 1;
        $this->Post->current_team_id = 1;
        $postData = [
            'Post' => [
                'body'       => 'test',
                'team_id'       => 1,
                'site_photo' => [
                    'type'     => 'binary/octet-stream',
                    'tmp_name' => "",
                ]
            ],
        ];
        $res = $this->Post->save($postData);
        $this->assertNotEmpty($res);
    }

    public function testGetNormal()
    {
        $this->_setDefault();

        $this->Post->get(1, 20, "2014-01-01", "2014-01-31");
    }

    public function testGetDefault()
    {
        $this->_setDefault();

        $this->Post->save([
            'Post' => [
                'body'     => 'test',
                'team_id'  => $this->Post->current_team_id,
                'user_id'  => 1,
                'type'     => Post::TYPE_NORMAL,
                'created'  => REQUEST_TIMESTAMP - 1000,
                'modified' => REQUEST_TIMESTAMP - 1000,
            ]
        ]);
        $post_id1 = $this->Post->getLastInsertID();

        $rows = $this->Post->get(1, 20);
        $ids = [];
        foreach ($rows as $v) {
            $ids[$v['Post']['id']] = true;
        }
        $this->assertFalse(isset($ids[$post_id1]));
    }

    public function testGetSinglePost()
    {
        $this->_setDefault();
        //post_id指定
        $this->Post->id = 1;
        $this->Post->saveField('goal_id', 1);
        $this->Post->get(1, 20, "2014-01-01", "2014-01-31", ['named' => ['post_id' => 1]]);
    }

    public function testGetGoalPost()
    {
        $this->_setDefault();
        //goal_id指定&Action指定
        $this->Post->get(1, 20, "2014-01-01", "2014-01-31", ['named' => ['goal_id' => 1, 'type' => Post::TYPE_ACTION]]);

    }

    public function testGetUserPost()
    {
        $this->_setDefault();

        $res = $this->Post->get(1, 20, "2014-01-01", "2014-01-31",
            ['named' => ['user_id' => 103, 'type' => Post::TYPE_NORMAL]]);

        $this->assertNotEmpty($res);

        $res = $this->Post->get(1, 20, "2014-01-01", "2014-01-31",
            ['named' => ['user_id' => 104, 'type' => Post::TYPE_NORMAL]]);
        $this->assertEmpty($res);

        $res = $this->Post->get(1, 20, "2014-01-01", "2014-01-31",
            ['named' => ['user_id' => 1, 'type' => Post::TYPE_NORMAL]]);
        $this->assertEmpty($res);
    }

    public function testGetLoadedPostTime()
    {
        $this->_setDefault();

        // 2ページ目のデータを読み込む
        $res1 = $this->Post->get(2, 1, "2014-01-01", "2014-01-31");
        $this->assertEmpty($res1);

        // We stopped to use $post_time_before temporarily because of a hotfix bug
        // https://jira.goalous.com/browse/GL-6888
//        $post_time_before = $res1[0]['Post']['created'];

        // 時間指定ありで１ページ目を取得
//        $res2 = $this->Post->get(1, 1, "2014-01-01", "2014-01-31",
//            ['named' => ['post_time_before' => $post_time_before]]);
//        $this->assertEquals($res1[0]['Post']['id'], $res2[0]['Post']['id']);
    }

    function testGetShareAllMemberList()
    {
        $this->_setDefault();
        $post_id = 1;

        $this->Post->getShareAllMemberList($post_id);
        $this->Post->id = $post_id;
        $this->Post->getShareAllMemberList($post_id);
        $this->Post->getShareAllMemberList(9999999999);
    }

    public function testIsMyPost()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $data = [
            'user_id' => $uid,
            'team_id' => $team_id,

            'body' => 'test'
        ];
        $this->Post->save($data);
        $res = $this->Post->isMyPost($this->Post->id);
        $this->assertTrue($res);
    }

    function testGetRandomShareCircleNames()
    {
        $this->Post->PostShareCircle->Circle->current_team_id = 1;
        $data = [
            [
                'PostShareCircle' => [
                    ['circle_id' => 1]
                ]
            ]
        ];
        $res = $this->Post->getRandomShareCircleNames($data);
        $excepted = [
            [
                'PostShareCircle'   => [
                    ['circle_id' => 1]
                ],
                'share_circle_name' => 'test'
            ]
        ];
        $this->assertEquals($excepted, $res);
        $data = [
            [
                'PostShareCircle' => [
                    ['circle_id' => 99999999]
                ]
            ]
        ];
        $res = $this->Post->getRandomShareCircleNames($data);
        $excepted = [
            [
                'PostShareCircle'   => [
                    ['circle_id' => 99999999]
                ],
                'share_circle_name' => null
            ]
        ];
        $this->assertEquals($excepted, $res);
    }

    function testGetRandomShareUserNames()
    {
        $data = [
            [
                'PostShareUser' => [
                    ['user_id' => 1]
                ]
            ]
        ];
        $res = $this->Post->getRandomShareUserNames($data);
        $excepted = [
            [
                'PostShareUser'   => [
                    ['user_id' => 1]
                ],
                'share_user_name' => 'firstname lastname'
            ]
        ];
        $this->assertEquals($excepted, $res);

        $data = [
            [
                'PostShareUser' => [
                    ['user_id' => 9999999999999]
                ]
            ]
        ];
        $res = $this->Post->getRandomShareUserNames($data);
        $excepted = [
            [
                'PostShareUser'   => [
                    ['user_id' => 9999999999999]
                ],
                'share_user_name' => null
            ]
        ];
        $this->assertEquals($excepted, $res);
    }

    function testGetCount()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 101;

        // 自分
        $res = $this->Post->getCount('me', null, null);
        $this->assertEquals(2, $res);

        // TODO: termのstart_date,end_dateがtimestampからdate型に変わったことにより通らなくなったのであとで修正すること
//        $res = $this->Post->getCount('me', 200, 200);
//        $this->assertEquals(2, $res);
//
//        $res = $this->Post->getCount('me', 200, 200, 'created');
//        $this->assertEquals(1, $res);

        // ユーザID指定
        $res = $this->Post->getCount(102, null, null);
        $this->assertEquals(1, $res);
    }

    function testGetUniqueUserCount()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;

        $now = time();
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 2, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);

        $count = $this->Post->getUniqueUserCount([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(2, $count);

        $count = $this->Post->getUniqueUserCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 1,
        ]);
        $this->assertEquals(1, $count);
    }

    function testGetPostCountUserRanking()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;

        $now = time();
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 2, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);

        $ranking = $this->Post->getPostCountUserRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(['1' => 2, '2' => 1], $ranking);

        $ranking = $this->Post->getPostCountUserRanking([
            'start' => $now - HOUR,
            'end'   => $now + HOUR,
            'limit' => 1
        ]);
        $this->assertEquals(['1' => 2], $ranking);

        $ranking = $this->Post->getPostCountUserRanking([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 2
        ]);
        $this->assertEquals(['2' => 1], $ranking);
    }

    function testGetShareMode()
    {
        $data = [
            [
                'Post' => [
                    'public_flg' => true
                ]
            ],
            [
                'Post'            => [
                    'public_flg' => false
                ],
                'PostShareCircle' => [1]
            ],
            [
                'Post'            => [
                    'public_flg' => false
                ],
                'PostShareCircle' => [],
                'PostShareUser'   => [1]
            ],
            [
                'Post'            => [
                    'public_flg' => false
                ],
                'PostShareCircle' => [],
                'PostShareUser'   => []
            ],
        ];
        $res = $this->Post->getShareMode($data);
        $excepted = [
            [
                'Post'       => [
                    'public_flg' => true
                ],
                'share_mode' => 3,
            ],
            [
                'Post'            => [
                    'public_flg' => false
                ],
                'PostShareCircle' => [1],
                'share_mode'      => 4,
            ],
            [
                'Post'            => [
                    'public_flg' => false
                ],
                'PostShareCircle' => [],
                'PostShareUser'   => [1],
                'share_mode'      => 2,
            ],
            [
                'Post'            => [
                    'public_flg' => false
                ],
                'PostShareCircle' => [],
                'PostShareUser'   => [],
                'share_mode'      => 3,
            ],
        ];
        $this->assertEquals($excepted, $res);
    }

    function testGetShareMessages()
    {
        $data = [
            [
                'share_mode' => 1,
            ],
            [
                'share_mode' => 3,
            ],
            [
                'share_mode'      => 2,
                'share_user_name' => 'test_user',
                'PostShareUser'   => [1],
            ],
            [
                'share_mode'      => 2,
                'share_user_name' => 'test_user',
                'PostShareUser'   => [1, 2],
            ],
            [
                'share_mode'        => 4,
                'share_user_name'   => 'test_user',
                'share_circle_name' => 'test_circle',
                'PostShareUser'     => [],
                'PostShareCircle'   => [1],
            ],
            [
                'share_mode'        => 4,
                'share_user_name'   => 'test_user',
                'share_circle_name' => 'test_circle',
                'PostShareUser'     => [],
                'PostShareCircle'   => [1, 2],
            ],
            [
                'share_mode'        => 4,
                'share_user_name'   => 'test_user',
                'share_circle_name' => 'test_circle',
                'PostShareUser'     => [1],
                'PostShareCircle'   => [1],
            ],
            [
                'share_mode'        => 4,
                'share_user_name'   => 'test_user',
                'share_circle_name' => 'test_circle',
                'PostShareUser'     => [1],
                'PostShareCircle'   => [1, 2],
            ],
            [
                'share_mode'        => 4,
                'share_user_name'   => 'test_user',
                'share_circle_name' => 'test_circle',
                'PostShareUser'     => [1, 2],
                'PostShareCircle'   => [1],
            ],
            [
                'share_mode'        => 4,
                'share_user_name'   => 'test_user',
                'share_circle_name' => 'test_circle',
                'PostShareUser'     => [1, 2],
                'PostShareCircle'   => [1, 2],
            ],
        ];
        $this->Post->getShareMessages($data);
    }

    function testAddGoalPost()
    {
        $this->_setDefault();
        $this->Post->addGoalPost(Post::TYPE_CREATE_GOAL, 1, 1, true, null, 'public');
    }

    function testAddGoalPostShareCircle()
    {
        $this->_setDefault();
        $res = $this->Post->addGoalPost(Post::TYPE_CREATE_GOAL, 1, 1, false, 1, 'circle_id');
        $this->assertNotEmpty($res);
    }

    function testGetRelatedPostList()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Goal->Follower->current_team_id = 1;
        $this->Post->Goal->GoalMember->current_team_id = 1;

        $this->Post->Goal->Follower->save(['user_id' => 1, 'team_id' => 1, 'goal_id' => 1]);
        $this->Post->getRelatedPostList(1, 10000);

        // 関連ゴールがないユーザー
        $this->Post->my_uid = 99999;
        $res = $this->Post->getRelatedPostList(PHP_INT_MAX, PHP_INT_MAX);
        $this->assertEmpty($res);
    }

    function testIsPermittedGoalPostSuccess()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Goal->Follower->current_team_id = 1;
        $this->Post->Goal->GoalMember->current_team_id = 1;
        $this->Post->Goal->Follower->my_uid = 1;
        $this->Post->Goal->GoalMember->my_uid = 1;

        $this->Post->save(['user_id' => 1, 'team_id' => 1, 'goal_id' => 1, 'body' => 'test']);
        $res = $this->Post->isGoalPost($this->Post->getLastInsertID());
        $this->assertTrue($res);
    }

    function testIsPermittedGoalPostFailNotGoal()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Goal->Follower->current_team_id = 1;
        $this->Post->Goal->GoalMember->current_team_id = 1;
        $this->Post->Goal->Follower->my_uid = 1;
        $this->Post->Goal->GoalMember->my_uid = 1;

        $this->Post->save(['user_id' => 1, 'team_id' => 1, 'body' => 'test']);
        $res = $this->Post->isGoalPost($this->Post->getLastInsertID());
        $this->assertFalse($res);
    }

    function testGetCommentMyUnreadCount()
    {
        $uid = '1';
        $team_id = '1';
        $comment_uid = '2';
        $comment_num = 4;
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $test_save_data = [
            'Post' => [
                'user_id' => $uid,
                'team_id' => $team_id,
                'body'    => 'test',
            ]
        ];
        for ($i = 0; $i < $comment_num; $i++) {
            $test_save_data['Comment'][] =
                [
                    'user_id' => $comment_uid,
                    'team_id' => $team_id,
                    'body'    => 'test',
                ];
        }
        $this->Post->saveAll($test_save_data);
        $options = [
            'conditions' => [
                'Post.id' => $this->Post->getLastInsertID(),
            ],
            'contain'    => [
                'Comment' => [
                    'conditions' => ['Comment.team_id' => $team_id],
                    'order'      => [
                        'Comment.created' => 'desc'
                    ],
                    'limit'      => 3,
                ],
                'CommentId',
            ],
        ];
        $res = $this->Post->find('all', $options);
        $res = $this->Post->getCommentMyUnreadCount($res);
        $this->assertEquals($comment_num, $res[0]['unread_count']);
    }

    function testCreateCirclePost()
    {
        $this->_setDefault();

        $expected = [
            'Post' => [
                'user_id' => '1',
                'team_id' => '1',
                'type'    => (int)7,

                'circle_id' => (int)1,
            ]
        ];

        $actual = $this->Post->createCirclePost(1);
        unset($actual['Post']['created']);
        unset($actual['Post']['modified']);
        unset($actual['Post']['id']);

        $this->assertEquals($expected, $actual);
    }

    function testGetForRed()
    {
        $this->_setDefault();
        $res = $this->Post->getForRed(1, true);
        $this->assertNotEmpty($res);
    }

    function testExecRedPostComment()
    {
        $this->_setDefault();
        $res = $this->Post->execRedPostComment(1, true);
        $this->assertNull($res);
    }

    function testDoShare()
    {
        $this->_setDefault();
        $res = $this->Post->doShare(1, 'public,circle_1,user_1');
        $this->assertTrue($res);
    }

    function testDoShareNoData()
    {
        $this->_setDefault();
        $res = $this->Post->doShare(1, "");
        $this->assertFalse($res);
    }

    function testGetFilesOnCircle()
    {
        $this->_setDefault();
        App::uses('AttachedFile', 'Model');
        $this->Post->create();
        $this->Post->save(['user_id' => 1, 'team_id' => 1, 'body' => 'test']);
        $p_id = $this->Post->getLastInsertID();
        $this->Post->PostShareCircle->create();
        $this->Post->PostShareCircle->save(['circle_id' => 1, 'post_id' => $p_id, 'team_id' => 1]);
        $this->Post->Comment->create();
        $this->Post->Comment->save(['post_id' => $p_id, 'team_id' => 1, 'user_id' => 1, 'body' => 'test']);
        $c_id = $this->Post->Comment->getLastInsertID();
        $f_ids = [];
        for ($i = 0; $i < 2; $i++) {
            $this->Post->PostFile->AttachedFile->create();
            $this->Post->PostFile->AttachedFile->save(
                [
                    'user_id'            => 1,
                    'team_id'            => 1,
                    'attached_file_name' => 'test.jpg',
                    'file_type'          => 0,
                    'file_ext'           => 'jpg',
                    'file_size'          => 100,
                ]
            );
            $f_ids[] = $this->Post->PostFile->AttachedFile->getLastInsertID();
        }
        $this->Post->PostFile->create();
        $this->Post->PostFile->save(
            [
                'post_id'          => $p_id,
                'team_id'          => 1,
                'attached_file_id' => $f_ids[0]
            ]
        );
        $this->Post->Comment->CommentFile->create();
        $this->Post->Comment->CommentFile->save(
            [
                'comment_id'       => $c_id,
                'team_id'          => 1,
                'attached_file_id' => $f_ids[1]
            ]
        );
        $res = $this->Post->getFilesOnCircle(1, 1, null, 1, 100000000000, 'image');
        $this->assertCount(2, $res);
    }

    function testPostEdit()
    {
        $this->_setDefault();
        // 通常 edit
        $data = [
            'Post' => [
                'id'   => 1,
                'body' => 'edit string',
                'team_id' => 1
            ]
        ];
        $res = $this->Post->postEdit($data);
        $this->assertTrue($res);
        $row = $this->Post->findById(1);
        $this->assertEquals($row['Post']['body'], $data['Post']['body']);

        // 添付ファイルあり
        $this->Post->PostFile->AttachedFile = $this->getMockForModel('AttachedFile', array('updateRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Post->PostFile->AttachedFile->expects($this->any())
            ->method('updateRelatedFiles')
            ->will($this->returnValue(true));
        $data = [
            'Post'    => [
                'id'   => 1,
                'body' => 'edit string2',
            ],
            'file_id' => ['aaa', 'bbb']
        ];
        $res = $this->Post->postEdit($data);
        $this->assertTrue($res);
        $row = $this->Post->findById(1);
        $this->assertEquals($row['Post']['body'], $data['Post']['body']);

        // rollback
        $this->Post->PostFile->AttachedFile = $this->getMockForModel('AttachedFile', array('updateRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Post->PostFile->AttachedFile->expects($this->any())
            ->method('updateRelatedFiles')
            ->will($this->returnValue(false));
        $data = [
            'Post'    => [
                'id'   => 1,
                'body' => 'edit string3',
            ],
            'file_id' => ['aaa', 'bbb']
        ];
        $res = $this->Post->postEdit($data);
        $this->assertFalse($res);
        $row = $this->Post->findById(1);
        $this->assertNotEquals($row['Post']['body'], $data['Post']['body']);
    }

    public function test_editPostDeleteTranslation_success()
    {
        $this->_setDefault();

        /** @var Translation $Translation */
        $Translation = ClassRegistry::init('Translation');
        $Translation->createEntry(TranslationContentType::CIRCLE_POST(), 1, "es");

        $translation = $Translation->getTranslation(TranslationContentType::CIRCLE_POST(), 1, "es");
        $this->assertNotEmpty($translation);
        $data = [
            'Post' => [
                'id'   => 1,
                'body' => 'edit string',
            ]
        ];
        $this->Post->postEdit($data);
        $translation = $Translation->getTranslation(TranslationContentType::CIRCLE_POST(), 1, "es");
        $this->assertEmpty($translation);
    }

    function testGetPostById()
    {
        $this->Post->current_team_id = '1';
        $data = [
            'user_id' => 99,
            'team_id' => 1,
            'body'    => 'test'
        ];
        $this->Post->save($data);
        $res = $this->Post->getPostById($this->Post->getLastInsertID());
        $this->assertEquals($data['body'], $res['Post']['body']);
    }

    function testGetPhotoPath()
    {
        $data = [
            'user_id'         => 99,
            'team_id'         => 1,
            'photo_file_name' => ''
        ];
        $res = $this->Post->User->save($data);
        $this->assertNotEmpty($this->Post->getPhotoPath($res));
    }

    function testGetPostsById()
    {
        $this->_setDefault();
        $posts = $this->Post->getPostsById([1]);
        $this->assertEquals(1, $posts[0]['Post']['id']);
        $posts = $this->Post->getPostsById([1, 2]);
        $this->assertEquals(1, $posts[0]['Post']['id']);
        $this->assertEquals(2, $posts[1]['Post']['id']);
        $posts = $this->Post->getPostsById([1, 8], ['include_action' => true]);
        $this->assertEquals(1, $posts[0]['Post']['id']);
        $this->assertEquals(null, $posts[0]['ActionResult']['id']);
        $this->assertEquals(8, $posts[1]['Post']['id']);
        $this->assertEquals(1, $posts[1]['ActionResult']['id']);
        $posts = $this->Post->getPostsById([1, 8], ['include_action' => true, 'include_user' => true]);
        $this->assertEquals(1, $posts[0]['Post']['id']);
        $this->assertEquals(null, $posts[0]['ActionResult']['id']);
        $this->assertEquals(2, $posts[0]['User']['id']);
        $this->assertEquals(8, $posts[1]['Post']['id']);
        $this->assertEquals(1, $posts[1]['ActionResult']['id']);
        $this->assertEquals(1, $posts[1]['User']['id']);

    }

    function testGetConditionGetMyPostList()
    {
        $this->_setDefault();
        $actual = $this->Post->getConditionGetMyPostList();
        $expected = ['Post.user_id' => $this->Post->my_uid];
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterPostIdShareWithMe()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $actual = $this->Post->getSubQueryFilterPostIdShareWithMe($db, 0, 1);
        $expected = "SELECT PostShareUser.post_id FROM {$db->fullTableName($this->Post->PostShareUser)} AS `PostShareUser`   WHERE `PostShareUser`.`user_id` = 1 AND `PostShareUser`.`team_id` = 1 AND `PostShareUser`.`modified` BETWEEN 0 AND 1";
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterPostIdShareWithMeWithUserId()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $actual = $this->Post->getSubQueryFilterPostIdShareWithMe($db, 0, 1, ['user_id' => 1]);
        $expected = "SELECT PostShareUser.post_id FROM {$db->fullTableName($this->Post->PostShareUser)} AS `PostShareUser` LEFT JOIN {$db->fullTableName($this->Post)} AS `Post` ON (`PostShareUser`.`post_id`=`Post`.`id`)  WHERE `PostShareUser`.`user_id` = 1 AND `PostShareUser`.`team_id` = 1 AND `PostShareUser`.`modified` BETWEEN 0 AND 1 AND `Post`.`user_id` = 1";
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterMyCirclePostId()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $expected = "SELECT PostShareCircle.post_id FROM {$db->fullTableName($this->Post->PostShareCircle)} AS `PostShareCircle`   WHERE `PostShareCircle`.`circle_id` IN (1, 2, 3, 4) AND `PostShareCircle`.`team_id` = 1 AND `PostShareCircle`.`modified` BETWEEN 0 AND 1";
        $actual = $this->Post->getSubQueryFilterMyCirclePostId($db, 0, 1);
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterMyCirclePostIdWithAllParams()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $actual = $this->Post->getSubQueryFilterMyCirclePostId($db, 0, 1, [1], PostShareCircle::SHARE_TYPE_SHARED);
        $expected = "SELECT PostShareCircle.post_id FROM {$db->fullTableName($this->Post->PostShareCircle)} AS `PostShareCircle`   WHERE `PostShareCircle`.`circle_id` = (1) AND `PostShareCircle`.`team_id` = 1 AND `PostShareCircle`.`modified` BETWEEN 0 AND 1 AND `PostShareCircle`.`share_type` = 0";
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterMyCirclePostIdTeamAllCircle()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $this->Post->Circle->create();
        $this->Post->Circle->save(
            [
                'team_id'      => $this->Post->current_team_id,
                'team_all_flg' => 1,
                'name'         => 'team all'
            ]
        );
        $this->Post->Circle->CircleMember->create();
        $this->Post->Circle->CircleMember->save(
            [
                'circle_id' => $this->Post->Circle->getLastInsertID(),
                'team_id'   => $this->Post->current_team_id,
                'user_id'   => $this->Post->my_uid
            ]
        );
        $actual = $this->Post->getSubQueryFilterMyCirclePostId($db, 0, 1, $this->Post->Circle->getLastInsertID());
        $expected = "SELECT PostShareCircle.post_id FROM {$db->fullTableName($this->Post->PostShareCircle)} AS `PostShareCircle`   WHERE `PostShareCircle`.`circle_id` = " . $this->Post->Circle->getLastInsertID() . " AND `PostShareCircle`.`team_id` = 1 AND `PostShareCircle`.`modified` BETWEEN 0 AND 1 AND NOT (`type` IN (3, 2, 6, 5))";
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterGoalPostList()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $this->Post->orgParams['author_id'] = 1;
        $actual = $this->Post->getSubQueryFilterGoalPostList($db, 1, Post::TYPE_ACTION, 0, 1);
        $expected = "SELECT Post.id FROM {$db->fullTableName($this->Post)} AS `Post`   WHERE `Post`.`type` = 3 AND `Post`.`team_id` = 1 AND `Post`.`goal_id` = 1 AND `Post`.`user_id` = 1";
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterKrPostList()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $actual = $this->Post->getSubQueryFilterKrPostList($db, 1, null, Post::TYPE_ACTION, 0, 1);
        $expected = "SELECT Post.id FROM {$db->fullTableName($this->Post)} AS `Post` LEFT JOIN {$db->fullTableName($this->Post->ActionResult)} AS `ActionResult` ON (`ActionResult`.`id`=`Post`.`action_result_id`)  WHERE `Post`.`type` = 3 AND `Post`.`team_id` = 1 AND `ActionResult`.`key_result_id` = 1 AND `Post`.`modified` BETWEEN 0 AND 1";
        $this->assertEquals($this->repQuote($expected), $this->repQuote($actual));
    }

    function testGetSubQueryFilterRelatedGoalPost()
    {
        $this->_setDefault();
        /**
         * @var DboSource $db
         */
        $db = $this->Post->getDataSource();
        $actual = $this->Post->getSubQueryFilterRelatedGoalPost($db, 0, 1, [1]);
        $this->assertNotEmpty($actual);
    }

    function testGetConditionGoalPostId()
    {
        $this->_setDefault();
        $this->assertNotEmpty($this->Post->getConditionAllGoalPostId([1]));
    }

    function testIsPostedCircleForSetupBy()
    {
        $this->_setDefault();
        $this->_setTerm();

        $this->Post->deleteAll(['Post.user_id' => $this->Post->my_uid], false);
        $res = $this->Post->isPostedCircleForSetupBy($this->Post->my_uid);
        $this->assertFalse($res);

        $this->User->id = 1;
        $created = (new GoalousDateTime($this->start_date))->subDay(1)->format('Y-m-d');
        $this->User->save(['created' => $created]);
        // In case that user posted circle post
        $this->Post->save([
            'id'       => 1,
            'body'     => 'test',
            'team_id'  => $this->Post->current_team_id,
            'user_id'  => 1,
            'type'     => Post::TYPE_NORMAL,
            'created'  => $this->start_date,
            'modified' => $this->start_date,
        ]);
        $this->Post->PostShareCircle->save([
            'id'        => 1,
            'post_id'   => 1,
            'circle_id' => 1,
            'team_id'   => 1,
            'created'   => $this->start_date,
            'modified'  => $this->start_date,
        ]);
        $res = $this->Post->isPostedCircleForSetupBy($this->Post->my_uid);
        $this->assertTrue($res);
    }

    function testGetByActionResultId()
    {
        $this->_setDefault();
        $this->Post->ActionResult->save(
            [
                'goal_id' => 99999,
                'post_id' => $this->Post->getLastInsertID(),
                'team_id' => 1,
                'name'    => 'test',
                'type'    => ActionResult::TYPE_GOAL,
            ]
        );
        $this->Post->save(
            [
                'user_id'          => 1,
                'team_id'          => 1,
                'action_result_id' => $this->Post->ActionResult->getLastInsertID(),
                'body'             => 'test',
                'type'             => Post::TYPE_ACTION,
            ]
        );
        $res = $this->Post->getByActionResultId($this->Post->ActionResult->getLastInsertID());
        $this->assertNotEmpty($res);
    }

    public function test_updateCommentCount_success()
    {
        $postId = 1;
        $newCommentCount = 123;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $Post->updateCommentCount($postId, $newCommentCount);

        $post = $Post->getEntity($postId);

        $this->assertEquals($newCommentCount, $post['comment_count']);
        $this->assertTrue($post['modified'] > 1);
    }

    public function test_getPostType_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $postType = $Post->getPostType(1);
        $this->assertEquals(1, $postType);

        $postType = $Post->getPostType(6);
        $this->assertEquals(7, $postType);
    }


    public function test_getPostWithTranslationLanguage_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        /** @var TeamTranslationStatus $TeamTranslationStatus */
        $TeamTranslationStatus = ClassRegistry::init('TeamTranslationStatus');

        $teamId = 1;

        $TeamTranslationStatus->createEntry($teamId);

        $this->insertTranslationLanguage($teamId, "en");
        $this->insertTranslationLanguage($teamId, "ja");
        $this->insertTranslationLanguage($teamId, "de");

        $Post->my_uid = 1;
        $Post->current_team_id = 1;
        $posts = $Post->get();

        foreach ($posts as $post) {
            $this->assertNotEmpty($post['Post']);
            $this->assertFalse($post['Post']['translation_limit_reached']);
            $this->assertCount(3, $post['Post']['translation_languages']);
            foreach ($post['Post']['translation_languages'] as $translationLanguage) {
                $this->assertNotEmpty($translationLanguage['language']);
                $this->assertNotEmpty($translationLanguage['intl_name']);
                $this->assertNotEmpty($translationLanguage['local_name']);
            }
        }
    }

    public function test_updateLanguage_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $postId = 1;

        $post = $Post->getById($postId);
        $this->assertEmpty($post['language']);

        $Post->updateLanguage($postId, "es");

        $post = $Post->getById($postId);
        $this->assertEquals("es", $post['language']);
    }

    public function test_clearLanguage_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $postId = 1;

        $post = $Post->getById($postId);
        $this->assertEmpty($post['language']);

        $Post->updateLanguage($postId, "es");

        $post = $Post->getById($postId);
        $this->assertEquals("es", $post['language']);

        $Post->clearLanguage($postId);
        $post = $Post->getById($postId);
        $this->assertEmpty($post['language']);
    }

    public function test_getByCommentId_success()
    {
        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');

        $commentId = 1;

        $post = $Post->getByCommentId($commentId);

        $this->assertEquals($Post->getById(5), $post['Post']);
    }


    function _setDefault()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $this->Post->Circle->my_uid = $uid;
        $this->Post->Circle->current_team_id = $team_id;
        $this->Post->PostRead->my_uid = $uid;
        $this->Post->PostRead->current_team_id = $team_id;
        $this->Post->Comment->CommentRead->my_uid = $uid;
        $this->Post->Comment->CommentRead->current_team_id = $team_id;
        $this->Post->PostShareCircle->my_uid = $uid;
        $this->Post->PostShareCircle->current_team_id = $team_id;
        $this->Post->PostShareUser->my_uid = $uid;
        $this->Post->PostShareUser->current_team_id = $team_id;
        $this->Post->User->CircleMember->my_uid = $uid;
        $this->Post->User->CircleMember->current_team_id = $team_id;
        $this->Post->Team->my_uid = $uid;
        $this->Post->Team->current_team_id = $team_id;
        $this->Post->Team->TeamMember->my_uid = $uid;
        $this->Post->Team->TeamMember->current_team_id = $team_id;
        $this->Post->Goal->my_uid = $uid;
        $this->Post->Goal->current_team_id = $team_id;
        $this->Post->Goal->GoalMember->my_uid = $uid;
        $this->Post->Goal->GoalMember->current_team_id = $team_id;
        $this->Post->Team->Term->current_team_id = $team_id;
        $this->Post->Team->Term->my_uid = $uid;
    }

    function _setTerm()
    {
        $this->Post->Team->Term->addTermData(Term::TYPE_CURRENT);
        $this->Post->Team->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->Post->Team->Term->addTermData(Term::TYPE_NEXT);
        $this->current_date = REQUEST_TIMESTAMP;
        $this->start_date = $this->Post->Team->Term->getCurrentTermData()['start_date'];
        $this->end_date = $this->Post->Team->Term->getCurrentTermData()['end_date'];
        $timezone = $this->Post->Team->Term->getCurrentTermData()['timezone'];
        $this->start_date_format = (new GoalousDateTime($this->start_date))->addHour($timezone)->format('Y-m-d');
        $this->end_date_format = (new GoalousDateTime($this->end_date))->addHour($timezone)->format('Y-m-d');
    }

    function repQuote($str)
    {
        return str_replace($str, '`', '"');
    }
}
