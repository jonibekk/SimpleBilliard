<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Circle', 'Model');
App::import('Model/Entity', 'CircleEntity');

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
        $this->assertFalse($this->Circle->add([], 1));
    }

    public function testAddCircles()
    {
        $this->_setDefault();
        $data = [
            'Circle' => [
                'name'        => 'test',
                'description' => 'description',
                'public_flg'  => true
            ]
        ];
        $res = $this->Circle->add($data, 1);
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
                'description'  => 'test',
                'public_flg'   => true,
                'members'      => 'user_1,user_2,user_3',
                'team_all_flg' => 1,
            ]
        ];
        $this->Circle->add($data, 1);

        $edit_data = [
            'Circle' => [
                'id'          => $this->Circle->getLastInsertID(),
                'name'        => 'test',
                'description' => 'test',
                'public_flg'  => true,
            ]
        ];
        $this->assertNotEmpty($this->Circle->edit($edit_data));
    }

    public function test_findByKeyword()
    {
        $allCircles = Hash::combine($this->Circle->find('all'), '{n}.Circle.id', '{n}.Circle.name');
        $allCircleIds = array_keys($allCircles);
        /* Japanese */
        $res = $this->Circle->findByKeyword('チーム全体', 10, []);
        $this->assertEmpty($res);

        $res = $this->Circle->findByKeyword('チーム全体', 10, $allCircleIds);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);
        $this->assertEquals($res[0]['name'], 'チーム全体');

        $res = $this->Circle->findByKeyword('ち', 10, $allCircleIds);
        $this->assertEmpty($res);

        $res = $this->Circle->findByKeyword('全', 10, $allCircleIds);
        $this->assertEmpty($res);

        $res = $this->Circle->findByKeyword('チ', 10, $allCircleIds);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);

        $res = $this->Circle->findByKeyword('チー', 10, $allCircleIds);
        $this->assertEquals(count($res), 1);
        $this->assertEquals($res[0]['id'], 3);

        /* Alphabet */
        // Uppercase
        $res = $this->Circle->findByKeyword('l', 10, $allCircleIds);
        $this->assertEquals(count($res), 3);
        $this->assertEquals(Hash::extract($res, '{n}.id'), [15, 16, 17]);

        // Lowercase
        $res = $this->Circle->findByKeyword('L', 10, $allCircleIds);
        $this->assertEquals(count($res), 3);
        $this->assertEquals(Hash::extract($res, '{n}.id'), [15, 16, 17]);

        // Perfect match
        $res = $this->Circle->findByKeyword('Lorem ipsum dolor sit amet', 10, $allCircleIds);
        $this->assertEquals(count($res), 3);
        $this->assertEquals(Hash::extract($res, '{n}.id'), [15, 16, 17]);

        /* Secret circles */
        // Public flg: true
        $res = $this->Circle->findByKeyword('秘', 10, $allCircleIds);
        $this->assertEmpty($res);
        // Public flg: false
        $res = $this->Circle->findByKeyword('秘', 10, $allCircleIds, false);
        $this->assertEquals(count($res), 2);
        $this->assertEquals(Hash::extract($res, '{n}.id'), [4, 6]);

        // Perfect match
        $res = $this->Circle->findByKeyword('秘密サークル', 10, $allCircleIds, false);
        $this->assertEquals(count($res), 2);
        $this->assertEquals(Hash::extract($res, '{n}.id'), [4, 6]);

        /* Only space */
        $res = $this->Circle->findByKeyword(' ', 10, $allCircleIds, false);
        $this->assertEmpty($res);
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
        $this->assertNotEmpty($circles);

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
        $this->assertNotEmpty($circles);

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

    public function test_belongToTeam()
    {
        $circleId = $this->createCircle(['team_id' => 1]);
        $this->assertTrue($this->Circle->belongToTeam(1, $circleId));
        $this->assertFalse($this->Circle->belongToTeam(2, $circleId));
    }

    public function test_convertTypeOnFind_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->useType()->find('first')['Circle'];

        $this->assertInternalType('int', $result['id']);
        $this->assertInternalType('int', $result['team_id']);
    }

    public function test_convertEntityOnFind_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->useEntity()->find('first');

        $this->assertTrue($result instanceof CircleEntity);

    }

    public function test_convertTypeEntityOnFind_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->useType()->useEntity()->find('first');

        $this->assertTrue($result instanceof CircleEntity);
        $this->assertInternalType('int', $result['id']);
        $this->assertInternalType('int', $result['team_id']);
    }

    public function test_convertTypeOnSave_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $newData = [
            "name"         => "Circle Name",
            "description"  => "Circle description",
            "team_id"      => 1,
            "public_flg"   => true,
            "team_all_flg" => false
        ];

        $result = $Circle->useType()->save($newData, false)['Circle'];

        $this->assertInternalType('int', $result['id']);
        $this->assertInternalType('int', $result['team_id']);

    }

    public function test_convertEntityOnSave_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $newData = [
            "name"         => "Circle Name",
            "description"  => "Circle description",
            "team_id"      => 1,
            "public_flg"   => true,
            "team_all_flg" => false
        ];

        $result = $Circle->useType()->useEntity()->save($newData, false);

        $this->assertTrue($result instanceof CircleEntity);

    }

    public function test_convertTypeEntityOnSave_success()
    {
        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $newData = [
            "name"         => "Circle Name",
            "description"  => "Circle description",
            "team_id"      => 1,
            "public_flg"   => true,
            "team_all_flg" => false
        ];

        $result = $Circle->useType()->useEntity()->save($newData, false);

        $this->assertTrue($result instanceof CircleEntity);
        $this->assertInternalType('int', $result['id']);
        $this->assertInternalType('int', $result['team_id']);
    }

    public function test_updateMemberCount_success()
    {

        $circleId = 1;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $condition = [
            'conditions' => [
                'id' => $circleId
            ]
        ];

        $startingCircle = $Circle->useType()->useEntity()->find('first', $condition);

        $Circle->updateAll(['circle_member_count' => 0], ['id' => $circleId]);

        $Circle->updateMemberCount($circleId);

        $updatedCircle = $Circle->useType()->useEntity()->find('first', $condition);

        $this->assertNotEqual($startingCircle['modified'], $updatedCircle['modified']);
        $this->assertNotEqual($startingCircle['circle_member_count'], $updatedCircle['circle_member_count']);
    }

    public function test_updateLatestPosted_success()
    {

        $circleId = 1;
        $newTime = 1000000;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $Circle->updateLatestPosted($circleId, $newTime);

        $circle = $Circle->getEntity($circleId);
        $this->assertEquals($newTime, $circle['latest_post_created']);

        $Circle->updateLatestPosted($circleId);

        $circle = $Circle->getEntity($circleId);
        $this->assertTrue($newTime < $circle['latest_post_created']);
    }

    public function test_updateLatestPostedInCircles_success()
    {
        $newTime = 1000000;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $Circle->updateLatestPostedInCircles([1, 2], $newTime);

        $circle = $Circle->getEntity(1);
        $this->assertEquals($newTime, $circle['latest_post_created']);

        $circle = $Circle->getEntity(2);
        $this->assertEquals($newTime, $circle['latest_post_created']);

        $Circle->updateLatestPostedInCircles([1, 2]);

        $circle = $Circle->getEntity(1);
        $this->assertTrue($newTime < $circle['latest_post_created']);

        $circle = $Circle->getEntity(2);
        $this->assertTrue($newTime < $circle['latest_post_created']);
    }


    function test_getSharedSecretCircleByPostId()
    {
        // Exist
        $res = $this->Circle->getSharedSecretCircleByPostId(7);
        $this->assertNotEmpty($res);
        $this->assertEquals($res['id'], 4);
        $this->assertEquals($res['name'], '秘密サークル');

        // Not exist post in secret circle
        $res = $this->Circle->getSharedSecretCircleByPostId(99);
        $this->assertEquals($res, []);

        // Post belongs to circle, but public circle
        $res = $this->Circle->getSharedSecretCircleByPostId(1);
        $this->assertEquals($res, []);

        // Post doesn't belong to circle, action post
        $res = $this->Circle->getSharedSecretCircleByPostId(8);
        $this->assertEquals($res, []);
    }

    public function test_getEntity_success(){

        $id = 1;

        /** @var Circle $Circle */
        $Circle = ClassRegistry::init('Circle');

        $result = $Circle->getEntity($id);

        $this->assertTrue($result instanceof BaseEntity);

        $arrayForm = $result->toArray();

        $this->assertInternalType('array',$arrayForm);

        $this->assertEquals($id, $arrayForm['id']);
        $this->assertNotEmpty($arrayForm['name']);
    }
}
