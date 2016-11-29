<?php App::uses('GoalousTestCase', 'Test');
App::uses('GroupVision', 'Model');

/**
 * GroupVision Test Case
 *
 * @property GroupVision $GroupVision
 */
class GroupVisionTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.group_vision',
        'app.team',
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
        $this->GroupVision = ClassRegistry::init('GroupVision');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupVision);

        parent::tearDown();
    }

    function testSaveGroupVisionNoData()
    {
        $this->_setDefault();
        $this->assertFalse($this->GroupVision->saveGroupVision([]));
    }

    function testSaveGroupVisionSuccess()
    {
        $this->_setDefault();
        $data = [
            'GroupVision' => [
                'name' => 'test'
            ]
        ];
        $this->assertNotEmpty($this->GroupVision->saveGroupVision($data));
    }

    function testGetGroupVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name
        ];
        $this->GroupVision->save($data);
        $res = $this->GroupVision->getGroupVision($team_id, 1);
        $this->assertEquals($res[0]['GroupVision']['name'], $name);
    }

    function testSetGroupVisionActiveFlag()
    {
        $team_id = 1;
        $name = 'test';
        $active_flg = 1;
        $data = [
            'team_id'    => $team_id,
            'name'       => $name,
            'active_flg' => $active_flg
        ];
        $this->GroupVision->save($data);
        $res = $this->GroupVision->setGroupVisionActiveFlag($this->GroupVision->getLastInsertID(), 0);
        $this->assertEquals($res['GroupVision']['active_flg'], 0);
    }

    function testDeleteGroupVision()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name,
        ];
        $this->GroupVision->save($data);
        $this->GroupVision->deleteGroupVision($this->GroupVision->getLastInsertID());

        $options = [
            'fields'     => ['del_flg'],
            'conditions' => [
                'id' => $this->GroupVision->getLastInsertID()
            ]
        ];
        $res = $this->GroupVision->find('first', $options);
        $this->assertCount(0, $res);
    }

    function _setDefault()
    {
        $this->GroupVision->current_team_id = 1;
        $this->GroupVision->my_uid = 1;
    }

    function testGetGroupVisionDetail()
    {
        $team_id = 1;
        $name = 'test';
        $data = [
            'team_id' => $team_id,
            'name'    => $name
        ];
        $this->GroupVision->save($data);

        $res = $this->GroupVision->getGroupVisionDetail($this->GroupVision->getLastInsertID(), 1);
        $this->assertEquals($res['GroupVision']['name'], $name);
    }

}
