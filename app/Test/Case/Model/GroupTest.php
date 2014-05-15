<?php
App::uses('Group', 'Model');

/**
 * Group Test Case
 *
 * @property mixed Group
 */
class GroupTest extends CakeTestCase
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

    //ダミーテスト
    function testDummy()
    {
    }

}
