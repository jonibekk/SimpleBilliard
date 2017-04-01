<?php
App::uses('TopicMember', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * TopicMember Test Case
 *
 * @property TopicMember $TopicMember
 * @property TeamMember  $TeamMember
 */
class TopicMemberTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.topic_member',
        'app.team',
        'app.team_member',
        'app.topic',
        'app.user',
        'app.local_name',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TopicMember = ClassRegistry::init('TopicMember');
        $this->TeamMember = ClassRegistry::init('TeamMember');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TopicMember);

        parent::tearDown();
    }

    function test_countMember()
    {
        $this->setDefaultTeamIdAndUid();
        $topicId = $this->saveTopic([1, 2]);
        $actual = $this->TopicMember->countMember($topicId);
        $this->assertEquals(2, $actual);
    }

    function test_countReadMember()
    {
        $this->setDefaultTeamIdAndUid();
        $this->saveTopic([1, 2]);

        // nobody has last_read_message_id, so `readCount` is expected 0
        $actual = $this->TopicMember->countReadMember(1);
        $this->assertEquals(0, $actual);

        $this->TopicMember->updateAll(
            ['TopicMember.last_read_message_id' => 1],
            ['user_id' => 1]
        );
        // 1 member has last_read_message_id, so `readCount` is expected 1
        $actual = $this->TopicMember->countReadMember(1);
        $this->assertEquals(1, $actual);

        $this->TopicMember->updateAll(
            ['TopicMember.last_read_message_id' => 1],
            ['user_id' => 2]
        );
        // 2 members have last_read_message_id, so `readCount` is expected 2
        $actual = $this->TopicMember->countReadMember(1);
        $this->assertEquals(2, $actual);
    }

    function test_findSortedBySentMessage()
    {
        $this->setDefaultTeamIdAndUid();
        $topicId = $this->saveTopic([1, 2]);
        // normal case
        $actual = $this->TopicMember->findSortedBySentMessage($topicId);
        $this->assertcount(2, $actual);
        // limit case
        $actual = $this->TopicMember->findSortedBySentMessage($topicId, 1);
        $this->assertcount(1, $actual);
    }

    function test_isMember()
    {
        $this->setDefaultTeamIdAndUid();
        $topicId = $this->saveTopic([1]);
        $actual = $this->TopicMember->isMember($topicId, 1);
        $this->assertTrue($actual);
        $actual = $this->TopicMember->isMember($topicId, 2);
        $this->assertFalse($actual);
    }

    function test_updateLastMessageSentDate()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_leave()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }

    function test_bulkAdd()
    {
        $this->markTestIncomplete('testClear not implemented.');
    }
}
