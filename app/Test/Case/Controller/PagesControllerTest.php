<?php App::uses('GoalousControllerTestCase', 'Test');
App::uses('PagesController', 'Controller');

/**
 * PagesController Test Case
 * @method testAction($url = '', $options = array()) GoalousControllerTestCase::_testAction
 *
 * @property User $User
 */
class PagesControllerTest extends GoalousControllerTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.key_result',
        'app.evaluation',
        'app.evaluate_term',
        'app.action_result',

        'app.local_name',
        'app.follower',
        'app.cake_session',
        'app.evaluation_setting',
        'app.user',
        'app.notify_setting',
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
        'app.comment_read',
        'app.goal',
        'app.goal_member',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.email',
        'app.oauth_token',
        'app.post_share_user',
        'app.post_share_circle',
        'app.circle',
        'app.circle_member',
    );

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * testHomepage method
     *
     * @return void
     */
    public function testHomepage()
    {
        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        Configure::write('Config.language', 'en');
        $this->testAction('/', ['return' => 'contents']);
//        $this->assertTextContains("Let's start Goalous!", $this->view, "ブラウザが日本語以外の場合、英語表記される");
        unset($Pages);
        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        Configure::write('Config.language', 'ja');
        $this->testAction('/', ['return' => 'contents']);
//        $this->assertTextContains("Goalousをはじめよう！", $this->view, "ブラウザが日本語の場合、日本語表記される");
        unset($Pages);
        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        Configure::write('Config.language', 'ja');
        $this->testAction('/en/', ['return' => 'contents']);
//        $this->assertTextContains("Let's start Goalous!", $this->view, "ブラウザが日本語の場合でも、言語で英語を指定した場合は英語表記される");
        unset($Pages);
        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        $Pages->Team->Term->my_uid = 1;
        $Pages->Team->Term->current_team_id = 1;

        Configure::write('Config.language', 'en');
        $this->testAction('/ja/', ['return' => 'contents']);
//        $this->assertTextContains("Goalousをはじめよう！", $this->view, "ブラウザが英語の場合でも、言語で日本語を指定した場合は日本語表記される");
    }

    public function testHomeAuth()
    {
        Configure::write('Config.language', 'en');

        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Session',
                'Auth',
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $user_id = 1;
        $team_id = 1;
        $value_map = [
            [null, $user_id],
            ['language', 'jpn'],
            ['auto_language_flg', true],
            ['default_team_id', $team_id],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', $team_id]]));
        $Pages->Post->current_team_id = $team_id;
        $Pages->Post->Team->TeamMember->myStatusWithTeam['TeamMember']['admin_flg'] = 1;
        $post_data = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Pages->User->Post->saveAll($post_data);
        $share_user_data = [
            'PostShareUser' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $Pages->User->Post->getLastInsertID()
            ]
        ];
        $Pages->Post->PostShareUser->save($share_user_data);
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';
        $Pages->Team->Term->my_uid = 1;
        $Pages->Team->Term->current_team_id = 1;
        $this->testAction('/team_id:1');
    }

    public function testHomeAuthCircle()
    {
        Configure::write('Config.language', 'en');

        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Session',
                'Auth',
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $user_id = 1;
        $team_id = 1;
        $value_map = [
            [null, $user_id],
            ['language', 'jpn'],
            ['auto_language_flg', true],
            ['default_team_id', $team_id],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', $team_id]]));
        $Pages->Post->current_team_id = $team_id;
        $Pages->Post->Team->TeamMember->myStatusWithTeam['TeamMember']['admin_flg'] = 1;
        $post_data = [
            'Post'    => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'body'    => 'test'
            ],
            'Comment' => [
                [
                    'user_id' => $user_id,
                    'team_id' => $team_id,
                    'body'    => 'test'
                ]
            ]
        ];
        $Pages->User->Post->saveAll($post_data);
        $share_user_data = [
            'PostShareUser' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'post_id' => $Pages->User->Post->getLastInsertID()
            ]
        ];
        $Pages->Post->PostShareUser->save($share_user_data);
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';
        $Pages->Team->Term->my_uid = 1;
        $Pages->Team->Term->current_team_id = 1;
        $this->testAction('/pages/display/home/circle_id:11111111');
    }

    public function testHomeAuthNewProfile()
    {
        Configure::write('Config.language', 'en');

        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Session',
                'Auth',
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, 1],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';
        $Pages->Team->Term->my_uid = 1;
        $Pages->Team->Term->current_team_id = 1;

        $this->testAction('/', ['return' => 'contents']);
    }

    /**
     * testHomepage method
     *
     * @return void
     */
    public function testNonActiveMember()
    {
        /**
         * @var UsersController $Pages
         */
        $Pages = $this->generate('Pages', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Session',
                'Auth'     => ['user', 'loggedIn'],
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
            ['default_team_id', 1],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Pages->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );

        $TeamMember = $this->getMockForModel('TeamMember', array('isActive'));
        /** @noinspection PhpUndefinedMethodInspection */
        $TeamMember->expects($this->any())->method('isActive')
                   ->will($this->returnValue(false));
        $Pages->User->TeamMember = $TeamMember;

        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->User->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->User->current_team_id = '1';

        $this->testAction('/', ['return' => 'contents']);
    }
}
