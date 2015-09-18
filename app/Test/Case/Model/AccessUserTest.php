<?php
App::uses('AccessUser', 'Model');

/**
 * AccessUser Test Case
 *
 * @property AccessUser $AccessUser
 */
class AccessUserTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.access_user',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->AccessUser = ClassRegistry::init('AccessUser');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AccessUser);

        parent::tearDown();
    }


    function testGetUserList()
    {
        $this->AccessUser->current_team_id = 1;
        $this->AccessUser->my_uid = 1;

    }


    function testGetUniqueUserCount()
    {
        $this->AccessUser->current_team_id = 1;
        $this->AccessUser->my_uid = 1;

    }


}
