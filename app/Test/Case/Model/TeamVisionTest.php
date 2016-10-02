<?php App::uses('GoalousTestCase', 'Test');
App::uses('TeamVision', 'Model');

/**
 * TeamVision Test Case
 *
 * @property TeamVision $TeamVision
 */
class TeamVisionTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_vision',
        'app.group_vision',
        'app.team',
        'app.member_group',
        'app.group',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamVision = ClassRegistry::init('TeamVision');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TeamVision);

        parent::tearDown();
    }

    function testSaveTeamVisionNoData()
    {
        $this->_setDefault();
        $this->assertFalse($this->TeamVision->saveTeamVision([]));
    }

    function testSaveTeamVisionSuccess()
    {
        $this->_setDefault();
        $data = ['TeamVision' => ['name' => 'test']];
        $this->assertNotEmpty($this->TeamVision->saveTeamVision($data));
    }

    function _setDefault()
    {
        $team_id = 1;
        $user_id = 1;

        $this->TeamVision->current_team_id
            = $this->TeamVision->Team->current_team_id
            = $this->TeamVision->Team->Group->MemberGroup->current_team_id
            = $this->TeamVision->Team->GroupVision->current_team_id
            = $this->TeamVision->Team->Group->MemberGroup->current_team_id
            = $team_id;

        $this->TeamVision->my_uid
            = $this->TeamVision->Team->my_uid
            = $this->TeamVision->Team->Group->MemberGroup->my_uid
            = $this->TeamVision->Team->GroupVision->my_uid
            = $this->TeamVision->Team->Group->MemberGroup->my_uid
            = $user_id;
    }

    function testGetTeamVision()
    {
        $this->_setDefault();
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->getTeamVision($team_id, 1);
        $this->assertEquals($res[0]['TeamVision']['name'], $name);
        $res = $this->TeamVision->getTeamVision($team_id, 1);
        $this->assertEquals($res[0]['TeamVision']['name'], $name);
    }

    function testSetTeamVisionActiveFlag()
    {
        $team_id = 1;
        $name = 'test';
        $active_flg = 1;
        $data = [
            'team_id'    => $team_id,
            'name'       => $name,
            'active_flg' => $active_flg
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->setTeamVisionActiveFlag($this->TeamVision->getLastInsertID(), 0);
        $this->assertEquals($res['TeamVision']['active_flg'], 0);
    }

    function testDeleteTeamVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name,
        ];
        $this->TeamVision->save($data);
        $this->TeamVision->deleteTeamVision($this->TeamVision->getLastInsertID());

        $options = [
            'fields'     => ['del_flg'],
            'conditions' => [
                'id' => $this->TeamVision->getLastInsertID()
            ]
        ];
        $res = $this->TeamVision->find('first', $options);
        $this->assertCount(0, $res);
    }

    function testConvertData()
    {
        $team_id = 1;
        $name = 'test';
        $image_name = 'test.jpg';
        $data = [
            'team_id'         => $team_id,
            'name'            => $name,
            'photo_file_name' => $image_name
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->getTeamVision($team_id, 1);
        $convert_data = $this->TeamVision->convertData($res);
        $this->assertNotEquals($image_name, $convert_data[0]['TeamVision']['photo_path']);
    }

    function testConvertDetailData()
    {
        $team_id = 1;
        $name = 'test';
        $image_name = 'test.jpg';
        $data = [
            'team_id'         => $team_id,
            'name'            => $name,
            'photo_file_name' => $image_name
        ];
        $this->TeamVision->save($data);
        $res = $this->TeamVision->getTeamVisionDetail($this->TeamVision->getLastInsertID(), 1);
        $convert_data = $this->TeamVision->convertData($res);
        $this->assertNotEquals($image_name, $convert_data['TeamVision']['photo_path']);
    }

    function testGetTeamVisionDetail()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name
        ];
        $this->TeamVision->save($data);

        $res = $this->TeamVision->getTeamVisionDetail($this->TeamVision->getLastInsertID(), 1);
        $this->assertEquals($res['TeamVision']['name'], $name);
    }

    function testGetDisplayVisionRandomNoTeamId()
    {
        $res = $this->TeamVision->getDisplayVisionRandom();
        $this->assertNull($res);
    }

    function testGetDisplayVisionRandomNoData()
    {
        $this->_setDefault();
        $res = $this->TeamVision->getDisplayVisionRandom();
        $this->assertNull($res);
    }

    function testGetDisplayVisionRandomExistsData()
    {
        $this->_setDefault();
        $team_vision = [
            'name'             => 'team vision',
            'team_id'          => 1,
            'create_user_id'   => 1,
            'modified_user_id' => 1,
        ];
        $this->TeamVision->create();
        $this->TeamVision->save($team_vision);

        $group_vision = [
            'name'             => 'group vision',
            'team_id'          => 1,
            'create_user_id'   => 1,
            'modified_user_id' => 1,
            'group_id'         => 1,
        ];
        $this->TeamVision->Team->GroupVision->create();
        $this->TeamVision->Team->GroupVision->save($group_vision);

        $res = $this->TeamVision->getDisplayVisionRandom();
        $this->assertNotEmpty($res);
    }

}
