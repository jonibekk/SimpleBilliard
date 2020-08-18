<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ApprovalHistory', 'Model');
App::import('Service/Api', 'ApiApprovalHistoryService');

/**
 * ApiApprovalHistoryServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 17:50
 *
 * @property ApiApprovalHistoryService $ApiApprovalHistoryService
 */
class ApiApprovalHistoryServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.approval_history'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->ApiApprovalHistoryService = ClassRegistry::init('ApiApprovalHistoryService');
    }

    function test_processApprovalHistories()
    {
        $this->assertEmpty($this->ApiApprovalHistoryService->processApprovalHistories([]));

        $approvalHistories = [
            [
                'select_clear_status'     => 0,
                'select_important_status' => 0,
            ],
            [
                'select_clear_status'     => 1,
                'select_important_status' => 1,
            ],
            [
                'select_clear_status'     => 2,
                'select_important_status' => 2,
            ],
            [
                'select_clear_status'     => 1,
                'select_important_status' => 2,
            ],
            [
                'select_clear_status'     => 2,
                'select_important_status' => 1,
            ],
        ];
        $actual = $this->ApiApprovalHistoryService->processApprovalHistories($approvalHistories);
        $expected = [
            (int)0 => [
                'select_clear_status'      => (int)0,
                'select_important_status'  => (int)0,
                'clear_and_important_word' => ''
            ],
            (int)1 => [
                'select_clear_status'      => (int)1,
                'select_important_status'  => (int)1,
                'clear_and_important_word' => 'This Top Key Result is clear and most important.'
            ],
            (int)2 => [
                'select_clear_status'      => (int)2,
                'select_important_status'  => (int)2,
                'clear_and_important_word' => 'This Top Key Result is not clear.'
            ],
            (int)3 => [
                'select_clear_status'      => (int)1,
                'select_important_status'  => (int)2,
                'clear_and_important_word' => 'This Top Key Result is not most important.'
            ],
            (int)4 => [
                'select_clear_status'      => (int)2,
                'select_important_status'  => (int)1,
                'clear_and_important_word' => 'This Top Key Result is not clear.'
            ]
        ];
        $this->assertEquals($expected, $actual);
    }

    function test_getClearImportantWord()
    {
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(0, 0);
        $this->assertEquals('', $ret);
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(1, 0);
        $this->assertEquals('', $ret);
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(0, 1);
        $this->assertEquals('', $ret);
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(1, 2);
        $this->assertEquals('This Top Key Result is not most important.', $ret);
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(1, 1);
        $this->assertEquals('This Top Key Result is clear and most important.', $ret);
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(2, 1);
        $this->assertEquals('This Top Key Result is not clear.', $ret);
        $ret = $this->ApiApprovalHistoryService->getClearImportantWord(2, 2);
        $this->assertEquals('This Top Key Result is not clear.', $ret);
    }

    function test_getLatestCoachActionStatement()
    {
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init('ApprovalHistory');
        $ApprovalHistory->current_team_id = 1;
        $ret = $this->ApiApprovalHistoryService->getLatestCoachActionStatement(1,1);
        $this->assertEquals('',$ret);

        $ApprovalHistory->save(
            [
                'goal_member_id'          => 1,
                'user_id'                 => 1,
                'action_status'           => $ApprovalHistory::STATUS_ACTION_IS_TARGET_FOR_EVALUATION,
                'select_clear_status'     => 0,
                'select_important_status' => 0,
            ]
        );
        $ret = $this->ApiApprovalHistoryService->getLatestCoachActionStatement(1,1);
        $this->assertEquals('You have added this Goal as a target of evaluation.',$ret);

        $ApprovalHistory->save(
            [
                'goal_member_id'          => 2,
                'user_id'                 => 2,
                'action_status'           => $ApprovalHistory::STATUS_ACTION_IS_NOT_TARGET_FOR_EVALUATION,
                'select_clear_status'     => 0,
                'select_important_status' => 0,
            ]
        );
        $ret = $this->ApiApprovalHistoryService->getLatestCoachActionStatement(2,2);
        $this->assertEquals('You have not added this Goal as a target of evaluation.',$ret);
    }
}