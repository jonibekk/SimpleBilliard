<?php
App::uses('PostsController', 'Controller');

/**
 * PostsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction

 */
class PostsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.post',
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.comment_read',
        'app.comment_mention',
        'app.given_badge',
        'app.group',
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.post_like',
        'app.post_mention',
        'app.post_read',
        'app.thread',
        'app.message',
        'app.email',
        'app.oauth_token',
        'app.local_name',
        'app.image',
        'app.images_post'
    );

    function testAdd()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, [
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->me = ['id' => '537ce224-8c0c-4c99-be76-433dac11b50b'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '537ce224-c21c-41b6-a808-433dac11b50b';
        $data = [
            'Post' => [
                'body' => 'test'
            ],
        ];
        $this->testAction('/posts/add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

    function testAddFail()
    {
        /**
         * @var UsersController $Posts
         */
        $Posts = $this->generate('Posts', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
            ]
        ]);
        $value_map = [
            [null, [
                'id'         => 'xxx',
                'last_first' => true,
                'language'   => 'jpn'
            ]],
            ['language', 'jpn'],
            ['auto_language_flg', true],
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedMethodInspection */
        $Posts->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['add_new_mode', MODE_NEW_PROFILE]]));
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->me = ['id' => '537ce224-8c0c-4c99-be76-433dac11b50b'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Posts->Post->current_team_id = '537ce224-c21c-41b6-a808-433dac11b50b';
        $data = [];
        $this->testAction('/posts/add',
                          ['method' => 'POST', 'data' => $data, 'return' => 'contents']);
    }

}
