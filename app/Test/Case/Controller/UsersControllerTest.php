<?php
App::uses('UsersController', 'Controller');

/**
 * UsersController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 *
 */
class UsersControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user'
    );

    /**
     * testIndex method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->testAction('/users/index');
        $this->assertTextContains('Users', $this->view);
    }

    /**
     * testView method
     *
     * @return void
     */
    public function testView()
    {
    }

    /**
     * testAdd method
     *
     * @return void
     */
    public function testAdd()
    {
    }

    /**
     * testEdit method
     *
     * @return void
     */
    public function testEdit()
    {
    }

    /**
     * testDelete method
     *
     * @return void
     */
    public function testDelete()
    {
    }

}
