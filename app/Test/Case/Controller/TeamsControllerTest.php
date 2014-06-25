<?php
App::uses('TeamsController', 'Controller');

/**
 * TeamsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 */
class TeamsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.cake_session',
        'app.team',
        'app.image',
        'app.user',
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
        'app.job_category',
        'app.invite',
        'app.thread',
        'app.message'
    );

    /**
     * testAdd method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->testAction('/teams/add');
    }
}
