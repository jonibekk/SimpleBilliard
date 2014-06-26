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
        'app.invite',
        'app.thread',
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
}
