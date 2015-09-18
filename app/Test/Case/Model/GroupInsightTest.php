<?php
App::uses('GroupInsight', 'Model');

/**
 * GroupInsight Test Case
 *
 * @property GroupInsight $GroupInsight
 */
class GroupInsightTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.group_insight',
        'app.team',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->GroupInsight = ClassRegistry::init('GroupInsight');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GroupInsight);

        parent::tearDown();
    }

    function testGetTotal()
    {

    }
}
