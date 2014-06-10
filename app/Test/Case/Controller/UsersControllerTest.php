<?php
App::uses('UsersController', 'Controller');

/**
 * UsersController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction

 */
class UsersControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.cake_session',
        'app.user',
        'app.image',
        'app.badge',
        'app.team',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.comment_mention',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.images_post',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.thread',
        'app.message',
        'app.email',
        'app.send_mail',
        'app.oauth_token'
    );

    /**
     * testRegister method
     *
     * @return void
     */
    public function testRegister()
    {

        $this->generateMockSecurity();
        Configure::write('Config.language', 'ja');

        $this->testAction('/users/register', ['method' => 'GET', 'return' => 'contents']);
        $this->assertTextContains('新しいアカウントを作成', $this->view, "[ユーザ登録画面]通常のアクセス");

        $this->generateMockSecurity();
        $data = [
            'User'  => [
                'first_name' => '',
            ],
            'Email' => []
        ];
        $this->testAction(
             '/users/register',
             [
                 'return' => 'contents',
                 'data'   => $data,
                 'method' => 'post',
             ]
        );
        $this->assertTextContains('help-block text-danger', $this->view, "【異常系】[ユーザ登録画面]Post");

        $this->generateMockSecurity();
        $data = [
            'User'  => [
                'first_name'       => 'taro',
                'last_name'        => 'sato',
                'password'         => '12345678',
                'password_confirm' => '12345678',
                'agree_tos'        => true,
                'local_date'       => date('Y-m-d H:i:s'),
            ],
            'Email' => [
                ['email' => 'taro@sato.com'],
            ]
        ];
        $this->testAction(
             '/users/register',
             [
                 'return' => 'contents',
                 'data'   => $data,
                 'method' => 'post',
             ]
        );
        $this->assertTextNotContains('help-block text-danger', $this->view, "【正常系】[ユーザ登録画面]Post");
    }

    function testSentMailSuccess()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
            ]
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['tmp_email', 'test@aaa.com']]));
        $res = $this->testAction('/users/sent_mail', ['method' => 'GET', 'return' => 'contents']);
        $this->assertContains("おめでとうございます！", $res, "[正常]ユーザ仮登録");
    }

    function testLogin()
    {
        $this->testAction('/users/login', ['method' => 'GET', 'return' => 'contents']);
    }

    function testLoginAlreadyLoggedIn()
    {
        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth' => ['user'],
            ]
        ]);
        $value_map = [
            [null, true],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );

        $this->testAction('/users/login', ['method' => 'GET', 'return' => 'contents']);
    }

    function testLoggedInSuccess()
    {
        Configure::write('Config.language', 'en');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth' => ['user', 'loggedIn'],
            ]
        ]);
        $value_map = [
            [null, null],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        $this->generateMockSecurity();
        $data = [
            'User' => [
                'email'    => "to@email.com",
                'password' => "12345678",
            ]
        ];
        $this->testAction('/users/login', ['data' => $data, 'method' => 'post', 'return' => 'vars']);
    }

//    function testLoggedInSuccessRedirect()
//    {
//        Configure::write('Config.language', 'en');
//
//        /**
//         * @var UsersController $Users
//         */
//        $Users = $this->generate('Users', [
//            'components' => [
//                'Session' => ['read'],
//                'Auth'    => ['user', 'loggedIn'],
//            ]
//        ]);
//        $value_map = [
//            [null, null],
//            ['language', 'jpn'],
//            ['auto_language_flg', true],
//        ];
//        /** @noinspection PhpUndefinedMethodInspection */
//        $Users->Auth->staticExpects($this->any())->method('user')
//                    ->will($this->returnValueMap($value_map)
//            );
//        /** @noinspection PhpUndefinedMethodInspection */
//        $Users->Auth->expects($this->any())->method('loggedIn')
//                    ->will($this->returnValue(true));
//        $value_map = [
//            ['Auth.Redirect', '/'],
//        ];
//        /** @noinspection PhpUndefinedMethodInspection */
//        $Users->Session->expects($this->any())->method('read')
//                       ->will($this->returnValueMap($value_map));
//        $this->generateMockSecurity();
//        $data = [
//            'User' => [
//                'email'    => "to@email.com",
//                'password' => "12345678",
//            ]
//        ];
//        $this->testAction('/users/login', ['data' => $data, 'method' => 'post', 'return' => 'vars']);
//    }

    function testLoggedInFailed()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session' => ['setFlash'],
                'Auth'    => ['user', 'loggedIn'],
            ]
        ]);
        $value_map = [
            [null, null],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(false));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        $this->generateMockSecurity();
        $data = [
            'User' => [
                'email' => "abcdefgto@email.com",
                'password' => "12345678",
            ]
        ];
        $Users->Auth->logout();
        $this->testAction('/users/login', ['data' => $data, 'method' => 'post', 'return' => 'contents']);
//        $this->assertContains("メールアドレスもしくはパスワードが正しくありません。",$res,"[異常系]ログイン");
    }

    function testLogout()
    {
        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth',
            ]
        ]);
        $value_map = [
            [null, [
                'display_username' => 'test taro'
            ]],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        $this->testAction('/users/logout', ['method' => 'GET']);
    }

    function testSentMailFail()
    {
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
            ]
        ]);
        $value_map = [
            ['tmp_email', null],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Session->staticExpects($this->any())->method('read')
                       ->will($this->returnValueMap($value_map)
            );
        try {
            $this->testAction('/users/sent_mail', ['method' => 'GET', 'return' => 'contents']);
        } catch (NotFoundException $e) {
            $this->controller->beforeRender();

        }
        $this->assertTrue(isset($e), "[異常]ユーザ登録");
    }

    function testVerifySuccess()
    {
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
            ]
        ]);
        $value_map = [
            [null, null],
            ['Auth.redirect', '/aaa'],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($value_map));
        $this->testAction('/users/verify/1234567890', ['method' => 'GET', 'return' => 'contents']);
    }

    function testVerifyEmailNotLoggedIn()
    {
        $this->testAction('/users/verify/12345678', ['method' => 'GET', 'return' => 'contents']);
    }

    function testVerifyEmailLoggedInYet()
    {
        $this->testAction('/users/verify/12345', ['method' => 'GET', 'return' => 'contents']);
    }

    function testVerifyEmailNotFound()
    {
        try {
            $this->testAction('/users/verify/123456', ['method' => 'GET', 'return' => 'contents']);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]メールアドレス認証で存在しないトークンを指定された場合に例外処理");
    }

    function testSetAppLanguageAutoOn()
    {
        Configure::write('Config.language', 'en');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth',
            ]
        ]);
        $value_map = [
            [null, 1],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        $this->testAction('/users/register', ['method' => 'GET']);
        $this->assertEquals('en', Configure::read('Config.language'), "自動言語設定がonの場合は言語設定が無視される");
    }

    function testSetAppLanguageAutoOff()
    {
        Configure::write('Config.language', 'en');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth',
            ]
        ]);
        $value_map = [
            [null, 1],
            ['language', 'jpn'],
            ['auto_language_flg', false],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        $this->testAction('/users/register', ['method' => 'GET']);
        $this->assertEquals('jpn', Configure::read('Config.language'), "自動言語設定がoffの場合は言語設定が適用される");
    }

    function generateMockSecurity()
    {
        $Users = $this->generate('Users', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
            ],
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
    }

    function testAddProfileJpn()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth' => ['user', 'loggedIn'],
            ]
        ]);
        $value_map = [
            [null, [
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', 1]]));

        $this->testAction('/users/add_profile', ['method' => 'GET', 'return' => 'contents']);
        $this->assertContains('姓(母国語)', $this->contents, "[正常]日本語でローカル名の入力項目が表示される");
    }

    function testAddProfileEng()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth' => ['user', 'loggedIn'],
            ]
        ]);
        $value_map = [
            [null, [
                'last_first' => true,
                'language'   => 'eng'
            ]],
            ['language', 'eng'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Session->expects($this->any())->method('read')
            ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));

        $this->testAction('/users/add_profile', ['method' => 'GET', 'return' => 'contents']);
        $this->assertNotContains('姓(母国語)', $this->contents, "[正常]英語でローカル名の入力項目が表示されない");
    }

    function testAddProfileException()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session',
                'Auth' => ['user', 'loggedIn'],
            ]
        ]);
        $value_map = [
            [null, [
                'last_first' => true,
                'language'   => 'eng'
            ]],
            ['language', 'eng'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', null]]));

        try {
            $this->testAction('/users/add_profile', ['method' => 'GET', 'return' => 'contents']);
        } catch (NotFoundException $e) {

        }
        $this->assertTrue(isset($e), "[異常]新規ユーザ登録モード以外は例外発生");
    }
}
