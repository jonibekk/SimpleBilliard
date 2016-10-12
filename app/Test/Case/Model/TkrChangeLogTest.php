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

    function testSaveAndGetSnapshot()
    {
        $this->_setDefault();
        //データの準備
        $kr = $this->TkrChangeLog->KeyResult->findByGoalId(1);
        $kr['KeyResult']['tkr_flg'] = true;
        $this->TkrChangeLog->KeyResult->save($kr);

        $this->TkrChangeLog->saveSnapshot(1);
        $snapshot = $this->TkrChangeLog->findLatestSnapshot(1);
        $this->assertNotEmpty($snapshot);
        $this->assertNotEmpty($snapshot['data']);
    }

    function _setDefault()
    {
        $this->TkrChangeLog->current_team_id = 1;
        $this->TkrChangeLog->Goal->current_team_id = 1;
        $this->TkrChangeLog->KeyResult->current_team_id = 1;
    }

}
