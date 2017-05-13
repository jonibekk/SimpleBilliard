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
        $this->Term = ClassRegistry::init('Term');
        $this->TermService = ClassRegistry::init('TermService');
    }

    /**
     * validate update
     * - model validation (type checking)
     * - data correct checking
     * - next_start_ym should be...
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
            'next_start_ym' => date('Y-m', strtotime("+1 month")),
            'term_range'  => 6
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes);

        // from this month
        $requestData = [
            'next_start_ym' => date('Y-m', time()),
            'term_range'  => 6
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes !== true);

        // range is too long
        $requestData = [
            'next_start_ym' => date('Y-m', strtotime("+1 month")),
            'term_range'  => 13
        ];
        $validRes = $this->TermService->validateUpdate($requestData);
        $this->assertTrue($validRes !== true);

        // TODO: after end of current term start date
    }

    // TODO: SQLiteではupdateにjoin句が使えないため、このテストの実行不可。
    //       この部分だけMySQLへのDB設定切り替えできるようにしたらコメントアウトを外す。
    function test_updateKrsRangeWithinGoalRange()
    {
        // /** @var KeyResult $KeyResult */
        // $KeyResult = ClassRegistry::init('KeyResult');
        //
        // $teamId = 1;
        // $this->setDefaultTeamIdAndUid();
        // $goalStart = '2016-04-01';
        // $goalEnd = '2016-09-30';
        // $goalId = $this->createSimpleGoal([
        //     'team_id'    => $teamId,
        //     'start_date' => $goalStart,
        //     'end_date'   => $goalEnd
        // ]);
        //
        // // register test krs
        // $krBeforeGoalStartId = $this->createSimpleKr($krBeforeGoalStart = [
        //     'goal_id' => $goalId,
        //     'start_date' => '2016-02-01',
        //     'end_date' => '2016-03-31',
        // ]);
        // $krOverGoalStartBorderId = $this->createSimpleKr($krOverGoalStartBorder = [
        //     'goal_id' => $goalId,
        //     'start_date' => '2016-03-01',
        //     'end_date' => '2016-06-30',
        // ]);
        // $krWithinGoalRangeId = $this->createSimpleKr($krWithinGoalRange = [
        //     'goal_id' => $goalId,
        //     'start_date' => '2016-05-01',
        //     'end_date' => '2016-08-31',
        // ]);
        // $krOverGoalEndBorderId = $this->createSimpleKr($krOverGoalEndBorder = [
        //     'goal_id' => $goalId,
        //     'start_date' => '2016-08-01',
        //     'end_date' => '2016-10-01'
        // ]);
        // $krAfterGoalEndId = $this->createSimpleKr($krAfterGoalEnd = [
        //     'goal_id' => $goalId,
        //     'start_date' => '2016-10-01',
        //     'end_date' => '2016-12-30'
        // ]);
        //
        // // update krs range
        // $this->assertTrue($this->TermService->updateKrsRangeWithinGoalRange($goalStart));
        //
        // // KRのstart, endともにゴールのrangeがセットされていること
        // $res = $KeyResult->getById($krBeforeGoalStartId);
        // $this->assertEquals($res['start_date'], $goalStart);
        // $this->assertEquals($res['end_date'], $goalEnd);
        //
        // // KRのstartにゴールのstartがセットされていること
        // $res = $KeyResult->getById($krOverGoalStartBorderId);
        // $this->assertEquals($res['start_date'], $goalStart);
        // $this->assertEquals($res['end_date'], $krOverGoalStartBorder['end_date']);
        //
        // // KRのrangeがアップデートされていないこと
        // $res = $KeyResult->getById($krWithinGoalRangeId);
        // $this->assertEquals($res['start_date'], $krWithinGoalRange['start_date']);
        // $this->assertEquals($res['end_date'], $krWithinGoalRange['end_date']);
        //
        // // KRのendにゴールのendがセットされていること
        // $res = $KeyResult->getById($krOverGoalEndBorderId);
        // $this->assertEquals($res['start_date'], $krOverGoalEndBorder['start_date']);
        // $this->assertEquals($res['end_date'], $goalEnd);
        //
        // // KRのstart, endともにゴールのrangeがセットされていること
        // $res = $KeyResult->getById($krAfterGoalEndId);
        // $this->assertEquals($res['start_date'], $goalStart);
        // $this->assertEquals($res['end_date'], $goalEnd);
    }

    function test_getSelectableNextStartYmList()
    {
        $res = $this->TermService->getSelectableNextStartYmList(
            $currentTermStart = '2017-04', $current = '2018-01'
        );
        $this->assertEquals($res, [
            '2018-02' => 'Feb 2018', '2018-03' => 'Mar 2018', '2018-04' => 'Apr 2018'
        ]);
    }

}
