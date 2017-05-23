<?php
App::uses('GoalousTestCase', 'Test');
App::uses('ConsoleOutput', 'Console');
App::uses('ShellDispatcher', 'Console');
App::uses('Shell', 'Console');
App::uses('Folder', 'Utility');
App::uses('TermMigration2Shell', 'Console/Command');
App::uses('Team', 'Model');
App::uses('Term', 'Model');
App::uses('Goal', 'Model');
App::uses('KeyResult', 'Model');

/**
 * Class TermMigration2ShellTest
 *
 * @property TermMigration2Shell $TermMigration2Shell
 * @property Term                Term
 * @property Goal                Goal
 * @property KeyResult           KeyResult
 */
class TermMigration2ShellTest extends GoalousTestCase
{
    public $TermMigration2Shell;

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
        'app.term',
        'app.goal',
        'app.key_result',
        'app.local_name',
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
        /** @var TermMigration2Shell $TermMigration2Shell */
        $this->TermMigration2Shell = new TermMigration2Shell($output, $error, $in);
        $this->TermMigration2Shell->initialize();
        $this->Term = ClassRegistry::init('Term');
        $this->Team = ClassRegistry::init('Team');
        $this->Goal = ClassRegistry::init('Goal');
        $this->KeyResult = ClassRegistry::init('KeyResult');
    }

    public function tearDown()
    {

    }

    public function testConstruct()
    {
        $this->assertEquals('TermMigration2', $this->TermMigration2Shell->name);
    }

    public function test_findTermsEachTeamId()
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

        $ret = $this->TermMigration2Shell->findTermsEachTeamId();
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
        $ret = $this->TermMigration2Shell->findTermsEachTeamId();
        $this->assertEmpty($ret);

        /* 期があるチームが取得できていること */
        // 期が一つのみ
        $addTerm = [
            'team_id'    => $teamId,
            'start_date' => strtotime('2017-01-01'),
            'end_date'   => strtotime('2017-01-31'),
            'timezone'   => 9,
        ];
        $this->Term->save($addTerm, false);
        $ret = $this->TermMigration2Shell->findTermsEachTeamId();
        $this->assertTrue(!empty($ret[$teamId]));
        $this->assertTrue(count($ret[$teamId]) == 1);

        // 期が複数
        $addTerm = [
            'team_id'    => $teamId,
            'start_date' => strtotime('2017-02-01'),
            'end_date'   => strtotime('2017-02-28'),
            'timezone'   => 9,
        ];
        $this->Term->create();
        $this->Term->save($addTerm, false);
        $ret = $this->TermMigration2Shell->findTermsEachTeamId();
        $this->assertTrue(!empty($ret[$teamId]));
        $this->assertTrue(count($ret[$teamId]) == 2);

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
        $this->Term->create();
        $this->Term->saveAll($addTerms, ['validate' => false]);
        $ret = $this->TermMigration2Shell->findTermsEachTeamId();
        $this->assertTrue(!empty($ret[$teamId2]));
        $this->assertTrue(count($ret[$teamId2]) == 3);

        $correctStartDates = [
            strtotime('2016-12-31'),
            strtotime('2017-01-01'),
            strtotime('2017-01-02')
        ];
        foreach ($ret[$teamId2] as $i => $term) {
            $this->assertEquals($term['start_date'], $correctStartDates[$i]);
        }

    }

    private function resetTeams()
    {
        // 全チーム初期化
        $this->Team->updateAll(['del_flg' => 1]);
        $this->Term->updateAll(['del_flg' => 1]);
    }

    public function test_findAllGoalsByTeam()
    {
        $teamId = 999;
        /* 既に削除されているゴールは結果に含まれないこと */
        $this->Goal->create();
        $this->Goal->save([
            'team_id'        => $teamId,
            'del_flg'        => 1,
            'start_date'     => '2017-01-01',
            'end_date'       => '2017-03-31',
            'old_start_date' => strtotime('2017-01-01'),
            'old_end_date'   => strtotime('2017-03-31'),
        ], false);
        $ret = $this->TermMigration2Shell->findAllGoalsByTeam($teamId);
        $this->assertEmpty($ret);

        /* ゴールが結果に含まれること */
        $this->Goal->create();
        $this->Goal->save([
            'team_id'        => $teamId,
            'start_date'     => '2017-01-01',
            'end_date'       => '2017-03-31',
            'old_start_date' => strtotime('2017-01-01'),
            'old_end_date'   => strtotime('2017-03-31'),
        ], false);
        $ret = $this->TermMigration2Shell->findAllGoalsByTeam($teamId);
        $this->assertNotEmpty($ret);

        $this->Goal->create();
        $this->Goal->save([
            'team_id'        => $teamId,
            'start_date'     => '2017-04-01',
            'end_date'       => '2017-06-30',
            'old_start_date' => strtotime('2017-04-01'),
            'old_end_date'   => strtotime('2017-06-30'),
        ], false);
        $ret = $this->TermMigration2Shell->findAllGoalsByTeam($teamId);
        $this->assertNotEmpty($ret);
        $this->assertEquals(count($ret), 2);
        $this->assertEquals($ret[0]['old_start_date'], strtotime('2017-01-01'));
        $this->assertEquals($ret[0]['old_end_date'], strtotime('2017-03-31'));
        $this->assertEquals($ret[1]['old_start_date'], strtotime('2017-04-01'));
        $this->assertEquals($ret[1]['old_end_date'], strtotime('2017-06-30'));

    }

    public function test_updateGoalAndKrs()
    {
        // タイムスタンプがずれているのを前提として期の範囲を開始と終了で1日ずつ拡張した上でゴールがどの期に属しているかを判定

    }

    public function test_updateGoal()
    {
        /* 開始日・終了日ともにどの期にも属してない ケース1 */
        $teamId = 999;
        $timezone = 9.0;
        $terms = [
            [
                'team_id'         => $teamId,
                'start_date'      => '2016-07-01',
                'end_date'        => '2016-09-30',
                'evaluate_status' => 1,
                'timezone'        => $timezone
            ],
            [
                'team_id'         => $teamId,
                'start_date'      => '2016-10-01',
                'end_date'        => '2016-12-31',
                'evaluate_status' => 2,
                'timezone'        => $timezone
            ],

        ];

        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2017-01-01'),
            'old_end_date'   => strtotime('2017-03-31 23:59:59'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertNotEmpty($err);

        /* 開始日・終了日ともにどの期にも属してない ケース2 */
        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2016-04-01'),
            'old_end_date'   => strtotime('2016-06-30 23:59:59'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertNotEmpty($err);

        /* 開始日がどの期にも属して無い ケース3 */
        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2016-04-01'),
            'old_end_date'   => strtotime('2016-07-01'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertNotEmpty($err);

        /* 終了日がどの期にも属してない 開始日が属する期の終了日に合わせる */
        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2016-12-01'),
            'old_end_date'   => strtotime('2017-01-02'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $ret = $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertEmpty($err);
        $updateGoal = $this->Goal->getById($goal['id']);
        $this->assertEquals($updateGoal['start_date'], '2016-12-01');
        $this->assertEquals($updateGoal['end_date'], '2016-12-31');
        $this->assertEquals($ret['start_date'], $updateGoal['start_date']);
        $this->assertEquals($ret['end_date'], $updateGoal['end_date']);

        /* 開始日と終了日が期の範囲内 */
        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2016-09-30 12:00:00'),
            'old_end_date'   => strtotime('2017-01-01 11:59:59'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $ret = $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertEmpty($err);
        $updateGoal = $this->Goal->getById($goal['id']);
        $this->assertEquals($updateGoal['start_date'], '2016-10-01');
        $this->assertEquals($updateGoal['end_date'], '2016-12-31');
        $this->assertEquals($ret['start_date'], $updateGoal['start_date']);
        $this->assertEquals($ret['end_date'], $updateGoal['end_date']);

        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2016-07-01 00:00:00'),
            'old_end_date'   => strtotime('2016-09-30 23:59:59'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $ret = $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertEmpty($err);
        $updateGoal = $this->Goal->getById($goal['id']);
        $this->assertEquals($updateGoal['start_date'], '2016-07-01');
        $this->assertEquals($updateGoal['end_date'], '2016-09-30');
        $this->assertEquals($ret['start_date'], $updateGoal['start_date']);
        $this->assertEquals($ret['end_date'], $updateGoal['end_date']);

        $this->Goal->create();
        $goal = [
            'team_id'        => $teamId,
            'start_date'     => '0000-00-00',
            'end_date'       => '0000-00-00',
            'old_start_date' => strtotime('2016-07-01 00:00:00'),
            'old_end_date'   => strtotime('2016-10-01 11:59:59'),
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $this->Goal->getLastInsertID();
        $err = null;
        try {
            $ret = $this->TermMigration2Shell->updateGoal($goal, $terms);
        } catch (Exception $e) {
            $err = $e->getMessage();
        }
        $this->assertEmpty($err);
        $updateGoal = $this->Goal->getById($goal['id']);
        $this->assertEquals($updateGoal['start_date'], '2016-07-01');
        $this->assertEquals($updateGoal['end_date'], '2016-09-30');
        $this->assertEquals($ret['start_date'], $updateGoal['start_date']);
        $this->assertEquals($ret['end_date'], $updateGoal['end_date']);
    }

    public function test_updateKrs()
    {
        /* KRの開始日・終了日ともにゴールの期間範囲外 */
        $uid = 1;
        $addTeam = [
            'Team' => [
                'name'     => "test",
                'type'     => 1,
                'timezone' => 9.0
            ]
        ];
        $team = $addTeam['Team'];
        $this->Team->add($addTeam, $uid);
        $teamId = $this->Team->getLastInsertID();
        $this->Goal->create();
        $goal = [
            'team_id'    => $teamId,
            'start_date' => '2016-01-01',
            'end_date'   => '2016-12-31',
        ];
        $this->Goal->save($goal, false);
        $goal['id'] = $goalId = $this->Goal->getLastInsertID();
        $goal['belongTerm'] = [
            ''
        ];

        $krs = [
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2015-12-31 11:59:59'),
                'old_end_date'   => strtotime('2017-01-01 00:00:00'),
            ]
        ];
        $this->KeyResult->saveAll($krs, ['validate' => false]);

        // Run test method
        $this->TermMigration2Shell->updateKrs($goal, $team);
        $krs = Hash::extract($this->KeyResult->findAllByGoalId($goalId), '{n}.KeyResult');
        $this->assertEquals(count($krs), 1);
        $this->assertEquals($krs[0]['start_date'], '2016-01-01');
        $this->assertEquals($krs[0]['end_date'], '2016-12-31');

        /* KRの開始日・終了日ともにゴールの期間範囲内 */
        $this->KeyResult->deleteAll(['goal_id' => $goalId]);
        $krs = [
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2015-12-31 23:00:00'),
                'old_end_date'   => strtotime('2016-12-31 14:59:59'),

            ]
        ];
        $this->KeyResult->saveAll($krs, ['validate' => false]);

        // Run test method
        $this->TermMigration2Shell->updateKrs($goal, $team);
        $krs = Hash::extract($this->KeyResult->findAllByGoalId($goalId), '{n}.KeyResult');
        $this->assertEquals(count($krs), 1);
        $this->assertEquals($krs[0]['start_date'], '2016-01-01');
        $this->assertEquals($krs[0]['end_date'], '2016-12-31');


        /* KRの開始日のみ期間範囲外 */
        $this->KeyResult->deleteAll(['goal_id' => $goalId]);
        $krs = [
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2015-12-31 16:00:00'),
                'old_end_date'   => strtotime('2016-01-01 14:59:59'),

            ]
        ];
        $this->KeyResult->saveAll($krs, ['validate' => false]);

        // Run test method
        $this->TermMigration2Shell->updateKrs($goal, $team);
        $krs = Hash::extract($this->KeyResult->findAllByGoalId($goalId), '{n}.KeyResult');
        $this->assertEquals(count($krs), 1);
        $this->assertEquals($krs[0]['start_date'], '2016-01-01');
        $this->assertEquals($krs[0]['end_date'], '2016-01-01');

        /* KRの終了日のみ期間範囲外 */
        $this->KeyResult->deleteAll(['goal_id' => $goalId]);
        $krs = [
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2016-02-29 15:00:00'),
                'old_end_date'   => strtotime('2017-01-01 14:59:59'),

            ]
        ];
        $this->KeyResult->saveAll($krs, ['validate' => false]);

        // Run test method
        $this->TermMigration2Shell->updateKrs($goal, $team);
        $krs = Hash::extract($this->KeyResult->findAllByGoalId($goalId), '{n}.KeyResult');
        $this->assertEquals(count($krs), 1);
        $this->assertEquals($krs[0]['start_date'], '2016-03-01');
        $this->assertEquals($krs[0]['end_date'], '2016-12-31');

        /* KR複数 */
        $this->KeyResult->deleteAll(['goal_id' => $goalId]);
        $krs = [
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2015-12-31 16:00:00'),
                'old_end_date'   => strtotime('2016-01-01 14:59:59'),

            ]
        ];
        $this->KeyResult->saveAll($krs, ['validate' => false]);

        // Run test method
        $this->KeyResult->deleteAll(['goal_id' => $goalId]);
        $krs = [
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2016-02-29 15:00:00'),
                'old_end_date'   => strtotime('2017-01-01 14:59:59'),

            ],
            [
                'goal_id'        => $goalId,
                'team_id'        => $teamId,
                'old_start_date' => strtotime('2016-05-01 15:00:00'),
                'old_end_date'   => strtotime('2016-12-31 14:59:59'),

            ]
        ];
        $this->KeyResult->saveAll($krs, ['validate' => false]);

        // Run test method
        $this->TermMigration2Shell->updateKrs($goal, $team);
        $krs = Hash::extract($this->KeyResult->findAllByGoalId($goalId), '{n}.KeyResult');
        $this->assertEquals(count($krs), 2);
        $this->assertEquals($krs[0]['start_date'], '2016-03-01');
        $this->assertEquals($krs[0]['end_date'], '2016-12-31');
        $this->assertEquals($krs[1]['start_date'], '2016-05-02');
        $this->assertEquals($krs[1]['end_date'], '2016-12-31');


    }
}
