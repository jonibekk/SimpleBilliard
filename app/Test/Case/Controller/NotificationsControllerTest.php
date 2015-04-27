<?php
App::uses('NotificationsController', 'Controller');

/**
 * NotificationsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction

 */
class NotificationsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',
        'app.purpose',
        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.collaborator',
        'app.follower',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.group',
        'app.member_group',
        'app.invite',
        'app.job_category',
        'app.team_member',
        'app.member_type',
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.thread',
        'app.message',
        'app.evaluator',
        'app.evaluation_setting',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.approval_history',
        'app.evaluation'
    );

    function testIndex()
    {
        $this->testAction('/notifications/', ['method' => 'GET']);
    }

    function testAjaxGetNotifyListMore()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/notifications/ajax_get_notify_list_more', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetUnreadCount()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/notifications/ajax_get_unread_count', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetNewNotifyItems()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/notifications/ajax_get_new_notify_items', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }


}

