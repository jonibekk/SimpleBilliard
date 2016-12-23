<?php
App::uses('KrChangeLog', 'Model');
App::uses('GoalousTestCase', 'Test');

/**
* KrChangeLog Test Case
*
* @property KrChangeLog $KrChangeLog
*/
class KrChangeLogTest extends GoalousTestCase
{

   /**
    * Fixtures
    *
    * @var array
    */
   public $fixtures = array(
       'app.kr_change_log',
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
       $this->KrChangeLog = ClassRegistry::init('KrChangeLog');
   }

   /**
    * tearDown method
    *
    * @return void
    */
   public function tearDown()
   {
       unset($this->KrChangeLog);

       parent::tearDown();
   }

   function testSaveAndGetSnapshot()
   {
       $this->_setDefault();
       //データの準備
       $kr = $this->KrChangeLog->KeyResult->findByGoalId(1);
       $saveKr = [
           'id' => $kr['KeyResult']['id'],
           'tkr_flg' => true
       ];
       $this->KrChangeLog->KeyResult->save($saveKr);
       $this->KrChangeLog->KeyResult->id = $kr['KeyResult']['id'];
       $this->KrChangeLog->KeyResult->saveField('name', 'test1');
       $this->KrChangeLog->saveSnapshot(1, 1, KrChangeLog::TYPE_MODIFY);
       $this->KrChangeLog->KeyResult->id = $kr['KeyResult']['id'];
       $this->KrChangeLog->KeyResult->saveField('name', 'test2');
       $this->KrChangeLog->saveSnapshot(1, 1, KrChangeLog::TYPE_MODIFY);
       $snapshot = $this->KrChangeLog->getLatestSnapshot(1, KrChangeLog::TYPE_MODIFY);
       $this->assertNotEmpty($snapshot);
       $this->assertEquals('test2', $snapshot['name']);
   }

   function _setDefault()
   {
       $this->KrChangeLog->current_team_id = 1;
       $this->KrChangeLog->Goal->current_team_id = 1;
       $this->KrChangeLog->KeyResult->current_team_id = 1;
   }

}
