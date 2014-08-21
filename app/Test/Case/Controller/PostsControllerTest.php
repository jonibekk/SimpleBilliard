<?php
App::uses('PostsController', 'Controller');

/**
 * PostsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction

 */
class PostsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.cake_session',
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
        'app.notification',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token',
        'app.local_name',
        'app.image',
        'app.images_post',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
    );

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
                   ->will($this->returnValueMap([['test', ['title' => 'test', 'description' => 'test', 'image' => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png']]]));
        $data = [
            'Post' => [
                'body'  => 'test',
                'share' => 'public,circle_1,user_12'
            ],
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
        $data = ['Post' => ['comment_count' => 'test', 'public_flg' => 1]];
        $this->testAction('/posts/add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddComment()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();

        $data = [
            'user_id' => 1,
            'team_id' => 1,
            'body'    => 'test'
        ];
        $Posts->Post->save($data);
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Ogp->expects($this->any())->method('getOgpByUrlInText')
                   ->will($this->returnValueMap([['test', ['title' => 'test', 'description' => 'test', 'image' => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png']]]));
        $data = [
            'Comment' => [
                'body'    => 'test',
                'post_id' => 1,
            ],
        ];

        $this->testAction('/posts/comment_add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddCommentFailNotPost()
    {
        $this->_getPostsCommonMock();
        try {
            $this->testAction('/posts/comment_add',
                              ['method' => 'GET', 'return' => 'contents']);

        } catch (RuntimeException $e) {

        }
        $this->assertTrue(isset($e), "[異常]Postsコントローラのaddメソッドにgetでアクセス");
    }

    function testAddCommentFail()
    {
        $this->_getPostsCommonMock();
        $data = [];
        $this->testAction('/posts/comment_add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddCommentFailNotFound()
    {
        $this->_getPostsCommonMock();
        $data = ['Comment' => ['post_id' => 9999999999]];
        $this->testAction('/posts/comment_add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddCommentFailValidate()
    {
        $this->_getPostsCommonMock();
        $data = ['Comment' => ['post_id' => 1, 'comment_like_count' => 'test']];
        $this->testAction('/posts/comment_add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAjaxGetFeedNoPageNum()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedWithPageNum()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/page:2', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedWithCircle()
    {
        $this->_getPostsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/circle_id:1', ['method' => 'GET']);
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
        $this->testAction('/posts/ajax_get_comment/' . $post_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCommentException()
    {
        $this->_getPostsCommonMock();
        try {
            $this->testAction('/posts/ajax_get_comment/2', ['method' => 'GET']);
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
        $this->testAction('/posts/ajax_post_like/' . $post_id, ['method' => 'GET']);
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
        $this->testAction('/posts/ajax_post_like/' . $post_id, ['method' => 'GET']);
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
        $this->testAction('/posts/ajax_comment_like/' . $comment_id, ['method' => 'GET']);
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
        $this->testAction('/posts/ajax_comment_like/' . $comment_id, ['method' => 'GET']);
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
        $this->testAction('/posts/ajax_get_post_liked_users/' . $post_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_post_red_users/' . $post_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_comment_liked_users/' . $comment_id, ['method' => 'GET']);
        $this->testAction('/posts/ajax_get_comment_red_users/' . $comment_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetShareCirclesUsersModal()
    {
        $this->_getPostsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_share_circles_users_modal/1', ['method' => 'GET']);
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
            $this->testAction('posts/post_delete/0', ['method' => 'POST']);
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
            $this->testAction('posts/post_delete/' . $post['Post']['id'], ['method' => 'POST']);
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
            $this->testAction('posts/post_delete/' . $post['Post']['id'], ['method' => 'POST']);
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
            $this->testAction('posts/post_edit/0', ['method' => 'POST']);
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
            $this->testAction('posts/post_edit/' . $post['Post']['id'], ['method' => 'POST']);
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
                   ->will($this->returnValueMap([['test_aaaa', ['title' => 'test', 'description' => 'test', 'image' => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png']]]));

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
            $this->testAction('posts/post_edit/' . $post['Post']['id'], ['data' => $data, 'method' => 'POST']);
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
            $this->testAction('posts/post_edit/' . $post['Post']['id'], ['data' => $data, 'method' => 'POST']);
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
            $this->testAction('posts/comment_delete/0', ['method' => 'POST']);
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
            $this->testAction('posts/comment_delete/' . $comment['Comment']['id'], ['method' => 'POST']);
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
            $this->testAction('posts/comment_delete/' . $comment['Comment']['id'], ['method' => 'POST']);
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
            $this->testAction('posts/comment_edit/0', ['method' => 'POST']);
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
            $this->testAction('posts/comment_edit/' . $comment['Comment']['id'], ['method' => 'POST']);
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
                   ->will($this->returnValueMap([['test_aaaa', ['title' => 'test', 'description' => 'test', 'image' => 'http://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_no_str_60x60.png']]]));

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
            $this->testAction('posts/comment_edit/' . $comment['Comment']['id'], ['data' => $data, 'method' => 'POST']);
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
            $this->testAction('posts/comment_edit/' . $comment['Comment']['id'], ['data' => $data, 'method' => 'POST']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[異常ValidationError]コメント編集");
    }

    function testFeedCircle()
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
        $this->testAction('/post_permanent/1');
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
                'user_id'    => $user_id,
                'team_id'    => $team_id,
                'public_flg' => false,
                'body'       => 'test'
            ],
        ];
        $Posts->Post->save($post_data);

        $this->testAction('/post_permanent/' . $Posts->Post->getLastInsertID());
    }

    function _getPostsCommonMock()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'      => ['user', 'loggedIn'],
                'Security'  => ['_validateCsrf', '_validatePost'],
                'Ogp',
                'NotifyBiz' => ['sendNotify']
            ],
        ]);
        $value_map = [
            [null, [
                'id'         => '1',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['id', '1'],
            ['language', 'jpn'],
            ['auto_language_flg', true],
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
        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        //$Posts->NotifyBiz->expects($this->any())->method('sendNotify')->will($this->returnValue(true));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareCircle->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareUser->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->User->CircleMember->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->User->CircleMember->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->current_team_id = '1';

        return $Posts;
    }

}
