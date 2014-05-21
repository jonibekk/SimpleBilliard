<?php
App::uses('User', 'Model');

/**
 * User Test Case
 *
 * @property User $User
 */
class UserTest extends CakeTestCase
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
        'app.posts_image',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->User);

        parent::tearDown();
    }

    public $baseData = [];

    /**
     * ユーザモデルのバリデーションチェックのテスト
     */
    public function testUserValidations()
    {
        $this->assertTrue(
             $this->getValidationRes(['first_name' => 'daiki']),
             "[正常系]英名はアルファベットのみ"
        );
        $this->assertFalse(
             $this->getValidationRes(['first_name' => '']),
             "[異常系]英名は空を認めない"
        );
        $this->assertFalse(
             $this->getValidationRes(['first_name' => 'だいき']),
             "[異常系]英名はアルファベットのみ"
        );
        $this->assertTrue(
             $this->getValidationRes(['last_name' => 'hirakata']),
             "[正常系]英姓はアルファベットのみ"
        );
        $this->assertFalse(
             $this->getValidationRes(['last_name' => '']),
             "[異常系]英姓は空を認めない"
        );
        $this->assertFalse(
             $this->getValidationRes(['last_name' => 'ひらかた']),
             "[異常系]英姓はアルファベットのみ"
        );
        $this->assertTrue(
             $this->getValidationRes(['password' => 'goalous1234', 'password_confirm' => 'goalous1234']),
             "[正常系]パスワードは確認パスワードと一致"
        );
        $this->assertFalse(
             $this->getValidationRes(['password' => 'goalous1234', 'password_confirm' => '1234goalous']),
             "[異常系]パスワードは確認パスワードと一致"
        );
        $this->assertFalse(
             $this->getValidationRes(['password' => '',]),
             "[異常系]パスワードは空を認めない"
        );
    }

    function getValidationRes($data = [])
    {
        if (empty($data)) {
            return null;
        }
        $testData = array_merge($this->baseData, $data);
        $this->User->create();
        $this->User->set($testData);
        return $this->User->validates();
    }
}
