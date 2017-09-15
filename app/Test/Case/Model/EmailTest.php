<?php App::uses('GoalousTestCase', 'Test');
App::uses('Email', 'Model');
use Goalous\Model\Enum as Enum;

/**
 * Email Test Case
 *
 * @property Email $Email
 * @property TeamMember $TeamMember
 */
class EmailTest extends GoalousTestCase
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
        'app.notify_setting',
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
        $this->TeamMember = ClassRegistry::init('TeamMember');
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
        $res = $this->Email->isActiveOnTeamByEmail($email, $team_id);
        $this->assertTrue($res, "[正常]メアドからユーザが既にチームに存在しているかのチェック");
    }

    function testIsVerified()
    {
        $email = 'aaaaa@aaaaaaa.com';
        $res = $this->Email->isVerified($email);
        $this->assertFalse($res);
        $this->Email->save(['email' => $email, 'email_verified' => true]);
        $res = $this->Email->isVerified($email);
        $this->assertTrue($res);
    }

    function test_findNotBelongAnyTeamsByEmails()
    {
        $teamId = 1;
        $this->Email->deleteAll(['Email.del_flg' => false]);
        $emails = ['test@company.com'];
        $res = $this->Email->findNotBelongAnyTeamsByEmails($emails);
        $this->assertEmpty($res);

        $email1 = 'test@company.com';
        $emails = [$email1];
        $this->Email->save(['email' => $email1], false);
        $res = $this->Email->findNotBelongAnyTeamsByEmails($emails);
        $this->assertEmpty($res);

        $userId = $this->createActiveUser($teamId);
        $this->Email->id = $this->Email->getLastInsertID();
        $this->Email->saveField('user_id', $userId);

        $res = $this->Email->findNotBelongAnyTeamsByEmails($emails);
        $this->assertEmpty($res);

        $this->TeamMember->updateAll(
            ['status' => Enum\TeamMember\Status::INACTIVE],
            ['user_id' => $userId, 'team_id' => $teamId]
        );

        $res = $this->Email->findNotBelongAnyTeamsByEmails($emails);
        $this->assertNotEmpty($res);

        $this->TeamMember->updateAll(
            ['status' => Enum\TeamMember\Status::INVITED],
            ['user_id' => $userId, 'team_id' => $teamId]
        );

        $res = $this->Email->findNotBelongAnyTeamsByEmails($emails);
        $this->assertEmpty($res);
    }

    function test_findExistUsersByEmail()
    {
        // TODO.Payment:add unit tests
    }

    function test_findExistByTeamId()
    {
        // TODO.Payment:add unit tests
    }
}
