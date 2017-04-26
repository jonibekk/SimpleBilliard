<?php App::uses('GoalousControllerTestCase', 'Test');
App::uses('PostsController', 'Controller');

/**
 * PostsController Test Case
 * @method testAction($url = '', $options = array()) GoalousControllerTestCase::_testAction
 */
class PostsControllerTest extends GoalousControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.action_result_file',
        'app.attached_file',
        'app.post_file',
        'app.comment_file',
        'app.goal_category',
        'app.term',
        'app.action_result',
        'app.key_result',

        'app.goal',
        'app.evaluation_setting',
        'app.key_result',
        'app.goal_member',
        'app.follower',
        'app.cake_session',
        'app.post',
        'app.user',
        'app.notify_setting',
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
        'app.email',
        'app.oauth_token',
        'app.local_name',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.evaluation',
    );

    function testMessage()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/posts/message/', ['method' => 'GET']);
    }

    function testMessageList()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/posts/message_list/', ['method' => 'GET']);
    }

    function testAjaxGetMessageList()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_message_list/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetMessageInfo()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_message_info/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetMessageInfoFromMe()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_message_info/8', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetMessage()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $post_id = 1;
        $this->testFileUploadMessagePageRender($post_id, true);
        $this->testAction('/posts/ajax_get_message/' . $post_id . '/2/3', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetMessageNotImage()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $post_id = 1;
        $this->testFileUploadMessagePageRender($post_id, false);
        $this->testAction('/posts/ajax_get_message/' . $post_id . '/2/3', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxPutMessage()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_put_message/1/',
            ['method' => 'POST', 'data' => ['body' => 'test', 'file_redis_key' => 'xxx']]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxPutMessageRead()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_put_message_read/1/2', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAdd()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Post' => [
                'body'         => 'test',
                'share_public' => 'public,circle_1,user_12',
                'share_secret' => '',
                'share_range'  => 'public',
            ],
        ];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testAddMessage()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Post'      => [
                'body'         => 'test',
                'share_public' => 'public,circle_1,user_12',
                'share_secret' => '',
                'share_range'  => 'public',
            ],
            'socket_id' => 'test',
        ];
        $this->testAction('/posts/add_message',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddMessageNoSocketId()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Post' => [
                'body'         => 'test',
                'share_public' => 'public,circle_1,user_12',
                'share_secret' => '',
                'share_range'  => 'public',
            ],
        ];
        $this->testAction('/posts/add_message',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddSecretCircle()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));

        // 秘密サークルへの投稿
        $data = [
            'Post' => [
                'body'         => 'test',
                'share_public' => '',
                'share_secret' => 'circle_4',
                'share_range'  => 'secret',
            ],
        ];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testAddOnlyCircle()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Post'      => [
                'body'         => 'test',
                'share_public' => 'circle_1',
                'share_secret' => '',
                'share_range'  => 'public',
                'team_id'      => '1'
            ],
            'socket_id' => 'test',
        ];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddOnlyMember()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Post'      => [
                'body'         => 'test',
                'share_public' => 'user_1',
                'share_secret' => '',
                'share_range'  => 'public',
                'team_id'      => '1'
            ],
            'socket_id' => 'test',
        ];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddNotExistOgp()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        $data = [
            'Post'      => [
                'body'         => 'test',
                'share_public' => 'public,circle_1,user_12',
                'share_secret' => '',
                'share_range'  => 'public',
                'team_id'      => '1'
            ],
            'socket_id' => 'test',
        ];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddFailNotPost()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        try {
            $this->testAction('/posts/add',
                ['method' => 'GET', 'return' => 'contents']);

        } catch (RuntimeException $e) {

        }
        $this->assertTrue(isset($e), "[異常]Postsコントローラのaddメソッドにgetでアクセス");
    }

    function testAddFail()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        $data = [];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddFailValidate()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        $data = ['Post' => ['comment_count' => 'test']];
        $this->testAction('/posts/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testPushCommentToPost()
    {
        $Posts = $this->_getPostsCommonMock();

        $date = time();
        $socket_id = 'test';
        $post_id = 1;
        $Posts->request->data['socket_id'] = $socket_id;
        $hash = Security::hash($date);
        $data = [
            'notify_id'         => $hash,
            'is_comment_notify' => true,
            'post_id'           => $post_id
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->NotifyBiz->expects($this->any())->method('commentPush')
                         ->will($this->returnValueMap([[$socket_id, $data, true]]));

        $Posts->_pushCommentToPost($post_id, $date);
    }

    function testAddCommentSuccessWithoutSocketId()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'body'    => 'test'
        ];
        $Posts->Post->save($data);
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           ['title' => 'test', 'description' => 'test', 'image' => null]
                       ]
                   ]));
        $data = [
            'Comment' => [
                'body'    => 'test',
                'team_id' => 1,
                'post_id' => 1,
            ],
        ];

        $this->testAction('/posts/ajax_add_comment/',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddCommentActionSuccessWithoutSocketId()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'body'    => 'test',
            'type'    => 3
        ];
        $Posts->Post->save($data);
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Comment' => [
                'body'    => 'test',
                'post_id' => $Posts->Post->getLastInsertID(),
                'team_id' => 1,
            ],
        ];

        $this->testAction('/posts/ajax_add_comment/',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddCommentFailNotExistsWithoutSocketId()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));
        $data = [
            'Comment' => [
                'body'    => 'test',
                'post_id' => 10000000000,
                'team_id' => 1,
            ],
        ];

        $this->testAction('/posts/ajax_add_comment/',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddCommentFailNotPost()
    {
        $this->_getPostsCommonMock();
        try {
            $this->testAction('/posts/ajax_add_comment',
                ['method' => 'GET', 'return' => 'contents']);

        } catch (RuntimeException $e) {

        }
        $this->assertTrue(isset($e), "[異常]Postsコントローラのaddメソッドにgetでアクセス");
    }

    function testAddCommentFail()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [];
        $this->testAction('/posts/ajax_add_comment',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddCommentFailNotFound()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = ['Comment' => ['post_id' => 9999999999]];
        $this->testAction('/posts/ajax_add_comment',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddCommentFailValidate()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = ['Comment' => ['post_id' => 1, 'comment_like_count' => 'test']];
        $this->testAction('/posts/ajax_add_comment',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedNoPageNum()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_feed/', ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedWithPageNum()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_feed/page:2', ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetActionListMoreNoPageNum()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_action_list_more/', ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetGoalActionFeed()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_goal_action_feed/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetActionListMoreWithPageNum()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_action_list_more/page:2', ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedWithCircle()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_feed/circle_id:1', ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedException()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('/posts/ajax_get_feed/', ['method' => 'GET']);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]feedをajax以外で取得しようとしたとき");
    }

    function testAjaxGetFeedMonthIndex()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_feed/page:1/month_index:2', ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetNewCommentFormSuccess()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_new_comment_form/post_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetNewCommentFormFail()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_new_comment_form/post_id:9999', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditPostFormSuccess()
    {
        $Posts = $this->_getPostsCommonMock();
        $user_id = 1;
        $team_id = 1;
        $post_data = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_edit_post_form/post_id:' . $Posts->Post->getLastInsertID(),
            ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditPostFormFail()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_edit_post_form/post_id:9999', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditCommentFormSuccess()
    {
        $Posts = $this->_getPostsCommonMock();
        $user_id = 1;
        $team_id = 1;
        $post_data = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_edit_comment_form/comment_id:' . $Posts->Post->Comment->getLastInsertID(),
            ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditCommentFormFail()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_edit_comment_form/9999', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetComment()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        //投稿記事を20個いれる
        $user_id = 1;
        $team_id = 1;

        $post_data[] = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);
        $post_id = $Posts->Post->getLastInsertID();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_old_comment/post_id:' . $post_id . '/5/long_text:1/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCommentException()
    {
        $this->_getPostsCommonMock();
        try {
            $this->testAction('/posts/ajax_get_comment/comment_id:2', ['method' => 'GET']);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]commentをajax以外で取得しようとしたとき");
    }

    function testAjaxGetLatestComment()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        //投稿記事を20個いれる
        $user_id = 1;
        $team_id = 1;
        $get_num = 3;

        $post_data[] = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);
        $post_id = $Posts->Post->getLastInsertID();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_latest_comment/post_id:' . $post_id . '/' . $get_num, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetLatestCommentException()
    {
        $this->_getPostsCommonMock();
        try {
            $this->testAction('/posts/ajax_get_latest_comment/post_id:2/2', ['method' => 'GET']);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]commentをajax以外で取得しようとしたとき");
    }

    function testAjaxPostLike()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        //投稿記事を20個いれる
        $user_id = 1;
        $team_id = 1;

        $post_data[] = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);
        $post_id = $Posts->Post->getLastInsertID();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_post_like/post_id:' . $post_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxPostLikeExists()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        //投稿記事を20個いれる
        $user_id = 1;
        $team_id = 1;

        $post_data[] = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ],
        ];
        $Posts->Post->saveAll($post_data);
        $post_id = $Posts->Post->getLastInsertID();
        $post_like = [
            'PostLike' => [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'team_id' => $team_id,
            ]
        ];
        $Posts->Post->PostLike->save($post_like);

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_post_like/post_id:' . $post_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxCommentLike()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        //投稿記事を20個いれる
        $user_id = 1;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);
        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $post['Post']['id'],
                'body'    => 'test'
            ]
        ];
        $comment = $Posts->Post->Comment->save($comment_data);
        $comment_id = $comment['Comment']['id'];
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_comment_like/comment_id:' . $comment_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxCommentLikeExists()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        //投稿記事を20個いれる
        $user_id = 1;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);
        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $post['Post']['id'],
                'body'    => 'test'
            ]
        ];
        $comment = $Posts->Post->Comment->save($comment_data);
        $comment_id = $comment['Comment']['id'];
        $comment_like_data = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'comment_id' => $comment_id,
        ];
        $Posts->Post->Comment->CommentLike->save($comment_like_data);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_comment_like/comment_id:' . $comment_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetLikedRedUsers()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 1;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);
        $post_id = $post['Post']['id'];
        $post_like_read_data = [
            'user_id' => $user_id,
            'team_id' => $team_id,
            'post_id' => $post_id,
        ];
        $Posts->Post->PostLike->save($post_like_read_data);
        $Posts->Post->PostRead->save($post_like_read_data);
        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $post_id,
                'body'    => 'test'
            ]
        ];
        $comment = $Posts->Post->Comment->save($comment_data);
        $comment_id = $comment['Comment']['id'];
        $comment_read_like_data = [
            'user_id'    => $user_id,
            'team_id'    => $team_id,
            'comment_id' => $comment_id,
        ];
        $Posts->Post->Comment->CommentLike->save($comment_read_like_data);
        $Posts->Post->Comment->CommentRead->save($comment_read_like_data);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_post_liked_users/post_id:' . $post_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_post_red_users/post_id:' . $post_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_message_red_users/post_id:' . $post_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_comment_liked_users/comment_id:' . $comment_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_comment_red_users/comment_id:' . $comment_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_message_red_users/comment_id:' . $comment_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetShareCirclesUsersModal()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_share_circles_users_modal/post_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetShareMessageModal()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_share_message_modal/post_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetUserPagePostFeed()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_user_page_post_feed/user_id:2/month_index:1/page:1',
            ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCircleFiles()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_get_circle_files/circle_id:1/month_index:1/page:1',
            ['method' => 'GET']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('html', $data);
        $this->assertArrayHasKey('count', $data);
        $this->assertArrayHasKey('page_item_num', $data);
        $this->assertArrayHasKey('start', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadFile()
    {
        $this->_getPostsCommonMock();
        $_FILES = ['file' => []];

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_upload_file/', ['method' => 'POST']);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('id', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxRemoveFile()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $res = $this->testAction('/posts/ajax_remove_file/',
            ['method' => 'POST', 'data' => ['AttachedFile' => ['file_id' => 'xxx']]]);
        $data = json_decode($res, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('msg', $data);
        $this->assertArrayHasKey('id', $data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    /**
     * testDelete method
     *
     * @return void
     */
    public function testPostDeleteFail()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('posts/post_delete/post_id:0', ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]投稿削除");
    }

    public function testPostDeleteNotOwn()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 10;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);
        $Posts->Post->Team->TeamMember->myStatusWithTeam['TeamMember']['admin_flg'] = 0;
        try {
            $this->testAction('posts/post_delete/post_id:' . $post['Post']['id'], ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]所有していない投稿削除");
    }

    public function testPostDeleteSuccess()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 1;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);

        try {
            $this->testAction('posts/post_delete/post_id:' . $post['Post']['id'], ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[正常]投稿削除");
    }

    /**
     * testDelete method
     *
     * @return void
     */
    public function testPostEditFail()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('posts/post_edit/0', ['method' => 'PUT']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]投稿編集");
    }

    public function testPostEditNotOwn()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 10;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);

        try {
            $this->testAction('posts/post_edit/post_id:' . $post['Post']['id'], ['method' => 'PUT']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]所有していない投稿編集");
    }

    public function testPostEditSuccess()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test_aaaa',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));

        $user_id = 1;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);

        $data = [
            'Post'         => [
                'body' => 'test_aaaa'
            ],
            'photo_delete' => [
                1 => 1
            ]
        ];

        try {
            $this->testAction('posts/post_edit/post_id:' . $post['Post']['id'], ['data' => $data, 'method' => 'PUT']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[正常]投稿編集");
    }

    public function testPostEditValidationError()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 1;
        $team_id = 1;

        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $post = $Posts->Post->save($post_data);

        $data = [
            'Post' => [
                'important_flg' => 'test'
            ],
        ];

        try {
            $this->testAction('posts/post_edit/post_id:' . $post['Post']['id'], ['data' => $data, 'method' => 'PUT']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[異常ValidationError]投稿編集");
    }

    /**
     * testDelete method
     *
     * @return void
     */
    public function testCommentDeleteFail()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('posts/comment_delete/comment_id:0', ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]コメント削除");
    }

    public function testCommentDeleteNotOwn()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 10;
        $team_id = 1;

        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $comment = $Posts->Post->Comment->save($comment_data);
        $Posts->Post->Team->TeamMember->myStatusWithTeam['TeamMember']['admin_flg'] = 0;
        try {
            $this->testAction('posts/comment_delete/comment_id:' . $comment['Comment']['id'], ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]所有していないコメント削除");
    }

    public function testCommentDeleteSuccess()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 1;
        $team_id = 1;

        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $comment = $Posts->Post->Comment->save($comment_data);

        try {
            $this->testAction('posts/comment_delete/comment_id:' . $comment['Comment']['id'], ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[正常]コメント削除");
    }

    /**
     * testDelete method
     *
     * @return void
     */
    public function testCommentEditFail()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('posts/comment_edit/comment_id:0', ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]コメント編集");
    }

    public function testCommentEditNotOwn()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 10;
        $team_id = 1;

        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $comment = $Posts->Post->Comment->save($comment_data);

        try {
            $this->testAction('posts/comment_edit/comment_id:' . $comment['Comment']['id'], ['method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]所有していないコメント編集");
    }

    public function testCommentEditSuccess()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([
                       [
                           'test_aaaa',
                           [
                               'title'       => 'test',
                               'description' => 'test',
                               'image'       => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png'
                           ]
                       ]
                   ]));

        $user_id = 1;
        $team_id = 1;

        $comment_data = [
            'Comment'      => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'photo_delete' => [
                1 => 1
            ]
        ];
        $comment = $Posts->Post->Comment->save($comment_data);
        $data = [
            'Comment' => [
                'body' => 'test_aaaa'
            ],
        ];

        try {
            $this->testAction('posts/comment_edit/comment_id:' . $comment['Comment']['id'],
                ['data' => $data, 'method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[正常]コメント編集");
    }

    public function testCommentEditValidationError()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $user_id = 1;
        $team_id = 1;

        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $comment = $Posts->Post->Comment->save($comment_data);
        $data = [
            'Comment' => [
                'comment_like_count' => 'test_aaaa'
            ],
        ];

        try {
            $this->testAction('posts/comment_edit/comment_id:' . $comment['Comment']['id'],
                ['data' => $data, 'method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[異常ValidationError]コメント編集");
    }

    function testFeedShareUser()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        $user_id = 1;
        $team_id = 1;
        $post_data = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);
        $share_user_data = [
            'PostShareUser' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $Posts->Post->getLastInsertID()
            ]
        ];
        $Posts->Post->PostShareUser->save($share_user_data);
        $this->testAction('/circle_feed/1');
    }

    function testFeedCircleSuccess()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/circle_feed/1');
    }

    function testFeedCircleNotFound()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/circle_feed/9999999999');
    }

    function testFeedPermanentLink()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        $user_id = 1;
        $team_id = 1;
        $post_data = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Posts->Post->saveAll($post_data);
        $share_user_data = [
            'PostShareUser' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $Posts->Post->getLastInsertID()
            ]
        ];
        $Posts->Post->PostShareUser->save($share_user_data);
        $this->testAction('/post_permanent/1/notify_id:1234');
    }

    function testFeedPermanentLinkNotShare()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        $user_id = 5;
        $team_id = 1;
        $post_data = [
            'Post' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
        ];
        $Posts->Post->save($post_data);

        $this->testAction('/post_permanent/' . $Posts->Post->getLastInsertID());
    }

    function testFeedGoal()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/posts/feed/filter_goal:1');
    }

    function testGetTotalShareUserCount()
    {
        $Posts = $this->_getPostsCommonMock();
        $circles = [
            [
                'CircleMember' => [
                    ['User' => ['id' => 1]],
                    ['User' => ['id' => 2]],
                ]
            ],
            [
                'CircleMember' => [
                    ['User' => ['id' => 2]],
                    ['User' => ['id' => 3]],
                ]
            ],
        ];
        $users = [
            ['User' => ['id' => 1]],
            ['User' => ['id' => 4]],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $res = $Posts->_getTotalShareUserCount($circles, $users);
        $this->assertEquals(4, $res);
    }

    function testJoinCircleSuccess()
    {
        $Posts = $this->_getPostsCommonMock();

        $data = [
            'name'    => 'test',
            'team_id' => 1,
        ];

        $Posts->Post->Circle->save($data);
        $circle_id = $Posts->Post->Circle->getLastInsertID();
        $this->testAction("/posts/join_circle/circle_id:{$circle_id}", ['method' => 'get']);
    }

    function testJoinCircleFailed()
    {
        $this->_getPostsCommonMock();

        $this->testAction('/posts/join_circle/circle_id:1', ['method' => 'get']);
    }

    function testJoinCircleNotFound()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/posts/join_circle/', ['method' => 'get']);
    }

    function testJoinCircleNotExists()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/posts/join_circle/circle_id:999999', ['method' => 'get']);
    }

    function testUnJoinCircle()
    {
        $this->_getPostsCommonMock();

        $circle_id = '1';
        $this->testAction("/posts/unjoin_circle/circle_id:{$circle_id}", ['method' => 'get']);
    }

    function testUnJoinCircleAllTeam()
    {
        $Posts = $this->_getPostsCommonMock();

        $circle_id = $Posts->Post->Circle->getTeamAllCircleId();
        $this->testAction("/posts/unjoin_circle/circle_id:{$circle_id}", ['method' => 'get']);
    }

    function testUnJoinCircleNotFound()
    {
        $this->_getPostsCommonMock();
        $this->testAction("/posts/unjoin_circle/", ['method' => 'get']);
    }

    function testUserCircleStatusAdmin()
    {
        $Posts = $this->_getPostsCommonMock();
        $this->assertEquals('admin', $Posts->_userCircleStatus(1));
    }

    function testUserCircleStatusJoined()
    {
        $Posts = $this->_getPostsCommonMock();
        $this->assertEquals('joined', $Posts->_userCircleStatus(2));
    }

    function testUserCircleStatusNodJoined()
    {
        $Posts = $this->_getPostsCommonMock();
        $this->assertEquals('not_joined', $Posts->_userCircleStatus(100000000));
    }

    function testCircleToggleStatusSuccess()
    {
        $this->_getPostsCommonMock();
        $this->testAction("/posts/circle_toggle_status/circle_id:20/1", ['method' => 'get']);
    }

    function testCircleToggleStatusFailure()
    {
        $this->_getPostsCommonMock();
        try {
            $this->testAction("/posts/circle_toggle_status/circle_id:20/1111", ['method' => 'get']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "Invalid Status Request");
    }

    function testAttachedFileList()
    {
        $this->_getPostsCommonMock();
        $this->testAction('/posts/attached_file_list/circle_id:1', ['method' => 'GET']);
    }

    function testGetRedirectUrl()
    {
        $Posts = $this->_getPostsCommonMock();
        $value_map = [
            [
                null,
                true,
                '/posts/attached_file_list/circle_id:1'
            ]
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->expects($this->any())->method('referer')
              ->will($this->returnValueMap($value_map));
        $res = $Posts->_getRedirectUrl();
        $this->assertEquals('/circle_feed/1', $res);
    }

    function testAttachedFileDownload()
    {
        $Posts = $this->_getPostsCommonMock();

        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->expects($this->any())
              ->method('_getHttpSocket')
              ->will($this->returnValue(new HttpSocketMockSuccess()));

        $this->testAction('/posts/attached_file_download/file_id:1', ['method' => 'GET']);
    }

    function testAttachedFileDownloadFailed()
    {
        $Posts = $this->_getPostsCommonMock();

        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->expects($this->any())
              ->method('_getHttpSocket')
              ->will($this->returnValue(new HttpSocketMockFailed()));

        try {
            $this->testAction('/posts/attached_file_download/file_id:1', ['method' => 'GET']);
        } catch (NotFoundException $e) {
        }
    }

    function testAttachedFileDownloadInvalidParam()
    {
        $this->_getPostsCommonMock();

        // ダウンロード出来ないファイル
        try {
            $this->testAction('/posts/attached_file_download/file_id:8', ['method' => 'GET']);
        } catch (NotFoundException $e) {
        }

        // 存在しないデータ
        try {
            $this->testAction('/posts/attached_file_download/file_id:9999888', ['method' => 'GET']);
        } catch (NotFoundException $e) {
        }
    }

    function testGetHttpRequest()
    {
        $Posts = $this->generate('Posts', [
            'methods'    => ['referer'],
            'components' => [
                'Session',
                'Auth'      => ['user', 'loggedIn'],
                'Security'  => ['_validateCsrf', '_validatePost'],
                'Ogp',
                'NotifyBiz' => ['sendNotify', 'commentPush', 'push']
            ],
        ]);

        /** @noinspection PhpUndefinedMethodInspection */
        $socket = $Posts->_getHttpSocket();
        $this->assertTrue(method_exists($socket, 'get'));
    }

    function testFileUploadMessagePageRender($post_id, $attached_flg = false)
    {
        $user_id = 1;
        $team_id = 1;

        $comment = $this->testSaveCommentData($team_id, $user_id, $post_id);
        $comment_id = $comment['Comment']['id'];

        if ($attached_flg === true) {
            $attached_file = $this->testSaveAttachedFileData($team_id, $user_id);
            $attached_file_id = $attached_file['AttachedFile']['id'];
            $this->testSaveCommentFileData($team_id, $comment_id, $attached_file_id);
        }
    }

    function testSaveCommentData($team_id, $user_id, $post_id)
    {
        $Posts = $this->_getPostsCommonMock();
        $comment_data = [
            'Comment' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $post_id,
                'body'    => 'test'
            ],
        ];
        return $Posts->Post->Comment->save($comment_data);
    }

    function testSaveAttachedFileData($team_id, $user_id)
    {
        $Posts = $this->_getPostsCommonMock();
        $attached_file_data = [
            'user_id'            => $user_id,
            'team_id'            => $team_id,
            'attached_file_name' => 'test_image.jpeg',
            'file_ext'           => 'jpeg'
        ];
        return $Posts->Post->Comment->CommentFile->AttachedFile->save($attached_file_data);
    }

    function testSaveCommentFileData($team_id, $comment_id, $attached_file_id)
    {
        $Posts = $this->_getPostsCommonMock();
        $comment_file_data = [
            'team_id'          => $team_id,
            'comment_id'       => $comment_id,
            'attached_file_id' => $attached_file_id
        ];
        return $Posts->Post->Comment->CommentFile->save($comment_file_data);
    }

    function _getPostsCommonMock()
    {
        /**
         * @var PostsController $Posts
         */
        $Posts = $this->generate('Posts', [
            'methods'    => ['referer', '_getHttpSocket'],
            'components' => [
                'Session',
                'Auth'      => ['user', 'loggedIn'],
                'Security'  => ['_validateCsrf', '_validatePost'],
                'Ogp',
                'NotifyBiz' => ['sendNotify', 'commentPush', 'push']
            ],
        ]);
        $value_map = [
            [
                null,
                [
                    'id'              => '1',
                    'last_first'      => true,
                    'language'        => 'jpn',
                    'photo_file_name' => ''
                ]
            ],
            ['id', '1'],
            ['language', 'jpn'],
            ['auto_language_flg', true]
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));

        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        //$Posts->NotifyBiz->expects($this->any())->method('sendNotify')->will($this->returnValue(true));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareCircle->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareUser->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->User->CircleMember->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->User->CircleMember->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->ActionResult->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->ActionResult->current_team_id = '1';
        $Posts->Team->Term->my_uid = 1;
        $Posts->Team->Term->current_team_id = 1;
        $Posts->Team->Circle->current_team_id = 1;

        $Posts->Post->current_team_id = 1;
        $Posts->Post->my_uid = 1;
        $Posts->Post->PostShareCircle->current_team_id = 1;
        $Posts->Post->PostShareCircle->my_uid = 1;
        $Posts->Post->PostShareUser->current_team_id = 1;
        $Posts->Post->PostShareUser->my_uid = 1;
        $Posts->Post->PostFile->AttachedFile->current_team_id = 1;
        $Posts->Post->PostFile->AttachedFile->my_uid = 1;
        $Posts->Post->PostFile->current_team_id = 1;
        $Posts->Post->PostFile->my_uid = 1;
        $Posts->Post->Comment->CommentFile->current_team_id = 1;
        $Posts->Post->Comment->CommentFile->my_uid = 1;

        return $Posts;
    }

}

// テスト用モック
class HttpSocketMockSuccess
{
    public function get($url)
    {
        $obj = new stdClass();
        $obj->body = "success";
        return $obj;
    }
}

class HttpSocketMockFailed
{
    public function get($url)
    {
        $obj = new stdClass();
        $obj->body = "";
        return $obj;
    }
}
