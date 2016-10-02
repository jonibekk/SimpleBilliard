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

    public function testDummy()
    {

    }

}
