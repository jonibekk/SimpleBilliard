<?php
App::uses('Topic', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * Topic Test Case
 *
 * @property Topic $Topic
 */
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

    function _setDefault() {
        $teamId = 1;
        $this->Topic->current_team_id = $teamId;
        $this->Topic->TopicMember->current_team_id = $teamId;
        $this->Topic->TopicMember->User->current_team_id = $teamId;
        $this->Topic->TopicMember->User->TeamMember->current_team_id = $teamId;
        $this->Topic->TopicMember->User->TeamMember->Team->current_team_id = $teamId;
    }
}
