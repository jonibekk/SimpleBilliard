<?php
App::uses('TopicMember', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * TopicMember Test Case
 *
 * @property TopicMember $TopicMember
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
        'app.topic',
        'app.user',
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

    public function testDummy()
    {

    }

}
