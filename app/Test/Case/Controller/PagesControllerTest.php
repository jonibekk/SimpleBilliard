<?php
App::uses('PagesController', 'Controller');

/**
 * PagesController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 *
 * @property User $User
 */
class PagesControllerTest extends ControllerTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.local_name',
        'app.cake_session',
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
        $this->assertTextContains("Let's start Goalous!", $this->view, "ブラウザが日本語以外の場合、英語表記される");
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
        $this->assertTextContains("Goalousをはじめよう！", $this->view, "ブラウザが日本語の場合、日本語表記される");
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
        $this->assertTextContains("Let's start Goalous!", $this->view, "ブラウザが日本語の場合でも、言語で英語を指定した場合は英語表記される");
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
        Configure::write('Config.language', 'en');
        $this->testAction('/ja/', ['return' => 'contents']);
        $this->assertTextContains("Goalousをはじめよう！", $this->view, "ブラウザが英語の場合でも、言語で日本語を指定した場合は日本語表記される");
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
        $Pages->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';
        $this->testAction('/');
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
        $Pages->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';
        $this->testAction('/circle_feed/1');
    }

    public function testHomeAuthPermanentLink()
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
        $Pages->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';
        $this->testAction('/post_permanent/1');
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
        $Pages->Post->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->Comment->CommentRead->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareUser->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->PostShareCircle->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Pages->Post->User->CircleMember->current_team_id = '1';

        $this->testAction('/', ['return' => 'contents']);
    }

    public function testFeatureAuth()
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

        $this->testAction('/features', ['return' => 'contents']);
    }

    /**
     * testFeaturesPage method
     *
     * @return void
     */
    public function testFeaturesPage()
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
        $this->testAction('/features', ['return' => 'contents']);
        $this->assertTextContains("Set up a goal", $this->view, "ブラウザが日本語以外の場合、英語表記される");
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
        $this->testAction('/features', ['return' => 'contents']);
        $this->assertTextContains("ゴールを作成する", $this->view, "ブラウザが日本語の場合、日本語表記される");
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
        $this->testAction('/en/features', ['return' => 'contents']);
        $this->assertTextContains("Set up a goal", $this->view, "ブラウザが日本語の場合でも、言語で英語を指定した場合は英語表記される");
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
        Configure::write('Config.language', 'en');
        $this->testAction('/ja/features', ['return' => 'contents']);
        $this->assertTextContains("ゴールを作成する", $this->view, "ブラウザが英語の場合でも、言語で日本語を指定した場合は日本語表記される");
    }
}
