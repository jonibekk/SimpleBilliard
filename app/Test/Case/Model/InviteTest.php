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

}
