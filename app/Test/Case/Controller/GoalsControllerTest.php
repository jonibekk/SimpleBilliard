<?php
App::uses('GoalsController', 'Controller');

/**
 * GoalsController Test Case
 * @method testAction($url = '', $options = array()) ControllerTestCase::_testAction

 */
class GoalsControllerTest extends ControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(
        'app.goal',
        'app.user',
        'app.team',
        'app.badge',
        'app.comment_like',
        'app.comment',
        'app.post',
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
        'app.team_member',
        'app.job_category',
        'app.invite',
        'app.notification',
        'app.notify_to_user',
        'app.notify_from_user',
        'app.thread',
        'app.message',
        'app.email',
        'app.notify_setting',
        'app.oauth_token',
        'app.local_name',
        'app.goal_category',
        'app.key_result',
        'app.key_result_user',
    );

    function testAdd()
    {
        $Goals = $this->_getGoalsCommonMock();
        $Goals->Goal->GoalCategory->deleteAll(['team_id' => 1]);

        $this->testAction('/goals/add', ['method' => 'GET']);
    }

    function testAddPost()
    {
        $this->_getGoalsCommonMock();
        $data = [
            'Goal'      => [
                'purpose' => 'test',
            ],
            'KeyResult' => [
                [
                    'name'         => 'test',
                    'target_value' => 1,
                    'start_value'  => 0,
                    'value_unit'   => 2,
                    'start_date'   => '2014/07/07',
                    'end_date'     => '2014/10/07',
                ]
            ]
        ];
        $this->testAction('/goals/add', ['method' => 'POST', 'data' => $data]);
    }

    function testAddPostEmptyKr()
    {
        $this->_getGoalsCommonMock();
        $data = [
            'Goal' => [
                'purpose' => 'test',
            ],
        ];
        $this->testAction('/goals/add', ['method' => 'POST', 'data' => $data]);
    }

    function testAddPostEmpty()
    {
        $this->_getGoalsCommonMock();
        $data = ['Goal' => []];
        $this->testAction('/goals/add', ['method' => 'POST', 'data' => $data]);
    }

    function testGetEndMonthLocalDateTime()
    {
        $Goals = $this->_getGoalsCommonMock();
        $Goals->getEndMonthLocalDateTime('test');
        $Goals->getEndMonthLocalDateTime(6, 'test');
    }

    function _getGoalsCommonMock()
    {
        /**
         * @var GoalsController $Goals
         */
        $Goals = $this->generate('Goals', [
            'components' => [
                'Session',
                'Auth'     => ['user', 'loggedIn'],
                'Security' => ['_validateCsrf', '_validatePost'],
                'Ogp',
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
        $Goals->Security
            ->expects($this->any())
            ->method('_validateCsrf')
            ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Goals->Security
            ->expects($this->any())
            ->method('_validatePost')
            ->will($this->returnValue(true));

        /** @noinspection PhpUndefinedMethodInspection */
        $Goals->Auth->expects($this->any())->method('loggedIn')
                    ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedMethodInspection */
        $Goals->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
            );
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->GoalCategory->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->GoalCategory->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->KeyResult->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->KeyResult->current_team_id = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->KeyResult->KeyResultUser->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->KeyResult->KeyResultUser->current_team_id = '1';
        return $Goals;
    }
}
