<?php
App::uses('TopicSearchKeyword', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * TopicSearchKeyword Test Case
 * @property TopicSearchKeyword $TopicSearchKeyword
 */
class TopicSearchKeywordTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.topic_search_keyword',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TopicSearchKeyword = ClassRegistry::init('TopicSearchKeyword');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TopicSearchKeyword);

        parent::tearDown();
    }

}
