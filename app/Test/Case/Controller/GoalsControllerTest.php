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
        'app.follower',
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

    function testIndex()
    {
        $Goals = $this->_getGoalsCommonMock();
        $goal_data = [
            'user_id' => 1,
            'team_id' => 1,
            'purpose' => 'test'
        ];
        $Goals->Goal->save($goal_data);
        $key_result_data = [
            'user_id'     => 1,
            'team_id'     => 1,
            'goal_id'     => $Goals->Goal->getLastInsertID(),
            'name'        => 'test',
            'special_flg' => true,
        ];
        $Goals->Goal->KeyResult->save($key_result_data);
        $goal_data = [
            'user_id' => 1,
            'team_id' => 1,
            'purpose' => 'test'
        ];
        $Goals->Goal->create();
        $Goals->Goal->save($goal_data);

        $this->testAction('/goals/index', ['method' => 'GET']);
    }

    function testAjaxGetGoalDetailModal()
    {
        $Goals = $this->_getGoalsCommonMock();
        $goal_data = [
            'user_id' => 1,
            'team_id' => 1,
            'purpose' => 'test'
        ];
        $Goals->Goal->save($goal_data);
        $goal_id = $Goals->Goal->getLastInsertID();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_goal_detail_modal/' . $goal_id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetMoreIndexItems()
    {
        $this->_getGoalsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_more_index_items/page:2', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAdd()
    {
        $Goals = $this->_getGoalsCommonMock();
        $Goals->Goal->GoalCategory->deleteAll(['team_id' => 1]);

        $this->testAction('/goals/add', ['method' => 'GET']);
    }

    function testAddWithId()
    {
        $Goals = $this->_getGoalsCommonMock();
        $goal_data = [
            'user_id' => 1,
            'team_id' => 1,
            'purpose' => 'test'
        ];
        $Goals->Goal->save($goal_data);
        //存在するゴールで自分が作成したもの
        $this->testAction('/goals/add/' . $Goals->Goal->getLastInsertID(), ['method' => 'GET']);
    }

    function testAddWithIdNotOwn()
    {
        $Goals = $this->_getGoalsCommonMock();
        $goal_data = [
            'user_id' => 2,
            'team_id' => 1,
            'purpose' => 'test'
        ];
        $Goals->Goal->create();
        $Goals->Goal->save($goal_data);
        //存在するゴールで他人が作成したもの
        $this->testAction('/goals/add/' . $Goals->Goal->getLastInsertID(), ['method' => 'GET']);
    }

    function testAddWithIdNotExists()
    {
        $this->_getGoalsCommonMock();
        //存在しないゴール
        $this->testAction('/goals/add/' . 9999999999, ['method' => 'GET']);
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

    function testAddPostMode2()
    {
        $Goal = $this->_getGoalsCommonMock();
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
        $Goal->Goal->save($data);
        $id = $Goal->Goal->getLastInsertID();
        $this->testAction('/goals/add/' . $id . "/mode:2", ['method' => 'POST', 'data' => $data]);
    }

    function testAddPostMode3()
    {
        $Goal = $this->_getGoalsCommonMock();
        $data = [
            'Goal' => [
                'purpose' => 'test',
            ],
        ];
        $Goal->Goal->save($data);
        $id = $Goal->Goal->getLastInsertID();
        $this->testAction('/goals/add/' . $id . "/mode:3", ['method' => 'POST', 'data' => $data]);
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
        $Goals->getEndMonthLocalDateTime();
    }

    /**
     * testDelete method
     *
     * @return void
     */
    public function testDeleteFail()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('goals/delete/0', ['method' => 'POST']);
    }

    public function testDeleteNotOwn()
    {
        /**
         * @var UsersController $Goals
         */
        $Goals = $this->_getGoalsCommonMock();

        $user_id = 10;
        $team_id = 1;

        $goal_data = [
            'Goal' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'purpose' => 'test'
            ],
        ];
        $goal = $Goals->Goal->save($goal_data);
        $this->testAction('goals/delete/' . $goal['Goal']['id'], ['method' => 'POST']);
    }

    public function testDeleteSuccess()
    {
        /**
         * @var UsersController $Goals
         */
        $Goals = $this->_getGoalsCommonMock();

        $user_id = 1;
        $team_id = 1;

        $goal_data = [
            'Goal' => [
                'user_id' => $user_id,
                'team_id' => $team_id,
                'purpose' => 'test'
            ],
        ];
        $goal = $Goals->Goal->save($goal_data);

        $this->testAction('goals/delete/' . $goal['Goal']['id'], ['method' => 'POST']);
    }

    function testEditCollaboSuccess()
    {
        $this->_getGoalsCommonMock();
        $data = [
            'KeyResultUser' => [
                'role'          => 'test',
                'description'   => 'test',
                'key_result_id' => 1,
            ]
        ];
        $this->testAction('/goals/edit_collabo', ['method' => 'POST', 'data' => $data]);
    }

    function testEditCollaboFail()
    {
        $this->_getGoalsCommonMock();
        $data = [];
        $this->testAction('/goals/edit_collabo', ['method' => 'POST', 'data' => $data]);
    }

    function testDeleteCollaboSuccess()
    {
        $Goals = $this->_getGoalsCommonMock();
        $data = [
            'role'          => 'test',
            'description'   => 'test',
            'team_id'       => 1,
            'user_id'       => 1,
            'key_result_id' => 1,
        ];
        $Goals->Goal->KeyResult->KeyResultUser->save($data);
        $key_result_user_id = $Goals->Goal->KeyResult->KeyResultUser->getLastInsertID();
        $this->testAction('/goals/delete_collabo/' . $key_result_user_id, ['method' => 'POST']);
    }

    function testDeleteCollaboFailNotExists()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('/goals/delete_collabo/' . 99999, ['method' => 'POST']);
    }

    function testDeleteCollaboFailNotOwn()
    {
        $Goals = $this->_getGoalsCommonMock();
        $data = [
            'role'          => 'test',
            'description'   => 'test',
            'team_id'       => 1,
            'user_id'       => 99999,
            'key_result_id' => 1,
        ];
        $Goals->Goal->KeyResult->KeyResultUser->save($data);
        $key_result_user_id = $Goals->Goal->KeyResult->KeyResultUser->getLastInsertID();
        $this->testAction('/goals/delete_collabo/' . $key_result_user_id, ['method' => 'POST']);
    }

    function testAddFollowSuccess()
    {
        $Goals = $this->_getGoalsCommonMock();
        $data = [
            'name'    => 'test',
            'team_id' => 1,
            'user_id' => 1,
            'goal_id' => 1,
        ];
        $Goals->Goal->KeyResult->save($data);
        $key_result_user_id = $Goals->Goal->KeyResult->getLastInsertID();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_toggle_follow/' . $key_result_user_id, ['method' => 'POST']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddFollowFailExist()
    {
        $Goals = $this->_getGoalsCommonMock();
        $data = [
            'name'    => 'test',
            'team_id' => 1,
            'user_id' => 1,
            'goal_id' => 1,
        ];
        $Goals->Goal->KeyResult->save($data);
        $key_result_user_id = $Goals->Goal->KeyResult->getLastInsertID();
        $data = [
            'team_id'       => 1,
            'user_id'       => 1,
            'key_result_id' => $key_result_user_id,
        ];
        $Goals->Goal->KeyResult->Follower->save($data);
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_toggle_follow/' . $key_result_user_id, ['method' => 'POST']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddFollowFailNotExistKeyResult()
    {
        $this->_getGoalsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_toggle_follow/' . 999999999999999999, ['method' => 'POST']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testDeleteFollowSuccess()
    {
        $Goals = $this->_getGoalsCommonMock();
        $data = [
            'name'    => 'test',
            'team_id' => 1,
            'user_id' => 1,
            'goal_id' => 1,
        ];
        $Goals->Goal->KeyResult->save($data);
        $key_result_user_id = $Goals->Goal->KeyResult->getLastInsertID();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_toggle_follow/' . $key_result_user_id, ['method' => 'POST']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testDeleteFollowFailNotExistKeyResult()
    {
        $this->_getGoalsCommonMock();
        /** @noinspection PhpUndefinedFieldInspection */
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_toggle_follow/' . 999999999999999999, ['method' => 'POST']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCollaboChangeModal()
    {
        $this->_getGoalsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_collabo_change_modal/' . 1, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddKeyResultFail()
    {
        $this->_getGoalsCommonMock();
        $data = ['KeyResult' => []];
        $this->testAction('/goals/add_key_result/999999999', ['method' => 'POST', 'data' => $data]);
    }

    function testAddKeyResultSuccess()
    {
        $this->_getGoalsCommonMock();
        $data = ['KeyResult' =>
                     [
                         'name'        => 'test',
                         'value_unit'  => 0,
                         'start_value' => 1
                     ]
        ];
        $this->testAction('/goals/add_key_result/1', ['method' => 'POST', 'data' => $data]);
    }

    function testAjaxGetAddKeyResultModal()
    {
        $this->_getGoalsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_add_key_result_modal/' . 1, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetKeyResults()
    {
        $this->_getGoalsCommonMock();

        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_key_results/' . 1, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testEditKeyResult()
    {
        $Goals = $this->_getGoalsCommonMock();

        $data = [];
        $this->testAction('/goals/edit_key_result/' . 1, ['method' => 'PUT', 'data' => $data]);
        $data = [
            'KeyResult' => [
                'id'         => 1,
                'value_unit' => 2,
                'start_date' => time(),
                'end_date'   => time()
            ]
        ];
        $this->testAction('/goals/edit_key_result/' . 1, ['method' => 'PUT', 'data' => $data]);

        $Goals->Goal->KeyResult->id = 1;
        $Goals->Goal->KeyResult->saveField('user_id', 2);
        $this->testAction('/goals/edit_key_result/' . 1, ['method' => 'PUT', 'data' => $data]);
    }

    function testDeleteKeyResultSuccess()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('/goals/delete_key_result/' . 1, ['method' => 'POST']);
    }

    function testDeleteKeyResultFail()
    {
        $Goals = $this->_getGoalsCommonMock();
        $Goals->Goal->KeyResult->id = 1;
        $Goals->Goal->KeyResult->saveField('user_id', 2);
        $this->testAction('/goals/delete_key_result/' . 1, ['method' => 'POST']);
    }

    function testAjaxGetEditKeyResultModalSuccess()
    {
        $Goals = $this->_getGoalsCommonMock();
        $skr = [
            'user_id'     => 1,
            'team_id'     => 1,
            'goal_id'     => 1,
            'special_flg' => true,
            'start_date'  => time(),
            'end_date'    => time(),
        ];
        $Goals->Goal->KeyResult->create();
        $Goals->Goal->KeyResult->save($skr);
        $kr_user = [
            'user_id'       => 1,
            'key_result_id' => $Goals->Goal->KeyResult->getLastInsertID(),
        ];
        $Goals->Goal->KeyResult->KeyResultUser->save($kr_user);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_edit_key_result_modal/' . 1, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditKeyResultModalFail1()
    {
        $this->_getGoalsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_edit_key_result_modal/' . 9999999, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetEditKeyResultModalFail2()
    {
        $this->_getGoalsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/goals/ajax_get_edit_key_result_modal/' . 1, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testCompleteSuccess()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('/goals/complete/1', ['method' => 'POST']);
    }

    function testIncompleteSuccess()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('/goals/incomplete/1', ['method' => 'POST']);
    }

    function testCompleteFail()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('/goals/complete/9999999', ['method' => 'POST']);
    }

    function testIncompleteFail()
    {
        $this->_getGoalsCommonMock();
        $this->testAction('/goals/incomplete/9999999999', ['method' => 'POST']);
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
        $Goals->Goal->Team->my_uid = 1;
        $Goals->Goal->Team->current_team_id = 1;
        $Goals->Goal->Team->current_team = [
            'Team' => [
                'start_term_month' => 4,
                'border_months'    => 6
            ]
        ];
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
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->KeyResult->Follower->my_uid = '1';
        /** @noinspection PhpUndefinedFieldInspection */
        $Goals->Goal->KeyResult->Follower->current_team_id = '1';
        return $Goals;
    }
}
