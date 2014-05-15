<?php
App::uses('Message', 'Model');

/**
 * Message Test Case
 *
 * @property mixed Message
 */
class MessageTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.message',
        'app.thread'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Message = ClassRegistry::init('Message');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Message);

        parent::tearDown();
    }

    //ダミーテスト
    function testDummy()
    {
    }

}
