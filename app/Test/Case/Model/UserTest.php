<?php
App::uses('User', 'Model');

/**
 * User Test Case
 *
 * @property User $User
 */
class UserTest extends CakeTestCase
{

//    public $autoFixtures = false;

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
        $this->assertTrue(
             $this->getValidationRes(['password' => '12345678']),
             "[正常系]パスワードは8文字以上"
        );
        $this->assertFalse(
             $this->getValidationRes(['password' => '1234567']),
             "[異常系]パスワードは8文字以上"
        );
        $this->assertFalse(
             $this->getValidationRes(['password' => '',]),
             "[異常系]パスワードは空を認めない"
        );
        $this->assertTrue(
             $this->getValidationRes(['agree_tos' => true,]),
             "[正常系]利用規約に同意は必須"
        );
        $this->assertFalse(
             $this->getValidationRes(['agree_tos' => false,]),
             "[異常系]利用規約に同意は必須"
        );
    }

    public function testGetAllUsersCount()
    {
        //現在の結果
        $current_res = $this->User->getAllUsersCount();
        //アクティブユーザのレコードを１つ追加
        $this->User->create();
        $active_user_data = $this->User->save(
                                       [
                                           'first_name' => 'hoge',
                                           'last_name'  => 'fuga',
                                           'active_flg' => true
                                       ]
        );
        $after_add_a_active_res = $this->User->getAllUsersCount();

        $this->assertTrue(
             $current_res + 1 === $after_add_a_active_res,
             "アクティブユーザを１レコード追加した場合の取得結果が１レコード増えている"
        );
        $current_res = $after_add_a_active_res;
        //非アクティブユーザのレコードを１つ追加
        $this->User->create();
        $non_active_user_data = $this->User->save(
                                           [
                                               'first_name' => 'hoge',
                                               'last_name'  => 'fuga',
                                               'active_flg' => false
                                           ]
        );
        $after_add_a_non_active_res = $this->User->getAllUsersCount();
        $this->assertTrue(
             $current_res === $after_add_a_non_active_res,
             "非アクティブユーザを追加しても取得結果のレコード数に変化がない"
        );
        $current_res = $after_add_a_non_active_res;
        $this->User->delete($active_user_data['User']['id']);
        $this->assertTrue(
             $current_res - 1 === $this->User->getAllUsersCount(),
             "アクティブユーザを１つ削除すると、結果が１減っている"
        );
        $current_res = $this->User->getAllUsersCount();
        $this->User->delete($non_active_user_data['User']['id']);
        $this->assertTrue(
             $current_res === $this->User->getAllUsersCount(),
             "非アクティブユーザを１つ削除しても結果が変わらない"
        );
    }

    public function testTransaction()
    {
        $user_id = '537ce224-8c0c-4c99-be76-433dac11b50b';

        //トランザクション開始前にデータが存在する事を確認
        $this->assertNotEmpty($this->User->findById($user_id), "トランザクション開始前にデータが存在する事を確認。");
        //トランザクション開始
        $this->User->begin();
        //レコードを削除
        $this->User->delete($user_id);
        //結果はempty
        $this->assertEmpty($this->User->findById($user_id), "トランザクション開始後のレコード削除でfindの結果はempty");
        //ロールバックすると結果取得できる
        $this->User->rollback();
        $this->assertNotEmpty($this->User->findById($user_id), "ロールバックで削除したデータが復活する。");
        //トランザクション開始
        $this->User->begin();
        //レコードを削除
        $this->User->delete($user_id);
        //コミットした後はempty
        $this->User->commit();
        $this->assertEmpty($this->User->findById($user_id), "コミット後は削除したデータは参照できない。");
    }

    function testUserProvisionalRegistration()
    {
        //異常系
        $data = [
            'User'  => [
                'first_name' => '',
            ],
            'Email' => []
        ];
        $res = $this->User->userProvisionalRegistration($data);
        $this->assertFalse($res, "[異常系]ユーザ仮登録");
        //正常系
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
        $res = $this->User->userProvisionalRegistration($data);
        $this->assertTrue($res, "[正常系]ユーザ仮登録");
    }

}
