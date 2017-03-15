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

    function test_getLatestMessageId()
    {
        $this->setDefaultTeamIdAndUid();
        $this->Message->save(['topic_id' => 1, 'team_id' => 1, 'sender_user_id' => 1]);
        $this->Message->create();
        $this->Message->save(['topic_id' => 1, 'team_id' => 1, 'sender_user_id' => 1]);
        $expectedId = $this->Message->getLastInsertID();
        $latestMessageId = $this->Message->getLatestMessageId(1);
        $this->assertEquals($expectedId, $latestMessageId);
    }

}
