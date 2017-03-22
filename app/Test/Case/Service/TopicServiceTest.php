<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TopicService');

/**
 * TopicServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2017/3/15
 * Time: 17:50
 *
 * @property TopicService $TopicService
 * @property TeamMember   $TeamMember
 * @property Topic        $Topic
 */
class TopicServiceTest extends GoalousTestCase
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
        'app.team_member',
        'app.team',
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
        $this->TopicService = ClassRegistry::init('TopicService');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->Topic = ClassRegistry::init('Topic');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TopicService);
        unset($this->TeamMember);
        unset($this->Topic);
        parent::tearDown();
    }

    function test_findTopicDetail()
    {
        $this->setDefaultTeamIdAndUid();
        $user1 = $this->createActiveUser(1);
        $this->TeamMember->User->save(['id' => $user1, 'first_name' => 'One', 'last_name' => 'test'], false);
        $user2 = $this->createActiveUser(1);
        $this->TeamMember->User->save(['id' => $user2, 'first_name' => 'Two', 'last_name' => 'test'], false);
        $user3 = $this->createActiveUser(1);
        $this->TeamMember->User->save(['id' => $user3, 'first_name' => 'Three', 'last_name' => 'test'], false);

        $topicId = $this->saveTopic([$user1, $user2]);
        // no title case
        $actual = $this->TopicService->findTopicDetail($topicId);
        $this->assertNull($actual['title']);
        $this->assertEquals('One, Two', $actual['display_title']);
        $this->assertFalse($actual['can_leave_topic']);

        // title exists case
        $this->Topic->id = $topicId;
        $this->Topic->saveField('title', 'test');
        $actual = $this->TopicService->findTopicDetail($topicId);
        $this->assertEquals('test', $actual['title']);
        $this->assertEquals($actual['title'], $actual['display_title']);

        // if 3 members exist, can_leave_topic is true
        $topicId = $this->saveTopic([$user1, $user2, $user3]);
        $actual = $this->TopicService->findTopicDetail($topicId);
        $this->assertTrue($actual['can_leave_topic']);
    }

    function test_getMemberNamesAsString()
    {
        $this->setDefaultTeamIdAndUid();
        $topicId = $this->saveTopic([1, 2]);
        $actual = $this->TopicService->getMemberNamesAsString($topicId, 2);
        $this->assertEquals('firstname, firstname', $actual);
        $actual = $this->TopicService->getMemberNamesAsString($topicId, 1);
        $this->assertEquals('firstname', $actual);
    }

}
