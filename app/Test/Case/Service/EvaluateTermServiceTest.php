<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Term', 'Model');
App::import('Service', 'EvaluateTermService');

/**
 * EvaluateTermServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 9:10
 *
 * @property EvaluateTermService $EvaluateTermService
 */
class EvaluateTermServiceTest extends GoalousTestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluateTermService = ClassRegistry::init('EvaluateTermService');
    }

    function testProcessEvaluateTerm()
    {
        $data = [
            'start_date' => strtotime('2016-11-30 15:00:00'),//UTC
            'end_date'   => strtotime('2016-12-31 14:59:59'),//UTC
            'timezone'   => '+9'
        ];
        $actual = $this->EvaluateTermService->processEvaluateTerm($data, Term::TERM_TYPE_CURRENT);
        $expected = [
            'start_date' => '2016-12-01',
            'end_date'   => '2016-12-31',
            'timezone'   => '+9',
            'type'       => 'current'
        ];
        $this->assertEquals($expected, $actual);
    }

    function testRegenerateDateByTimezone()
    {
        $actual = $this->EvaluateTermService->regenerateDateByTimezone(strtotime('2016-11-30 15:00:00'), '+9');
        $this->assertEquals($actual, '2016-12-01');

        $actual = $this->EvaluateTermService->regenerateDateByTimezone(strtotime('2016-11-30 15:00:00'), '+8');
        $this->assertEquals($actual, '2016-11-30');
    }

}
