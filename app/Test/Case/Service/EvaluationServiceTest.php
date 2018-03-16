<?php
App::uses('GoalousTestCase', 'Test');
App::uses('Term', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');
App::import('Service', 'EvaluationService');

/**
 * EvaluationServiceTest Class
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2016/12/08
 * Time: 9:42
 *
 * @property EvaluationService $EvaluationService
 * @property TeamMember        $TeamMember
 * @property User              $User
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
        'app.team_member',
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
        $this->TeamMember = ClassRegistry::init('TeamMember');
        $this->User = ClassRegistry::init('User');
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

    function testGetEvaluateesFromCoachUserId_empty()
    {
        $termId = 1;
        // no evaluatee test
        $ret = $this->EvaluationService->getEvaluateesFromCoachUserId($termId, 3);
        $this->assertSame([], $ret);
    }

    function testGetEvaluateesFromCoachUserId_succeed()
    {
        $Evaluation = $this->_getEvaluationObject($teamId = 1, $userId = 1);
        $termId = 1;
        $Evaluation->saveAll([
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
        $user = $this->User->save($user);

        $teamMember = $this->TeamMember->create();
        $teamMember = reset($teamMember);
        $teamMember['user_id'] = $user['User']['id'];
        $teamMember['team_id'] = $teamId;
        $teamMember['evaluation_enable_flg'] = 1;
        $teamMember['coach_user_id'] = $userId;
        $teamMember = $this->TeamMember->save($teamMember);

        // no evaluatee test
        $ret = $this->EvaluationService->getEvaluateesFromCoachUserId($termId, 1);
        $this->assertSame(3, count($ret));
        $userHasFlow = $ret[0];
        $this->assertSame(2, count($userHasFlow['flow']));
    }

    function _getEvaluationObject(int $teamId, int $userId): Evaluation
    {
        /** @var Evaluation $Evaluation */
        $Evaluation = ClassRegistry::init('Evaluation');
        $Evaluation->current_team_id = $teamId;
        $Evaluation->my_uid = $userId;
        return $Evaluation;
    }

}
