<?php App::uses('GoalousTestCase', 'Test');
App::uses('Invite', 'Model');

/**
 * Invite Test Case
 *
 * @property  Invite $Invite
 * @property  Email  $Email
 */
class InviteTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.local_name',
        'app.notify_setting',
        'app.team_member',
        'app.invite',
        'app.team',
        'app.email',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Invite = ClassRegistry::init('Invite');
        $this->Email  = ClassRegistry::init('Email');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Invite);
        unset($this->Email);

        parent::tearDown();
    }

    function testSaveInvite()
    {
        $team_id = "1";
        $user_id = '1';
        $email = "nodata@aaaaaaaaaaaaa.com";
        //登録済みでもなく招待もしていない場合
        $before_count = $this->Invite->find('count');
        $this->Invite->saveInvite($email, $team_id, $user_id);
        $after_count = $this->Invite->find('count');
        $this->assertEquals($before_count + 1, $after_count, "[正常]招待データ保存:登録済みでもなく招待もしていない場合");
        $id = $this->Invite->getLastInsertID();
        //既に招待しているユーザの場合
        $before_count = $this->Invite->find('count');
        $this->Invite->saveInvite($email, $team_id, $user_id);
        $after_count = $this->Invite->find('count');
        $this->assertEquals($before_count, $after_count, "[正常]招待データ保存:既に招待しているユーザの場合");
        $this->assertEmpty($this->Invite->findById($id), "[正常]招待データ保存:既に招待しているユーザの場合、以前のデータは削除");
        //登録済みのユーザの場合
        $before_count = $this->Invite->find('count');
        $res = $this->Invite->saveInvite("test@aaa.com", $team_id, $user_id);
        $after_count = $this->Invite->find('count');
        $this->assertEquals($before_count + 1, $after_count, "[正常]招待データ保存:登録済みの場合");
        $this->assertArrayHasKey("to_user_id", $res['Invite'], "[正常]招待データ保存:登録済みの場合、user_idがセットされている");
        //メッセージあり
        $before_count = $this->Invite->find('count');
        $res = $this->Invite->saveInvite($email, $team_id, $user_id, "message");
        $after_count = $this->Invite->find('count');
        $this->assertEquals($before_count, $after_count, "[正常]招待データ保存:メッセージあり");
        $this->assertArrayHasKey("message", $res['Invite'], "[正常]招待データ保存:メッセージありの場合、メッセージが保存されている");
    }

    function testGetByToken()
    {
        $token = 'token_test001';
        $this->Invite->tokenData = null;
        $res = $this->Invite->getByToken($token);
        $this->assertNotEmpty($res, "[正常]トークンデータ取得(データなし)");
        $res2 = $this->Invite->getByToken($token);
        $this->assertEquals($res, $res2, "[正常]トークンデータ取得(データあり)");
    }

    function testIsUser()
    {
        $token = 'token_test001';
        $res = $this->Invite->isUser($token);
        $this->assertTrue($res, "[異常]存在するユーザか？");

        $this->Invite->tokenData['Invite']['to_user_id'] = null;
        $res = $this->Invite->isUser($token);
        $this->assertTrue($res, "[正常]存在するユーザか？");

        $this->Invite->tokenData['Invite']['email'] = "not_found";
        $res = $this->Invite->isUser($token);
        $this->assertFalse($res, "[正常]存在するユーザか？");

    }

    function testIsForMe()
    {
        $token = 'token_test001';
        $uid = "1234567891";
        $res = $this->Invite->isForMe($token, $uid);
        $this->assertTrue($res, "[正常]トークン自分宛");
        $this->Invite->tokenData = null;

        $uid = "1234567892";
        $res = $this->Invite->isForMe($token, $uid);
        $this->assertFalse($res, "[異常]トークン自分宛");
        $this->Invite->tokenData = null;

        /** @noinspection PhpUndefinedMethodInspection */
        $token_data = $this->Invite->findByEmailToken($token);
        $this->Invite->id = $token_data['Invite']['id'];
        $this->Invite->saveField('to_user_id', null);
        $uid = "1234567891";
        $res = $this->Invite->isForMe($token, $uid);
        $this->assertFalse($res, "[異常]to_user_idなし");
    }

    function testConfirmToken()
    {
        $token = "not_found";
        $res = $this->Invite->confirmToken($token);
        $this->assertTrue($res !== true, "[異常]tokenデータなし");

        $id = '1';
        $this->Invite->tokenData = null;
        $this->Invite->id = $id;
        $this->Invite->saveField('email_verified', true);
        $token = "token_test001";
        $res = $this->Invite->confirmToken($token);
        $this->assertTrue($res !== true, "[異常]既に認証済みの古いtoken");

        $id = '1';
        $this->Invite->tokenData = null;
        $this->Invite->id = $id;
        $this->Invite->saveField('email_verified', false);
        $this->Invite->saveField('email_token_expires', strtotime("-1 day", time()));
        $token = "token_test001";
        $res = $this->Invite->confirmToken($token);
        $this->assertTrue($res !== true, "[異常]token期限切れ");

        $id = '1';
        $this->Invite->tokenData = null;
        $this->Invite->id = $id;
        $this->Invite->saveField('email_token_expires', strtotime("+1 day", time()));
        $token = "token_test001";
        $res = $this->Invite->confirmToken($token);
        $this->assertTrue($res, "[正常]token確認");
    }

    function testVerify()
    {
        $id = '2';
        $this->Invite->tokenData = null;
        $this->Invite->id = $id;
        $this->Invite->saveField('email_token_expires', strtotime("+1 day", time()));
        $token = "token_test002";
        $res = $this->Invite->verify($token, 2);
        $this->assertArrayHasKey('id', $res['Invite']);
    }

    function testIsByBatchSetupFail()
    {
        $this->assertFalse($this->Invite->isByBatchSetup('not_exists_token'));
    }

    function testGetInviterUserNoData()
    {
        $res = $this->Invite->getInviteById(1111);
        $this->assertEmpty($res);
    }

    function testGetInviterUserSuccess()
    {
        $res = $this->Invite->getInviteById(2);
        $this->assertNotEmpty($res);
    }

    function testGetInviteUserList()
    {
        $team_id = '999';
        $email   = 'test999@isao.co.jp';
        $user_id = 20;
        $this->Invite->save([
            'team_id' => $team_id,
            'email'   => $email,
        ]);
        $this->Email->save([
            'email'   => $email,
            'user_id' => $user_id,
        ]);

        $res = $this->Invite->getInviteUserList($team_id);
        $this->assertEquals($email, $res[0]['Invite']['email']);
        $this->assertEquals(20, $res[0]['Email']['user_id']);
    }

    function test_findUnverifiedBeforeExpired()
    {
        $baseDate = strtotime('2017-06-20');
        $verifiedInviteId = $this->createInvite(['email_verified' => true]);
        $tokenExpiredInviteId = $this->createInvite(['email_token_expires' => $baseDate - DAY]);
        $targetInviteId = $this->createInvite();

        $result = $this->Invite->findUnverifiedBeforeExpired($baseDate);
        $resultIds = Hash::extract($result, '{n}.id');

        $this->assertNotContains($verifiedInviteId, $resultIds);
        $this->assertNotContains($tokenExpiredInviteId, $resultIds);
        $this->assertContains($targetInviteId, $resultIds);
    }

    function test_getUnverifiedWithEmailByUserId()
    {
        // case regular
        $result = $this->Invite->getUnverifiedWithEmailByUserId(1, 1);
        $this->assertEquals('from@email.com', $result['Email']['email']);
        // case not found
        $result = $this->Invite->getUnverifiedWithEmailByUserId(100, 100);
        $this->assertEquals([], $result);
    }

    public function test_deleteInvite_success()
    {

        // regist test data
        $teamId = 1000;
        $email  = 'tester1000@isao.co.jp';
        $userId = 25;

        $this->Invite->save([
            'team_id' => $teamId,
            'email'   => $email,
        ]);
        $this->Email->save([
            'email'   => $email,
            'user_id' => $userId,
        ]);

        $res1 = $this->Invite->find('first',[
                'conditions' => [
                    'team_id' => $teamId,
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );
        $this->assertCount(1, $res1);

        // exeute target function
        $this->Invite->deleteInvite($teamId, $email);

        $res2 = $this->Invite->find('first',[
                'conditions' => [
                    'team_id' => $teamId,
                    'email'   => $email,
                    'del_flg' => false
                ]
            ]
        );

        $this->assertCount(0, $res2);
    }

    /**
     * @expectedException \Goalous\Exception\GoalousNotFoundException
     */
    public function test_deleteInvite_failure()
    {
        // regist test data
        $teamId = '999';
        $email  = 'tester999@isao.co.jp';
        $userId = '25';
        $this->Invite->save([
            'team_id' => $teamId,
            'email'   => $email,
        ]);
        $this->Email->save([
            'email'   => $email,
            'user_id' => $userId,
        ]);

        $notExistEmail = 'tester000@isao.co.jp';

        // exeute target function
        $this->Invite->deleteInvite($teamId, $notExistEmail);
    }
}
