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
        'app.user',
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
        'app.images_post'
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
        $data = [
            'Post' => [
                'body' => 'test'
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

    function testAjaxGetFeedNoPageNum()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->current_team_id = '1';

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedWithPageNum()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->_getPostsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->current_team_id = '1';

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/2', ['method' => 'GET']);
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
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentRead->current_team_id = '1';

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
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->current_team_id = '1';

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
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->PostLike->current_team_id = '1';

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
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->current_team_id = '1';

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
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->current_team_id = '1';

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
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->CommentLike->current_team_id = '1';

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

    /**
     * testDelete method
     *
     * @return void
     */
    public function testPostDeleteFail()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('posts/post_delete', ['method' => 'POST']);
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
    public function testCommentDeleteFail()
    {
        $this->_getPostsCommonMock();

        try {
            $this->testAction('posts/comment_delete', ['method' => 'POST']);
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
        $this->assertFalse(isset($e), "[正常]投稿削除");
    }

    function _getPostsCommonMock()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
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
        $Posts->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';
        return $Posts;
    }

}
