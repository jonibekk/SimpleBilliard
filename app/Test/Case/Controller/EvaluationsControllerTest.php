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
        'app.evaluate_score'
    ];

    /**
     * index method
     *
     * @return void
     */
    public function testIndexSuccess()
    {
        $this->_getEvaluationsCommonMock();
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
        $this->_getEvaluationsCommonMock();
        $this->testAction('/evaluations/view/1/1', ['method' => 'GET']);
    }

    public function testViewNotMatchPramater()
    {
        $this->_getEvaluationsCommonMock();
        $this->testAction('/evaluations/view/1/2', ['method' => 'GET']);
    }

    /**
     * view method
     *
     * @return void
     */
    public function testViewNotExistParamater()
    {
        $this->_getEvaluationsCommonMock();
        $this->testAction('/evaluations/view/', ['method' => 'GET']);
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

        return $Evaluations;
    }
}
