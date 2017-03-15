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
        $topicId = $this->_saveTopic([1, 2]);
        $actual = $this->TopicMember->countMember($topicId);
        $this->assertEquals(2, $actual);
    }

    function test_countReadMember()
    {
        $this->setDefaultTeamIdAndUid();
    }

    function test_findMembers()
    {
        $this->setDefaultTeamIdAndUid();
        $topicId = $this->_saveTopic([1, 2]);
        // normal case
        $actual = $this->TopicMember->findMembers($topicId);
        $this->assertcount(2, $actual);
        // limit case
        $actual = $this->TopicMember->findMembers($topicId, 1);
        $this->assertcount(1, $actual);
    }

    function test_isMember()
    {
        $this->setDefaultTeamIdAndUid();
        $topicId = $this->_saveTopic([1]);
        $actual = $this->TopicMember->isMember($topicId, 1);
        $this->assertTrue($actual);
        $actual = $this->TopicMember->isMember($topicId, 2);
        $this->assertFalse($actual);
    }

    function _saveTopic(array $memberUserIds): int
    {
        $this->TopicMember->Topic->create();
        $this->TopicMember->Topic->save([
            'team_id'         => 1,
            'creator_user_id' => 1
        ]);
        $topicId = $this->TopicMember->Topic->getLastInsertID();
        $this->TopicMember->create();
        $topicMemberData = [];
        foreach ($memberUserIds as $uid) {
            $topicMemberData[] = [
                'team_id'  => 1,
                'topic_id' => $topicId,
                'user_id'  => $uid
            ];
        }
        $this->TopicMember->saveAll($topicMemberData);
        return $topicId;
    }

}
