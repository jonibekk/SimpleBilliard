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

}
