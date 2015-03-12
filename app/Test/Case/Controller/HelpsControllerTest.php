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
    public $fixtures = array(
        'app.action_result',
        'app.evaluator',
        'app.evaluation_setting',
        'app.member_type',
        'app.goal',
        'app.follower',
        'app.collaborator',
        'app.local_name',
        'app.cake_session',
        'app.user', 'app.notify_setting',
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
        'app.images_post',
        'app.comment_read',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.thread',
        'app.message',
        'app.email',
        'app.send_mail',
        'app.send_mail_to_user',
        'app.oauth_token',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
    );

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
