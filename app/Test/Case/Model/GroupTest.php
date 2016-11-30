<?php App::uses('GoalousTestCase', 'Test');
App::uses('Group', 'Model');

/**
 * Group Test Case
 *
 * @property Group $Group
 */
class GroupTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.group',
        'app.team',
        'app.team_member'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Group = ClassRegistry::init('Group');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Group);

        parent::tearDown();
    }

    function testGetByName()
    {
        $this->Group->current_team_id = 1;
        $this->assertEmpty($this->Group->getByName('test', null));
    }

    function testGetByAllName()
    {
        $team_id = 999;
        $params = [
            'team_id' => $team_id,
            'name'    => 'SDG'
        ];
        $this->Group->save($params);
        $res = $this->Group->findAllList($team_id);
        $this->assertContains('SDG', $res);
    }

    function testSaveNewGroup()
    {
        $this->_setDefault();
        $actual = $this->Group->saveNewGroup('test');
        $this->assertNotEmpty($actual);
    }

    function testGetByNameIfNotExistsSave()
    {
        $this->_setDefault();
        $this->Group->getByNameIfNotExistsSave('test');
        $this->assertNotEquals(false, $this->Group->id);

        $this->Group->create();
        $actual = $this->Group->getByNameIfNotExistsSave('test');
        $this->assertNotEmpty($actual);
        $this->assertFalse($this->Group->id);
    }

    function testGetAll()
    {
        $this->_setDefault();

        $groups = $this->Group->getAll();
        $this->assertNotEmpty($groups);
        foreach ($groups as $v) {
            $this->assertEquals($this->Group->current_team_id, $v['Group']['team_id']);
        }
    }

    function testGetGroupsByKeyword()
    {
        $this->_setDefault();

        $groups = $this->Group->getGroupsByKeyword('グループ');
        $this->assertNotEmpty($groups);
        foreach ($groups as $v) {
            $this->assertEquals(0, strpos($v['Group']['name'], 'グループ'));
        }

        $groups = $this->Group->getGroupsByKeyword('');
        $this->assertEmpty($groups);

        $groups = $this->Group->getGroupsByKeyword('テスト');
        $this->assertNotEmpty($groups);
        foreach ($groups as $v) {
            $this->assertEquals(0, strpos($v['Group']['name'], 'テスト'));
        }
    }

    function _setDefault()
    {
        $this->Group->current_team_id = 1;
        $this->Group->my_uid = 1;
    }
}
