<?php App::uses('GoalousControllerTestCase', 'Test');
App::uses('CirclePinsController', 'Controller');
App::uses('CirclePinsController', 'Api/Controller');

/**
 * CirclePinsController Test Case
 * @method testAction($url = '', $options = array()) GoalousControllerTestCase::_testAction
 */
class CirclePinsControllerTest extends GoalousControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.user',
        'app.team',
        'app.circle_pin',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.goal',

        'app.goal_category',
        'app.key_result',
        'app.action_result',
        'app.goal_member',
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
        'app.evaluator',
        'app.evaluation_setting',
        'app.term',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.approval_history',
        'app.evaluation'
    );

    function testIndex()
    {
        $this->_getCirclePinsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $result = ControllerTestCase::testAction('/circle_pins/index', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditModal()
    {
        // $this->_getCirclePinsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        ControllerTestCase::testAction('/circle_pins/ajax_get_edit_modal/circle_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testPost()
    {
        // $this->_getCirclePinsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data = [
            'csv' => [
                '1,2,3,4,5,6,7,8,9,10,11',
            ],
        ];
        ControllerTestCase::testAction('/api/v1/circle_pins/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertInternalType('array', $this->vars['data']['csv']);
    }

    function _getCirclePinsCommonMock()
    {
        /**
         * @var CirclePinsController $CirclePins
         */
        $CirclePins = $this->getMock('CirclePins', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
                'Ogp',
            ]
        ]);
        $value_map = [
            [
                null,
                [
                    'id'         => '1',
                    'last_first' => true,
                    'language'   => 'jpn'
                ]
            ],
            ['id', '1'],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $CirclePins->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $CirclePins->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $CirclePins->Auth->expects($this->any())->method('loggedIn')
                      ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $CirclePins->Auth->staticExpects($this->any())->method('user')
                      ->will($this->returnValueMap($value_map)
                      );
        $CirclePins->CirclePin->current_team_id = 1;
        $CirclePins->CirclePin->my_uid = 1;

        return $CirclePins;
    }

}
