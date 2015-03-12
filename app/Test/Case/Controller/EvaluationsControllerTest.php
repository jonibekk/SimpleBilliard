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
        'app.goal',
        'app.follower',
        'app.collaborator',
        'app.evaluation',
        'app.evaluation_setting',
    ];

    /**
     * index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->testAction('/evaluations/', ['method' => 'GET']);
    }

    /**
     * view method
     *
     * @return void
     */
    public function testView()
    {
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
                    'evaluatee_user_id' => 1,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => 1,
                    'comment'           => 'あいうえお',
                    'evaluate_score_id' => 1,
                    'index'             => 0,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 2,
                    'team_id'           => 1,
                    'evaluatee_user_id' => 1,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => 1,
                    'comment'           => 'かきくけこ',
                    'evaluate_score_id' => 1,
                    'index'             => 1,
                    'goal_id'           => 1,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 3,
                    'team_id'           => 1,
                    'evaluatee_user_id' => 1,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => 1,
                    'comment'           => 'さしすせそ',
                    'evaluate_score_id' => 1,
                    'index'             => 2,
                    'goal_id'           => 2,
                ],
            ],
            [
                'Evaluation' => [
                    'id'                => 4,
                    'team_id'           => 1,
                    'evaluatee_user_id' => 1,
                    'evaluator_user_id' => 2,
                    'evaluate_term_id'  => 1,
                    'comment'           => 'たちつてと',
                    'evaluate_score_id' => 1,
                    'index'             => 3,
                    'goal_id'           => 3,
                ],
            ],
        ];

        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

}
