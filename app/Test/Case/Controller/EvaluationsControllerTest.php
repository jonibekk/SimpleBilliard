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
        $this->testAction('/evaluations/view', ['method' => 'GET']);
    }

    /**
     * add method
     *
     * @return void
     */
    public function testAddPost()
    {
        $data = [
            'Purpose' => [
                'name' => 'test',
            ],
        ];
        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

    /**
     * add method
     *
     * @return void
     */
    public function testAddPostFail()
    {
        $data = [
            'Purpose' => [
                'name' => 'test',
            ],
        ];
        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

    /**
     * add method
     *
     * @return void
     */
    public function testAddPostDraft()
    {
        $data = [
            'Purpose' => [
                'name' => 'test',
            ],
        ];
        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

    public function testAddPostDraftFail()
    {
        $data = [
            'Purpose' => [
                'name' => 'test',
            ],
        ];
        $this->testAction('/evaluations/add', ['method' => 'POST', 'data' => $data]);
    }

}
