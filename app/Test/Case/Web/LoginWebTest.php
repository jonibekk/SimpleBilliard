<?php

App::uses('GoalousWebTestCase', 'Test');

/**
 * ログインテスト
 *
 * @package GoalousWebTest
 * @version 2016/03/11
 *
 */
class LoginWebTest extends GoalousWebTestCase
{
    /**
     * LoginTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        $this->setBrowserUrl($this->url);
        $this->shareSession(true);
    }

    /**
     * #### ログインテスト
     * - 正しいIDとPASSWORDを入力する
     */
    public function testLogin()
    {
        $this->url('/users/login');

        $email = $this->byName('data[User][email]');
        $email->clear();
        $email->value($this->email);

        $password = $this->byName('data[User][password]');
        $password->clear();
        $password->value($this->password);

        $button = $this->byClassName('btn-primary');
        $this->moveto($button);
        $this->byId('UserLoginForm')->submit();

        $link = $this->byLinkText('ホーム');
        $this->assertEquals('ホーム', $link->text());
        $this->saveSceenshot();
    }
}