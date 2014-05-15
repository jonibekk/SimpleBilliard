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
        'app.user',
        'app.image',
        'app.badge',
        'app.team',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.comment_mention',
        'app.given_badge',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.posts_image',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token'
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

    /**
     * testPageLoadTest method
     *
     * @return void
     */
    public function testPageLoadTest()
    {
	}

}
