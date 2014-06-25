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
        'app.local_name',
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

    function testLoggedInFailed()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
            'components' => [
                'Session' => ['setFlash'],
                'Auth' => ['user'],
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
        $this->testAction('/users/verify/123456', ['method' => 'GET', 'return' => 'contents']);
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
            ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));

        $this->testAction('/users/add_profile', ['method' => 'GET', 'return' => 'contents']);
        $this->assertContains('姓(日本語)', $this->contents, "[正常]日本語でローカル名の入力項目が表示される");
    }

    function testAddProfilePost()
    {
        Configure::write('Config.language', 'ja');

        /**
         * @var UsersController $Users
         */
        $Users = $this->generate('Users', [
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
        $Users->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

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
        $data = [
            'User' => [
                'local_last_name'  => 'めい',
                'local_first_name' => 'せい',
            ]
        ];
        $this->testAction('/users/add_profile', ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
        $this->assertRegExp("/" . preg_quote("/teams/add", "/") . "$/", $this->headers["Location"],
                            "[正常]Post後にチーム作成画面へ遷移");
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

    function testPasswordReset()
    {
        $this->testAction('/users/password_reset');
        $this->testAction('/users/password_reset/aaaaa');
    }

    function testPasswordResetAuthenticated()
    {
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
                'id' => "xxxxxx",
            ]],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        try {
            $this->testAction('users/password_reset');
        } catch (Exception $e) {
        }
        $this->assertTrue(isset($e), "[異常]パスワードリセット ログイン中の例外");

    }

    function testPasswordResetPost()
    {
        App::uses('UserTest', 'Test/Case/Model');
        $UserTest = new UserTest;
        $UserTest->setUp();
        $uid = $UserTest->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $UserTest->User->Email->findByUserId($uid);

        $data = ['User' => ['email' => $email['Email']['email']]];
        $this->testAction('users/password_reset', ['data' => $data]);
    }

    function testPasswordResetPostToken()
    {
        App::uses('UserTest', 'Test/Case/Model');
        $UserTest = new UserTest;
        $UserTest->setUp();
        $uid = $UserTest->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $user = $UserTest->User->findById($uid);

        $this->testAction('users/password_reset/' . $user['User']['password_token']);
    }

    function testPasswordResetPostPassword()
    {
        $Users = $this->generate('Users');
        $basic_data = [
            'User'  => [
                'first_name'     => 'basic',
                'last_name'      => 'user',
                'password'   => 'aaaaaaaaaa',
                'password_token' => 'abcde',
                'active_flg' => true,
            ],
            'Email' => [
                [
                    'email' => 'basic@email.com',
                    'email_verified'      => true,
                    'email_token_expires' => date('Y-m-d H:i:s', time() + 60 * 60)
                ]
            ]
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Users->User->saveAll($basic_data);
        /** @noinspection PhpUndefinedFieldInspection */
        $Users->User->save(['primary_email_id' => $Users->User->Email->getLastInsertID()]);

        $data = [
            'User' => [
                'password'         => '12345678',
                'password_confirm' => '12345678',
            ]
        ];
        $this->testAction('users/password_reset/abcde', ['data' => $data, 'method' => 'POST']);
    }

    function testTokenResend()
    {
        $this->_testAction('users/token_resend');
    }

    function testTokenResendAuthenticated()
    {
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
                'id' => "xxxxxx",
            ]],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        try {
            $this->testAction('users/token_resend');
        } catch (Exception $e) {
        }
        $this->assertTrue(isset($e), "[異常]トークン再送。ログイン中は例外処理");
    }

    function testTokenResendPostEmail()
    {
        $Users = $this->generate('Users');
        $basic_data = [
            'User'  => [
                'first_name'     => 'basic',
                'last_name'      => 'user',
                'password'       => 'aaaaaaaaaa',
                'password_token' => 'abcde',
                'active_flg'     => false,
            ],
            'Email' => [
                [
                    'email'               => 'basic@email.com',
                    'email_verified'      => false,
                    'email_token_expires' => date('Y-m-d H:i:s', time() + 60 * 60)
                ]
            ]
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Users->User->saveAll($basic_data);
        /** @noinspection PhpUndefinedFieldInspection */
        $Users->User->save(['primary_email_id' => $Users->User->Email->getLastInsertID()]);

        $data = [
            'User' => [
                'email' => 'basic@email.com',
            ]
        ];
        $this->testAction('users/token_resend', ['data' => $data, 'method' => 'POST']);
    }

    function testSetting()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));

        $this->testAction('users/settings');
    }

    function testSettingPutSuccess()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $data = [
            'User' => [
                'update_email_flg' => true,
            ]
        ];

        $this->testAction('users/settings', ['method' => 'PUT', 'data' => $data]);
    }

    function testSettingPutFail()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $data = [
            'User' => [
                'first_name' => null,
                'last_name'  => null,
            ]
        ];

        $this->testAction('users/settings', ['method' => 'PUT', 'data' => $data]);
    }

    function testChangePasswordFail()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $data = [
            'User' => [
                'old_password'     => null,
                'password'         => null,
                'password_confirm' => null
            ]
        ];

        $this->testAction('users/change_password', ['method' => 'PUT', 'data' => $data]);
    }

    function testChangePasswordSuccess()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        $uid = "537ce224-54b0-4081-b044-433dac11aaab";
        $Users->User->id = $uid;
        $Users->User->saveField('password', $Users->User->generateHash('12345678'));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $data = [
            'User' => [
                'id'               => '537ce224-54b0-4081-b044-433dac11aaab',
                'old_password'     => '12345678',
                'password'         => '12345678',
                'password_confirm' => '12345678'
            ]
        ];
        $this->testAction('users/change_password', ['method' => 'PUT', 'data' => $data]);
    }

    function testChangePasswordFailNotSame()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        $uid = "537ce224-54b0-4081-b044-433dac11aaab";
        $Users->User->id = $uid;
        $Users->User->saveField('password', $Users->User->generateHash('12345678'));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $data = [
            'User' => [
                'id'           => '537ce224-54b0-4081-b044-433dac11aaab',
                'old_password' => '1234567890',
                'password'         => '12345678',
                'password_confirm' => '12345678'
            ]
        ];
        $this->testAction('users/change_password', ['method' => 'PUT', 'data' => $data]);
    }

    function testChangePasswordException()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        try {
            $this->testAction('users/change_password', ['method' => 'GET']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[例外]パスワード変更");
    }

    function testChangeEmailVerifySuccess()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $token = "token_test0123456789";
        try {
            $this->testAction('users/change_email_verify/' . $token, ['method' => 'GET']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[正常]メアド追加");
    }

    function testChangeEmailVerifyFail()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        $token = "token_test0123456789aaaa";
        try {
            $this->testAction('users/change_email_verify/' . $token, ['method' => 'GET']);
        } catch (NotFoundException $e) {
        }
        $this->assertFalse(isset($e), "[異常]メアド変更");
    }

    function testChangeEmailFail()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        try {
            $this->testAction('users/change_email', ['method' => 'POST', 'data' => []]);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]メアド追加");
    }

    function testChangeEmailFailNotData()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11aaab"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        try {
            $this->testAction('users/change_email', ['method' => 'PUT', 'data' => ['User' => ['email' => null]]]);
        } catch (NotFoundException $e) {
        }
    }

    function testChangeEmailSuccess()
    {
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
            ['id', "537ce224-54b0-4081-b044-433dac11b50b"],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map));
        try {
            $this->testAction('users/change_email',
                              ['method' => 'PUT', 'data' => ['User' => ['email' => 'abcde@1234.com']]]);
        } catch (NotFoundException $e) {
        }
    }

}
