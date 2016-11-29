<?php App::uses('GoalousTestCase', 'Test');
App::uses('MemberGroup', 'Model');

/**
 * MemberGroup Test Case
 *
 * @property MemberGroup $MemberGroup
 */
class MemberGroupTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.group_vision',
        'app.member_group',
        'app.team_member',
        'app.team',
        'app.user',
        'app.local_name',
        'app.group',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MemberGroup = ClassRegistry::init('MemberGroup');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MemberGroup);

        parent::tearDown();
    }

    function testDummy()
    {

    }

    function testGetGroupMemberUserId()
    {
        $user_id = 999;
        $team_id = 888;
        $group_id = 777;
        $params = [
            'user_id'  => $user_id,
            'team_id'  => $team_id,
            'group_id' => $group_id,
        ];
        $this->MemberGroup->save($params);
        $res = $this->MemberGroup->getGroupMemberUserId($team_id, $group_id);
        $this->assertContains($user_id, $res);
    }

    function testGetMyGroupList()
    {
        $this->_setDefault();
        $this->_saveGroup();
        $this->assertNotEmpty($this->MemberGroup->getMyGroupList());
    }

    function testGetMyGroupListNotExistsVisionNotEmpty()
    {
        $this->_setDefault();
        $this->_saveGroup();
        $this->assertNotEmpty($this->MemberGroup->getGroupListNotExistsVision());
    }

    function testGetMyGroupListNotExistsVisionEmpty()
    {
        $this->_setDefault();
        $this->MemberGroup->Group->deleteAll(['Group.team_id' => 1]);
        $this->MemberGroup->deleteAll(['MemberGroup.team_id' => 1]);
        $this->_saveGroup();
        $group_id = $this->MemberGroup->Group->getLastInsertID();
        $this->MemberGroup->Group->GroupVision->save(
            [
                'name'             => 'test',
                'group_id'         => $group_id,
                'team_id'          => 1,
                'create_user_id'   => 1,
                'modified_user_id' => 1,
            ]
        );
        $this->assertEmpty($this->MemberGroup->getGroupListNotExistsVision());
    }

    function testGetGroupMember()
    {
        $this->_setDefault();
        $members = $this->MemberGroup->getGroupMember(1);
        $this->assertNotEmpty($members);
        $this->assertNotEmpty($members[0]['User']);
    }

    function _saveGroup()
    {
        $this->MemberGroup->Group->save(['name' => 'test', 'team_id' => 1,]);
        $this->MemberGroup->save(
            [
                'team_id'  => 1,
                'user_id'  => 1,
                'group_id' => $this->MemberGroup->Group->getLastInsertID()
            ]
        );
    }

    function _setDefault()
    {
        $this->MemberGroup->Team->TeamMember->current_team_id = 1;
        $this->MemberGroup->Team->TeamMember->my_uid = 1;
        $this->MemberGroup->current_team_id = 1;
        $this->MemberGroup->my_uid = 1;
        $this->MemberGroup->Group->current_team_id = 1;
        $this->MemberGroup->Group->my_uid = 1;
    }

}
