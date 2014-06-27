<?php
App::uses('TeamMember', 'Model');

/**
 * TeamMember Test Case
 *
 * @property TeamMember $TeamMember
 */
class TeamMemberTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.team_member',
        'app.user',
        'app.team',
        'app.group',
        'app.job_category'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TeamMember);

        parent::tearDown();
    }

    function testGetActiveTeamList()
    {
        $uid = '537ce224-8c0c-4c99-be76-433dac11b50b';
        $data = [
            'TeamMember' => [['user_id' => $uid,]],
            'Team'       => [
                'name' => 'test'
            ]
        ];
        $this->TeamMember->Team->saveAll($data);
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), 1);

        $this->TeamMember->Team->saveAll($data);
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), 2);

        $this->TeamMember->Team->delete();
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), 1);

        $this->TeamMember->Team->saveAll($data);
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), 2);

        $this->TeamMember->saveField('active_flg', false);
        $res = $this->TeamMember->getActiveTeamList($uid);
        $this->assertEquals(count($res), 1);

    }

}
