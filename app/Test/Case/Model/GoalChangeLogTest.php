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

    function testSaveAndGetSnapshot()
    {
        $this->_setDefault();
        $this->GoalChangeLog->Goal->id = 1;
        $this->GoalChangeLog->Goal->saveField('name','test1');
        $this->GoalChangeLog->saveSnapshot(1);
        $this->GoalChangeLog->Goal->id = 1;
        $this->GoalChangeLog->Goal->saveField('name','test2');
        $this->GoalChangeLog->saveSnapshot(1);
        $snapshot = $this->GoalChangeLog->findLatestSnapshot(1);
        $this->assertNotEmpty($snapshot);
        $this->assertNotEmpty($snapshot);
        $this->assertEquals('test2',$snapshot['name']);
    }

    function _setDefault()
    {
        $this->GoalChangeLog->current_team_id = 1;
        $this->GoalChangeLog->Goal->current_team_id = 1;
    }
}
