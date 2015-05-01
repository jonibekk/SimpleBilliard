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
        'app.evaluate_term',
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
        $this->_getNotificationsCommonMock();
        $this->testAction('/notifications/', ['method' => 'GET']);
    }

    function testAjaxGetOldNotifyMoreCaseItemCntIsZero()
    {
        $oldest_score_id = 1;
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction("/notifications/ajax_get_old_notify_more/{$oldest_score_id}", ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetOldNotifyMoreCaseItemCntIsMany()
    {
        $oldest_score_id = 1;
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction("/notifications/ajax_get_old_notify_more/{$oldest_score_id}", ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetNewNotifyCount()
    {
        $this->_getNotificationsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction("/notifications/ajax_get_new_notify_count", ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetLatestNotifyItems()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/notifications/ajax_get_latest_notify_items', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function _getNotificationsCommonMock()
    {
        /**
         * @var NotificationsController $Notifications
         */
        $Notifications = $this->generate('Notifications', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
                'NotifyBiz',
                'GlEmail',
            ]
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
        $Notifications->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Notifications->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Notifications->Auth->expects($this->any())->method('loggedIn')
                            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Notifications->Auth->staticExpects($this->any())->method('user')
                            ->will($this->returnValueMap($value_map)
                            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Notifications->Session->expects($this->any())->method('read')
                               ->will($this->returnValueMap([['current_team_id', 1]]));

        return $Notifications;
    }

}

