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

        $user_list = $this->AccessUser->getUserList(1, '2015-01-01', 9);
        $this->assertEquals([1 => '1', 2 => '2'], $user_list);

    }


    function testGetUniqueUserCount()
    {
        $this->AccessUser->current_team_id = 1;
        $this->AccessUser->my_uid = 1;

        $count = $this->AccessUser->getUniqueUserCount('2015-01-01', '2015-01-01', 9);
        $this->assertEquals(2, $count);

        $count = $this->AccessUser->getUniqueUserCount('2015-01-01', '2015-01-02', 9);
        $this->assertEquals(2, $count);

        $count = $this->AccessUser->getUniqueUserCount('2015-01-01', '2015-01-03', 9);
        $this->assertEquals(3, $count);

        $count = $this->AccessUser->getUniqueUserCount('2015-01-01', '2015-01-03', 9, ['user_id' => [1, 3]]);
        $this->assertEquals(2, $count);
    }


}
