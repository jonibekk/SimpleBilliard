<?php
App::uses('Invite', 'Model');

/**
 * Invite Test Case
 *
 * @property  Invite $Invite
 */
class InviteTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
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
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Invite);

        parent::tearDown();
    }

    function testSaveInvite()
    {
        $team_id = "537ce224-c21c-41b6-a808-433dac11b50b";
        $user_id = '537ce224-8c0c-4c99-be76-433dac11b50b';
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
        $this->assertFalse($res, "[正常]存在するユーザか？");
    }

    function testIsForMe()
    {
        $token = 'token_test001';
        $uid = "bbb";
        $res = $this->Invite->isForMe($token, $uid);
        $this->assertTrue($res, "[正常]トークン自分宛");
        $uid = "bbc";
        $res = $this->Invite->isForMe($token, $uid);
        $this->assertFalse($res, "[異常]トークン自分宛");

    }

}
