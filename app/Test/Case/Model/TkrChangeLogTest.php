<?php
App::uses('TkrChangeLog', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * TkrChangeLog Test Case
 *
 * @property TkrChangeLog $TkrChangeLog
 */
class TkrChangeLogTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.tkr_change_log',
        'app.goal',
        'app.key_result',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TkrChangeLog = ClassRegistry::init('TkrChangeLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->TkrChangeLog);

        parent::tearDown();
    }

    function testDummy()
    {

    }

}
