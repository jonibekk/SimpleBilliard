<?php
App::uses('Message', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * Message Test Case
 *
 * @property ApiTopicService $ApiTopicService
 * @property Message $Message
 * @property Topic $Topic
 * @property TopicMember $TopicMember
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
        'app.topic_member',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApiTopicService = ClassRegistry::init('ApiTopicService');
        $this->Message = ClassRegistry::init('Message');
        $this->Topic = ClassRegistry::init('Topic');
        $this->TopicMember = ClassRegistry::init('TopicMember');
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

    function test_findMessages()
    {
        //TODO: it should be written later.
        $this->markTestIncomplete('testClear not implemented.');
    }

    public function test_findNewerMessageId_success()
    {
        $data = $this->createMessageTestData();
        $r = $this->Message->findNewerMessageId($data["topic_id"], 100);
        $this->assertNull($r);
        $r = $this->Message->findNewerMessageId($data["topic_id"], 1);
        $this->assertEquals(2, $r);
    }


    private function createMessageTestData(): array
    {
        $userId = 1;
        $teamId = 1;
        $this->Message->my_uid = $userId;
        $topic = $this->Topic->save([
            'creator_user_id' => $userId,
            'team_id' => $teamId,
            'title' => "TestTopic",
        ]);
        $topicId = $topic['Topic']['id'];
        $this->TopicMember->save([
            'topic_id' => $topicId,
            'user_id' => $userId,
            'team_id' => $teamId,
        ]);

        $message = null;
        for ($i =1; $i <= 10; $i++) {
            $this->Message->create();
            $message = $this->Message->save([
                'topic_id' => $topicId,
                'sender_user_id' => $userId,
                'body' => "text" . $i,
                'type' => 1,
            ]);
        }
        $this->Topic->save([
            'id' => $topicId,
            'latest_message_id' => $message['Message']['id']
        ]);

        return [
            'user_id' => $userId,
            'topic_id' => $topicId,
        ];
    }
}
