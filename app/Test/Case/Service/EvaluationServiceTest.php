<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Email', 'Model');
App::uses('Term', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');
App::uses('Evaluator', 'Model');
App::uses('Evaluation', 'Model');
App::uses('Experiment', 'Model');
App::uses('EvaluationSetting', 'Model');
App::import('Service', 'EvaluationService');
App::uses('TestTermTrait', 'Test/Trait');
App::uses('TestEvaluationTrait', 'Test/Trait');
App::uses('TestGoalTrait', 'Test/Trait');
App::import('Service', 'ExperimentService');

use Goalous\Enum as Enum;

/**
 * EvaluationServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 9:42
 *
 * @property EvaluationService $EvaluationService
 * @property Evaluation        $Evaluation
 * @property Experiment        $Experiment
 * @property EvaluationSetting $EvaluationSetting
 * @property TeamMember        $TeamMember
 * @property User              $User
 */
class EvaluationServiceTest extends GoalousTestCase
{
    use TestTermTrait, TestEvaluationTrait, TestGoalTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.evaluation',
        'app.user',
        'app.local_name',
        'app.email',
        'app.term',
        'app.team',
        'app.evaluation_setting',
        'app.team_member',
        'app.evaluator',
        'app.goal',
        'app.goal_member',
        'app.key_result',
        'app.goal_label',
        'app.label',
        'app.post',
        'app.circle',
        'app.evaluate_score',
        'app.experiment',
        'app.evaluation_setting',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EvaluationService = ClassRegistry::init('EvaluationService');
        $this->Evaluation = ClassRegistry::init('Evaluation');
        $this->EvaluationSetting = ClassRegistry::init('EvaluationSetting');
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->User = ClassRegistry::init('User');
        $this->Experiment = ClassRegistry::init('Experiment');
    }

    function testGetEvalStatusEmpty()
    {
        $retEmpty = $this->EvaluationService->getEvalStatus(1, 1);
        $this->assertEmpty($retEmpty, 'There are no evaluation data');
    }

    function testGetEvalStatusOnlyMe()
    {
        $this->EvaluationSetting->current_team_id = 1;
        $Evaluation = $this->_getEvaluationObject($teamId = 1, $userId = 1);
        $termId = 1;
        $Evaluation->saveAll([
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => $userId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'status'            => 5,
                'my_turn_flg'       => 1,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
            ],
        ]);
        $ret = $this->EvaluationService->getEvalStatus($termId, $userId);
        $expected = [
            'flow'        => [
                [
                    'name'            => 'You',
                    'status'          => '5',
                    'this_turn'       => true,
                    'other_evaluator' => false,
                    'evaluate_type'   => Evaluation::TYPE_ONESELF
                ]
            ],
            'status_text' => [
                'your_turn' => true,
                'body'      => 'Please evaluate yourself.'
            ],
            'User'        => [
                'id'                    => '1',
                'first_name'            => 'firstname',
                'last_name'             => 'lastname',
                'photo_file_name'       => 'photo.png',
                'cover_photo_file_name' => null,
                'language'              => 'jpn',
                'auto_language_flg'     => true,
                'romanize_flg'          => true,
                'display_first_name'    => 'firstname',
                'display_last_name'     => 'lastname',
                'display_username'      => 'firstname lastname',
                'local_username'        => null,
                'roman_username'        => 'firstname lastname',
                'last_first'            => true,
                'PrimaryEmail'          => [
                    'email' => 'from@email.com',
                    'id'    => '1'
                ]
            ],
            'eval_stage'  => EvaluationService::STAGE_NONE
        ];

        $this->assertEquals($expected, $ret);
    }

    function testGetEvalStatusMulti()
    {
        $this->EvaluationSetting->current_team_id = 1;
        $Evaluation = $this->_getEvaluationObject($teamId = 1, $userId = 1);
        $termId = 1;

        $Evaluation->saveAll([
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => $userId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
            ],
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => 2,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],
        ]);
        $retMulti = $this->EvaluationService->getEvalStatus($termId, $userId);
        $expected = [
            'flow'        => [
                [
                    'name'            => 'You',
                    'status'          => '0',
                    'this_turn'       => false,
                    'other_evaluator' => false,
                    'evaluate_type'   => Evaluation::TYPE_ONESELF
                ],
                [
                    'name'            => '1(firstname lastname)',
                    'status'          => '0',
                    'this_turn'       => false,
                    'other_evaluator' => true,
                    'evaluate_type'   => Evaluation::TYPE_EVALUATOR
                ]
            ],
            'status_text' => [
                'your_turn' => false,
                'body'      => null
            ],
            'User'        => [
                'id'                    => '1',
                'first_name'            => 'firstname',
                'last_name'             => 'lastname',
                'photo_file_name'       => 'photo.png',
                'cover_photo_file_name' => null,
                'language'              => 'jpn',
                'auto_language_flg'     => true,
                'romanize_flg'          => true,
                'display_first_name'    => 'firstname',
                'display_last_name'     => 'lastname',
                'display_username'      => 'firstname lastname',
                'local_username'        => null,
                'roman_username'        => 'firstname lastname',
                'last_first'            => true,
                'PrimaryEmail'          => [
                    'email' => 'from@email.com',
                    'id'    => '1'
                ]
            ],
            'eval_stage'  => EvaluationService::STAGE_NONE
        ];
        $this->assertEquals($expected, $retMulti);
    }

    function testGetEvaluateeEvalStatusAsEvaluator()
    {
        $this->EvaluationSetting->current_team_id = 1;
        $Evaluation = $this->_getEvaluationObject($teamId = 1, $userId = 1);
        $termId = 1;
        $Evaluation->saveAll([
            [
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'status'            => 2,
                'my_turn_flg'       => 0,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
            ],
            [
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => $userId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 1,
                'status'            => 1,
                'my_turn_flg'       => 1,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],
            [
                'evaluatee_user_id' => 3,
                'evaluator_user_id' => 3,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'status'            => 2,
                'my_turn_flg'       => 0,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
            ],
            [
                'evaluatee_user_id' => 3,
                'evaluator_user_id' => $userId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 1,
                'status'            => 1,
                'my_turn_flg'       => 1,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],

        ]);

        $ret = $this->EvaluationService->getEvaluateeEvalStatusAsEvaluator($termId);

        $expected = [
            (int)0 => [
                'User'        => [
                    'id'                    => '2',
                    'first_name'            => 'firstname',
                    'last_name'             => 'lastname',
                    'photo_file_name'       => '',
                    'cover_photo_file_name' => null,
                    'language'              => 'jpn',
                    'auto_language_flg'     => true,
                    'romanize_flg'          => true,
                    'display_first_name'    => 'firstname',
                    'display_last_name'     => 'lastname',
                    'display_username'      => 'firstname lastname',
                    'local_username'        => null,
                    'roman_username'        => 'firstname lastname',
                    'last_first'            => true,
                    'PrimaryEmail'          => [
                        'email' => 'test@aaa.com',
                        'id'    => '2'
                    ]
                ],
                'flow'        => [
                    (int)0 => [
                        'name'            => 'Members',
                        'status'          => '2',
                        'this_turn'       => false,
                        'other_evaluator' => false,
                        'evaluate_type'   => Evaluation::TYPE_ONESELF

                    ],
                    (int)1 => [
                        'name'            => 'You',
                        'status'          => '1',
                        'this_turn'       => true,
                        'other_evaluator' => false,
                        'evaluate_type'   => Evaluation::TYPE_EVALUATOR

                    ]
                ],
                'status_text' => [
                    'your_turn' => true,
                    'body'      => 'Please evaluate.'
                ],
                'eval_stage'  => EvaluationService::STAGE_NONE
            ],
            (int)1 => [
                'User'        => [
                    'id'                    => '3',
                    'first_name'            => 'firstname',
                    'last_name'             => 'lastname',
                    'photo_file_name'       => null,
                    'cover_photo_file_name' => null,
                    'language'              => 'jpn',
                    'auto_language_flg'     => true,
                    'romanize_flg'          => true,
                    'display_first_name'    => 'firstname',
                    'display_last_name'     => 'lastname',
                    'display_username'      => 'firstname lastname',
                    'local_username'        => null,
                    'roman_username'        => 'firstname lastname',
                    'last_first'            => true,
                    'PrimaryEmail'          => [
                        'email' => null,
                        'id'    => null
                    ]
                ],
                'flow'        => [
                    (int)0 => [
                        'name'            => 'Members',
                        'status'          => '2',
                        'this_turn'       => false,
                        'other_evaluator' => false,
                        'evaluate_type'   => Evaluation::TYPE_ONESELF

                    ],
                    (int)1 => [
                        'name'            => 'You',
                        'status'          => '1',
                        'this_turn'       => true,
                        'other_evaluator' => false,
                        'evaluate_type'   => Evaluation::TYPE_EVALUATOR
                    ]
                ],
                'status_text' => [
                    'your_turn' => true,
                    'body'      => 'Please evaluate.'
                ],
                'eval_stage'  => EvaluationService::STAGE_NONE
            ]
        ];
        $this->assertEquals($expected, $ret);
    }

    function testGetEvaluateesFromCoachUserId_empty()
    {
        $termId = 1;
        // no evaluatee test
        $ret = $this->EvaluationService->getEvaluateesFromCoachUserId($termId, 3);
        $this->assertSame([], $ret);
    }

    function testGetEvaluateesFromCoachUserId_succeed()
    {
        $this->EvaluationSetting->current_team_id = 1;
        $Evaluation = $this->_getEvaluationObject($teamId = 1, $coachUserId = 1);
        $termId = 1;
        $Evaluation->saveAll([
            [
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => $coachUserId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 1,
                'status'            => 1,
                'my_turn_flg'       => 1,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],
            [
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 3,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 1,
                'status'            => 1,
                'my_turn_flg'       => 1,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],
        ]);
        $user = $this->User->create();
        $user['User']['active_flg'] = true;
        $user = $this->User->save($user);

        /** @var Email $Email */
        $Email = ClassRegistry::init('Email');

        $email = $Email->create();
        $email = reset($email);
        $email['user_id'] = $user['User']['id'];
        $email['email'] = 'test@test.com';
        $email['email_verified'] = true;

        $Email->save($email);

        $teamMember = $this->TeamMember->create();
        $teamMember = reset($teamMember);
        $teamMember['user_id'] = $user['User']['id'];
        $teamMember['team_id'] = $teamId;
        $teamMember['evaluation_enable_flg'] = true;
        $teamMember['coach_user_id'] = $coachUserId;
        $teamMember['status'] = Enum\Model\TeamMember\Status::ACTIVE;

        $this->TeamMember->save($teamMember);

        // no evaluatee test
        $ret = $this->EvaluationService->getEvaluateesFromCoachUserId($termId, $coachUserId);

        $this->assertCount(3, $ret);
        $userHasFlow = $ret[0];
        $this->assertCount(2, $userHasFlow['flow']);
    }

    function _getEvaluationObject(int $teamId, int $userId): Evaluation
    {
        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $Evaluation->current_team_id = $teamId;
        $Evaluation->my_uid = $userId;
        return $Evaluation;
    }

    function test_isEditable()
    {

        // Term doesn't exist
        $res = $this->EvaluationService->isEditable(99, 1, 1);
        $this->assertFalse($res);

        $this->_setDefault();
        $teamId = 1;
        $userId = 1;
        $this->Team->current_team_id = $teamId;
        $this->Team->my_uid = $userId;
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $termId = $this->Term->getTermId(Term::TYPE_CURRENT);

        // Evaluate status: not started
        $res = $this->EvaluationService->isEditable($termId, 1, $userId);
        $this->assertFalse($res);
        // Evaluate status: freeze
        $this->Term->changeFreezeStatus($termId);
        $res = $this->EvaluationService->isEditable($termId, 1, $userId);
        $this->assertFalse($res);

        // Evaluate status: started
        $this->Term->changeToInProgress($termId);

        $res = $this->EvaluationService->isEditable($termId, 1, $userId);
        $this->assertFalse($res);

        // Evaluate status: started, No evaluation data
        $res = $this->EvaluationService->isEditable($termId, 1, $userId);
        $this->assertFalse($res);

        // Evaluatee can edit evaluation
        $evaluatorId1 = 2;
        $evaluatorId2 = 3;
        $finalEvaluatorId = 4;
        $this->Evaluation->saveAll([
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => $userId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'evaluate_type'     => Evaluation::TYPE_ONESELF,
            ],
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => $evaluatorId1,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => $evaluatorId2,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'evaluate_type'     => Evaluation::TYPE_EVALUATOR,
            ],
            [
                'evaluatee_user_id' => $userId,
                'evaluator_user_id' => $finalEvaluatorId,
                'term_id'           => $termId,
                'team_id'           => $teamId,
                'index_num'         => 0,
                'evaluate_type'     => Evaluation::TYPE_FINAL_EVALUATOR,
            ],
        ]);
        $this->EvaluationSetting->save([
            'team_id'    => $teamId,
            'enable_flg' => true,
        ]);
        // TODO: This test case succeed when current_team_id = null;
        // This might be a bug, need to investigate
        $this->EvaluationSetting->current_team_id = null;
        $this->EvaluationSetting->my_uid = null;
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);

        // Evaluator can't edit evaluation
        $this->Evaluation->updateAll(['status' => Enum\Model\Evaluation\Status::DRAFT], [
            'term_id'           => $termId,
            'evaluatee_user_id' => $userId,
            'evaluator_user_id' => $userId
        ]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);

        // Evaluator can edit evaluation
        $this->Evaluation->updateAll(['status' => Enum\Model\Evaluation\Status::DONE], [
            'term_id'           => $termId,
            'evaluatee_user_id' => $userId,
            'evaluator_user_id' => $userId
        ]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);

        // Evaluatee can't edit evaluation after one evaluator evaluated
        $this->Evaluation->updateAll(['status' => Enum\Model\Evaluation\Status::DONE], [
            'term_id'           => $termId,
            'evaluatee_user_id' => $userId,
            'evaluator_user_id' => $evaluatorId1
        ]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);

        $this->Evaluation->updateAll(['status' => Enum\Model\Evaluation\Status::DONE], [
            'term_id'           => $termId,
            'evaluatee_user_id' => $userId,
            'evaluator_user_id' => $evaluatorId2
        ]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);

        $this->Term->clear();
        $this->Term->id = $termId;
        $this->Term->save(['evaluate_status' => Enum\Model\Term\EvaluateStatus::FROZEN], false);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertFalse($res);
        // Final can only update evaluations by uploading csv and not allowed to edit evaluation on page even after frozen evaluation
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);
    }

    private function _setDefault(int $teamId = 1, int $userId = 1)
    {
        $this->Evaluation->current_team_id = $teamId;
        $this->Evaluation->my_uid = $userId;
        $this->EvaluationSetting->current_team_id = $teamId;
        $this->EvaluationSetting->my_uid = $userId;
        $this->Term->current_team_id = $teamId;
        $this->Term->my_uid = $userId;
        $this->Team->current_team_id = $teamId;
        $this->Team->my_uid = $userId;
        $this->TeamMember->current_team_id = $teamId;
        $this->TeamMember->my_uid = $userId;
        $this->Experiment->current_team_id = $teamId;
        $this->Experiment->my_uid = $userId;
        /** @var Experiment $Experiment */
        $Experiment = ClassRegistry::init("Experiment");
        $Experiment->create();
        $Experiment->save([
            'name'    => 'EnableEvaluationFeature',
            'team_id' => $teamId,
        ]);
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->Term->addTermData(Term::TYPE_NEXT);
    }

    /**
     * test evaluation can not start if no evaluation
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage evaluation is not enabled
     */
    function test_startEvaluation_will_throw_disabled_error()
    {
        $teamId = 5;
        $this->_setDefault($teamId);
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $this->EvaluationService->startEvaluation($teamId, $term['id']);
    }

    /**
     * test evaluation can not start if no evaluation
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage evaluations are empty
     */
    function test_startEvaluation_will_throw_empty_error()
    {
        $teamId = 5;
        $this->EvaluationSetting->save([
            'team_id'    => $teamId,
            'enable_flg' => true,
        ]);
        $this->_setDefault($teamId);
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $this->EvaluationService->startEvaluation($teamId, $term['id']);
    }

    /**
     * test simply evaluation can start
     */
    function test_startEvaluation_simple_evaluator()
    {
        $this->_setDefault();
        $teamId = 1;
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $result = $this->EvaluationService->startEvaluation($teamId, $term['id']);
        $this->assertTrue($result);

        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $evaluations = $Evaluation->findAllByTeamIdAndTermId($teamId, $termId);

        // Check $evaluations.term_id is equal
        foreach ($evaluations as $evaluation) {
            $this->assertEquals($evaluation['Evaluation']['term_id'], $termId);
        }

        // Check $evaluations contains the evaluatee_user_id=1, evaluator_user_id=3
        $result = Hash::extract($evaluations, '{n}.Evaluation[evaluatee_user_id=1][evaluator_user_id=3]');
        $this->assertCount(1, $result);
        $this->assertEquals($evaluateeUserId, $result[0]['evaluatee_user_id']);
        $this->assertEquals($evaluatorUserId, $result[0]['evaluator_user_id']);

        // Check $evaluations contains the goal_id = null only (not created goal evaluation)
        $countNotNull = 0;
        // difficult to get by Hash::extract on Evaluation.goal_id = null
        foreach (Hash::extract($evaluations, '{n}.Evaluation.goal_id') as $goalId) {
            if (!is_null($goalId)) {
                $countNotNull++;
            }
        }
        $this->assertEquals(0, $countNotNull);

        $term = $this->Term->getById($termId);
        $this->assertEquals($term['evaluate_status'], Enum\Model\Term\EvaluateStatus::IN_PROGRESS);
    }

    /**
     * test can not start evaluation twice on same term
     *
     * @expectedException RuntimeException
     */
    function test_startEvaluation_can_not_run_twice()
    {
        $this->_setDefault();
        $teamId = 1;
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);

        // running evaluation start twice on same terms.id
        $this->EvaluationService->startEvaluation($teamId, $termId);
        $this->EvaluationService->startEvaluation($teamId, $termId);
    }

    /**
     * test of the
     * goal evaluation will be target of evaluate
     */
    function test_startEvaluation_goal_evaluate()
    {
        $this->_setDefault();
        $teamId = 1;
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];

        // This Goal will be target of evaluate (evaluation term)
        // because "goal end date" is before "term end date"
        $goalWillEvaluate = $this->createGoalSimple($userId = 1, $teamId, GoalousDateTime::now(),
            GoalousDateTime::now()->addMonth(2));
        $goalIdWillEvaluate = $goalWillEvaluate['id'];
        $this->makeGoalAsTargetEvaluation($goalIdWillEvaluate);

        // This Goal will NOT be target of evaluate (evaluation term, but not target of evaluation)
        // because "goal end date" is before "term end date"
        $goalNotTargetOfEvaluation = $this->createGoalSimple($userId = 1, $teamId, GoalousDateTime::now(),
            GoalousDateTime::now()->addMonth(2));
        $goalIdNotTargetOfEvaluation = $goalNotTargetOfEvaluation['id'];

        // This Goal will NOT be target of evaluate (previous term)
        // because "goal end date" is after "term end date"
        $dateTime3MonthAgo = GoalousDateTime::now()->subMonth(3);
        $goalWillNotEvaluatePreviousTerm = $this->createGoalSimple($userId = 1, $teamId, $dateTime3MonthAgo,
            $dateTime3MonthAgo->addMonth(2));
        $goalIdWillNotEvaluatePreviousTerm = $goalWillNotEvaluatePreviousTerm['id'];
        $this->makeGoalAsTargetEvaluation($goalIdWillNotEvaluatePreviousTerm);

        // This Goal will NOT be target of evaluate (next term)
        // because "goal end date" is after "term end date"
        $goalWillNotEvaluateNextTerm = $this->createGoalSimple($userId = 1, $teamId, GoalousDateTime::now(),
            GoalousDateTime::now()->addMonth(3));
        $goalIdWillNotEvaluateNextTerm = $goalWillNotEvaluateNextTerm['id'];
        $this->makeGoalAsTargetEvaluation($goalIdWillNotEvaluateNextTerm);

        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $result = $this->EvaluationService->startEvaluation($teamId, $term['id']);
        $this->assertTrue($result);

        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $evaluations = $Evaluation->findAllByTeamIdAndTermId($teamId, $termId);

        // target term goal is included to evaluation
        $goalEvaluations = Hash::extract($evaluations, sprintf('{n}.Evaluation[goal_id=%d]', $goalIdWillEvaluate));
        $this->assertTrue(0 < count($goalEvaluations));

        // evaluation count is influenced by evaluated goal
        $userEvaluations = Hash::extract($evaluations,
            sprintf('{n}.Evaluation[evaluatee_user_id=%d]', $goalWillEvaluate['user_id']));
        $countEvaluation = count($goalEvaluations) * 2 + 1;
        $countEvaluationForOnlyEvaluatee = count($userEvaluations);
        $this->assertSame($countEvaluation, $countEvaluationForOnlyEvaluatee);

        // un-evaluation goal is not included to evaluation
        $goalEvaluations = Hash::extract($evaluations,
            sprintf('{n}.Evaluation[goal_id=%d]', $goalIdNotTargetOfEvaluation));
        $this->assertCount(0, $goalEvaluations);

        // previous term goal is not included to evaluation
        $goalEvaluations = Hash::extract($evaluations,
            sprintf('{n}.Evaluation[goal_id=%d]', $goalIdWillNotEvaluatePreviousTerm));
        $this->assertCount(0, $goalEvaluations);

        // next term goal is not included to evaluation
        $goalEvaluations = Hash::extract($evaluations,
            sprintf('{n}.Evaluation[goal_id=%d]', $goalIdWillNotEvaluateNextTerm));
        $this->assertCount(0, $goalEvaluations);
    }

    /**
     * test the evaluation count increased after started
     */
    function test_startEvaluation_count()
    {
        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $countEvaluationsBeforeStart = $Evaluation->find('count');

        $this->_setDefault();
        $teamId = 1;
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);

        // running evaluation start twice on same terms.id
        $this->EvaluationService->startEvaluation($teamId, $termId);
        $countEvaluationsAfterStart = $Evaluation->find('count');
        $countEvaluationsStarted = count($Evaluation->findAllByTeamIdAndTermId($teamId, $termId));

        $this->assertSame($countEvaluationsBeforeStart, $countEvaluationsAfterStart - $countEvaluationsStarted);
    }

    /**
     * test EvaluationService::startEvaluation() rollback when exception has threw
     *
     * @expectedException RuntimeException
     */
    function test_startEvaluation_rollback()
    {
        $TermModel = $this->getMockForModel('Term', array('changeToInProgress'));
        /** @noinspection PhpUndefinedMethodInspection */
        $TermModel->expects($this->once())
                  ->method('changeToInProgress')
                  ->will($this->throwException(new RuntimeException()));

        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');

        $this->_setDefault();
        $teamId = 1;
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);

        $countEvaluationsBeforeStart = $Evaluation->find('count');
        $exception = null;
        try {
            $this->EvaluationService->startEvaluation($teamId, $term['id']);
        } catch (\Throwable $e) {
            $exception = $e;
        }
        $countEvaluationsAfterRollback = $Evaluation->find('count');
        $this->assertSame($countEvaluationsBeforeStart, $countEvaluationsAfterRollback);
        throw $exception;
    }

    /**
     * test startEvaluation() function throw error because experiment not defined
     *
     * @expectedException RuntimeException
     */
    function test_startEvaluation_experiment_undefined()
    {
        $this->_setDefault();
        $teamId = 1;

        /** @var Experiment $Experiment */
        $Experiment = ClassRegistry::init("Experiment");
        $Experiment->deleteAll([
            'team_id' => $teamId,
        ]);
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $this->EvaluationService->startEvaluation($teamId, $term['id']);
    }

    /**
     * test startEvaluation() works my_turn_flg if enabled fixed order
     */
    function test_startEvaluation_fixed_order_enabled()
    {
        $this->_setDefault();
        $teamId = 1;

        /** @var EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
        $EvaluationSetting->updateAll([
            'fixed_evaluation_order_flg' => true,
        ], [
            'team_id' => $teamId,
        ]);

        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $this->EvaluationService->startEvaluation($teamId, $term['id']);

        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $evaluations = $Evaluation->find('all');

        $index0Evaluations = Hash::extract($evaluations, '{n}.Evaluation[index_num=0]');
        $this->assertTrue(0 < count($index0Evaluations));
        foreach ($index0Evaluations as $evaluation) {
            $this->assertTrue($evaluation['my_turn_flg']);
        }
    }

    /**
     * test startEvaluation() my_turn_flg set to false if disabled fixed order
     */
    function test_startEvaluation_fixed_order_disabled()
    {
        $this->_setDefault();
        $teamId = 1;

        /** @var EvaluationSetting $EvaluationSetting */
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");
        $EvaluationSetting->updateAll([
            'fixed_evaluation_order_flg' => false,
        ], [
            'team_id' => $teamId,
        ]);
        $term = $this->createTerm($teamId, new GoalousDateTime('first day of this month'), $termMonth = 3,
            Enum\Model\Term\EvaluateStatus::NOT_STARTED());
        $termId = $term['id'];
        $evaluator = $this->createEvaluator($teamId, $evaluateeUserId = 1, $evaluatorUserId = 3, $index = 0);
        $this->EvaluationService->startEvaluation($teamId, $term['id']);

        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $evaluations = $Evaluation->find('all');

        foreach ($evaluations as $evaluation) {
            if (isset($evaluation['my_turn_flg'])) {
                $this->assertFalse($evaluation['my_turn_flg']);
            }
        }
    }
}
