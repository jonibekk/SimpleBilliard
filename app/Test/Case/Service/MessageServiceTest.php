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
 * @property MessageService $MessageService
 */
class MessageServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.message',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->MessageService = ClassRegistry::init('MessageService');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MessageService);
        parent::tearDown();
    }

    function test_extendAttachedFileUrl()
    {
        //TODO: it should be written later.
    }

    function test_extendBody()
    {
        //TODO: it should be written later.
    }

    function test_filterFields()
    {
        //TODO: it should be written later.
    }

    function test_findMessages()
    {
        //TODO: it should be written later.
    }

}
