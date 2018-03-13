<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Term', 'Model');
App::import('Service', 'EvaluationService');

use Goalous\Model\Enum as Enum;
/**
 * EvaluationServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 9:42
 *
 * @property EvaluationService $EvaluationService
 * @property Evaluation $Evaluation
 * @property EvaluationSetting $EvaluationSetting
 */
class EvaluationServiceTest extends GoalousTestCase
{
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
        $this->EvaluationService = ClassRegistry::init('EvaluationService');
        $this->Evaluation = ClassRegistry::init('Evaluation');
        $this->EvaluationSetting = ClassRegistry::init('EvaluationSetting');
    }

    function testGetEvalStatusEmpty()
    {
        $retEmpty = $this->EvaluationService->getEvalStatus(1, 1);
        $this->assertEmpty($retEmpty, 'There are no evaluation data');
    }

    function testGetEvalStatusOnlyMe()
    {
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
                    'email' => 'from@email.com',
                    'id'    => '1'
                ]
            ]
        ];

        $this->assertEquals($expected, $ret);
    }

    function testGetEvalStatusMulti()
    {
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
                    'email' => 'from@email.com',
                    'id'    => '1'
                ]
            ]
        ];
        $this->assertEquals($expected, $retMulti);
    }

    function testGetEvaluateeEvalStatusAsEvaluator()
    {
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
                ]
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
                ]
            ]
        ];
        $this->assertEquals($expected, $ret);
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
            'team_id' => $teamId,
            'enable_flg' => true,
        ]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);

        // Evaluator can't edit evaluation
        $this->Evaluation->updateAll(['status' => Enum\Evaluation\Status::DRAFT]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertFalse($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);


        // Evaluator can edit evaluation
        $this->Evaluation->updateAll(['status' => Enum\Evaluation\Status::DONE]);
        $res = $this->EvaluationService->isEditable($termId, $userId, $userId);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId1);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $evaluatorId2);
        $this->assertTrue($res);
        $res = $this->EvaluationService->isEditable($termId, $userId, $finalEvaluatorId);
        $this->assertFalse($res);
    }

    private function _setDefault()
    {
        $this->Evaluation->current_team_id = 1;
        $this->Evaluation->my_uid = 1;
        $this->Term->current_team_id = 1;
        $this->Term->my_uid = 1;
        $this->Team->current_team_id = 1;
        $this->Team->my_uid = 1;
        $this->Term->addTermData(Term::TYPE_CURRENT);
        $this->Term->addTermData(Term::TYPE_PREVIOUS);
        $this->Term->addTermData(Term::TYPE_NEXT);
    }

}
