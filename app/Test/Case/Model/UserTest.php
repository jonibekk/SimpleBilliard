<?php
App::uses('GoalousTestCase', 'Test');
App::uses('User', 'Model');
App::uses('LocalName', 'Model');
App::uses('CircleMember', 'Model');
App::uses('TeamMember', 'Model');
App::import('Model/Entity', 'UserEntity');

use Goalous\Enum as Enum;
/**
 * User Test Case
 *
 * @property User $User
 * @property LocalName $LocalName
 * @property CircleMember $CircleMember
 * @property TeamMember $TeamMember
 */
class UserTest extends GoalousTestCase
{

//    public $autoFixtures = false;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.user',
        'app.team',
        'app.circle',
        'app.circle_member',
        'app.post_share_user',
        'app.group',
        'app.team_member',
        'app.email',
        'app.post',
        'app.notify_setting',
        'app.member_group',
        'app.device',
        'app.term',
        'app.goal',
        'app.action_result',
        'app.post_share_circle',
        'app.job_category',
        'app.member_type',
        'app.terms_of_service'
    );

    public $basicUserDefault = [
        'User'  => [
            'first_name' => 'basic',
            'last_name'  => 'user',
            'password',
            'active_flg' => true
        ],
        'Email' => [
            [
                'email'          => 'basic@email.com',
                'email_verified' => true
            ]
        ]
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        $this->LocalName = ClassRegistry::init('LocalName');
        $this->CircleMember = ClassRegistry::init('CircleMember');
        $this->TeamMember = ClassRegistry::init('TeamMember');
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

    function getPasswordValidationRes($data = [])
    {
        if (empty($data)) {
            return null;
        }
        $testData = array_merge($this->baseData, $data);
        $this->User->create();
        return $this->User->validatePassword($testData);
    }

    /**
     * @param array $user_data
     * @param array $email_data
     *
     * @return mixed
     */
    function generateBasicUser($user_data = [], $email_data = [])
    {
        $data = $this->basicUserDefault;
        $data['User']['password'] = $this->User->generateHash('12345678');
        if (!empty($user_data)) {
            $data['User'] = array_merge($data['User'], $user_data);
        }
        if (!empty($email_data)) {
            $data['Email'][0] = array_merge($data['Email'][0], $email_data);
        }
        $this->User->saveAll($data);
        $this->User->save(['primary_email_id' => $this->User->Email->getLastInsertID()]);
        return $this->User->getLastInsertID();
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
            $this->getValidationRes(['agree_tos' => true,]),
            "[正常系]利用規約に同意は必須"
        );
        $this->assertFalse(
            $this->getValidationRes(['agree_tos' => false,]),
            "[異常系]利用規約に同意は必須"
        );

        $latinCharStrings = [
            'áâãāăȧä',
            'ảåǎȁȃąạḁ',
            'ẚầấẫẩằắẵ',
            'ẳǡǟǻậặⱥɐ',
            'ɒæǽǣ',
            'ÀÁÂÃĀĂȦÄ',
            'ẢÅǍȀȂĄẠḀ',
            'ẦẤẪẨẰẮẴẲ',
            'ǠǞǺẬẶȺⱭⱯ',
            'ⱰÆǼǢ',
            'ḃɓḅḇƀƃƅß',
            'ḂƁḄḆƂƄɃ',
            'ćĉċčƈçḉȼ',
            'ĆĈĊČƇÇḈȻ',
            'ḋɗḍḏḑḓďđƌȡ',
            'ḊƊḌḎḐḒĎÐƋ',
            'èéêẽēĕėë',
            'ẻěȅȇẹȩęḙ',
            'ḛềếễểḕḗệ',
            'ḝɇɛǝⱸⱻ',
            'ÈÉÊẼĒĔĖë',
            'ẺĚȄȆẸȨĘḘ',
            'ḚỀẾỄỂḔḖỆ',
            'ḜƎɆƐƏḟƒḞƑ',
            'ǵĝḡğġǧɠģǥ',
            'ǴĜḠĞĠǦƓĢǤ',
            'ĥḣḧȟḥḩḫẖħⱨⱶƕ',
            'ĤḦȞḤḨḪĦⱧⱵǶ',
            'ìíîĩīĭıï',
            'ỉǐịįȉȋḭɨḯĳ',
            'ÌÍÎĨĪĬİÏ',
            'ỈǏỊĮȈȊḬƗḮĲ',
            'ĳĵǰȷɉĲĴɈ',
            'ḱǩḵƙḳķĸⱪ',
            'ḰǨḴƘḲĶⱩ',
            'ĺḻḷļḽľŀł',
            'ƚḹȴⱡ',
            'ĹḺḶĻḼĽĿŁ',
            'ḸȽⱠⱢ',
            'ḿṁṃɱɯ',
            'ḾṀṂⱮƜ',
            'ǹńñṅňŋɲṇ',
            'ņṋṉŉƞȵ',
            'ǸŃÑṄŇŊƝṆ',
            'ŅṊṈȠ',
            'òóôõōŏȧö',
            'ỏőǒȍȏơǫọ',
            'ɵøồốỗổȱȫ',
            'ȭṍṏṑṓờớỡ',
            'ởợǭộǿɔœƍⱷⱺƣ',
            'ÒÓÔÕŌŎȮÖ',
            'ỎŐǑȌȎƠǪỌ',
            'ƟØỒỐỖỔȰȪ',
            'ȬṌṐṒỜỚỠỞ',
            'ỢǬỘǾƆŒƢṕṗƥ',
            'ṔṖƤⱣɋɊ',
            'ŕṙřȑȓṛŗṟṝɍⱹ',
            'ŔṘŘȐȒṚŖṞ',
            'ṜƦɌⱤ',
            'śŝṡšṣșşȿ',
            'ṥṧṩƨß',
            'ŚŜṠŠṢȘŞⱾ',
            'ṤṦṨƧ',
            'ſẛṫẗťƭʈƫ',
            'ṭțţṱṯŧⱦȶ',
            'ṪŤƬƮṬȚŢṰ',
            'ṮŦȾ',
            'ùúûũūŭüủ',
            'ůűǔȕȗưụṳ',
            'ųṷṵṹṻǜǘǖ',
            'ǚừứữửựʉ',
            'ÙÚÛŨŪŬÜỦ',
            'ŮŰǓȔȖƯỤṲ',
            'ŲṶṴṸṺǛǗǕ',
            'ǙỪỨỮỬỰɄ',
            'ṽṿⱱⱴʌ',
            'ṼṾƲɅ',
            'ẁẃŵẇẅẘẉⱳ',
            'ẀẂŴẆẄẈⱲẋẍẊ',
            'ỳýŷȳẏÿỷ',
            'ẙƴỵɏ',
            'ỲýŶỸȲẎŸ',
            'ỶƳỴɏ',
            'źẑżžȥẓẕ',
            'ƶɀⱬ',
            'ŹẐŻŽȤẒẔ',
            'ƵⱿⱫ',
            'ÞþƔƛƖƪƩ',
            'ƱƷǮƸƹȜȝƺ',
            'ǯƻƼƽƾǷƿȢ',
            'ȣðȸȹɁɂ',];

        foreach (['first_name', 'last_name'] as $nameKey) {
            $this->assertTrue($this->getValidationRes([$nameKey => 'a']));
            $this->assertTrue($this->getValidationRes([$nameKey => str_pad('', 128, 'a')]));
            $this->assertFalse($this->getValidationRes([$nameKey => str_pad('', 129, 'a')]));
            foreach ($latinCharStrings as $latinString) {
                $this->assertTrue(
                    $this->getValidationRes([$nameKey => $latinString]),
                    sprintf('Latin chars "%s" not pass validation.', $latinString)
                    );
            }
            // Validation fail, containing number.
            $this->assertFalse($this->getValidationRes([$nameKey => 'áâãā1ăȧä']));
        }
    }

    public function testPasswordValidation()
    {
        $this->assertTrue(
            $this->getPasswordValidationRes(['password' => 'goalous1234', 'password_confirm' => 'goalous1234']),
            "[正常系]パスワードは確認パスワードと一致"
        );
        $this->assertFalse(
            $this->getPasswordValidationRes(['password' => 'goalous1234', 'password_confirm' => '1234goalous']),
            "[異常系]パスワードは確認パスワードと一致"
        );
        $this->assertTrue(
            $this->getPasswordValidationRes(['password' => '1234567a']),
            "[正常系]パスワードは8文字以上"
        );
        $this->assertFalse(
            $this->getPasswordValidationRes(['password' => '1234567']),
            "[異常系]パスワードは8文字以上"
        );
        $this->assertFalse(
            $this->getPasswordValidationRes(['password' => '',]),
            "[異常系]パスワードは空を認めない"
        );
    }

    public function testTransaction()
    {
        $user_id = '1';

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
        //正常系
        $data = [
            'User'  => [
                'first_name'       => 'taro',
                'last_name'        => 'sato',
                'password'         => '1234567a',
                'password_confirm' => '1234567a',
                'agree_tos'        => true,
            ],
            'Email' => [
                ['email' => 'taro@sato.com'],
            ]
        ];
        $res = $this->User->userRegistration($data, false);
        $this->assertTrue($res, "[正常系]ユーザ本登録");
    }

    function testVerifyEmail()
    {
        $token = "12345678";
        $user_id = "15";
        $tokenExpires = strtotime('2099-05-22 02:28:03');
        $this->User->Email->updateAll(['email_token_expires' => $tokenExpires], ['Email.user_id' => $user_id]);
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
                    'email_token_expires' => $tokenExpires,
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
        $tokenExpires = strtotime('2099-05-22 02:28:03');
        $this->User->Email->updateAll(['email_token_expires' => $tokenExpires], ['Email.email_token' => $exists_token]);

        try {
            $this->User->verifyEmail($exists_token);
        } catch (RuntimeException $e) {
        }
        $this->assertFalse(isset($e), "[正常系]tokenが正しくない例外の発生");
    }

    function testVerifyEmailUid()
    {
        $uid = "1";
        $token = 'abcd1234';
        $data = [
            'Email' => [
                'user_id'             => $uid,
                'email_verified'      => false,
                'email_token'         => $token,
                'email_token_expires' => strtotime("+1 day", time()),
            ]
        ];
        $this->User->Email->save($data);
        $res = $this->User->verifyEmail($token, $uid);
        $this->assertNotEmpty($res, "[正常]ユーザIDを指定したメールアドレスの認証");
    }

    function testVerifyEmailUidExpire()
    {
        $uid = "1";
        $token = 'abcd1234';
        $data = [
            'Email' => [
                'user_id'             => $uid,
                'email_verified'      => false,
                'email_token'         => $token,
                'email_token_expires' => strtotime("-1 day", time()),
            ]
        ];
        $this->User->Email->save($data);
        try {
            $this->User->verifyEmail($token, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]メアド認証でトークンの期限切れ");
    }

    function testFind()
    {
        $user_id = "5";
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        $expected = "firstname lastname";
        $this->assertEquals($expected, $actual, "[正常]英語ユーザの場合は表示ユーザ名が`first_name last_name`になる");

        $user_id = "6";
        $this->User->me['language'] = 'jpn';
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        $expected = "姓 名";
        $this->assertEquals($expected, $actual, "[正常]日本語ユーザの場合で且つローカル名が入っている場合は`local_last_name local_first_name`になる");

        $user_id = "7";
        $res = $this->User->findById($user_id);
        $actual = $res['User']['display_username'];
        $expected = "firstname lastname";
        $this->assertEquals($expected, $actual, "[正常]日本語ユーザの場合で且つローカル名が入っていない場合は`first_name last_name`になる");
    }

    function testPasswordResetPreNoData()
    {
        $res = $this->User->passwordResetPre([]);
        $this->assertFalse($res, "[異常]パスワードリセット前のデータなしの場合");
    }

    function testPasswordResetPreNoDataNoUser()
    {
        $res = $this->User->passwordResetPre([]);
        $this->assertFalse($res, "[異常]パスワードリセット前のユーザデータなしの場合");
        $res = $this->User->passwordResetPre(['User' => ['email' => 'no_data@xxx.xxx.com']]);
        $this->assertFalse($res, "[異常]パスワードリセット前のユーザデータなしの場合");
    }

    function testPasswordResetPreSuccess()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $data = ['User' => ['email' => $email['Email']['email']]];
        $res = $this->User->passwordResetPre($data);
        $this->assertTrue(!empty($res['User']['password_token']), "[正常]パスワードリセット前のトークン生成に成功");
    }

    function testPasswordResetPreNotEmailVerified()
    {
        $email_data = ['email_verified' => false];
        $uid = $this->generateBasicUser([], $email_data);
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $data = ['User' => ['email' => $email['Email']['email']]];
        $res = $this->User->passwordResetPre($data);
        $this->assertFalse($res, "[異常]パスワードリセット前のメアド未認証");
    }

    function testPasswordResetPreNotUserActive()
    {
        $user_data = ['active_flg' => false];
        $uid = $this->generateBasicUser($user_data);
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $data = ['User' => ['email' => $email['Email']['email']]];
        $res = $this->User->passwordResetPre($data);
        $this->assertFalse($res, "[異常]パスワードリセット前のユーザ非アクティブ");
    }

    function testCheckPasswordToken()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $data = ['User' => ['email' => $email['Email']['email']]];
        $res = $this->User->passwordResetPre($data);

        $res = $this->User->checkPasswordToken($res['User']['password_token']);
        $this->assertTrue(!empty($res), "[成功]パスワードトークンチェック");
    }

    function testCheckPasswordTokenFail()
    {
        $res = $this->User->checkPasswordToken('no_data');
        $this->assertFalse(!empty($res), "[異常]パスワードトークンチェック");
    }

    function testPasswordResetSuccess()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $data = ['User' => ['email' => $email['Email']['email']]];
        $res = $this->User->passwordResetPre($data);

        $user_email = $this->User->checkPasswordToken($res['User']['password_token']);

        $postData = [
            'User' => [
                'password'         => '1234567a',
                'password_confirm' => '1234567a',
            ],
        ];
        $res = $this->User->passwordReset($user_email, $postData);
        $this->assertTrue($res, "[正常]パスワードリセット");
    }

    function testPasswordResetFailNotSameConfirm()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $data = ['User' => ['email' => $email['Email']['email']]];
        $res = $this->User->passwordResetPre($data);

        $user_email = $this->User->checkPasswordToken($res['User']['password_token']);

        $postData = [
            'User' => [
                'password'         => '12345678',
                'password_confirm' => '123456789',
            ],
        ];
        $res = $this->User->passwordReset($user_email, $postData);
        $this->assertFalse($res, "[異常]パスワードリセットでパスワード確認用と一致しない");
    }

    function testPasswordResetFailNoData()
    {
        $res = $this->User->passwordReset([], []);
        $this->assertFalse($res, "[異常]パスワードリセットでデータなし");
    }

    function testSaveEmailToken()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $res = $this->User->saveEmailToken($email['Email']['email']);
        $this->assertTrue(!empty($res['Email']['email_token']), "[正常]トークン発行でトークンが存在する");
        $this->assertTrue(!empty($res['Email']['email_token_expires']), "[正常]トークン発行でトークン期限が存在する");

    }

    function testSaveEmailTokenFail()
    {
        $res = $this->User->saveEmailToken('test_xxxxx');
        $this->assertFalse($res, "[異常]トークンの保存");
    }

    function testAddEmailSuccess()
    {
        $uid = $this->generateBasicUser();
        $postData = [];
        $postData['User']['email'] = "test@aaaaaaa.com";
        try {
            $this->User->addEmail($postData, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertFalse(isset($e), "[正常]emailアドレス追加");
    }

    function testAddEmailFailNotEmail()
    {
        $uid = $this->generateBasicUser();
        $postData = [];
        $postData['User']['email'] = null;
        try {
            $this->User->addEmail($postData, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]emailアドレス追加でメアドの入力がない");
    }

    function testAddEmailFailPassword()
    {
        $uid = $this->generateBasicUser();
        $postData = [];
        $postData['User']['email'] = "aaaaaaa@aaaaccc.com";
        $postData['User']['password_request2'] = "1111111111111";

        try {
            $this->User->addEmail($postData, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]emailアドレス追加でパスワードが間違っている");
    }

    function testAddEmailFailNotAllVerified()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $email['Email']['email_verified'] = false;
        $this->User->Email->save($email);

        $postData = [];
        $postData['User']['email'] = "test@aaaaaaa.com";
        try {
            $this->User->addEmail($postData, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]emailアドレス追加で認証ができていないメアドが存在する");
    }

    function testAddEmailFailExistsEmail()
    {
        $uid = $this->generateBasicUser();
        /** @noinspection PhpUndefinedMethodInspection */
        $email = $this->User->Email->findByUserId($uid);
        $email['Email']['email_verified'] = true;
        $this->User->Email->save($email);
        $postData = [];
        $postData['User']['email'] = "test@abc.com";
        try {
            $this->User->addEmail($postData, $uid);
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e), "[異常]emailアドレス追加で既にメアドが存在する");
    }

    function testChangePrimaryEmailSuccess()
    {
        $uid = $this->generateBasicUser();
        $postData = [];
        $postData['User']['email'] = "test@aaaaaaa.com";
        $this->User->addEmail($postData, $uid);
        $res = $this->User->changePrimaryEmail($uid, $this->User->getLastInsertID());
        $this->assertNotEmpty($res['User']['id'], "[正常]通常使うメアドの変更");
    }

    function testPasswordCheckSuccess()
    {
        $uid = $this->generateBasicUser();
        $value = ['password_request' => '12345678'];
        $field_name = "password_request";
        $this->User->my_uid = $uid;
        $res = $this->User->passwordCheck($value, $field_name);
        $this->assertTrue($res, "[正常]パスワード確認に成功");
    }

    function testPasswordCheckFail()
    {
        $uid = $this->generateBasicUser();
        $value = ['password_request' => '1234567800'];
        $field_name = "password_request";
        $this->User->my_uid = $uid;
        $res = $this->User->passwordCheck($value, $field_name);
        $this->assertFalse($res, "[異常]パスワード確認で間違ったパスワード");
    }

    function testPasswordCheckFailNoData()
    {
        $uid = $this->generateBasicUser();
        $value = ['password_request' => '1234567800'];
        $field_name = "password_request_aaaa";
        $this->User->my_uid = $uid;
        $res = $this->User->passwordCheck($value, $field_name);
        $this->assertFalse($res, "[異常]パスワード確認で間違ったヴァリデーション指定");
    }

    function testUpdateDefaultTeam()
    {
        $uid = $this->generateBasicUser();
        $this->User->my_uid = $uid;
        $this->User->me['default_team_id'] = null;
        $this->User->me['language'] = 'jpn';
        $res = $this->User->updateDefaultTeam('team_aaaaaaa');
        $this->assertTrue($res, "[正常]デフォルトチーム更新");

        $this->User->my_uid = $uid;
        $this->User->me['default_team_id'] = "team_xxxxxxxx";
        $res = $this->User->updateDefaultTeam('team_aaaaaaa');
        $this->assertFalse($res, "[異常]デフォルトチーム更新");
    }

    function testGetProfileAndEmail()
    {
        $this->User->my_uid = 1;
        $this->User->me['language'] = "eng";
        $this->User->getProfileAndEmail(1, 'jpn');
    }

    function testGetUsersCirclesSelect2()
    {
        $this->User->CircleMember->current_team_id = 1;
        $this->User->CircleMember->my_uid = 1;
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;

        // チーム全体サークルを取得する場合
        $res = $this->User->getUsersCirclesSelect2('チーム全体');
        $this->assertEquals('public', $res['results'][0]['id']);
        // チーム全体サークルを取得する場合(公開サークルのみ)
        $res = $this->User->getUsersCirclesSelect2('チーム全体', 10, 'public');
        $this->assertEquals('public', $res['results'][0]['id']);

        // 通常のサークルを取得する場合
        $res = $this->User->getUsersCirclesSelect2('test');
        $this->assertEquals('circle_1', $res['results'][0]['id']);

        // グループ含む
        $res = $this->User->getUsersCirclesSelect2('first', 10, 'all', true);
        $this->assertArrayHasKey('results', $res);
        $group_found = false;
        foreach ($res['results'] as $v) {
            if (strpos($v['id'], 'group_') === 0) {
                $group_found = true;
                $this->assertNotEmpty($v['users']);
                foreach ($v['users'] as $user) {
                    $this->assertNotEquals($this->User->my_uid, $user['id']);
                }
            }
        }
        $this->assertTrue($group_found);
    }

    function testGetAllUsersCirclesSelect2()
    {
        $this->User->my_uid = 1;
        $this->User->me['language'] = "jpn";
        $this->User->current_team_id = 1;
        $this->User->CircleMember->current_team_id = 1;
        $this->User->CircleMember->my_uid = 1;
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;

        $this->User->TeamMember->Team->id = 1;
        $this->User->TeamMember->Team->saveField('photo_file_name', null);

        $this->User->getAllUsersCirclesSelect2();
    }

    function testGetUsersProf()
    {
        $res = $this->User->getUsersProf(1);
        $this->assertTrue(!empty($res));
    }

    function testGetNewUsersByKeyword()
    {
        $this->User->current_team_id = 3;
        $this->User->my_uid = 9;
        $this->User->TeamMember->current_team_id = 3;
        $this->User->TeamMember->my_uid = 9;
        $post_id = 13;

        $res = $this->User->getNewUsersByKeywordNotSharedOnPost('user_9', 10, true, $post_id);
        $this->assertEmpty($res);

        $res = $this->User->getNewUsersByKeywordNotSharedOnPost('user_10', 10, true, $post_id);
        $this->assertNotEmpty($res);

        $res = $this->User->getNewUsersByKeywordNotSharedOnPost('user_11', 10, true, $post_id);
        $this->assertEmpty($res);
    }

    function testGetUsersSelectOnly()
    {
        $this->User->current_team_id = 1;
        $this->User->my_uid = 1;
        $this->User->Post->PostShareUser->current_team_id = 1;
        $this->User->Post->PostShareUser->my_uid = 1;
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;

        $post_id = 14;

        // 秘密サークル
        $res = $this->User->getUsersSelectOnly('first', 10, $post_id);
        $this->assertNotEmpty($res['results']);

        $this->User->TeamMember->create();
        $this->User->TeamMember->save([
            'user_id' => '14',
            'team_id' => '1',
            'status'  => TeamMember::USER_STATUS_ACTIVE,
        ]);

        $users = $this->User->getUsersSelectOnly('first', 10, $post_id, true);
        $this->assertArrayHasKey('results', $users);
        $group_found = false;
        foreach ($users['results'] as $v) {
            if (strpos($v['id'], 'group_') === 0) {
                $group_found = true;
                $this->assertNotEmpty($v['users']);
                foreach ($v['users'] as $user) {
                    $this->assertNotEquals($this->User->my_uid, $user['id']);
                }
            }
        }
        $this->assertTrue($group_found);
    }

    function testGetSecretCirclesSelect2()
    {
        $this->User->CircleMember->current_team_id = 1;
        $this->User->CircleMember->my_uid = 1;
        $this->User->CircleMember->Circle->current_team_id = 1;
        $this->User->CircleMember->Circle->my_uid = 1;

        // 秘密サークル
        $res = $this->User->getSecretCirclesSelect2('秘密サークル');
        $this->assertNotEmpty('public', $res['results']);

        // 秘密サークル
        $res = $this->User->getSecretCirclesSelect2('秘密');
        $this->assertNotEmpty('public', $res['results']);

        // 通常のサークル
        $res = $this->User->getSecretCirclesSelect2('test');
        $this->assertEquals(['results' => []], $res);

        // チーム全体サークル
        $res = $this->User->getSecretCirclesSelect2('チーム全体');
        $this->assertEquals(['results' => []], $res);
    }

    function testClearDefaultTeamId()
    {
        $this->User->current_team_id = 1;
        $this->User->my_uid = 1;

        $count1 = $this->User->find('count', [
            'conditions' => [
                'User.default_team_id' => 1,
            ]
        ]);
        $this->assertNotEmpty($count1);

        $count2 = $this->User->find('count', [
            'conditions' => [
                'User.default_team_id' => 2,
            ]
        ]);
        $this->assertNotEmpty($count2);

        $res = $this->User->clearDefaultTeamId(2);
        $this->assertTrue($res);

        $count1_2 = $this->User->find('count', [
            'conditions' => [
                'User.default_team_id' => 1,
            ]
        ]);
        $this->assertEquals($count1, $count1_2);

        $count2_2 = $this->User->find('count', [
            'conditions' => [
                'User.default_team_id' => 2,
            ]
        ]);
        $this->assertEquals(0, $count2_2);
    }

    function testGetUsersByKeyword()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;
        $this->User->LocalName->my_uid = 1;
        $this->User->LocalName->current_team_id = 1;

        $res = $this->User->getUsersByKeyword("first");
        $this->assertNotEmpty($res);
        $res = $this->User->getUsersByKeyword("irstname");
        $this->assertEmpty($res);
        $res = $this->User->getUsersByKeyword("");
        $this->assertEmpty($res);
    }

    public function test_getUsersByKeywordWithExclusion_success()
    {
        $excludedUserIdList = [10, 11];
        $excludedUserIdList2 = [2, 3];

        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;
        $this->User->LocalName->my_uid = 1;
        $this->User->LocalName->current_team_id = 1;

        $result = $this->User->getUsersByKeyword("first", 20, false);
        $this->assertCount(6, $result);
        $result = $this->User->getUsersByKeyword("first", 20, false, $excludedUserIdList);
        $this->assertCount(6, $result);
        $result = $this->User->getUsersByKeyword("first", 20, true);
        $this->assertCount(5, $result);
        $result = $this->User->getUsersByKeyword("first", 20, false, $excludedUserIdList2);
        $this->assertCount(4, $result);
        $result = $this->User->getUsersByKeyword("first", 20, true, $excludedUserIdList2);
        $this->assertCount(3, $result);
    }

    function testGetNewUsersByKeywordNotSharedOnPost()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;
        $this->User->LocalName->my_uid = 1;
        $this->User->LocalName->current_team_id = 1;

        $res = $this->User->getNewUsersByKeywordNotSharedOnPost("first", 10, true, 9999999);
        $this->assertNotEmpty($res);
        $res = $this->User->getNewUsersByKeywordNotSharedOnPost("irstname", 10, true, 9999999);
        $this->assertEmpty($res);
        $res = $this->User->getNewUsersByKeywordNotSharedOnPost("", 10, true, 9999999);
        $this->assertEmpty($res);
    }

    function testBuildLocalUserName()
    {
        $local_name = $this->User->buildLocalUserName('jpn', '名', '姓');
        $this->assertEquals('姓 名', $local_name);
        $local_name = $this->User->buildLocalUserName('eng', 'first', 'last');
        $this->assertEquals('first last', $local_name);
    }

    function testGetCacheKey()
    {
        $this->User->my_uid = 2;
        $this->User->current_team_id = 1;
        $actual = $this->User->getCacheKey(CACHE_KEY_TERM_CURRENT, true, null, true);
        $expected = 'current_term:team:1:user:2';
        $this->assertEquals($expected, $actual);
    }

    function testExcludeGroupMemberSelect2()
    {
        $test_data = [
            [
                'id'    => 'group_1',
                'users' => [
                    ['id' => 'user_1', 'text' => 'user1'],
                    ['id' => 'user_2', 'text' => 'user2'],
                    ['id' => 'user_3', 'text' => 'user3'],
                    ['id' => 'user_4', 'text' => 'user4'],
                ]
            ],
            [
                'id'    => 'group_2',
                'users' => [
                    ['id' => 'user_1', 'text' => 'user1'],
                    ['id' => 'user_4', 'text' => 'user4'],
                    ['id' => 'user_5', 'text' => 'user5'],
                ]
            ],
        ];

        $res = $this->User->excludeGroupMemberSelect2($test_data, [2 => 2, 4 => 4]);
        $this->assertEquals([
            [
                'id'    => 'group_1',
                'users' => [
                    ['id' => 'user_1', 'text' => 'user1'],
                    ['id' => 'user_3', 'text' => 'user3'],
                ]
            ],
            [
                'id'    => 'group_2',
                'users' => [
                    ['id' => 'user_1', 'text' => 'user1'],
                    ['id' => 'user_5', 'text' => 'user5'],
                ]
            ],
        ], $res);

        $res = $this->User->excludeGroupMemberSelect2($test_data, [1 => 1, 2 => 2, 3 => 3, 4 => 4]);
        $this->assertEquals([
            [
                'id'    => 'group_2',
                'users' => [
                    ['id' => 'user_5', 'text' => 'user5'],
                ]
            ],
        ], $res);

        $res = $this->User->excludeGroupMemberSelect2($test_data, [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5]);
        $this->assertEquals([], $res);

        $res = $this->User->excludeGroupMemberSelect2($test_data, []);
        $this->assertEquals($test_data, $res);
    }

    function testGetGroupsSelect2()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;
        $this->User->LocalName->my_uid = 1;
        $this->User->LocalName->current_team_id = 1;

        $groups = $this->User->getGroupsSelect2('first', 10);
        $this->assertArrayHasKey('results', $groups);
        foreach ($groups['results'] as $v) {
            $this->assertEquals(0, strpos($v['id'], 'group_'));

            // 自分以外の一人以上のユーザーが含まれているか
            $this->assertNotEmpty($v['users']);
            foreach ($v['users'] as $user) {
                $this->assertNotEquals($this->User->my_uid, $user['id']);
            }
        }

        // 別ユーザー
        $this->User->my_uid = 2;
        $this->User->TeamMember->my_uid = 2;
        $this->User->LocalName->my_uid = 2;

        $groups2 = $this->User->getGroupsSelect2('first', 10);
        $this->assertArrayHasKey('results', $groups2);
        $this->assertNotEquals($groups, $groups2);
        foreach ($groups2['results'] as $v) {
            $this->assertEquals(0, strpos($v['id'], 'group_'));

            // 自分以外の一人以上のユーザーが含まれているか
            $this->assertNotEmpty($v['users']);
            foreach ($v['users'] as $user) {
                $this->assertNotEquals($this->User->my_uid, $user['id']);
            }
        }
    }

    function testGetUsersSelect2()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;
        $this->User->LocalName->my_uid = 1;
        $this->User->LocalName->current_team_id = 1;

        $users = $this->User->getUsersSelect2('first');
        $this->assertArrayHasKey('results', $users);
        $this->assertNotEmpty($users['results']);
        foreach ($users['results'] as $v) {
            $this->assertEquals(0, strpos($v['id'], 'user_'));
        }

        $users = $this->User->getUsersSelect2('first', 1);
        $this->assertArrayHasKey('results', $users);
        $this->assertCount(1, $users['results']);

        $users = $this->User->getUsersSelect2('first', 10, true);
        $this->assertArrayHasKey('results', $users);
        $group_found = false;
        foreach ($users['results'] as $v) {
            if (strpos($v['id'], 'group_') === 0) {
                $group_found = true;
                $this->assertNotEmpty($v['users']);
                foreach ($v['users'] as $user) {
                    $this->assertNotEquals($this->User->my_uid, $user['id']);
                }
            }
        }
        $this->assertTrue($group_found);
    }

    function testGetUsersSelect2_withSelf()
    {
        $userIdAuthorized = 1;
        $this->User->my_uid = $userIdAuthorized;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = $userIdAuthorized;
        $this->User->LocalName->my_uid = $userIdAuthorized;
        $this->User->LocalName->current_team_id = 1;

        $usersWithOutSelf = $this->User->getUsersSelect2('first', 10, true);
        $usersWithSelf = $this->User->getUsersSelect2('first', 10, true, true);
        $this->assertTrue(count($usersWithOutSelf['results']) !== $this->count($usersWithSelf['results']));

        $isContainingUserId = function (string $userId, array $resultSelect2) {
            $stringUserIdAuthorized = sprintf('user_%d', $userId);
            $extractedUserIds = Hash::extract($resultSelect2, 'results.{n}.id');
            return in_array($stringUserIdAuthorized, $extractedUserIds);
        };

        $this->assertTrue($isContainingUserId($userIdAuthorized, $usersWithSelf));
        $this->assertFalse($isContainingUserId($userIdAuthorized, $usersWithOutSelf));

    }

    function testMakeSelect2UserList()
    {
        $users = [
            [
                'User' => [
                    'id'               => 1,
                    'display_username' => '表示名 1',
                    'roman_username'   => 'display name 1',
                    'photo_file_name'  => 'test1.jpg',
                ]
            ],
            [
                'User' => [
                    'id'               => 2,
                    'display_username' => '表示名 2',
                    'roman_username'   => 'display name 2',
                    'photo_file_name'  => 'test2.jpg',
                ]
            ],
        ];
        $user_res = $this->User->makeSelect2UserList($users);
        foreach ($user_res as $k => $v) {
            $id = $k + 1;
            $this->assertEquals("user_{$id}", $v['id']);
            $this->assertEquals("表示名 {$id} (display name {$id})", $v['text']);
            $this->assertNotEquals("test{$id}.jpg", $v['image']);
            $this->assertTrue(strpos($v['image'], '.jpg') !== false);
        }
    }

    function testMakeSelect2User()
    {
        $user = [
            'User' => [
                'id'               => 1,
                'display_username' => '表示名 1',
                'roman_username'   => 'display name 1',
                'photo_file_name'  => 'test1.jpg',
            ]
        ];
        $user_res = $this->User->makeSelect2User($user);
        $this->assertEquals("user_1", $user_res['id']);
        $this->assertEquals("表示名 1 (display name 1)", $user_res['text']);
        $this->assertNotEquals("test1.jpg", $user_res['image']);
        $this->assertTrue(strpos($user_res['image'], '.jpg') !== false);
    }

    function testGetMyProf()
    {
        $this->User->my_uid = 1;
        $res = $this->User->getMyProf();
        $this->assertNotEmpty($res);
    }

    function testIsCompletedProfileForSetup()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;

        // In case that profile register is complete
        $this->User->save(['id' => 1, 'photo_file_name' => 'photo_file_name.png']);
        $this->User->TeamMember->save([
            'id'      => 1,
            'team_id' => 1,
            'user_id' => 1,
            'comment' => 'This is amazing comment'
        ]);
        $res = $this->User->isCompletedProfileForSetup($this->User->my_uid);
        $this->assertTrue($res);

        // In case that profile register is incomplete
        $this->User->save(['id' => 1, 'photo_file_name' => null]);
        $this->User->TeamMember->save(['id' => 1, 'team_id' => 1, 'user_id' => 1, 'comment' => null]);
        $res = $this->User->isCompletedProfileForSetup($this->User->my_uid);
        $this->assertFalse($res);
    }

    function testGenerateSetupGuideStatusDict()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->me['language'] = "jpn";
        $this->User->TeamMember->current_team_id = 1;
        $this->User->TeamMember->my_uid = 1;
        $this->User->TeamMember->Team->current_team_id = 1;
        $this->User->TeamMember->Team->my_uid = 1;
        $this->User->LocalName->my_uid = 1;
        $this->User->LocalName->current_team_id = 1;
        $this->User->TeamMember->Team->Term->current_team_id = 1;
        $this->User->TeamMember->Team->Term->addTermData(Term::TYPE_CURRENT);
        $this->User->TeamMember->Team->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->User->TeamMember->Team->Term->addTermData(Term::TYPE_NEXT);
        $this->current_date = strtotime('2015/7/1');
        $this->start_date = strtotime('2015/7/1');
        $this->end_date = strtotime('2015/10/1');
        $this->User->generateSetupGuideStatusDict($this->User->my_uid);
    }

    function testCompleteSetupGuide()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;
        $this->User->completeSetupGuide($this->User->my_uid);
    }

    function testGetUsersSetupNotCompleted()
    {
        // user: active, team: active, team_member: active
        $this->_saveUserRecords(['id' => $user_id = 1, 'setup_complete_flg' => false, 'active_flg' => true],
            $exec_delete_all = true);
        $this->_saveTeamRecords(['id' => $team_id = 1, 'del_flg' => false], $exec_delete_all = true);
        $this->_saveTeamMemberRecords([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'status'  => TeamMember::USER_STATUS_ACTIVE
        ],
            $exec_delete_all = true);
        $this->assertTrue((bool)$this->User->getUsersSetupNotCompleted());
        // assign team_id
        $this->_saveUserRecords(['id' => $user_id = 1, 'setup_complete_flg' => false, 'active_flg' => true],
            $exec_delete_all = true);
        $this->_saveTeamRecords(['id' => $team_id = 1, 'del_flg' => false], $exec_delete_all = true);
        $this->_saveTeamMemberRecords([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'status'  => TeamMember::USER_STATUS_ACTIVE
        ],
            $exec_delete_all = true);
        $this->assertTrue((bool)$this->User->getUsersSetupNotCompleted($team_id));

        // user: active, team: INACTIVE, team_member: active
        $this->_saveUserRecords(['id' => $user_id = 1, 'setup_complete_flg' => false, 'active_flg' => true],
            $exec_delete_all = true);
        $this->_saveTeamRecords(['id' => $team_id = 1, 'del_flg' => true], $exec_delete_all = true);
        $this->_saveTeamMemberRecords([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'status'  => TeamMember::USER_STATUS_ACTIVE
        ],
            $exec_delete_all = true);
        $this->assertFalse((bool)$this->User->getUsersSetupNotCompleted());

        // user: active, team: active, team_member: INACTIVE
        $this->_saveUserRecords(['id' => $user_id = 1, 'setup_complete_flg' => false, 'active_flg' => true],
            $exec_delete_all = true);
        $this->_saveTeamRecords(['id' => $team_id = 1, 'del_flg' => false], $exec_delete_all = true);
        $this->_saveTeamMemberRecords([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'status'  => TeamMember::USER_STATUS_INACTIVE
        ],
            $exec_delete_all = true);
        $this->assertFalse((bool)$this->User->getUsersSetupNotCompleted());

        // user: INACTIVE, team: active, team_member: active
        $this->_saveUserRecords(['id' => $user_id = 1, 'setup_complete_flg' => false, 'active_flg' => false],
            $exec_delete_all = true);
        $this->_saveTeamRecords(['id' => $team_id = 1, 'del_flg' => false], $exec_delete_all = true);
        $this->_saveTeamMemberRecords([
            'user_id' => $user_id,
            'team_id' => $team_id,
            'status'  => TeamMember::USER_STATUS_ACTIVE
        ],
            $exec_delete_all = true);
        $this->assertFalse((bool)$this->User->getUsersSetupNotCompleted());
    }

    function _saveUserRecords($data, $exec_delete_all = false)
    {
        if ($exec_delete_all) {
            $this->User->deleteAll(['User.id >' => 0]);
        }
        $this->User->saveAll($data, ['validate' => false]);
        return;
    }

    function _saveTeamRecords($data, $exec_delete_all = false)
    {
        if ($exec_delete_all) {
            $this->User->TeamMember->Team->deleteAll(['Team.id >' => 0]);
        }
        $this->User->TeamMember->Team->saveAll($data, ['validate' => false]);
        return;
    }

    function _saveTeamMemberRecords($data, $exec_delete_all = false)
    {
        if ($exec_delete_all) {
            $this->User->TeamMember->deleteAll(['TeamMember.id >' => 0]);
        }
        $this->User->TeamMember->saveAll($data, ['validate' => false]);
        return;
    }

    function testFilterActiveUserList()
    {
        $this->User->my_uid = 1;
        $this->User->current_team_id = 1;

        $current_list = $this->User->filterActiveUserList([1, 2, 3, 4, 5]);
        //to be inactive 1 user
        foreach ($current_list as $val) {
            $this->User->save(['id' => $val, 'active_flg' => false]);
            break;
        }
        $after_list = $this->User->filterActiveUserList([1, 2, 3, 4, 5]);
        $this->assertEquals(count($current_list), count($after_list) + 1);
    }

    function test_findNotBelongToTeamByEmail()
    {
        // TODO.Payment:add unit tests
    }

    public function test_typeConversionFromFind_success()
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $conditions = [
            'conditions' => [
                'id' => 1
            ]
        ];

        $res = $User->useType()->find('first', $conditions);

        $this->assertInternalType('int', $res["User"]['id']);
        $this->assertInternalType('int', $res["User"]['created']);
        $this->assertInternalType('int', $res["User"]['modified']);
    }

    public function test_getSingleEntityFromFind_success()
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $conditions = [
            'conditions' => [
                'id' => 1
            ]
        ];

        $result = $User->useEntity()->find('first', $conditions);

        $this->assertTrue($result instanceof UserEntity);
    }

    public function test_getManyEntityFromFind_success()
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $conditions = [
            'conditions' => [
                'del_flg' => false
            ]
        ];

        $result = $User->useEntity()->find('all', $conditions);

        foreach ($result as $element) {
            $this->assertTrue($element instanceof UserEntity);
        }
    }

    public function test_useTypeThenEntity_success()
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $conditions = [
            'conditions' => [
                'id' => 1
            ]
        ];

        $result = $User->useType()->useEntity()->find('first', $conditions);
        $this->assertTrue($result instanceof UserEntity);
        $this->assertInternalType('int', $result['id']);
    }

    public function test_useEntityThenType_success()
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $conditions = [
            'conditions' => [
                'id' => 1
            ]
        ];

        $result = $User->useEntity()->useType()->find('first', $conditions);
        $this->assertTrue($result instanceof UserEntity);
        $this->assertInternalType('int', $result['id']);
    }

    public function test_multipleFindAfterEntity_success()
    {
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $conditions = [
            'conditions' => [
                'id' => 1
            ]
        ];

        $result = $User->useType()->useEntity()->find('first', $conditions);
        $this->assertTrue($result instanceof UserEntity);
        $this->assertInternalType('int', $result['id']);

        /** @var User $User */
        $User = ClassRegistry::init('User');
        $result = $User->find('first', $conditions);
        $this->assertTrue(is_array($result));

        /** @var User $User */
        $User = ClassRegistry::init('User');
        $result = $User->useType()->useEntity()->find('first', $conditions);
        $this->assertTrue($result instanceof UserEntity);
        $this->assertInternalType('int', $result['id']);
    }

    public function test_findByKeywordRangeCircle()
    {
        $this->prepareTest_findByKeywordRangeCircle();
        $teamId = 1;
        $userId = 1;

        // Keyword is empty
        $res = $this->User->findByKeywordRangeCircle('', $teamId, $userId, 10, true);
        $this->assertEquals($res, []);


        /* Local name exit */
        /* Keyword: ja last name (local_names.last_name) */

        $res = $this->User->findByKeywordRangeCircle('東', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 1);
        $this->assertEquals($res[1]['id'], 3);
        // limit
        $res = $this->User->findByKeywordRangeCircle('東', $teamId, $userId, 1, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 1);
        // exclude auth user
        $res = $this->User->findByKeywordRangeCircle('東', $teamId, $userId, 2, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);
        // keyword: 2chara ja last name (local_names.last_name)
        $res = $this->User->findByKeywordRangeCircle('東大', $teamId, $userId, 2, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);
        // Not found
        $res = $this->User->findByKeywordRangeCircle('東大一', $teamId, $userId, 2, true);
        $this->assertEmpty($res);

        /* Keyword: ja first name (local_names.last_name) */
        // 1chara
        $res = $this->User->findByKeywordRangeCircle('次', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 2);
        $this->assertEquals($res[1]['id'], 12);
        // Perfect match
        $res = $this->User->findByKeywordRangeCircle('次郎', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 2);
        // Not found
        $res = $this->User->findByKeywordRangeCircle('次郎子', $teamId, $userId, 10, false);
        $this->assertEmpty($res);

        /* Keyword: roman last name (users.last_name) */
        // 1chara lowercase
        $res = $this->User->findByKeywordRangeCircle('t', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 1);
        $this->assertEquals($res[1]['id'], 3);
        // 1chara uppercase
        $res = $this->User->findByKeywordRangeCircle('T', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 1);
        $this->assertEquals($res[1]['id'], 3);
        // 2chara
        $res = $this->User->findByKeywordRangeCircle('To', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 1);
        $this->assertEquals($res[1]['id'], 3);
        // 3chara
        $res = $this->User->findByKeywordRangeCircle('Tod', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);
        // Perfect match
        $res = $this->User->findByKeywordRangeCircle('Todai', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);
        // Not found
        $res = $this->User->findByKeywordRangeCircle('Todaii', $teamId, $userId, 10, false);
        $this->assertEmpty($res);

        /* Keyword: roman first name (users.first_name) */
        // 1chara lowercase
        $res = $this->User->findByKeywordRangeCircle('j', $teamId, $userId, 10, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 2);
        $this->assertEquals($res[1]['id'], 12);
        // 1chara uppercase
        $res = $this->User->findByKeywordRangeCircle('J', $teamId, $userId, 10, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 2);
        $this->assertEquals($res[1]['id'], 12);
        // 2chara
        $res = $this->User->findByKeywordRangeCircle('Ji', $teamId, $userId, 10, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 2);
        $this->assertEquals($res[1]['id'], 12);
        // many chara
        $res = $this->User->findByKeywordRangeCircle('Jiro', $teamId, $userId, 10, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 2);
        $this->assertEquals($res[1]['id'], 12);
        // Perfect match
        $res = $this->User->findByKeywordRangeCircle('Jiroko', $teamId, $userId, 10, true);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 12);
        // Not found
        $res = $this->User->findByKeywordRangeCircle('Jirokoo', $teamId, $userId, 10, true);
        $this->assertEmpty($res);

        /* Secret circle */
        $res = $this->User->findByKeywordRangeCircle('東', $teamId, $userId, 10, false, 4);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 1);

        $res = $this->User->findByKeywordRangeCircle('埼玉', $teamId, $userId, 10, true, 4);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 2);

        $this->CircleMember->deleteAll(['circle_id' => 4]);
        $res = $this->User->findByKeywordRangeCircle('埼玉', $teamId, $userId, 10, true, 4);
        $this->assertEmpty($res);

        /* Team member status */
        // Inactive
        $this->TeamMember->updateAll(['status' => Enum\Model\TeamMember\Status::INACTIVE], ['user_id' => 12]);
        $res = $this->User->findByKeywordRangeCircle('J', $teamId, $userId, 10, true);
        $this->assertNotEmpty($res);
        $this->assertEquals($res[0]['id'], 2);

        // Invited
        $this->TeamMember->updateAll(['status' => Enum\Model\TeamMember\Status::INVITED], ['user_id' => 2]);
        $res = $this->User->findByKeywordRangeCircle('J', $teamId, $userId, 10, true);
        $this->assertEmpty($res);

        /* Local name doesn't exit */
        $this->LocalName->deleteAll(['LocalName.del_flg' => false]);
        $res = $this->User->findByKeywordRangeCircle('東', $teamId, $userId, 10, false);
        $this->assertEmpty($res);

        $this->LocalName->deleteAll(['LocalName.del_flg' => false]);
        $res = $this->User->findByKeywordRangeCircle('To', $teamId, $userId, 10, false);
        $this->assertNotEmpty($res);
        $this->assertEquals(count($res), 2);
        $this->assertEquals($res[0]['id'], 1);
        $this->assertEquals($res[1]['id'], 3);

        /* Different team */
        $res = $this->User->findByKeywordRangeCircle('To', 2, $userId, 10, false);
        $this->assertEmpty($res);

    }

    private function prepareTest_findByKeywordRangeCircle()
    {
        $this->User->me['language'] = 'jpn';
        $this->LocalName->deleteAll(['LocalName.del_flg' => false]);
        $this->User->saveAll([
            [
                'id' => 1,
                'first_name' => 'Ichiro',
                'last_name' => 'Tokyo'
            ],
            [
                'id' => 2,
                'first_name' => 'Jiro',
                'last_name' => 'Saitama'
            ],
            [
                'id' => 3,
                'first_name' => 'Saburo',
                'last_name' => 'Todai'
            ],
            [
                'id' => 12,
                'first_name' => 'Jiroko',
                'last_name' => 'Chiba'
            ],
            [
                'id' => 13,
                'first_name' => 'Idaten',
                'last_name' => 'Kitachiba'
            ],
        ], ['validate' => false]);
        $this->LocalName->saveAll([
            [
                'user_id' => 1,
                'first_name' => '一郎',
                'last_name' => '東京',
                'language' => 'jpn'
            ],
            [
                'user_id' => 2,
                'first_name' => '次郎',
                'last_name' => '埼玉',
                'language' => 'jpn'
            ],
            [
                'user_id' => 3,
                'first_name' => '三郎',
                'last_name' => '東大',
                'language' => 'jpn'
            ],
            [
                'user_id' => 12,
                'first_name' => '次露子',
                'last_name' => '千葉',
                'language' => 'jpn'
            ],
            [
                'user_id' => 13,
                'first_name' => '韋駄天',
                'last_name' => '北千葉',
                'language' => 'jpn'
            ],
        ], ['validate' => false]);
    }
}
