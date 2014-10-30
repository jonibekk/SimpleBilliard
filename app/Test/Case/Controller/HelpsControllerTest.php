<?php
App::uses('HelpsController', 'Controller');

/**
 * HelpsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 */
class HelpsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array();

    /**
     * testAjaxGetModal method
     *
     * @return void
     */
    public function testAjaxGetModal()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/helps/ajax_get_modal/' . 0, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/helps/ajax_get_modal/' . 999, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

}
