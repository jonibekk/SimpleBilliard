<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Message', 'Model');
App::import('Service/Api', 'ApiTopicService');

/**
 * Class ApiTopicServiceTest
 *
 * @property ApiMessageService $ApiMessageService
 * @property TeamMember        $TeamMember
 * @property Message           $Message
 */
class ApiMessageServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.message',
        'app.user',
        'app.local_name',
        'app.message_file',
        'app.attached_file',
        'app.team',
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
        $this->ApiMessageService = ClassRegistry::init('ApiMessageService');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->Message = ClassRegistry::init('Message');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApiMessageService);
        unset($this->TeamMember);
        parent::tearDown();
    }

    function test_convertKeyNames()
    {
        //TODO: it should be written later.
    }

    function test_findMessages()
    {
        //TODO: it should be written later.
//        $user1 = $this->createActiveUser(1);
//        $this->TeamMember->User->save(['id' => $user1, 'first_name' => 'One', 'last_name' => 'test'], false);
//        $user2 = $this->createActiveUser(1);
//        $this->TeamMember->User->save(['id' => $user2, 'first_name' => 'Two', 'last_name' => 'test'], false);
//        $user3 = $this->createActiveUser(1);
//        $this->TeamMember->User->save(['id' => $user3, 'first_name' => 'Three', 'last_name' => 'test'], false);
//
//        $this->ApiMessageService->findMessages(1);

        // TODO: asserting
        // message body
        // attached file url
        // user data
        // cursor is working
        // limit is working
        // reversing record is good
        // field names are fine

    }

    function test_setPaging()
    {
        //TODO: it should be written later.
    }
}
