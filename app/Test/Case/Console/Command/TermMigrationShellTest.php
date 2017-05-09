<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('TermMigrationShell', 'Console/Command');
App::uses('EvaluateTerm', 'Model');
App::uses('Team', 'Model');
App::uses('Term', 'Model');

/**
 * Class TermMigrationShellTest
 *
 * @property TermMigrationShell $TermMigrationShell
 * @property Term               Term
 */
class TermMigrationShellTest extends GoalousTestCase
{
    public $TermMigrationShell;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation',
        'app.team',
        'app.user',
        'app.circle',
        'app.circle_member',
        'app.team_member',
        'app.member_type',
        'app.local_name',
        'app.evaluate_term',
        'app.term',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $output = $this->getMock('ConsoleOutput', [], [], '', false);
        $error = $this->getMock('ConsoleOutput', [], [], '', false);
        $in = $this->getMock('ConsoleInput', [], [], '', false);
        /** @var TermMigrationShell $TermMigrationShell */
        $this->TermMigrationShell = new TermMigrationShell($output, $error, $in);
        $this->TermMigrationShell->initialize();
        $this->EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $this->Term = ClassRegistry::init('Term');
        $this->Team = ClassRegistry::init('Team');
    }

    public function tearDown()
    {

    }

    public function testConstruct()
    {
        $this->assertEquals('TermMigration', $this->TermMigrationShell->name);
    }

    public function test_transferTerms()
    {
        $this->resetTeams();
        $uid = 1;
        /* 最初の期の開始日がチーム作成日より後の場合、それまでの期が登録されていること */
        /* チーム作成時の期歯抜けケース1 */
        $this->TermMigrationShell->requestTimestamp = strtotime('2017-01-01');
        $teamId = 999;
        $timezone = 9.0;
        $teamWithTerms = [
            'id'               => $teamId,
            'border_months'    => 6,
            'start_term_month' => 1,
            'created'          => strtotime('2015-12-31 23:59:59'),
            'terms'            => [
                [
                    'id' => 1000,
                    'team_id'         => $teamId,
                    'start_date'      => $this->getLocalTimestampByDate('2017-01-01', $timezone),
                    'end_date'        => $this->getLocalTimestampByDate('2017-06-30', $timezone),
                    'evaluate_status' => 1,
                    'timezone'        => $timezone
                ],
                [
                    'id' => 1001,
                    'team_id'         => $teamId,
                    'start_date'      => $this->getLocalTimestampByDate('2017-07-01', $timezone),
                    'end_date'        => $this->getLocalTimestampByDate('2017-12-31', $timezone),
                    'evaluate_status' => 2,
                    'timezone'        => $timezone
                ],
            ]
        ];
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');

        $this->assertNotEmpty($retTerms);
        $this->assertEquals(count($retTerms), 5);

        // 既にある期の開始日時・終了日時がdate型で登録されていること
        $lastIndex = count($retTerms) - 1;
        $term = $retTerms[$lastIndex];
        $this->assertEquals($term['start_date'], '2017-07-01');
        $this->assertEquals($term['end_date'], '2017-12-31');

        $term = $retTerms[$lastIndex - 1];
        $this->assertEquals($term['start_date'], '2017-01-01');
        $this->assertEquals($term['end_date'], '2017-06-30');

        // 期が追加されているか
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2015-07-01');
        $this->assertEquals($term['end_date'], '2015-12-31');

        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2016-01-01');
        $this->assertEquals($term['end_date'], '2016-06-30');

        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2016-07-01');
        $this->assertEquals($term['end_date'], '2016-12-31');

        /* チーム作成時の期歯抜けケース2 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $teamWithTerms['created'] = strtotime('2017-01-01 00:00:00');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 2);

        /* チーム作成時の期歯抜けケース3 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $teamWithTerms['created'] = strtotime('2017-06-30 23:59:59');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 2);

        /* 今期・来期の期が存在しないケース1 */
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-09-30 23:59:59');
        $this->Term->deleteAll(['team_id' => $teamId]);
        $timezone = -3.5;
        $teamWithTerms = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 1,
            'created'          => strtotime('2016-07-01'),
            'terms'            => [
                [
                    'id' => 1000,
                    'team_id'         => $teamId,
                    'start_date'      => $this->getLocalTimestampByDate('2016-07-01', $timezone),
                    'end_date'        => $this->getLocalTimestampByDate('2016-09-30', $timezone),
                    'evaluate_status' => 1,
                    'timezone'        => $timezone
                ],
                [
                    'id' => 1001,
                    'team_id'         => $teamId,
                    'start_date'      => $this->getLocalTimestampByDate('2016-10-01', $timezone),
                    'end_date'        => $this->getLocalTimestampByDate('2016-12-31', $timezone),
                    'evaluate_status' => 2,
                    'timezone'        => $timezone
                ],
            ]
        ];
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 2);

        /* 今期・来期の期が存在しないケース2 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-10-01 00:00:00');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2017-01-01');

        /* 今期・来期の期が存在しないケース3 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-10-01 00:00:00');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2017-01-01');
        $this->assertEquals($term['end_date'], '2017-03-31');

        /* 今期・来期の期が存在しないケース4 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-12-31 00:00:00');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2017-01-01');
        $this->assertEquals($term['end_date'], '2017-03-31');

        /* 今期・来期の期が存在しないケース5 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-12-31 23:59:59');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2017-01-01');
        $this->assertEquals($term['end_date'], '2017-03-31');

        /* 今期・来期の期が存在しないケース6 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $this->TermMigrationShell->requestTimestamp = strtotime('2017-01-01 00:00:00');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 4);
        $term = $retTerms[3];
        $this->assertEquals($term['start_date'], '2017-04-01');
        $this->assertEquals($term['end_date'], '2017-06-30');

        /* 今期・来期の期が存在しないケース6 */
        $this->Term->deleteAll(['team_id' => $teamId]);
        $this->TermMigrationShell->requestTimestamp = strtotime('2017-03-31 23:59:59');
        $this->TermMigrationShell->transferTerm($teamWithTerms);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 4);
        $term = $retTerms[3];
        $this->assertEquals($term['start_date'], '2017-04-01');
        $this->assertEquals($term['end_date'], '2017-06-30');
    }

    private function getLocalTimestampByDate(string $date, float $timezone): int
    {
        return strtotime($date) - ($timezone * HOUR);
    }

    public function test_validateStartEndDate()
    {
        /* 開始日・終了日が空でないこと */
        $ret = $this->TermMigrationShell->validateStartEndDate('', '', 6);
        $errMsg = 'Start date or end date is empty.';
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('', '2017-01-31', 6);
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '', 6);
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-02', '2017-03-31', 6);
        $this->assertNotEquals($ret, $errMsg);

        /* 開始日が月初日であること */
        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-02', '2017-03-31', 6);
        $errMsg = 'Start date is not the first day of the month';
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2016-12-31', '2017-03-31', 6);
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2016-01-01', '2017-03-31', 6);
        $this->assertNotEquals($ret, $errMsg);

        /* 終了日が月末日であること */
        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '2017-03-30', 6);
        $errMsg = 'End date is not the last day of the month';
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '2017-04-01', 6);
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '2017-02-28', 6);
        $this->assertNotEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '2017-12-31', 6);
        $this->assertNotEquals($ret, $errMsg);

        /* 開始日〜終了日の期間がチーム設定の期の期間と同じであること */
        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '2017-03-31', 6);
        $errMsg = 'Invalid days of term.';
        $this->assertEquals($ret, $errMsg);

        $ret = $this->TermMigrationShell->validateStartEndDate('2017-01-01', '2017-03-31', 3);
        $this->assertEmpty($ret);

        $ret = $this->TermMigrationShell->validateStartEndDate('2016-12-01', '2017-05-31', 6);
        $this->assertEmpty($ret);

        $ret = $this->TermMigrationShell->validateStartEndDate('2016-11-01', '2017-10-31', 12);
        $this->assertEmpty($ret);
    }

    public function test_buildFirstTerm()
    {
        /* チーム作成日時が基準月に基づいた期間内の場合 */
        $teamId = 999;
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-04-30 23:59:59'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2016-02-01');
        $this->assertEquals($term['end_date'], '2016-04-30');

        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-02-01 00:00:00'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2016-02-01');
        $this->assertEquals($term['end_date'], '2016-04-30');

        $team = [
            'id'               => $teamId,
            'border_months'    => 12,
            'start_term_month' => 1,
            'created'          => strtotime('2016-12-31 23:59:59'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2016-01-01');
        $this->assertEquals($term['end_date'], '2016-12-31');

        /* チーム作成日時が開始日より前の場合 */
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-01-31 23:59:59'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2015-11-01');
        $this->assertEquals($term['end_date'], '2016-01-31');

        $team = [
            'id'               => $teamId,
            'border_months'    => 12,
            'start_term_month' => 12,
            'created'          => strtotime('2016-11-30 23:59:59'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2015-12-01');
        $this->assertEquals($term['end_date'], '2016-11-30');

        /* 終了日がチーム作成日時より後の場合 */
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 1,
            'created'          => strtotime('2016-02-01 00:00:00'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2016-01-01');
        $this->assertEquals($term['end_date'], '2016-03-31');

        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 1,
            'created'          => strtotime('2016-12-01 00:00:00'),
        ];
        $term = $this->TermMigrationShell->buildFirstTerm($team);
        $this->assertEquals($term['start_date'], '2016-10-01');
        $this->assertEquals($term['end_date'], '2016-12-31');

    }

    public function test_findTeamsNotExistTerms()
    {
        $this->resetTeams();
        $uid = '1';

        /* 削除されたチームが取得結果の中に含まれていないこと */
        $addTeam = [
            'Team' => [
                'name'    => "test",
                'type'    => 1,
                'del_flg' => 1
            ]
        ];
        $this->Team->add($addTeam, $uid);

        $ret = $this->TermMigrationShell->findTeamsNotExistTerms();
        $this->assertEmpty($ret);

        /* 期が全く無いチームが取得できること */
        $addTeam = [
            'Team' => [
                'name' => "test",
                'type' => 1,
            ]
        ];
        $this->Team->add($addTeam, $uid);
        $teamId = $this->Team->getLastInsertID();
        $ret = $this->TermMigrationShell->findTeamsNotExistTerms();
        $this->assertNotEmpty($ret);

        /* 期があるチームが取得結果に含まれないこと */
        // 期が一つのみ
        $addTerm = [
            'team_id'    => $teamId,
            'start_date' => strtotime('2017-01-01'),
            'end_date'   => strtotime('2017-01-31'),
            'timezone'   => 9,
        ];
        $this->EvaluateTerm->save($addTerm, false);
        $ret = $this->TermMigrationShell->findTeamsNotExistTerms();
        $this->assertEmpty($ret);

        // 期が複数
        $addTerm = [
            'team_id'    => $teamId,
            'start_date' => strtotime('2017-02-01'),
            'end_date'   => strtotime('2017-02-28'),
            'timezone'   => 9,
        ];
        $this->EvaluateTerm->create();
        $this->EvaluateTerm->save($addTerm, false);
        $ret = $this->TermMigrationShell->findTeamsNotExistTerms();
        $this->assertEmpty($ret);

        // チームが複数
        $this->EvaluateTerm->deleteAll(['team_id' => $teamId]);
        $addTeam = [
            'Team' => [
                'name' => "test2",
                'type' => 1,
            ]
        ];
        $this->Team->add($addTeam, $uid);
        $ret = $this->TermMigrationShell->findTeamsNotExistTerms();
        $this->assertEquals(count($ret), 2);
    }

    private function resetTeams()
    {
        // 全チーム初期化
        $this->Team->updateAll(['del_flg' => 1]);
        $this->EvaluateTerm->updateAll(['del_flg' => 1]);
    }

    public function test_findTeamsWithTerms()
    {
        $this->resetTeams();
        $uid = '1';

        /* 削除されたチームが取得結果の中に含まれていないこと */
        $addTeam = [
            'Team' => [
                'name'    => "test",
                'type'    => 1,
                'del_flg' => 1
            ]
        ];
        $this->Team->add($addTeam, $uid);

        $ret = $this->TermMigrationShell->findTeamsWithTerms();
        $this->assertEmpty($ret);

        /* 期が全く無いチームが取得結果の中に含まれていないこと */
        $addTeam = [
            'Team' => [
                'name' => "test",
                'type' => 1,
            ]
        ];
        $this->Team->add($addTeam, $uid);
        $teamId = $this->Team->getLastInsertID();
        $ret = $this->TermMigrationShell->findTeamsWithTerms();
        $this->assertEmpty($ret);

        /* 期があるチームが取得できていること */
        // 期が一つのみ
        $addTerm = [
            'team_id'    => $teamId,
            'start_date' => strtotime('2017-01-01'),
            'end_date'   => strtotime('2017-01-31'),
            'timezone'   => 9,
        ];
        $this->EvaluateTerm->save($addTerm, false);
        $ret = $this->TermMigrationShell->findTeamsWithTerms();
        $this->assertTrue(!empty($ret[$teamId]));
        $this->assertTrue(count($ret[$teamId]['terms']) == 1);

        // 期が複数
        $addTerm = [
            'team_id'    => $teamId,
            'start_date' => strtotime('2017-02-01'),
            'end_date'   => strtotime('2017-02-28'),
            'timezone'   => 9,
        ];
        $this->EvaluateTerm->create();
        $this->EvaluateTerm->save($addTerm, false);
        $ret = $this->TermMigrationShell->findTeamsWithTerms();
        $this->assertTrue(!empty($ret[$teamId]));
        $this->assertTrue(count($ret[$teamId]['terms']) == 2);

        // チームが複数
        $addTeam = [
            'Team' => [
                'name' => "test",
                'type' => 1,
            ]
        ];
        $this->Team->add($addTeam, $uid);
        $teamId2 = $this->Team->getLastInsertID();

        $addTerms = [
            [
                'team_id'    => $teamId2,
                'start_date' => strtotime('2017-01-01'),
            ],
            [
                'team_id'    => $teamId2,
                'start_date' => strtotime('2016-12-31'),
            ],
            [
                'team_id'    => $teamId2,
                'start_date' => strtotime('2017-01-02'),
            ],
        ];
        $this->EvaluateTerm->create();
        $this->EvaluateTerm->saveAll($addTerms, ['validate' => false]);
        $ret = $this->TermMigrationShell->findTeamsWithTerms();
        $this->assertTrue(!empty($ret[$teamId2]));
        $this->assertTrue(count($ret[$teamId2]['terms']) == 3);

        $correctStartDates = [
            strtotime('2016-12-31'),
            strtotime('2017-01-01'),
            strtotime('2017-01-02')
        ];
        foreach ($ret[$teamId2]['terms'] as $i => $term) {
            $this->assertEquals($term['start_date'], $correctStartDates[$i]);
        }
    }

    public function test_getDateByTimestamp()
    {
        $date = '2016-12-31 23:59:59';
        $timezone = 9.0;
        $timestamp = strtotime($date) - ($timezone * HOUR);
        $ret = $this->TermMigrationShell->getDateByTimestamp($timestamp, $timezone);
        $this->assertEquals(date('Y-m-d', strtotime($date)), $ret);

        $timezone = -0.5;
        $timestamp = strtotime($date) - ($timezone * HOUR);
        $ret = $this->TermMigrationShell->getDateByTimestamp($timestamp, $timezone);
        $this->assertEquals(date('Y-m-d', strtotime($date)), $ret);

        $date = '2017-01-01 00:00:00';
        $timezone = 0.5;
        $timestamp = strtotime($date) - ($timezone * HOUR);
        $ret = $this->TermMigrationShell->getDateByTimestamp($timestamp, $timezone);
        $this->assertEquals(date('Y-m-d', strtotime($date)), $ret);
    }

    public function test_remakeAllTerms()
    {
        /* チーム作成日時が基準月に基づいた期間内の場合 */
        // ケース1
        $teamId = 999;
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-04-30 00:00:00'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-04-30 23:59:59');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 2);
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2016-02-01');
        $this->assertEquals($term['end_date'], '2016-04-30');
        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2016-05-01');
        $this->assertEquals($term['end_date'], '2016-07-31');

        // ケース2
        $this->Term->deleteAll(['team_id' => $teamId]);
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-02-01 00:00:00'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-04-30 23:59:59');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 2);
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2016-02-01');
        $this->assertEquals($term['end_date'], '2016-04-30');
        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2016-05-01');
        $this->assertEquals($term['end_date'], '2016-07-31');

        // ケース3
        $this->Term->deleteAll(['team_id' => $teamId]);
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-01-31 23:59:59'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-04-30 23:59:59');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2015-11-01');
        $this->assertEquals($term['end_date'], '2016-01-31');
        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2016-02-01');
        $this->assertEquals($term['end_date'], '2016-04-30');
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2016-05-01');
        $this->assertEquals($term['end_date'], '2016-07-31');

        /* チーム作成日時が開始日より前の場合 */
        // ケース1
        $this->Term->deleteAll(['team_id' => $teamId]);
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 2,
            'created'          => strtotime('2016-01-31 23:59:59'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-02-01 00:00:00');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2015-11-01');
        $this->assertEquals($term['end_date'], '2016-01-31');
        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2016-02-01');
        $this->assertEquals($term['end_date'], '2016-04-30');
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2016-05-01');
        $this->assertEquals($term['end_date'], '2016-07-31');

        // ケース2
        $this->Term->deleteAll(['team_id' => $teamId]);
        $team = [
            'id'               => $teamId,
            'border_months'    => 12,
            'start_term_month' => 12,
            'created'          => strtotime('2015-11-30 23:59:59'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-11-30 00:00:00');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 3);
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2014-12-01');
        $this->assertEquals($term['end_date'], '2015-11-30');
        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2015-12-01');
        $this->assertEquals($term['end_date'], '2016-11-30');
        $term = $retTerms[2];
        $this->assertEquals($term['start_date'], '2016-12-01');
        $this->assertEquals($term['end_date'], '2017-11-30');

        /* 終了日がチーム作成日時より後の場合 */
        // ケース1
        $this->Term->deleteAll(['team_id' => $teamId]);
        $team = [
            'id'               => $teamId,
            'border_months'    => 3,
            'start_term_month' => 1,
            'created'          => strtotime('2016-02-01 00:00:00'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2016-02-01 00:00:00');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 2);
        $term = $retTerms[0];
        $this->assertEquals($term['start_date'], '2016-01-01');
        $this->assertEquals($term['end_date'], '2016-03-31');
        $term = $retTerms[1];
        $this->assertEquals($term['start_date'], '2016-04-01');
        $this->assertEquals($term['end_date'], '2016-06-30');

        // ケース2
        $this->Term->deleteAll(['team_id' => $teamId]);
        $team = [
            'id'               => $teamId,
            'border_months'    => 6,
            'start_term_month' => 1,
            'created'          => strtotime('2016-07-01 00:00:00'),
        ];
        $this->TermMigrationShell->requestTimestamp = strtotime('2017-12-31 23:59:59');
        $this->TermMigrationShell->remakeAllTerms($team);
        $retTerms = Hash::extract($this->Term->findAllByTeamId($teamId), '{n}.Term');
        $this->assertEquals(count($retTerms), 4);
    }
}
