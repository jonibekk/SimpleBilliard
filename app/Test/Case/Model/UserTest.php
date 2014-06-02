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

    function testVerifyEmail()
    {
        $token = "12345678";
        $user_id = "537ce224-c708-4084-b879-433dac11b50b";
        $before_data = $this->User->find('first', ['conditions' => ['User.id' => $user_id], 'contain' => ['Email']]);
        $before_data = [
            'User'  => [
                'active_flg' => $before_data['User']['active_flg'],
            ],
            'Email' => [
                [
                    'email_verified'      => $before_data['Email'][0]['email_verified'],
                    'email_token'         => $before_data['Email'][0]['email_token'],
                    'email_token_expires' => $before_data['Email'][0]['email_token_expires'],
                ]
            ]
        ];
        $before_expected = [
            'User'  => [
                'active_flg' => false,
            ],
            'Email' => [
                [
                    'email_verified'      => false,
                    'email_token'         => $token,
                    'email_token_expires' => '2017-05-22 02:28:03',
                ]
            ]
        ];
        $this->assertEquals($before_expected, $before_data, "[正常系]メール認証の事前確認");
        $this->User->verifyEmail($token);
        $after_data = $this->User->find('first', ['conditions' => ['User.id' => $user_id], 'contain' => ['Email']]);
        $after_data = [
            'User'  => [
                'active_flg' => $after_data['User']['active_flg'],
            ],
            'Email' => [
                [
                    'email_verified'      => $after_data['Email'][0]['email_verified'],
                    'email_token'         => $after_data['Email'][0]['email_token'],
                    'email_token_expires' => $after_data['Email'][0]['email_token_expires'],
                ]
            ]
        ];
        $after_expected = [
            'User'  => [
                'active_flg' => true,
            ],
            'Email' => [
                [
                    'email_verified'      => true,
                    'email_token'         => null,
                    'email_token_expires' => null,
                ]
            ]
        ];
        $this->assertEquals($after_expected, $after_data, "[正常系]メール認証後の確認");
    }

    function testVerifyEmailException()
    {
        $not_exists_token = "12345678aaa";
        try {
            $this->User->verifyEmail($not_exists_token);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常系]tokenが正しくない例外の発生");
        unset($e);

        $exists_token = "12345678";
        try {
            $this->User->verifyEmail($exists_token);
        } catch (RuntimeException $e) {
        }
        $this->assertFalse(isset($e), "[正常系]tokenが正しくない例外の発生");
    }

    function testFind()
    {
        $user_id = "537ce224-5ca4-4fd5-aaf2-433dac11b50b";
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        $expected = "English user Last name";
        $this->assertEquals($expected, $actual, "[正常]英語ユーザの場合は表示ユーザ名が`first_name last_name`になる");

        $user_id = "537ce224-8f08-4cf3-9c8f-433dac11b50b";
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        $expected = "姓 名";
        $this->assertEquals($expected, $actual, "[正常]日本語ユーザの場合で且つローカル名が入っている場合は`local_last_name local_first_name`になる");

        $user_id = "537ce224-c16c-4f12-a301-433dac11b50b";
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        $expected = "first last";
        $this->assertEquals($expected, $actual, "[正常]日本語ユーザの場合で且つローカル名が入っていない場合は`first_name last_name`になる");

        $user_id = "537ce224-24a4-415d-ba90-433dac11b50b";
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        debug($actual);
        $expected = "言語設定なしの性 言語設定なしの名";
        $this->assertEquals($expected, $actual, "[正常]言語設定なしのユーザの場合でローカル名が入っている場合は`local_first_name local_last_name`");

//        $user_id = "537ce224-f3d0-46a3-a1d3-433dac11b50b";
//        $res = $this->User->findById($user_id);
//        $actual = $res['User']['display_username'];
//        $expected = "First Last";
//        $this->assertEquals($expected, $actual, "[正常]日本語ユーザの場合で且つローカル名が入っいる場合でもローマ字表示フラグonの場合は`first_name last_name`になる");
    }
}
