<?php
App::uses('Message', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * Message Test Case
 *
 * @property Message $Message
 */
class MessageTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.message',
        'app.topic',
        'app.user',
        'app.team',
        'app.message_file',
    ];

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
}
