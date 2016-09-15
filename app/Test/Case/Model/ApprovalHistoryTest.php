<?php App::uses('GoalousTestCase', 'Test');
App::uses('ApprovalHistory', 'Model');

/**
 * ApprovalHistory Test Case
 *
 * @property ApprovalHistory $ApprovalHistory
 */
class ApprovalHistoryTest extends GoalousTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.approval_history',
        'app.collaborator',
        'app.team',
        'app.user',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApprovalHistory = ClassRegistry::init('ApprovalHistory');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ApprovalHistory);

        parent::tearDown();
    }

    public function testAdd()
    {
        $cb_id = 999;
        $user_id = 888;
        $action_status = 0;
        $comment = 'test';
        $this->ApprovalHistory->add($cb_id, $user_id, $action_status, $comment);
        $res = $this->ApprovalHistory->find('first', ['conditions' => ['collaborator_id' => $cb_id]]);
        $this->assertEquals($res['ApprovalHistory']['comment'], 'test');
    }

    public function testAddEmpty()
    {
        $cb_id = 999;
        $user_id = 888;
        $action_status = 0;
        $comment = '';
        $this->ApprovalHistory->add($cb_id, $user_id, $action_status, $comment);
        $res = $this->ApprovalHistory->find('first', ['conditions' => ['collaborator_id' => $cb_id]]);
        $this->assertEmpty($res);
    }

}
