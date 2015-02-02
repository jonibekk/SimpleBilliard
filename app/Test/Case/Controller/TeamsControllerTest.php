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
        'app.action_result',
        'app.goal',
        'app.local_name',
        'app.cake_session',
        'app.team',
        'app.image',
        'app.user', 'app.notify_setting',
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
        'app.send_mail',
        'app.send_mail_to_user',
        'app.message'
    );

    /**
     * testAdd method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->testAction('/teams/add', ['method' => 'get']);
    }

    public function testAddPostSuccess()
    {
        $this->_getTeamsCommonMock();

        $data = [
            'Team' => [
                'name' => 'team xxx'
            ]
        ];
        $this->testAction('/teams/add', ['method' => 'POST', 'data' => $data]);
    }

    public function testAddPostFail()
    {
        $this->_getTeamsCommonMock();

        $data = [
            'Team' => [
                'name' => null
            ]
        ];
        $this->testAction('/teams/add', ['method' => 'POST', 'data' => $data]);
    }

    function testAjaxSwitchTeamNoData()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_switch_team/', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSwitchTeamNotFountTeam()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_switch_team/test', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSwitchTeamSuccess()
    {
        $Teams = $this->_getTeamsCommonMock();
        $postData = [
            'Team' => [
                'name' => "test",
                'type' => 1
            ]
        ];
        $uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->add($postData, $uid);

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_switch_team/' . $Teams->Team->id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testInvite()
    {
        $this->_getTeamsCommonMock(null, true);

        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'GET']);
    }

    function testInvitePost()
    {
        $this->_getTeamsCommonMock(null, true);

        $emails = "aaa@example.com";
        $data = ['Team' => ['emails' => $emails]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'POST', 'data' => $data]);
    }

    function testInvitePostAllReadyInTeam()
    {
        $value_map = [
            [null, [
                'id'         => '1',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['id', 2],
        ];

        $Teams = $this->_getTeamsCommonMock($value_map);
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->TeamMember->myStatusWithTeam = null;

        $email = 'from@email.com';
        $team_id = '1';

        $data = [
            'TeamMember' => [
                [
                    'user_id'    => 2,
                    'active_flg' => true,
                    'admin_flg'  => true,
                ]
            ],
            'Team'       => [
                'id' => $team_id
            ]
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->saveAll($data);
        /** @noinspection PhpUndefinedFieldInspection */
        $session_value_map = [
            ['current_team_id', $team_id]
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($session_value_map)
                       );
        $data = ['Team' => ['emails' => $email]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'POST', 'data' => $data]);
    }

    function testInvitePostAllReadyInTeamAndNot()
    {
        $value_map = [
            [null, [
                'id'         => 2,
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['id', 2],
        ];

        $Teams = $this->_getTeamsCommonMock($value_map);
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->TeamMember->myStatusWithTeam = null;

        $email = 'from@email.com,abcd@efgh.ccc';
        $team_id = '1';

        $data = [
            'TeamMember' => [
                [
                    'user_id'    => 2,
                    'active_flg' => true,
                    'admin_flg'  => true,
                ]
            ],
            'Team'       => [
                'id' => $team_id
            ]
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->saveAll($data);
        /** @noinspection PhpUndefinedFieldInspection */
        $session_value_map = [
            ['current_team_id', $team_id]
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($session_value_map)
                       );
        $data = ['Team' => ['emails' => $email]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'POST', 'data' => $data]);
    }

    function testSettingsSuccess()
    {
        $this->_getTeamsCommonMock(null, true);
        $this->testAction('/teams/settings', ['method' => 'GET']);

    }

    function testSettingsFail()
    {
        $this->_getTeamsCommonMock(null, true, false);
        $this->testAction('/teams/settings', ['method' => 'GET']);

    }

    function testAjaxUploadNewMembersCsv()
    {
        $Teams = $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_new_members_csv/', ['method' => 'POST']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testDownloadAddMembersCsvFormat()
    {
        $this->_getTeamsCommonMock(null, true);
        $this->testAction('/teams/download_add_members_csv_format', ['method' => 'GET']);
    }

    function testDownloadTeamMembersCsv()
    {
        $this->_getTeamsCommonMock(null, true);
        $this->testAction('/teams/download_team_members_csv', ['method' => 'GET']);
    }

    function _getTeamsCommonMock($value_map = null, $insert_team_data = false, $is_admin = true)
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth',
                'Session'
            ],
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        if (!$value_map) {
            $value_map = [
                [null, [
                    'id'         => '1',
                    'last_first' => true,
                    'language'   => 'jpn'
                ]],
                ['id', 1],
            ];
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );

        if ($insert_team_data) {
            /** @noinspection PhpUndefinedFieldInspection */
            $Teams->Team->TeamMember->myStatusWithTeam = null;
            $data = [
                'TeamMember' => [
                    [
                        'user_id'    => 1,
                        'active_flg' => $is_admin,
                        'admin_flg'  => true,
                    ]
                ],
                'Team'       => [
                    'name' => 'test'
                ]
            ];
            /** @noinspection PhpUndefinedFieldInspection */
            $Teams->Team->saveAll($data);
            /** @noinspection PhpUndefinedFieldInspection */
            $session_value_map = [
                ['current_team_id', $Teams->Team->getLastInsertId()]
            ];
            /** @noinspection PhpUndefinedMethodInspection */
            $Teams->Auth->staticExpects($this->any())->method('user')
                        ->will($this->returnValueMap([['id', '1']])
                        );
            /** @noinspection PhpUndefinedMethodInspection */
            $Teams->Session->expects($this->any())->method('read')
                           ->will($this->returnValueMap($session_value_map)
                           );

        }

        return $Teams;
    }
}
