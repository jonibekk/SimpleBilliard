<?php
App::uses('Evaluation', 'Model');

/**
 * Evaluation Test Case
 *
 * @property Evaluation $Evaluation
 */
class EvaluationTest extends CakeTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.notify_setting',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.purpose',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.follower',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.oauth_token',
        'app.team_member',
        'app.job_category',
        'app.member_type',
        'app.local_name',
        'app.member_group',
        'app.group',
        'app.evaluator',
        'app.invite',
        'app.thread',
        'app.message',
        'app.evaluate_term',
        'app.evaluate_score',
        'app.evaluation_setting'
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Evaluation = ClassRegistry::init('Evaluation');
        $this->Evaluation->EvaluationScore = ClassRegistry::init('EvaluationScore');
        $this->Evaluation->EvaluationSetting = ClassRegistry::init('EvaluationSetting');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Evaluation);

        parent::tearDown();
    }

    function testAddDrafts()
    {
        $this->setDefault();

        $draftData = [
            [
                'Evaluation' => [
                    'id'                => 1,
                    'comment'           => 'あいうえお',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'comment'           => 'かきくけこ',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'comment'           => 'さしすせそ',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 4,
                    'comment'           => 'たちつてと',
                    'evaluate_score_id' => 1,
                ],
            ],
        ];
        $res = $this->Evaluation->add($draftData, "draft");
        $this->assertNotEmpty($res, "[正常]下書き保存");
        $res = $this->Evaluation->find('all',
                                       [
                                           'conditions' => [
                                               'evaluatee_user_id' => 1,
                                               'evaluate_term_id'  => 1,
                                               'status'            => 1
                                           ]
                                       ]
        );
        $this->assertEquals(count($res), count($draftData));
    }

    function testCheckAvailViewEvaluateListNoTeamMember()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }
    
    function testCheckAvailViewEvaluateListNotEnabled()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        $data = [
            'team_id'               => 1,
            'user_id'               => 1,
            'evaluation_enable_flg' => 0,
        ];
        $this->Evaluation->Team->TeamMember->save($data);
        $this->Evaluation->Team->TeamMember->current_team_id = 1;
        $this->Evaluation->Team->TeamMember->my_uid = 1;
        try {
            $this->Evaluation->checkAvailViewEvaluationList();
        } catch (RuntimeException $e) {
        }
        $this->assertTrue(isset($e));
    }

    function testAddRegisters()
    {
        $this->setDefault();

        $registerData = [
            [
                'Evaluation' => [
                    'id' => 1,
                    'comment' => 'あいうえお',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id' => 2,
                    'comment' => 'かきくけこ',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id' => 3,
                    'comment' => 'さしすせそ',
                    'evaluate_score_id' => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id' => 4,
                    'comment' => 'たちつてと',
                    'evaluate_score_id' => 1,
                ],
            ],
        ];
        $res = $this->Evaluation->add($registerData, "register");
        $this->assertNotEmpty($res, "[正常]評価登録");
        $res = $this->Evaluation->find(
            'all',
           [
               'conditions' => [
                   'evaluatee_user_id' => 1,
                   'evaluate_term_id'  => 1,
                   'status' => 2
               ]
           ]
        );
        $this->assertEquals(count($res), count($registerData));
    }

    function setDefault()
    {
        $this->current_date = strtotime('2015/7/1');
        $this->start_date = strtotime('2015/7/1');
        $this->end_date = strtotime('2015/10/1');

        $this->Evaluation->my_uid = 1;
        $this->Evaluation->current_team_id = 1;
        $this->Evaluation->EvaluationSetting->my_uid = 1;
        $this->Evaluation->EvaluationSetting->current_team_id = 1;
        $this->Evaluation->EvaluationScore->my_uid = 1;
        $this->Evaluation->EvaluationScore->current_team_id = 1;

    }


    function testCheckAvailViewEvaluateListTrue()
    {
        $this->Evaluation->Team->TeamMember->deleteAll(['TeamMember.team_id' => 1]);
        $data = [
            'team_id'               => 1,
            'user_id'               => 1,
            'evaluation_enable_flg' => 1,
        ];
        $this->Evaluation->Team->TeamMember->save($data);
        $this->Evaluation->Team->TeamMember->current_team_id = 1;
        $this->Evaluation->Team->TeamMember->my_uid = 1;
        $res = $this->Evaluation->checkAvailViewEvaluationList();
        $this->assertTrue($res);
    }

    function testGetEditableEvaluations()
    {
        $this->Evaluation->deleteAll(['Evaluation.team_id' => 1]);
        $data = [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 2,
            'status' => 0
        ];
        $this->Evaluation->save($data);
        $this->Evaluation->current_team_id = 1;
        $this->Evaluation->my_uid = 1;
        $actual = $this->Evaluation->getMyEvaluation();
        $expect = [
            (int)0 => [
                'Evaluation' => [
                    'id'                => '2',
                    'team_id'           => '1',
                    'evaluatee_user_id' => '1',
                    'evaluator_user_id' => '2',
                    'evaluate_term_id'  => null,
                    'evaluate_type'     => '0',
                    'goal_id'           => null,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                    'index'             => '0',
                    'status'            => '0',
                    'del_flg'           => false,
                    'deleted'           => null,
                    'created'           => '1426206160',
                    'modified'          => '1426206160'
                ]
            ]
        ];
        unset($actual[0]['Evaluation']['created']);
        unset($actual[0]['Evaluation']['modified']);
        $this->assertEquals(count($expect), count($actual));
    }
}
