<?php
App::uses('CirclesController', 'Controller');

/**
 * CirclesController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction
 */
class CirclesControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.circle',
        'app.team',
        'app.badge',
        'app.user',
        'app.email',
        'app.comment_like',
        'app.comment',
        'app.post',
        'app.post_like',
        'app.post_read',
        'app.comment_mention',
        'app.given_badge',
        'app.post_mention',
        'app.comment_read',
        'app.notification',
        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.job_category',
        'app.local_name',
        'app.invite',
        'app.thread',
        'app.message',
        'app.circle_member'
    );

    function testAddSuccess()
    {
        $this->_getCirclesCommonMock();
        $data = [
            'Circle' => [
                'name' => 'test'
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
        $Circles->Circle->me = ['id' => '1'];
        /** @noinspection PhpUndefinedFieldInspection */
        $Circles->Circle->current_team_id = '1';
        return $Circles;
    }

}
