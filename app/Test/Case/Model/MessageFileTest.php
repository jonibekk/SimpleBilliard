<?php
App::uses('MessageFile', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * MessageFile Test Case
 *
 * @property MessageFile $MessageFile
 */
class MessageFileTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.message_file',
        'app.message',
        'app.attached_file',
        'app.team',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MessageFile = ClassRegistry::init('MessageFile');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MessageFile);

        parent::tearDown();
    }

    public function testDummy()
    {

    }

}
