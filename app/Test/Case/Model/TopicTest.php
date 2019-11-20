<?php
App::uses('Topic', 'Model');
App::uses('TopicMember', 'Model');
App::uses('GoalousTestCase', 'Test');
App::import('Model/Entity', 'UserEntity');

/**
 * Topic Test Case
 *
 * @property Topic $Topic
 * @property TopicMember $TopicMember
 * @property Message Message
 */

use Goalous\Enum as Enum;

class TopicTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.topic',
        'app.topic_member',
        'app.message',
        'app.user',
        'app.team_member',
        'app.team',
        'app.local_name'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Topic = ClassRegistry::init('Topic');
        $this->TopicMember = ClassRegistry::init('TopicMember');
        $this->Message = ClassRegistry::init('Message');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Topic);

        parent::tearDown();
    }

    function test_findLatest()
    {
        $this->_setDefault();
        $teamId = 1;
        $mainUserId = $this->createActiveUser($teamId);
        $subUserId = $this->createActiveUser($teamId);
        $latestMessageDate = 222222;
        $this->createTopicAndMessages($teamId, $mainUserId, $subUserId, $latestMessageDate);
        $this->assertNotEmpty($this->Topic->findLatest($mainUserId, 0, 10));
    }

    // TODO: must write after connecting front-end
    function test_findLatest_empty()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    // TODO: must write after connecting front-end
    function test_findLatest_searchKeyWordInTitle()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    // TODO: must write after connecting front-end
    function test_findLatest_searchKeyWordMemberName()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    // TODO: must write after connecting front-end
    function test_findLatest_searchEmpty()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    // TODO: must write after connecting front-end
    function test_findLatest_topicSortByMessagedDatetime()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    // TODO: must write after connecting front-end
    function test_findLatest_userSortByMessagedDatetime()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    // TODO: must write after connecting front-end
    function test_findLatest_membersOver10()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    public function test_get()
    {
        $this->setDefaultTeamIdAndUid();
        $this->Topic->save(['creator_user_id' => 1, 'team_id' => 1, 'title' => 'test']);
        $topicId = $this->Topic->getLastInsertID();
        $actual = $this->Topic->get($topicId);
        $expected = [
            'id'                => $topicId,
            'title'             => 'test',
            'latest_message_id' => null,
            'team_id'           => 1
        ];

        $this->assertEquals($expected, $actual);
    }

    function _setDefault()
    {
        $teamId = 1;
        $this->Topic->current_team_id = $teamId;
        $this->Topic->TopicMember->current_team_id = $teamId;
        $this->Topic->TopicMember->User->current_team_id = $teamId;
        $this->Topic->TopicMember->User->TeamMember->current_team_id = $teamId;
        $this->Topic->TopicMember->User->TeamMember->Team->current_team_id = $teamId;
    }

    public function test_getLatestMembers_success()
    {
        $topicId = 1;

        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();
        $this->insertNewMessage($topicId, 10, 1, $currentTimeStamp++);
        $this->insertNewMessage($topicId, 13, 1, $currentTimeStamp++);
        $this->insertNewMessage($topicId, 12, 1, $currentTimeStamp++);
        $this->insertNewMessage($topicId, 11, 1, $currentTimeStamp);

        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        $result = $Topic->getLatestSenders(1, 10);

        $this->assertCount(3, $result);

        foreach ($result as $r) {
            $this->assertTrue($r instanceof UserEntity);
        }

        $this->assertEquals(11, $result[0]['id']);
        $this->assertEquals(12, $result[1]['id']);
        $this->assertEquals(13, $result[2]['id']);

        $result = $Topic->getLatestSenders(1, 1);

        $this->assertCount(4, $result);
        $this->assertEquals(11, $result[0]['id']);
        $this->assertEquals(12, $result[1]['id']);
        $this->assertEquals(13, $result[2]['id']);
        $this->assertEquals(10, $result[3]['id']);
    }

    public function test_getLatestMembersUnique_success()
    {
        $topicId = 1;

        $currentTimeStamp = GoalousDateTime::now()->getTimestamp();
        $this->insertNewMessage($topicId, 10, 1, $currentTimeStamp++);
        $this->insertNewMessage($topicId, 13, 1, $currentTimeStamp++);
        $this->insertNewMessage($topicId, 10, 1, $currentTimeStamp);

        $tm = $this->TopicMember->find('all');
        $ms = $this->Message->find('all');

        /** @var Topic $Topic */
        $Topic = ClassRegistry::init('Topic');

        $result = $Topic->getLatestSenders(1, 1);

        $this->assertCount(2, $result);

        foreach ($result as $r) {
            $this->assertTrue($r instanceof UserEntity);
        }

        $this->assertEquals(10, $result[0]['id']);
        $this->assertEquals(13, $result[1]['id']);

        $result = $Topic->getLatestSenders(1, 1, 1);

        $this->assertCount(1, $result);
    }

    private function insertNewMessage(int $topicId, int $senderId, int $teamId, int $currentTimeStamp)
    {
        $newData = [
            'sender_user_id' => $senderId,
            'team_id'        => $teamId,
            'topic_id'       => $topicId,
            'body'           => "Message from user $senderId in topic $topicId",
            'type'           => Enum\Model\Message\MessageType::NORMAL,
        ];

        $this->Message->create();
        $this->Message->save($newData, false);

        $this->TopicMember->create();
        $topicMember = $this->TopicMember->find('first', [
            'conditions' => ['user_id' => $senderId, 'topic_id' => $topicId]
        ]);
        $saveData = [
            'user_id'           => $senderId,
            'topic_id'          => $topicId,
            'team_id'           => $teamId,
            'last_message_sent' => $currentTimeStamp,
        ];

        if (!empty($topicMember)) {
            $this->TopicMember->id = $topicMember['TopicMember']['id'];
        }
        $this->TopicMember->save($saveData, false);
    }
}
