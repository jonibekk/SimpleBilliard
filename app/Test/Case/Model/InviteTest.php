<?php
App::uses('Invite', 'Model');

/**
 * Invite Test Case
 *
 * @property mixed Invite
 */
class InviteTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.invite',
        'app.team'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Invite = ClassRegistry::init('Invite');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Invite);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
