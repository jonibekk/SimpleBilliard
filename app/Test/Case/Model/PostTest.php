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
        'app.action_result',
        'app.key_result',
        'app.post',
        'app.user', 'app.notify_setting',
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
        $postData = [
            'Post' => [
                'body'       => 'test',
                'public_flg' => 1
            ]
        ];
        $res = $this->Post->addNormal($postData, Post::TYPE_NORMAL, $uid, $team_id);
        $this->assertNotEmpty($res, "[正常]投稿(uid,team_id指定)");

        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $this->Post->create();
        $res = $this->Post->addNormal($postData);
        $this->assertNotEmpty($res, "[正常]投稿(uid,team_id指定なし)");
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

    function testGetShareAllMemberList()
    {
        $this->_setDefault();
        $post_id = 1;

        $this->Post->getShareAllMemberList($post_id);
        $this->Post->id = $post_id;
        $this->Post->saveField('public_flg', true);
        $this->Post->getShareAllMemberList($post_id);
        $this->Post->getShareAllMemberList(9999999999);
    }

    public function testIsPublic()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $data = [
            'user_id'    => $uid,
            'team_id'    => $team_id,
            'public_flg' => true,
            'body'       => 'test'
        ];
        $this->Post->save($data);

        $res = $this->Post->isPublic($this->Post->id);
        $this->assertTrue($res);
    }

    public function testIsMyPost()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
        $data = [
            'user_id'    => $uid,
            'team_id'    => $team_id,
            'public_flg' => true,
            'body'       => 'test'
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
        $this->Post->getCount('me', 1, 1);
        $this->Post->getCount(null, 1, 1);
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
                'share_mode' => 1,
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
        $this->Post->current_team_id = 1;
        $this->Post->addGoalPost(Post::TYPE_CREATE_GOAL, 1, 1);
    }

    function testGetRelatedPostList()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Goal->Follower->current_team_id = 1;
        $this->Post->Goal->Collaborator->current_team_id = 1;

        $this->Post->Goal->Follower->save(['user_id' => 1, 'team_id' => 1, 'goal_id' => 1]);
        $this->Post->getRelatedPostList(1, 10000);
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
        $res = $this->Post->isPermittedGoalPost($this->Post->getLastInsertID());
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
        $res = $this->Post->isPermittedGoalPost($this->Post->getLastInsertID());
        $this->assertFalse($res);
    }

    function testIsPermittedGoalPostFailNotGoalFollowAndCollabo()
    {
        $this->Post->current_team_id = 1;
        $this->Post->my_uid = 1;
        $this->Post->Goal->Follower->current_team_id = 1;
        $this->Post->Goal->Collaborator->current_team_id = 1;
        $this->Post->Goal->Follower->my_uid = 1;
        $this->Post->Goal->Collaborator->my_uid = 1;

        $this->Post->save(['user_id' => 1, 'team_id' => 1, 'goal_id' => 1000, 'body' => 'test']);
        $res = $this->Post->isPermittedGoalPost($this->Post->getLastInsertID());
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
                'user_id'    => '1',
                'team_id'    => '1',
                'type'       => (int)7,
                'public_flg' => true,
                'circle_id'  => (int)1,
            ]
        ];

        $actual = $this->Post->createCirclePost(1);
        unset($actual['Post']['created']);
        unset($actual['Post']['modified']);
        unset($actual['Post']['id']);

        $this->assertEquals($expected, $actual);
    }

    function _setDefault()
    {
        $uid = '1';
        $team_id = '1';
        $this->Post->my_uid = $uid;
        $this->Post->current_team_id = $team_id;
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
