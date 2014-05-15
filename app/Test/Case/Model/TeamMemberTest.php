<?php
App::uses('TeamMember', 'Model');

/**
 * TeamMember Test Case
 *
 * @property mixed TeamMember
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

    //ダミーテスト
    function testDummy()
    {
    }

}
