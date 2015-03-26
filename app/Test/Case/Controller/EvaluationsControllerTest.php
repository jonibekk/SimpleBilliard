<?php
App::uses('EvaluationsController', 'Controller');

/**
 * HelpsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 */
class EvaluationsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.circle_member',
        'app.member_type',
        'app.evaluation_setting',
        'app.evaluation',
        'app.action_result',
        'app.goal',
        'app.goal_category',
        'app.follower',
        'app.collaborator',
        'app.local_name',
        'app.cake_session',
        'app.team',
        'app.image',
        'app.user',
        'app.notify_setting',
        'app.email',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.comment_mention',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.images_post',
        'app.comment_read',
        'app.notification',
        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.evaluator',
        'app.member_group',
        'app.job_category',
        'app.invite',
        'app.thread',
        'app.send_mail',
        'app.send_mail_to_user',
        'app.message',
        'app.evaluate_term',
        'app.evaluate_score',
        'app.key_result',
    ];

    /**
     * index method
     *
     * @return void
     */
    public function testIndexSuccess()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        $Evaluations->Team->EvaluateTerm->saveTerm();
        $eval_data = [
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 1,
            'evaluate_term_id'  => $Evaluations->Team->EvaluateTerm->getLastInsertID(),
            'evaluate_type'     => 0,
            'index_num'         => 0,
        ];
        $Evaluations->Evaluation->save($eval_data);
        $this->testAction('/evaluations/', ['method' => 'GET']);
    }

    public function testIndexNotEnabled()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $res = $Evaluations->Evaluation->Team->EvaluationSetting->findByTeamId(1);
        $Evaluations->Evaluation->Team->EvaluationSetting->id = $res['EvaluationSetting']['id'];
        $Evaluations->Evaluation->Team->EvaluationSetting->saveField('enable_flg', false);
        $this->testAction('/evaluations/', ['method' => 'GET']);
    }

    /**
     * view method
     *
     * @return void
     */

    public function testView()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        $Evaluations->Team->EvaluateTerm->saveTerm();
        $termId = $Evaluations->Team->EvaluateTerm->getLastInsertID();
        $records = [
            [
                'id'                => 1,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => $termId,
                'evaluate_type'     => 0,
                'comment'           => "a",
                'evaluate_score_id' => 1,
                'index_num'             => 0,
                'goal_id'           => 1,
                'status'            => 2
            ],
        ];
        $Evaluations->Evaluation->saveAll($records);
        $this->testAction("/evaluations/view/{$termId}/1", ['method' => 'GET']);
    }

    public function testViewNotExistTotal()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        $Evaluations->Team->EvaluateTerm->saveTerm();
        $termId = $Evaluations->Team->EvaluateTerm->getLastInsertID();
        $records = [
            [
                'id'                => 1,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => $termId,
                'evaluate_type'     => 0,
                'comment'           => "a",
                'evaluate_score_id' => 1,
                'index_num'             => 0,
                'goal_id'           => null,
                'status'            => 1
            ],
            [
                'id'                => 2,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => $termId,
                'evaluate_type'     => 0,
                'comment'           => "b",
                'evaluate_score_id' => 1,
                'index_num'             => 1,
                'goal_id'           => 1,
                'status'            => 1
            ],
        ];
        $Evaluations->Evaluation->saveAll($records);
        $this->testAction("/evaluations/view/{$termId}/1", ['method' => 'GET']);
    }

    public function testViewNotEnabled()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $res = $Evaluations->Evaluation->Team->EvaluationSetting->findByTeamId(1);
        $Evaluations->Evaluation->Team->EvaluationSetting->id = $res['EvaluationSetting']['id'];
        $Evaluations->Evaluation->Team->EvaluationSetting->saveField('enable_flg', false);
        $this->testAction('/evaluations/view/1/1', ['method' => 'GET']);
    }

    public function testViewNotMatchParameter()
    {
        $this->_getEvaluationsCommonMock();
        $this->testAction('/evaluations/view/1/2', ['method' => 'GET']);
    }

    public function testViewNotExistParameter()
    {
        $this->_getEvaluationsCommonMock();
        $this->testAction('/evaluations/view/', ['method' => 'GET']);
    }

    public function testViewNotExistEditableEvaluation()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        $Evaluations->Evaluation->deleteAll(['evaluate_term_id' => 1]);
        $records = [
            [
                'id'                => 1,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'comment'           => "a",
                'evaluate_score_id' => 1,
                'index_num'         => 0,
                'goal_id'           => 1,
                'status'            => 2
            ],
            [
                'id'                => 2,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'comment'           => "b",
                'evaluate_score_id' => 1,
                'index_num'         => 1,
                'goal_id'           => 2,
                'status'            => 2
            ],
            [
                'id'                => 3,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'comment'           => "c",
                'evaluate_score_id' => 1,
                'index_num'         => 2,
                'goal_id'           => 3,
                'status'            => 2
            ],
            [
                'id'                => 4,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'comment'           => "d",
                'evaluate_score_id' => 1,
                'index_num'         => 3,
                'goal_id'           => 10,
                'status'            => 2
            ],
            [
                'id'                => 5,
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => 2,
                'evaluate_type'     => 0,
                'comment'           => "e",
                'evaluate_score_id' => 1,
                'index_num'         => 0,
                'goal_id'           => 11,
                'status'            => 2
            ],
            [
                'id'                => 6,
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => 2,
                'evaluate_type'     => 0,
                'comment'           => "f",
                'evaluate_score_id' => 1,
                'index_num'         => 1,
                'goal_id'           => 12,
                'status'            => 2
            ],
        ];
        $Evaluations->Evaluation->saveAll($records);
        $this->testAction('/evaluations/view/1/1', ['method' => 'GET']);
    }

    public function testViewNotExistTotalEvaluation()
    {
        $Evaluations = $this->_getEvaluationsCommonMock();
        $Evaluations->Evaluation->deleteAll(['evaluate_term_id' => 1]);
        $records = [
            [
                'id'                => 1,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'index_num'         => 0,
                'goal_id'           => 1,
            ],
            [
                'id'                => 2,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'index_num'         => 1,
                'goal_id'           => 2,
            ],
            [
                'id'                => 3,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'index_num'         => 2,
                'goal_id'           => 3,
            ],
            [
                'id'                => 4,
                'team_id'           => 1,
                'evaluatee_user_id' => 1,
                'evaluator_user_id' => 1,
                'evaluate_term_id'  => 1,
                'evaluate_type'     => 0,
                'index_num'         => 3,
                'goal_id'           => 10,
            ],
            [
                'id'                => 5,
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => 2,
                'evaluate_type'     => 0,
                'index_num'         => 0,
                'goal_id'           => 11,
            ],
            [
                'id'                => 6,
                'team_id'           => 2,
                'evaluatee_user_id' => 2,
                'evaluator_user_id' => 2,
                'evaluate_term_id'  => 2,
                'evaluate_type'     => 0,
                'index_num'         => 1,
                'goal_id'           => 12,
            ],
        ];
        $Evaluations->Evaluation->saveAll($records);
        $this->testAction('/evaluations/view/1/1', ['method' => 'GET']);
    }

    /**
     * add method
     *
     * @return void
     */
    public function testAddPostDraft()
    {
        $data = [
            'is_draft' => true,
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

        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

    /**
     * add method
     *
     * @return void
     */
    public function testAddPostRegister()
    {
        $data = [
            'is_register' => true,
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

        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

    public function testAddPostRegisterValidationError()
    {
        $data = [
            'is_register' => true,
            [
                'Evaluation' => [
                    'id'                => 1,
                    'comment'           => null,
                    'evaluate_score_id' => null,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'comment'           => null,
                    'evaluate_score_id' => null,
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
                    'comment'           => null,
                    'evaluate_score_id' => null,
                ],
            ],
        ];
        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

    function _getEvaluationsCommonMock()
    {
        /**
         * @var EvaluationsController $Evaluations
         */
        $Evaluations = $this->generate('Evaluations', [
            'components' => [
                'Session',
                'Auth'      => ['user', 'loggedIn'],
                'Security'  => ['_validateCsrf', '_validatePost'],
                'Ogp',
                'NotifyBiz' => ['sendNotify']
            ],
        ]);
        $value_map = [
            [null, [
                'id'         => '1',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['id', '1'],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Evaluations->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Evaluations->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Evaluations->Auth->expects($this->any())->method('loggedIn')
                          ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Evaluations->Auth->staticExpects($this->any())->method('user')
                          ->will($this->returnValueMap($value_map)
                          );

        $Evaluations->Evaluation->Team->TeamMember->current_team_id = 1;
        $Evaluations->Evaluation->Team->TeamMember->my_uid = 1;
        $Evaluations->Evaluation->Team->EvaluationSetting->my_uid = 1;
        $Evaluations->Evaluation->Team->EvaluationSetting->current_team_id = 1;
        $Evaluations->Evaluation->Goal->my_uid = '1';
        $Evaluations->Evaluation->Goal->current_team_id = '1';
        $Evaluations->Evaluation->Team->TeamMember->my_uid = '1';
        $Evaluations->Evaluation->Team->TeamMember->current_team_id = '1';
        $Evaluations->Evaluation->Goal->ActionResult->my_uid = '1';
        $Evaluations->Evaluation->Goal->ActionResult->current_team_id = '1';
        $Evaluations->Evaluation->Goal->GoalCategory->my_uid = '1';
        $Evaluations->Evaluation->Goal->GoalCategory->current_team_id = '1';
        $Evaluations->Evaluation->Goal->KeyResult->my_uid = '1';
        $Evaluations->Evaluation->Goal->KeyResult->current_team_id = '1';
        $Evaluations->Evaluation->Goal->Collaborator->my_uid = '1';
        $Evaluations->Evaluation->Goal->Collaborator->current_team_id = '1';
        $Evaluations->Evaluation->Goal->Follower->my_uid = '1';
        $Evaluations->Evaluation->Goal->Follower->current_team_id = '1';
        $Evaluations->Evaluation->Goal->Post->my_uid = '1';
        $Evaluations->Evaluation->Goal->Post->current_team_id = '1';
        $Evaluations->Evaluation->current_team_id = 1;
        $Evaluations->Evaluation->my_uid = 1;
        $Evaluations->Team->EvaluateTerm->current_team_id = 1;
        $Evaluations->Team->EvaluateTerm->my_uid = 1;
        $Evaluations->Team->current_team_id = 1;
        $Evaluations->Team->my_uid = 1;

        return $Evaluations;
    }
}
