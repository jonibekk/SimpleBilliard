<?php App::uses('GoalousControllerTestCase', 'Test');
App::uses('CirclesController', 'Controller');

/**
 * CirclesController Test Case
 * @method testAction($url = '', $options = array()) GoalousControllerTestCase::_testAction
 */
class CirclesControllerTest extends GoalousControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(

        'app.action_result',
        'app.cake_session',
        'app.evaluation',
        'app.circle',
        'app.team',
        'app.evaluation_setting',
        'app.evaluate_term',
        'app.badge',
        'app.user',
        'app.notify_setting',
        'app.email',
        'app.comment_like',
        'app.send_mail',
        'app.comment',
        'app.post',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',

        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
        'app.circle_member',
        'app.post_share_user',
        'app.post_share_circle',
        'app.follower',
        'app.goal_member',
        'app.goal',
        'app.goal_category'
    );

    function testAddSuccess()
    {
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'name'    => 'test',
                'members' => '2,12',
            ],
        ];
        $this->testAction('/circles/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testAddFail()
    {
        $this->_getCirclesCommonMock();
        $data = [];
        $this->testAction('/circles/add',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testAjaxGetEditModal()
    {
        $this->_getCirclesCommonMock();

        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/circles/ajax_get_edit_modal/circle_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSelect2InitCircleMembers()
    {
        $this->_getCirclesCommonMock();

        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/circles/ajax_select2_init_circle_members/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testEditSuccess()
    {
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'          => 1,
                'name'        => 'xxx',
                'description' => 'xxx xxxxx',
            ],
        ];
        $this->testAction('/circles/edit/circle_id:1', ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testEditSuccessChangePrivacy()
    {
        $Circles = $this->_getCirclesCommonMock();

        $circle_id = 1;
        $circle = $Circles->Circle->findById($circle_id);
        $public_flg_orig = $circle['Circle']['public_flg'];

        $name = 'name changed';
        $data = [
            'Circle' => [
                'id'          => $circle_id,
                'name'        => $name,
                'description' => 'xxx xxxxx',
                'public_flg'  => !$public_flg_orig,
            ],
        ];
        $this->testAction('/circles/edit/circle_id:1', ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);

        // 公開フラグが変更されていない事を確認
        $circle = $Circles->Circle->findById($circle_id);
        $this->assertEquals($name, $circle['Circle']['name']);
        $this->assertEquals($public_flg_orig, $circle['Circle']['public_flg']);
    }

    function testEditTeamAll()
    {
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'          => 3,
                'name'        => 'xxx',
                'description' => 'xxxx yyyy',
            ],
        ];
        $this->testAction('/circles/edit/circle_id:3', ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testEditFail()
    {
        $this->_getCirclesCommonMock();
        $data = ['Circle' => ['id' => 1, 'name' => null]];
        $this->testAction('/circles/edit/circle_id:1', ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testEditNotExists()
    {
        $this->_getCirclesCommonMock();
        $data = [];
        $this->testAction('/circles/edit/circle_id:99999',
            ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testEditNotAdmin()
    {
        $this->_getCirclesCommonMock();
        $data = [];
        $this->testAction('/circles/edit/circle_id:2', ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testAddMemberSuccess()
    {
        // 正常時
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'      => 1,
                'members' => 'user_12,user_13',
            ],
        ];
        $this->testAction('/circles/add_member/circle_id:1',
            ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testAddMemberNotExists()
    {
        // 存在しないサークル
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'      => 99999,
                'members' => 'user_12,user_13',
            ],
        ];
        $this->testAction('/circles/add_member/circle_id:99999',
            ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testAddMemberNotAdmin()
    {
        // 管理者でないユーザーの操作
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'      => 2,
                'members' => 'user_12,user_13',
            ],
        ];
        $this->testAction('/circles/add_member/circle_id:2',
            ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testAddMemberTeamAll()
    {
        // チーム全体サークルの編集
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'      => 3,
                'members' => 'user_12,user_13',
            ],
        ];
        $this->testAction('/circles/add_member/circle_id:3',
            ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testAddMemberEmptyData()
    {
        // チーム全体サークルの編集
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'id'      => 1,
                'members' => '',
            ],
        ];
        $this->testAction('/circles/add_member/circle_id:1',
            ['method' => 'PUT', 'data' => $data, 'return' => 'contents']);
    }

    function testDeleteSuccess()
    {
        $this->_getCirclesCommonMock();
        $this->testAction('/circles/delete/circle_id:1', ['method' => 'POST', 'return' => 'contents']);
    }

    function testDeleteNotExists()
    {
        $this->_getCirclesCommonMock();
        $this->testAction('/circles/delete/circle_id:99999', ['method' => 'POST', 'return' => 'contents']);
    }

    function testDeleteNotAdmin()
    {
        $this->_getCirclesCommonMock();
        $this->testAction('/circles/delete/circle_id:2', ['method' => 'POST', 'return' => 'contents']);
    }

    function testDeleteFailTeamAll()
    {
        $this->_getCirclesCommonMock();
        $this->testAction('/circles/delete/circle_id:3', ['method' => 'POST', 'return' => 'contents']);
    }

    function testAjaxGetPublicCirclesModal()
    {
        $this->_getCirclesCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/circles/ajax_get_public_circles_modal/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCircleMembers()
    {
        $this->_getCirclesCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $this->testAction('/circles/ajax_get_circle_members/circle_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testJoinSuccess()
    {
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                [
                    'join'      => true,
                    'circle_id' => "1",
                ],
                [
                    'join'      => false,
                    'circle_id' => "2",
                ],
                [
                    'join'      => true,
                    'circle_id' => "5",
                ],
            ],
        ];
        $this->testAction('/circles/join',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testJoinTeamAllCircle()
    {
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                [
                    'join'      => true,
                    'circle_id' => "3",
                ],
            ],
        ];
        $this->testAction('/circles/join',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testJoinFail()
    {
        $this->_getCirclesCommonMock();
        $data = [];
        $this->testAction('/circles/join',
            ['method' => 'POST', 'data' => $data, 'return' => 'contents']);

    }

    function testAjaxEditAdminStatus()
    {
        // 非管理者 -> 管理者
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 2, 'admin_flg' => 1], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);

        // 管理者 -> 非管理者
        $this->_getCirclesCommonMock();

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 0,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 2, 'admin_flg' => 0], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxEditAdminStatusNotExists()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:999999',
            ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxEditAdminStatusNotAdmin()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:2', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxEditAdminStatusLastAdmin()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 1,
                'admin_flg' => 0,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxEditAdminStatusSelfUpdate()
    {
        // 非管理者 -> 管理者
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 2, 'admin_flg' => 1], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);

        // 自分自身を管理者から外す
        $this->_getCirclesCommonMock();

        $data = [
            'CircleMember' => [
                'user_id'   => 1,
                'admin_flg' => 0,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 1, 'admin_flg' => 0], $json_data['result']);
        $this->assertTrue($json_data['self_update']);
        $this->assertArrayHasKey('message', $json_data);

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxEditAdminStatusUpdateFailed()
    {
        $Circles = $this->_getCirclesCommonMock();
        $CircleMember = $this->getMockForModel('CircleMember', array('editAdminStatus'));
        /** @noinspection PhpUndefinedMethodInspection */
        $CircleMember->expects($this->any())
                     ->method('editAdminStatus')
                     ->will($this->returnValue(false));
        $Circles->Circle->CircleMember = $CircleMember;

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxLeaveCircle()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id' => 2,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_leave_circle/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 2], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxLeaveCircleNotExists()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id' => 2,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_leave_circle/circle_id:999999',
            ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxLeaveCircleNotAdmin()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id' => 2,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_leave_circle/circle_id:2', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxLeaveCircleLastAdmin()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_leave_circle/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxLeaveCircleSelfUpdate()
    {
        // 非管理者 -> 管理者
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id'   => 2,
                'admin_flg' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_edit_admin_status/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 2, 'admin_flg' => 1], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);

        // 自分自身をサークルから
        $this->_getCirclesCommonMock();

        $data = [
            'CircleMember' => [
                'user_id' => 1,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_leave_circle/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], false);
        $this->assertEquals(['user_id' => 1], $json_data['result']);
        $this->assertTrue($json_data['self_update']);
        $this->assertArrayHasKey('message', $json_data);

        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxLeaveCircleUpdateFailed()
    {
        $Circles = $this->_getCirclesCommonMock();
        $CircleMember = $this->getMockForModel('CircleMember', array('unjoinMember'));
        /** @noinspection PhpUndefinedMethodInspection */
        $CircleMember->expects($this->any())
                     ->method('unjoinMember')
                     ->will($this->returnValue(false));
        $Circles->Circle->CircleMember = $CircleMember;

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $data = [
            'CircleMember' => [
                'user_id' => 2,
            ]
        ];
        $ret = $this->testAction('/circles/ajax_leave_circle/circle_id:1', ['data' => $data, 'method' => 'POST']);
        $json_data = json_decode($ret, true);
        $this->assertEquals($json_data['error'], true);
        $this->assertEquals([], $json_data['result']);
        $this->assertArrayHasKey('message', $json_data);
        $this->assertArrayHasKey('self_update', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSelect2NonCircleMember()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $ret = $this->testAction('/circles/ajax_select2_non_circle_member/circle_id:1',
            ['data' => ['term' => 'name', 'page_limit' => 10], 'method' => 'GET']);
        $json_data = json_decode($ret, true);
        $this->assertArrayHasKey('results', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSelect2NonCircleMemberWithBlank()
    {
        $this->_getCirclesCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $ret = $this->testAction('/circles/ajax_select2_non_circle_member/circle_id:1',
            ['data' => ['term' => 'aa bb', 'page_limit' => 10], 'method' => 'GET']);
        $json_data = json_decode($ret, true);
        $this->assertArrayHasKey('results', $json_data);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function _getCirclesCommonMock()
    {
        /**
         * @var CirclesController $Circles
         */
        $Circles = $this->generate('Circles', [
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
        $Circles->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Circles->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Circles->Auth->expects($this->any())->method('loggedIn')
                      ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Circles->Auth->staticExpects($this->any())->method('user')
                      ->will($this->returnValueMap($value_map)
                      );
        /** @noinspection PhpUndefinedFieldInspection */
        $Circles->Circle->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Circles->Circle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Circles->Circle->CircleMember->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Circles->Circle->CircleMember->current_team_id = '1';
        $Circles->Team->EvaluateTerm->my_uid = 1;
        $Circles->Team->EvaluateTerm->current_team_id = 1;
        $Circles->Circle->Team->TeamMember->my_uid = '1';
        $Circles->Circle->Team->TeamMember->current_team_id = '1';

        return $Circles;
    }

}
