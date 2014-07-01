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
        'app.tokenData',
        'app.thread',
        'app.send_mail',
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

    public function testAddPostSuccess()
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth'
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
        $value_map = [
            ['id', '537ce224-8c0c-4c99-be76-433dac11b50b'],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );

        $data = [
            'Team' => [
                'name' => 'team xxx'
            ]
        ];
        $this->testAction('/teams/add', ['method' => 'POST', 'data' => $data]);
    }

    public function testAddPostFail()
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth'
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
        $value_map = [
            ['id', '537ce224-8c0c-4c99-be76-433dac11b50b'],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );

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
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth'
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
        $value_map = [
            ['id', '537ce224-8c0c-4c99-be76-433dac11b50b'],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        $postData = [
            'Team' => [
                'name' => "test",
                'type' => 1
            ]
        ];
        $uid = '537ce224-8c0c-4c99-be76-433dac11b50b';
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->add($postData, $uid);

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_switch_team/' . $Teams->Team->id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testInvite()
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth',
                'Session'
            ],
        ]);
        $uid = '537ce224-8c0c-4c99-be76-433dac11b50b';
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
        $value_map = [
            ['id', $uid],
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->TeamMember->myStatusWithTeam = null;
        $data = [
            'TeamMember' => [
                [
                    'user_id'    => $uid,
                    'active_flg' => true,
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
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($session_value_map)
            );
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/tokenData', ['method' => 'GET']);
    }

    function testInvitePost()
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth',
                'Session'
            ],
        ]);
        $uid = '537ce224-8c0c-4c99-be76-433dac11b50b';
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
        $value_map = [
            ['id', $uid],
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->TeamMember->myStatusWithTeam = null;
        $data = [
            'TeamMember' => [
                [
                    'user_id'    => $uid,
                    'active_flg' => true,
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
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($session_value_map)
            );
        $emails = "aaa@example.com";
        $data = ['Team' => ['emails' => $emails]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/tokenData', ['method' => 'POST', 'data' => $data]);
    }

    function testInvitePostAllReadyInTeam()
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth',
                'Session'
            ],
        ]);
        $uid = '537ce224-c708-4084-b879-433dac11b50b';
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
        $value_map = [
            ['id', $uid],
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->TeamMember->myStatusWithTeam = null;

        $email = 'from@email.com';
        $team_id = '537ce224-c21c-41b6-a808-433dac11b50b';

        $data = [
            'TeamMember' => [
                [
                    'user_id'    => $uid,
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
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($session_value_map)
            );
        $data = ['Team' => ['emails' => $email]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/tokenData', ['method' => 'POST', 'data' => $data]);
    }

    function testInvitePostAllReadyInTeamAndNot()
    {
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth',
                'Session'
            ],
        ]);
        $uid = '537ce224-c708-4084-b879-433dac11b50b';
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
        $value_map = [
            ['id', $uid],
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $Teams->Team->TeamMember->myStatusWithTeam = null;

        $email = 'from@email.com,abcd@efgh.ccc';
        $team_id = '537ce224-c21c-41b6-a808-433dac11b50b';

        $data = [
            'TeamMember' => [
                [
                    'user_id'    => $uid,
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
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap($session_value_map)
            );
        $data = ['Team' => ['emails' => $email]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/tokenData', ['method' => 'POST', 'data' => $data]);
    }
}
