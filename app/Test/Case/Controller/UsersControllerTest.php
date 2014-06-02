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

//        $Users = $this->generate('Users', [
//            'components' => [
//                'Security' => ['_validateCsrf', '_validatePost'],
//            ],
//            'models'     => ['User'],
//        ]);
//        $Users->Security
//            ->expects($this->any())
//            ->method('_validateCsrf')
//            ->will($this->returnValue(true));
//        $Users->Security
//            ->expects($this->any())
//            ->method('_validatePost')
//            ->will($this->returnValue(true));
        Configure::write('Config.language', 'ja');

        $this->testAction('/users/register', ['return' => 'contents']);
        $this->assertTextContains('新しいアカウントを作成', $this->view, "[ユーザ登録画面]通常のアクセス");

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

        $data = [
            'User'  => [
                'first_name'       => 'taro',
                'last_name'        => 'sato',
                'password'         => '12345678',
                'password_confirm' => '12345678',
                'agree_tos'        => true,
                'local_date' => date('Y-m-d H:i:s'),
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
        $res = $this->testAction('/users/sent_mail', ['return' => 'contents']);
        $this->assertContains("おめでとうございます！", $res, "[正常]ユーザ仮登録");
    }

    function testLogin()
    {
        $this->testAction('/users/login', ['return' => 'contents']);
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
                'Auth',
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
            [null, null],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Users->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        $data = [
            'User' => [
                'email'    => "aaaato@email.com",
                'password' => "12345678",
            ]
        ];
        $this->testAction('/users/login', ['data' => $data, 'method' => 'post', 'return' => 'vars']);
    }

    function testSentMailFail()
    {
        try {
            $this->testAction('/users/sent_mail', ['return' => 'contents']);
        } catch (NotFoundException $e) {
        }
        $this->assertTrue(isset($e), "[異常]ユーザ登録");
    }

    function testVerifyEmailNotLoggedIn()
    {
        $this->testAction('/users/verify/12345678', ['return' => 'contents']);
    }

    function testVerifyEmailLoggedInYet()
    {
        $this->testAction('/users/verify/12345', ['return' => 'contents']);
    }

    function testVerifyEmailNotFound()
    {
        try {
            $this->testAction('/users/verify/123456', ['return' => 'contents']);
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
        $this->testAction('/users/register');
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
        $this->testAction('/users/register');
        $this->assertEquals('jpn', Configure::read('Config.language'), "自動言語設定がoffの場合は言語設定が適用される");
    }

}
