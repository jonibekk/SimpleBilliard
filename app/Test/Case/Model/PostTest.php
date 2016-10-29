<?php App::uses('GoalousTestCase', 'Test');
App::uses('Post', 'Model');

/**
 * Post Test Case
 *
 * @property Post $Post
 */
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
        'app.team',
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
        'app.evaluate_term',
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
                'body'    => 'test',
            ]
        ];
        $postDataFour = [
            'Post' => [
                'post_id' => '30',
                'body'    => 'test',
                'share'   => '',
            ]
        ];

        $postDataSix = [
            'Post' => [
                'post_id' => '30',
                'body'    => 'test',
                'share'   => 'user_2,user_3',
            ]
        ];

        $postDataFive = [
            'share_public' => 'user_2,user_3',
            'post_id'      => 37,
            'share_range'  => 'public',
            'type'         => 8,
            'share'        => 'user_2,user_3'
        ];

        $res = $this->Post->editMessageMember($postDataTwo, $uid, $team_id);
        $this->assertNotEmpty($res, "[正常]投稿(指定)");

        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $this->Post->create();
        $res = $this->Post->editMessageMember($postDataOne);
        $this->assertEmpty($res);

        $res = $this->Post->editMessageMember($postDataOne, $uid, $team_id);
        $this->assertFalse($res, "[正常]投稿指定)");

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

    public function testAddWithSharing()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $this->Post->Circle->my_uid = $uid;
        $this->Post->Circle->current_team_id = $team_id;
        $this->Post->PostShareCircle->my_uid = $uid;
        $this->Post->PostShareCircle->current_team_id = $team_id;
        $this->Post->User->CircleMember->my_uid = $uid;
        $this->Post->User->CircleMember->current_team_id = $team_id;
        $postData = [
            'Post' => [
                'team_id' => 1,
                'user_id' => 1,
                'body'    => 'test',
                'share'   => 'public',
            ],
        ];
        //save circle member
        $this->Post->User->CircleMember->save([
            'user_id'   => 2,
            'circle_id' => 3,
            'team_id'   => 1,
        ]);

        $res = $this->Post->addNormal($postData);
        $this->assertNotEmpty($res);

        $last_id = $this->Post->getLastInsertID();
        $all = $this->Post->PostShareCircle->find('all', [
            'conditions' => [
                'PostShareCircle.post_id' => $last_id,
            ]
        ]);
        $this->assertCount(1, $all);
        $this->assertEquals(3, $all[0]['PostShareCircle']['circle_id']);
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

    public function testAddInvalidOgp()
    {
        $this->Post->my_uid = 1;
        $this->Post->current_team_id = 1;
        $postData = [
            'Post' => [
                'body'       => 'test',
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
        $this->assertTrue(isset($ids[$post_id1]));
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

        $res = $this->Post->getCount('me', 200, 200);
        $this->assertEquals(2, $res);

        $res = $this->Post->getCount('me', 200, 200, 'created');
        $this->assertEquals(1, $res);

        // ユーザID指定
        $res = $this->Post->getCount(102, null, null);
        $this->assertEquals(1, $res);
    }

    function testGetMessageCount()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Comment->current_team_id = 1;
        $this->Post->Comment->my_uid = 1;

        $now = time();
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_MESSAGE, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 2, 'type' => Post::TYPE_MESSAGE, 'body' => 'test']);
        $count = $this->Post->getMessageCount([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(2, $count);

        $post_id = $this->Post->getLastInsertID();
        $this->Post->Comment->create();
        $this->Post->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => $post_id, 'body' => 'test']);
        $count = $this->Post->getMessageCount([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(3, $count);

        $count = $this->Post->getMessageCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => 1
        ]);
        $this->assertEquals(2, $count);
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

    function testGetMessageUserCount()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Comment->current_team_id = 1;
        $this->Post->Comment->my_uid = 1;

        $now = time();
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_MESSAGE, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_NORMAL, 'body' => 'test']);
        $normal_post_id = $this->Post->getLastInsertID();
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 1, 'type' => Post::TYPE_MESSAGE, 'body' => 'test']);
        $this->Post->create();
        $this->Post->save(['team_id' => 1, 'user_id' => 2, 'type' => Post::TYPE_MESSAGE, 'body' => 'test']);
        $message_post_id = $this->Post->getLastInsertID();
        $this->Post->Comment->create();
        $this->Post->Comment->save(['team_id' => 1, 'user_id' => 1, 'post_id' => $message_post_id, 'body' => 'test']);
        $this->Post->Comment->create();
        $this->Post->Comment->save(['team_id' => 1, 'user_id' => 3, 'post_id' => $message_post_id, 'body' => 'test']);
        $this->Post->Comment->create();
        $this->Post->Comment->save(['team_id' => 1, 'user_id' => 4, 'post_id' => $normal_post_id, 'body' => 'test']);

        $count = $this->Post->getMessageUserCount([
            'start' => $now - HOUR,
            'end'   => $now + HOUR
        ]);
        $this->assertEquals(3, $count);

        $count = $this->Post->getMessageUserCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => [1, 2],
        ]);
        $this->assertEquals(2, $count);

        $count = $this->Post->getMessageUserCount([
            'start'   => $now - HOUR,
            'end'     => $now + HOUR,
            'user_id' => [1, 4],
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

    function testGetMessageList()
    {
        $user_id = 999;
        $team_id = 1;
        $this->Post->current_team_id = $team_id;

        //シェアされた時
        $data = [
            'user_id' => 888,
            'team_id' => $team_id,
            'body'    => 'test2',
            'type'    => Post::TYPE_MESSAGE
        ];
        $this->Post->save($data);
        $post_id = $this->Post->getLastInsertID();

        $this->Post->PostShareUser->current_team_id = $team_id;
        $share_user_data = [
            'post_id' => $post_id,
            'team_id' => $team_id,
            'user_id' => $user_id
        ];
        $this->Post->PostShareUser->save($share_user_data);

        $res = $this->Post->getMessageList($user_id);
        $this->assertNotEmpty($res);
    }

    function testGetMessageListPaging()
    {
        $user_id = 1;
        $team_id = 1;
        $this->Post->current_team_id = $team_id;
        $data = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'body'    => 'test',
            'type'    => Post::TYPE_MESSAGE
        ];
        $this->Post->save($data);
        $res = $this->Post->getMessageList($user_id, 1, 1);
        $this->assertNotEmpty($res);
    }

    function testConvertData()
    {
        $this->Post->current_team_id = '1';
        $owner_user_id = 1;
        $data = [
            'user_id' => $owner_user_id,
            'team_id' => 1,
            'body'    => 'test',
            'type'    => Post::TYPE_MESSAGE
        ];
        $this->Post->save($data);

        $data = [
            'team_id' => 1,
            'post_id' => $this->Post->getLastInsertID(),
            'body'    => 'comment test'
        ];
        $this->Post->Comment->save($data);

        $data = [
            'team_id' => 1,
            'post_id' => $this->Post->getLastInsertID(),
            'user_id' => 2,
        ];
        $this->Post->PostShareUser->save($data);

        $res = $this->Post->getMessageList($owner_user_id);
        $this->Post->convertData($res);
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
        $actual = $this->Post->getSubQueryFilterKrPostList($db, 1, Post::TYPE_ACTION, 0, 1);
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

        // In case that user posted circle post
        $this->Post->save([
            'id'      => 1,
            'body'    => 'test',
            'team_id' => $this->Post->current_team_id,
            'user_id' => 1,
            'type'    => Post::TYPE_NORMAL,
            'created' => $this->start_date,
        ]);
        $this->Post->PostShareCircle->save([
            'id'        => 1,
            'post_id'   => 1,
            'circle_id' => 1,
            'team_id'   => 1,
            'created'   => $this->start_date,
        ]);
        $res = $this->Post->isPostedCircleForSetupBy($this->Post->my_uid);
        $this->assertTrue($res);

        // In case that user posted notithing
        $this->Post->deleteAll([
            'Post.user_id'    => $this->Post->my_uid,
            'Post.created >=' => $this->Post->Team->EvaluateTerm->getPreviousTermData()['start_date'],
            'Post.created <=' => $this->end_date
        ]);
        $this->Post->PostShareCircle->deleteAll([
            'PostShareCircle.created >=' => $this->Post->Team->EvaluateTerm->getPreviousTermData()['start_date'],
            'PostShareCircle.created <=' => $this->end_date
        ]);
        $res = $this->Post->isPostedCircleForSetupBy($this->Post->my_uid);
        $this->assertFalse($res);
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
        $this->Post->Team->EvaluateTerm->current_team_id = $team_id;
        $this->Post->Team->EvaluateTerm->my_uid = $uid;
    }

    function _setTerm()
    {
        $this->Post->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_CURRENT);
        $this->Post->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_PREVIOUS);
        $this->Post->Team->EvaluateTerm->addTermData(EvaluateTerm::TYPE_NEXT);
        $this->current_date = REQUEST_TIMESTAMP;
        $this->start_date = $this->Post->Team->EvaluateTerm->getCurrentTermData()['start_date'];
        $this->end_date = $this->Post->Team->EvaluateTerm->getCurrentTermData()['end_date'];
        $timezone = $this->Post->Team->EvaluateTerm->getCurrentTermData()['timezone'];
        $this->start_date_format = date('Y-m-d', $this->start_date + $timezone * HOUR);
        $this->end_date_format = date('Y-m-d', $this->end_date + $timezone * HOUR);
    }

    function repQuote($str)
    {
        return str_replace($str, '`', '"');
    }

}
