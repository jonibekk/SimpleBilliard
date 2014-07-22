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
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, [
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '1';
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
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, [
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '1';
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
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, [
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '1';
        $data = [];
        $this->testAction('/posts/add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddComment()
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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $Posts->Post->Comment->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';

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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $Posts->Post->Comment->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';
        try {
            $this->testAction('/posts/comment_add',
                              ['method' => 'GET', 'return' => 'contents']);

        } catch (RuntimeException $e) {

        }
        $this->assertTrue(isset($e), "[異常]Postsコントローラのaddメソッドにgetでアクセス");
    }

    function testAddCommentFail()
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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $Posts->Post->Comment->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';
        $data = [];
        $this->testAction('/posts/comment_add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAjaxGetFeedNoPageNum()
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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedWithPageNum()
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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/posts/ajax_get_feed/2', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetFeedException()
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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, [
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        $Posts->Post->Comment->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->Comment->current_team_id = '1';

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
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
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
        try {
            $this->testAction('/posts/ajax_get_comment/2', ['method' => 'GET']);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]commentをajax以外で取得しようとしたとき");
    }
}
