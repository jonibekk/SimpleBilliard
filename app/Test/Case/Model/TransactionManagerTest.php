<?php
App::uses('GoalousTestCase', 'Test');

/**
 * TransactionManager Test Case
 *
 * @property TransactionManager TransactionManager
 */
class TransactionManagerTest extends GoalousTestCase
{

    /**
     * @var array
     * Fixtures
     */
    public $fixtures = array(
        'app.team',
        'app.team_member',
        'app.user',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->TransactionManager = ClassRegistry::init('TransactionManager');
        $this->User = ClassRegistry::init('User');
    }

    public function test_trnNormal()
    {
        $res = $this->TransactionManager->begin();
        $this->assertTrue($res);
        $teamId = $this->createTeam();
        $userId = $this->createActiveUser($teamId);
        $res = $this->TransactionManager->rollback();
        $this->assertTrue($res);
        $res = $this->Team->getById($teamId);
        $this->assertEmpty($res);
        $res = $this->User->getById($userId);
        $this->assertEmpty($res);

        $res = $this->TransactionManager->begin();
        $this->assertTrue($res);
        $teamId = $this->createTeam();
        $userId = $this->createActiveUser($teamId);
        $res = $this->TransactionManager->commit();
        $this->assertTrue($res);
        $res = $this->Team->getById($teamId);
        $this->assertNotEmpty($res);
        $res = $this->User->getById($userId);
        $this->assertNotEmpty($res);
    }

    public function test_trnDuplicate()
    {
        $res = $this->TransactionManager->begin();
        $this->assertTrue($res);
        $teamId = $this->createTeam();
        $res = $this->TransactionManager->begin();
        $this->assertFalse($res);
        $res = $this->TransactionManager->rollback();
        $this->assertTrue($res);
        $userId = $this->createActiveUser($teamId);
        $res = $this->TransactionManager->commit();
        $this->assertFalse($res);
        $res = $this->TransactionManager->rollback();
        $this->assertFalse($res);
        $res = $this->Team->getById($teamId);
        $this->assertEmpty($res);
        $res = $this->User->getById($userId);
        $this->assertNotEmpty($res);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PaymentSetting);

        parent::tearDown();
    }
}
