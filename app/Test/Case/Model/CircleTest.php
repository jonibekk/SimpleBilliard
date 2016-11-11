<?php App::uses('GoalousTestCase', 'Test');
App::uses('Circle', 'Model');

/**
 * Circle Test Case
 *
 * @property Circle $Circle
 */
class CircleTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(

        'app.circle',
        'app.team',
        'app.user',
        'app.post',
        'app.post_share_circle',
        'app.team_member',
        'app.local_name',
        'app.circle_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Circle = ClassRegistry::init('Circle');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Circle);

        parent::tearDown();
    }

    public function testUpdateModifiedIfEmpty()
    {
        $circle_list = [];
        $res = $this->Circle->updateModified($circle_list);
        $this->assertFalse($res);
    }

    function testGetPublicCircles()
    {
        $this->Circle->my_uid = 1;
        $this->Circle->current_team_id = 1;
        $this->Circle->Team->TeamMember->current_team_id = 1;

        $circles = $this->Circle->getPublicCircles($type = 'all');
        // CircleMemberに含まれているのが、全員アクティブなチームメンバーか確認
        $active_user_ids = $this->Circle->Team->TeamMember->getActiveTeamMembersList();
        foreach ($circles as $circle) {
            foreach ($circle['CircleMember'] as $member) {
                $this->assertContains($member['user_id'], $active_user_ids);
            }
        }

        $this->Circle->getPublicCircles($type = 'joined', 1, 100000000000);
        $this->Circle->getPublicCircles($type = 'joined');
        $this->Circle->getPublicCircles($type = 'non-joined');
        $this->Circle->my_uid = 2;
        $this->Circle->getPublicCircles($type = 'joined');
        $this->Circle->getPublicCircles($type = 'non-joined');
    }

    function testAddEmpty()
    {
        $this->_setDefault();
        $this->assertFalse($this->Circle->add([], true, true));
    }

    public function testAddCircles()
    {
        $this->_setDefault();
        $data = [
            'Circle' => [
                'name'       => 'test',
                'public_flg' => true,
                'members'    => 'user_1,user_2,user_3',
            ]
        ];
        $res = $this->Circle->add($data, true, true);
        $this->assertTrue($res);
    }

    function _setDefault()
    {
        $this->Circle->my_uid = 1;
        $this->Circle->current_team_id = 1;
        $this->Circle->PostShareCircle->Post->my_uid = 1;
        $this->Circle->PostShareCircle->Post->current_team_id = 1;
        $this->Circle->PostShareCircle->my_uid = 1;
        $this->Circle->PostShareCircle->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
    }

    function testIsSecret()
    {
        $this->Circle->id = 18;
        $res = $this->Circle->isSecret($this->Circle->id);
        $this->assertTrue(empty($res));
    }

    function testIsTeamAllCircle()
    {
        $this->Circle->id = 1;
        $res = $this->Circle->isTeamAllCircle($this->Circle->id);
        $this->assertTrue(empty($res));
    }

    function testGetTeamAllCircle()
    {
        // 正常系
        $this->Circle->current_team_id = 1;
        $testAllCircle = $this->Circle->getTeamAllCircle();
        $this->assertEquals(1, $testAllCircle['Circle']['team_id']);
        $this->assertEquals(1, $testAllCircle['Circle']['team_all_flg']);

        // 存在しないチームの場合
        $this->Circle->current_team_id = 9999999;
        $testAllCircle = $this->Circle->getTeamAllCircle();
        $this->assertEmpty($testAllCircle);
    }

    function testGetCirclesAndMemberById()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->Team->TeamMember->current_team_id = 1;

        $circles = $this->Circle->getCirclesAndMemberById([1]);
        $this->assertNotEmpty($circles);

        // CircleMemberに含まれているのが、全員アクティブなチームメンバーか確認
        $active_user_ids = $this->Circle->Team->TeamMember->getActiveTeamMembersList();
        foreach ($circles as $circle) {
            foreach ($circle['CircleMember'] as $member) {
                $this->assertContains($member['user_id'], $active_user_ids);
            }
        }
    }

    function testEditFailed()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->Team->TeamMember->current_team_id = 1;

        $circles = $this->Circle->edit([]);
        $this->assertFalse($circles);
    }

    function testEditSuccess()
    {
        $this->_setDefault();
        $data = [
            'Circle' => [
                'name'         => 'test',
                'public_flg'   => true,
                'members'      => 'user_1,user_2,user_3',
                'team_all_flg' => 1,
            ]
        ];
        $this->Circle->add($data, true, true);

        $edit_data = [
            'Circle' => [
                'id'         => $this->Circle->getLastInsertID(),
                'name'       => 'test',
                'public_flg' => true,
            ]
        ];
        $this->assertNotEmpty($this->Circle->edit($edit_data));
    }

    function testAddMember()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->User->TeamMember->current_team_id = 1;

        $circle_id = 1;
        $member_list = $this->Circle->CircleMember->getMemberList($circle_id);
        $member_count = count($member_list);
        $data = [
            'Circle' => [
                'id'           => 1,
                'members'      => 'user_13',
                'team_all_flg' => false,
            ]
        ];
        $res = $this->Circle->addMember($data);
        $this->assertTrue($res);

        // メンバーが増えているか確認
        $new_member_list = $this->Circle->CircleMember->getMemberList($circle_id);
        $this->assertEquals($member_count + 1, count($new_member_list));
    }

    function testAddMemberFailed()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->User->TeamMember->current_team_id = 1;

        // パラメータ不正
        $data = [
            'Circle' => [
                'id'      => 1,
                'members' => 'user_13',
            ]
        ];
        $res = $this->Circle->addMember($data);
        $this->assertFalse($res);

        $data = [
            'Circle' => [
                'id'           => 1,
                'members'      => 'user_',
                'team_all_flg' => false,
            ]
        ];
        $res = $this->Circle->addMember($data);
        $this->assertFalse($res);

        // チーム全体サークルはメンバー追加不可
        $data = [
            'Circle' => [
                'id'           => 3,
                'members'      => 'user_13',
                'team_all_flg' => true,

            ]
        ];
        $res = $this->Circle->addMember($data);
        $this->assertFalse($res);

        // 重複ユーザー
        $data = [
            'Circle' => [
                'id'           => 1,
                'members'      => 'user_2',
                'team_all_flg' => false,
            ]
        ];
        $res = $this->Circle->addMember($data);
        $this->assertFalse($res);
    }

    public function testGetCirclesByKeyword()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        $circles = $this->Circle->getCirclesByKeyword('チーム全体');

        $this->assertNotEmpty($circles);
        $circles = $this->Circle->getCirclesByKeyword('チーム');
        $this->assertNotEmpty($circles);
        $circles = $this->Circle->getCirclesByKeyword('全体');
        $this->assertEmpty($circles);

        // 秘密サークル
        $circles = $this->Circle->getCirclesByKeyword('秘密サークル');
        $this->assertNotEmpty($circles);

        // 存在しないサークル
        $circles = $this->Circle->getCirclesByKeyword('存在しないサークル名');
        $this->assertEmpty($circles);

        // スペースのみ
        $circles = $this->Circle->getCirclesByKeyword(' ');
        $this->assertEmpty($circles);
    }

    public function testGetPublicCirclesByKeyword()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        $circles = $this->Circle->getPublicCirclesByKeyword('チーム全体');

        $this->assertNotEmpty($circles);
        $circles = $this->Circle->getPublicCirclesByKeyword('チーム');
        $this->assertNotEmpty($circles);
        $circles = $this->Circle->getPublicCirclesByKeyword('全体');
        $this->assertEmpty($circles);

        // 秘密サークル
        $circles = $this->Circle->getPublicCirclesByKeyword('秘密サークル');
        $this->assertEmpty($circles);

        // 存在しないサークル
        $circles = $this->Circle->getPublicCirclesByKeyword('存在しないサークル名');
        $this->assertEmpty($circles);
    }

    public function testGetSecretCirclesByKeyword()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        $circles = $this->Circle->getSecretCirclesByKeyword('秘密サークル');
        $this->assertNotEmpty($circles);
        $circles = $this->Circle->getSecretCirclesByKeyword('秘密');
        $this->assertNotEmpty($circles);

        // 公開サークル
        $circles = $this->Circle->getSecretCirclesByKeyword('チーム全体');
        $this->assertEmpty($circles);

        // 存在しないサークル
        $circles = $this->Circle->getSecretCirclesByKeyword('存在しないサークル名');
        $this->assertEmpty($circles);
    }

    function testGetAccessibleCirclesByKeyword()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        // 公開サークル
        $circles = $this->Circle->getAccessibleCirclesByKeyword('チーム全体');
        $this->assertNotEmpty($circles);

        // 所属していない公開サークル
        $circles = $this->Circle->getAccessibleCirclesByKeyword('公開サークル１');
        $this->assertNotEmpty($circles);

        // 所属している秘密サークル
        $circles = $this->Circle->getAccessibleCirclesByKeyword('秘密サークル');
        $this->assertNotEmpty($circles);

        // 所属していない秘密サークル
        $circles = $this->Circle->getAccessibleCirclesByKeyword('秘密サークル２');
        $this->assertEmpty($circles);
    }

    function testGetAccessibleCirclesSelect2()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        // 公開サークル
        $circles = $this->Circle->getAccessibleCirclesSelect2('チーム全体');
        $this->assertArrayHasKey('results', $circles);
        $this->assertNotEmpty($circles['results']);
        $this->assertArrayHasKey('id', $circles['results'][0]);
        $this->assertArrayHasKey('text', $circles['results'][0]);
        $this->assertArrayHasKey('image', $circles['results'][0]);

        // 所属していない公開サークル
        $circles = $this->Circle->getAccessibleCirclesSelect2('公開サークル１');
        $this->assertArrayHasKey('results', $circles);
        $this->assertNotEmpty($circles['results']);
        $this->assertArrayHasKey('id', $circles['results'][0]);
        $this->assertArrayHasKey('text', $circles['results'][0]);
        $this->assertArrayHasKey('image', $circles['results'][0]);

        // 所属している秘密サークル
        $circles = $this->Circle->getAccessibleCirclesSelect2('秘密サークル');
        $this->assertArrayHasKey('results', $circles);
        $this->assertNotEmpty($circles['results']);
        $this->assertArrayHasKey('id', $circles['results'][0]);
        $this->assertArrayHasKey('text', $circles['results'][0]);
        $this->assertArrayHasKey('image', $circles['results'][0]);

        // 所属していない秘密サークル
        $circles = $this->Circle->getAccessibleCirclesSelect2('秘密サークル２');
        $this->assertArrayHasKey('results', $circles);
        $this->assertEmpty($circles['results']);
    }

    public function testGetList()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        $circles = $this->Circle->find('all');
        $circle_list = $this->Circle->getList();
        foreach ($circles as $v) {
            $this->assertArrayHasKey($v['Circle']['id'], $circle_list);
            unset($circle_list[$v['Circle']['id']]);
        }
        $this->assertEmpty($circle_list);
    }

    public function testPublicCircleGetList()
    {
        $this->Circle->current_team_id = 1;
        $this->Circle->my_uid = 1;
        $this->Circle->CircleMember->current_team_id = 1;
        $this->Circle->CircleMember->my_uid = 1;

        $circles = $this->Circle->find('all');
        $circle_list = $this->Circle->getPublicCircleList();
        foreach ($circles as $v) {
            if ($v['Circle']['public_flg'] == 1) {
                $this->assertTrue(isset($circle_list[$v['Circle']['id']]));
            }
        }
    }

}
