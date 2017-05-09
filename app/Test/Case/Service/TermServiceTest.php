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
     * - start_ym should be...
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
            'start_ym' => date('Y-m', strtotime("+1 month")),
            'term_range'  => 6
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes);

        // from this month
        $requestData = [
            'start_ym' => date('Y-m', time()),
            'term_range'  => 6
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes !== true);

        // range is too long
        $requestData = [
            'start_ym' => date('Y-m', strtotime("+1 month")),
            'term_range'  => 13
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes !== true);

        // TODO: after end of current term start date
    }

}
