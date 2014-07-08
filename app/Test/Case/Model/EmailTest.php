<?php
App::uses('Email', 'Model');

/**
 * Email Test Case
 *
 * @property Email $Email
 */
class EmailTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.email',
        'app.user',
        'app.team_member',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Email = ClassRegistry::init('Email');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Email);
        parent::tearDown();
    }

    public $baseData = [];

    function getValidationRes($data = [])
    {
        if (empty($data)) {
            return null;
        }
        $testData = array_merge($this->baseData, $data);
        $this->Email->create();
        $this->Email->set($testData);
        return $this->Email->validates();
    }

    /**
     * Emailモデルのバリデーションチェックのテスト
     */
    public function testEmailValidations()
    {
        $this->assertFalse(
             $this->getValidationRes(['email' => '']),
             "[異常系]メールアドレスは空を受け付けない"
        );
        $this->assertTrue(
             $this->getValidationRes(['email' => 'xxx@aaa.com']),
             "[正常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxxaaa.com']),
             "[異常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxxaaa.comaaaa']),
             "[異常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxxaaacomaaaa']),
             "[異常系]メールアドレスとして正しいか"
        );
        $this->assertFalse(
             $this->getValidationRes(['email' => 'xxx@aaa.com,xxx@aaa.com']),
             "[異常系]メールアドレスとして正しいか"
        );
    }

    function testIsAllVerifiedSuccess()
    {
        $uid = "1";
        $testData = [
            'user_id'        => $uid,
            'email'          => 'standalonea@email.com',
            'email_verified' => true,
        ];
        $this->Email->save($testData);
        $this->assertTrue($this->Email->isAllVerified($uid), "[正常]全て認証済みである");
    }

    function testIsAllVerifiedFail()
    {
        $uid = "1";
        $testData = [
            'user_id'        => $uid,
            'email'          => 'standalonea@email.com',
            'email_verified' => false,
        ];
        $this->Email->save($testData);
        $this->assertFalse($this->Email->isAllVerified($uid), "[異常]全て認証済みである");
    }

    function testIsOwn()
    {
        $uid = "1";
        $testData = [
            'user_id'        => $uid,
            'email'          => 'standalonea@email.com',
            'email_verified' => false,
        ];
        $this->Email->save($testData);
        $email_id = $this->Email->getLastInsertID();
        $this->assertTrue($this->Email->isOwner($uid, $email_id), "[正常]所有者チェック email_idあり");
        $this->Email->id = $email_id;
        $this->assertTrue($this->Email->isOwner($uid), "[正常]所有者チェック email_idなし");
        $this->Email->id = null;
        $this->assertFalse($this->Email->isOwner($uid), "[異常]所有者チェック email_idなし");
    }

    function testIsBelongTeamByEmail()
    {
        $email = 'from@email.com';
        $team_id = '1';
        $res = $this->Email->isBelongTeamByEmail($email, $team_id);
        $this->assertTrue($res, "[正常]メアドからユーザが既にチームに存在しているかのチェック");
    }

}
