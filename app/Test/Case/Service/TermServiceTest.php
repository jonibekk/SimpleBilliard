<?php
App::uses('GoalousTestCase', 'Test');
App::import('Service', 'TermService');

/**
 * @property TermService $TermService
 */
class TermServiceTest extends GoalousTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.term',
        'app.user',
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
        $this->Term = ClassRegistry::init('Term');
        $this->TermService = ClassRegistry::init('TermService');
    }

    /**
     * validate update
     * - model validation (type checking)
     * - data correct checking
     * - start_month should be...
     *  - after this month
     *  - before end month of this term
     *
     */
    function test_validateUpdate()
    {
        $this->setDefaultTeamIdAndUid();
        $thisTermStart = date("Y-m-01", time());
        $this->createTeam(['id' => 1, 'timezone' => 9]);
        $currentTerm = $this->saveTerm($teamId = 1, $thisTermStart, $range = 3);

        // valid case
        $requestData = [
            'start_month' => date('Y-m', strtotime("+1 month")),
            'term_range'  => 6
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes);

        // from this month
        $requestData = [
            'start_month' => date('Y-m', time()),
            'term_range'  => 6
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes !== true);

        // range is too long
        $requestData = [
            'start_month' => date('Y-m', strtotime("+1 month")),
            'term_range'  => 13
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes !== true);

        // TODO: after end of current term start date
    }

    // function test_validateForbiddenTeamSetting()
    // {
    //     // KRが存在するか
    //     $err = $this->KeyResultService->validateUpdate(1, 999, []);
    //     $this->assertEquals($err, ["status_code" => 400, "message" => __("Not exist")]);
    //
    //     // ゴールメンバーか
    //     $err = $this->KeyResultService->validateUpdate(999, 1, []);
    //     $this->assertEquals($err, ["status_code" => 403, "message" => __("You have no permission.")]);
    //
    //     // KRが既に完了していないか
    //     $err = $this->KeyResultService->validateUpdate(1, 6, []);
    //     $this->assertEquals($err, ["status_code" => 400, "message" => __("You can't edit achieved KR.")]);
    //
    //     /* フォームバリデーション */
    //     $err = $this->KeyResultService->validateUpdate(1, 1, [
    //         'id'            => '1',
    //         'name'          => 'test',
    //         'value_unit'    => 0,
    //         'start_value'   => 10,
    //         'target_value'  => 100,
    //         'current_value' => 11,
    //         'description'   => "This is test."
    //     ]);
    //     $this->assertEmpty($err);
    //
    //     $err = $this->KeyResultService->validateUpdate(1, 1, [
    //         'id'            => '1',
    //         'name'          => '',
    //         'value_unit'    => 0,
    //         'start_value'   => 10,
    //         'target_value'  => 100,
    //         'current_value' => 11,
    //         'description'   => "This is test."
    //     ]);
    //     $this->assertEquals(Hash::get($err, 'status_code'), 400);
    //     $this->assertNotEmpty(Hash::get($err, 'validation_errors'));
    // }

}
