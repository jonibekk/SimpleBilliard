<?php
App::uses('GoalChangeLog', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
 * GoalChangeLog Test Case
 *
 * @property GoalChangeLog $GoalChangeLog
 */
class GoalChangeLogTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal_change_log',
        'app.goal',
        'app.goal_category',
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
        $this->GoalChangeLog = ClassRegistry::init('GoalChangeLog');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->GoalChangeLog);

        parent::tearDown();
    }

    function testSaveSnapshot()
    {
        $this->_setDefault();
        $this->GoalChangeLog->saveSnapshot(1,1);
        $snapshot = $this->GoalChangeLog->getLatestSnapshot(1,1);
        debug($snapshot);

    }

    function testGetLatestSnapshot()
    {
        $this->_setDefault();

    }

    function _setDefault()
    {
        $this->GoalChangeLog->current_team_id = 1;
        $this->GoalChangeLog->Goal->current_team_id = 1;
    }
}
