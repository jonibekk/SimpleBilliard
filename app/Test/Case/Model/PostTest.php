<?php
App::uses('Post', 'Model');

/**
 * Post Test Case
 *
 * @property Post $Post
 */
class PostTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_category',
        'app.post_file',
        'app.comment_file',
        'app.action_result_file',
        'app.attached_file',
        'app.action_result',
        'app.key_result',
        'app.post',
        'app.user',
        'app.notify_setting',
        'app.team',
        'app.goal',
        'app.local_name',
        'app.purpose',
        'app.follower',
        'app.collaborator',
        'app.comment_mention',
        'app.comment',
        'app.comment_like',
        'app.comment_read',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.image',
        'app.badge',
        'app.images_post',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
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
        $this->Post = ClassRegistry::init('Post');
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

    public function testAdd()
    {
        $uid = '1';
        $team_id = '1';
        $this->_setDefault();

        $postData = [
            'Post' => [
                'body' => 'test',
            ]
        ];
        $res = $this->Post->addNormal($postData, $uid, $team_id);
        $this->assertNotEmpty($res, "[正常]投稿(uid,team_id指定)");

        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $this->Post->create();
        $res = $this->Post->addNormal($postData);
        $this->assertNotEmpty($res, "[正常]投稿(uid,team_id指定なし)");
    }

    public function testEditNormal()
    {
        $uid = '2';
        $team_id = '1';
        $this->_setDefault();

        $postDataOne = [];
        $postDataTwo = [
            'Post' => [
                'post_id' => '30',
                'body' => 'test',
            ]
        ];
        $postDataFour = [
            'Post' => [
                'post_id' => '30',
                'body' => 'test',
                'share' => '',
            ]
        ];

        $postDataSix = [
            'Post' => [
                'post_id' => '30',
                'body' => 'test',
                'share' => 'user_2,user_3',
            ]
        ];

        $postDataFive = ['share_public' => 'user_2,user_3','post_id' => 37,'share_range' => 'public','type' => 8,'share' => 'user_2,user_3'];

        $res = $this->Post->editMessageMember($postDataTwo,$uid,$team_id);
        $this->assertNotEmpty($res, "[正常]投稿(指定)");

        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $this->Post->create();
        $res = $this->Post->editMessageMember($postDataOne);
        $this->assertEmpty($res);

        $res = $this->Post->editMessageMember($postDataOne,$uid,$team_id);
        $this->assertFalse($res,"[正常]投稿指定)");

        $res = $this->Post->editMessageMember($postDataFour);
        $this->assertNotEmpty($res);

        $res = $this->Post->editMessageMember($postDataFive);
        $this->assertEmpty($res);

        $res = $this->Post->editMessageMember($postDataFive);
        $this->assertFalse($res);

        $res = $this->Post->editMessageMember($postDataSix);
        $this->assertNotEmpty($res);

    }

    public function testAddWithFile()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $postData = [
            'Post'    => [
                'body' => 'test',
            ],
            'file_id' => ['aaaaaa']
        ];
        $this->Post->PostFile->AttachedFile = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Post->PostFile->AttachedFile->expects($this->any())
                                           ->method('saveRelatedFiles')
                                           ->will($this->returnValue(true));
        $res = $this->Post->addNormal($postData, Post::TYPE_NORMAL, $uid, $team_id);
        $this->assertNotEmpty($res, "[正常]投稿(uid,team_id指定)");
    }

    public function testAddError()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $postData = [
            'Post'    => [
                'body' => 'test',
            ],
            'file_id' => ['aaaaaa']
        ];
        $this->Post->PostFile->AttachedFile = $this->getMockForModel('AttachedFile', array('saveRelatedFiles'));
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Post->PostFile->AttachedFile->expects($this->any())
                                           ->method('saveRelatedFiles')
                                           ->will($this->returnValue(false));
        $res = $this->Post->addNormal($postData, Post::TYPE_NORMAL, $uid, $team_id);
        $this->assertFalse($res);
    }

    public function testAddInvalidOgp() {
        $this->Post->my_uid = 1;
        $this->Post->current_team_id = 1;
        $postData = [
            'Post'    => [
                'body' => 'test',
                'site_photo' => [
                    'type' => 'binary/octet-stream'
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
        $this->assertNotEmpty($res1);

        $post_time_before = $res1[0]['Post']['created'];

        // 時間指定ありで１ページ目を取得
        $res2 = $this->Post->get(1, 1, "2014-01-01", "2014-01-31",
                                 ['named' => ['post_time_before' => $post_time_before]]);
        $this->assertEquals($res1[0]['Post']['id'], $res2[0]['Post']['id']);
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

            'body'    => 'test'
        ];
        $this->Post->save($data);
        $res = $this->Post->isMyPost($this->Post->id);
        $this->assertTrue($res);
    }

    function testGetGoalPostList()
    {
        $this->Post->my_uid = 1;
        $this->Post->current_team_id = 1;
        $data = [
            'team_id' => 1,
            'user_id' => 1,
            'body'    => 'test',
            'goal_id' => 1,
            'type'    => Post::TYPE_ACTION
        ];
        $this->Post->save($data);
        $res = $this->Post->getGoalPostList(1);
        $this->assertTrue(!empty($res));
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

        // ユーザID指定
        $res = $this->Post->getCount(102, null, null);
        $this->assertEquals(1, $res);
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
        $this->Post->Goal->Collaborator->current_team_id = 1;

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
        $this->Post->Goal->Collaborator->current_team_id = 1;
        $this->Post->Goal->Follower->my_uid = 1;
        $this->Post->Goal->Collaborator->my_uid = 1;

        $this->Post->save(['user_id' => 1, 'team_id' => 1, 'goal_id' => 1, 'body' => 'test']);
        $res = $this->Post->isGoalPost($this->Post->getLastInsertID());
        $this->assertTrue($res);
    }

    function testIsPermittedGoalPostFailNotGoal()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Goal->Follower->current_team_id = 1;
        $this->Post->Goal->Collaborator->current_team_id = 1;
        $this->Post->Goal->Follower->my_uid = 1;
        $this->Post->Goal->Collaborator->my_uid = 1;

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
                'user_id'   => '1',
                'team_id'   => '1',
                'type'      => (int)7,

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
                    'user_id'            => 1, 'team_id' => 1,
                    'attached_file_name' => 'test.jpg',
                    'file_type'          => 0, 'file_ext' => 'jpg', 'file_size' => 100,
                ]
            );
            $f_ids[] = $this->Post->PostFile->AttachedFile->getLastInsertID();
        }
        $this->Post->PostFile->create();
        $this->Post->PostFile->save(
            [
                'post_id' => $p_id, 'team_id' => 1, 'attached_file_id' => $f_ids[0]
            ]
        );
        $this->Post->Comment->CommentFile->create();
        $this->Post->Comment->CommentFile->save(
            [
                'comment_id' => $c_id, 'team_id' => 1, 'attached_file_id' => $f_ids[1]
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
                'id' => 1,
                'body' => 'edit string',
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
            'Post' => [
                'id' => 1,
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
            'Post' => [
                'id' => 1,
                'body' => 'edit string3',
            ],
            'file_id' => ['aaa', 'bbb']
        ];
        $res = $this->Post->postEdit($data);
        $this->assertFalse($res);
        $row = $this->Post->findById(1);
        $this->assertNotEquals($row['Post']['body'], $data['Post']['body']);

    }

    function testGetMessageList()
    {
        $this->Post->current_team_id = '1';
        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'body' => 'test',
            'type' => Post::TYPE_MESSAGE
        ];
        $this->Post->save($data);
        $res = $this->Post->getMessageList();
        $this->assertNotEmpty($res);
    }

    function testConvertData()
    {
        $this->Post->current_team_id = '1';
        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'body' => 'test',
            'type' => Post::TYPE_MESSAGE
        ];
        $this->Post->save($data);

        $data = [
            'team_id' => 1,
            'post_id' => $this->Post->getLastInsertID(),
            'body' => 'comment test'
        ];
        $this->Post->Comment->save($data);

        $data = [
            'team_id' => 1,
            'post_id' => $this->Post->getLastInsertID(),
            'user_id' => 2,
        ];
        $this->Post->PostShareUser->save($data);

        $res = $this->Post->getMessageList();
        $this->Post->convertData($res);
    }

    function testGetPostById()
    {
        $this->Post->current_team_id = '1';
        $data = [
            'user_id' => 99,
            'team_id' => 1,
            'body' => 'test'
        ];
        $this->Post->save($data);
        $res = $this->Post->getPostById($this->Post->getLastInsertID());
        $this->assertEquals($data['body'], $res['Post']['body']);
    }

    function testGetPhotoPath()
    {
        $data = [
            'user_id' => 99,
            'team_id' => 1,
            'photo_file_name' => ''
        ];
        $res = $this->Post->User->save($data);
        $this->assertNotEmpty($this->Post->getPhotoPath($res));
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
        $this->Post->Team->TeamMember->my_uid = $uid;
        $this->Post->Team->TeamMember->current_team_id = $team_id;
        $this->Post->Goal->my_uid = $uid;
        $this->Post->Goal->current_team_id = $team_id;
        $this->Post->Goal->Collaborator->my_uid = $uid;
        $this->Post->Goal->Collaborator->current_team_id = $team_id;

    }

}
