<?php App::uses('GoalousControllerTestCase', 'Test');
App::uses('TeamsController', 'Controller');

/**
 * TeamsController Test Case
 * @method testAction($url = '', $options = array()) GoalousControllerTestCase::_testAction
 */
class TeamsControllerTest extends GoalousControllerTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = array(

        'app.goal_category',
        'app.circle_member',
        'app.member_type',
        'app.evaluation_setting',
        'app.evaluation',
        'app.evaluate_score',
        'app.action_result',
        'app.goal',
        'app.key_result',
        'app.follower',
        'app.goal_member',
        'app.local_name',
        'app.cake_session',
        'app.team',
        'app.user',
        'app.notify_setting',
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
        'app.comment_read',
        'app.oauth_token',
        'app.team_member',
        'app.group',
        'app.evaluator',
        'app.member_group',
        'app.job_category',
        'app.invite',
        'app.send_mail',
        'app.send_mail_to_user',
        'app.term',
        'app.team_vision',
        'app.group_vision',
        'app.circle',
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
        $this->testAction('/teams/ajax_switch_team/team_id:test', ['method' => 'GET']);
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
        $this->testAction('/teams/ajax_switch_team/team_id:' . $Teams->Team->id, ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testInvite()
    {
        $this->_getTeamsCommonMock(null, true);

        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'GET']);
    }

    function testInviteFromSetting()
    {
        $this->_getTeamsCommonMock(null, true, true, true, '/teams/settings');

        $emails = "aaa@example.com";
        $data = ['Team' => ['emails' => $emails]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'POST', 'data' => $data]);
    }

    function testInvitePost()
    {
        $this->_getTeamsCommonMock(null, true);

        $emails = "aaa@example.com";
        $data = ['Team' => ['emails' => $emails]];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'POST', 'data' => $data]);
    }

    function testInviteNoEmails()
    {
        $this->_getTeamsCommonMock(null, true);

        $data = ['Team' => ['emails' => 'abc']];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/invite', ['method' => 'POST', 'data' => $data]);
    }

    function testInvitePostAllReadyInTeam()
    {
        $value_map = [
            [
                null,
                [
                    'id'         => '1',
                    'last_first' => true,
                    'language'   => 'jpn'
                ]
            ],
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
            [
                null,
                [
                    'id'         => 2,
                    'last_first' => true,
                    'language'   => 'jpn'
                ]
            ],
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

    function testSettingsSuccessNotAvailStartEvalButton()
    {
        $Teams = $this->_getTeamsCommonMock(null, true);
        $Teams->Team->Term->addTermData(Term::TYPE_CURRENT);

        $this->testAction('/teams/settings', ['method' => 'GET']);
    }

    function testSettingsFail()
    {
        $Teams = $this->_getTeamsCommonMock(null, true, false);
        $Teams->Team->TeamMember->updateAll(['admin_flg' => false],
            ['TeamMember.user_id' => 1, 'TeamMember.team_id' => 1]);
        $this->testAction('/teams/settings', ['method' => 'GET']);
    }

    function testStartEvaluationSuccess()
    {
        $this->_getTeamsCommonMock(null, true, false);
        $this->testAction('/teams/start_evaluation', ['method' => 'POST']);
    }

    function testStartEvaluationFailEvalNotEnable()
    {
        $Teams = $this->_getTeamsCommonMock(null, true, false);
        $Teams->Team->EvaluationSetting->deleteAll(['EvaluationSetting.team_id' => 1]);
        $this->testAction('/teams/start_evaluation', ['method' => 'POST']);
    }

    function testStartEvaluationFailSaveEvalFail()
    {
        $Teams = $this->_getTeamsCommonMock(null, true, false);
        $Teams->Team->TeamMember->updateAll(['TeamMember.evaluation_enable_flg' => false], ['TeamMember.team_id' => 1]);
        $this->testAction('/teams/start_evaluation', ['method' => 'POST']);
    }

    function testAjaxUploadNewMembersCsvEmpty()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'add_member_csv_format_only_title.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_new_members_csv/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadNewMembersCsvError()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'add_member_csv_format_error.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_new_members_csv/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadNewMembersCsvNoError()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'add_member_csv_format_no_error.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_new_members_csv/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadUpdateMembersCsvEmpty()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'update_member_csv_format_only_title.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_update_members_csv/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadUpdateMembersCsvError()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'update_member_csv_format_error.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_update_members_csv/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadUpdateMembersCsvNoError()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'update_member_csv_format_no_error.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_update_members_csv/', ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadFinalEvaluationsCsvEmpty()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'final_evaluations_csv_format_only_title.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_final_evaluations_csv/evaluate_term_id:1',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadFinalEvaluationsCsvError()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'final_evaluations_csv_format_error.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_final_evaluations_csv/evaluate_term_id:1',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxUploadFinalEvaluationsCsvNoError()
    {
        $Teams = $this->_getTeamsCommonMock(null, true);
        $data = [
            'id'                => 1,
            'team_id'           => 1,
            'evaluatee_user_id' => 1,
            'evaluator_user_id' => 2,
            'term_id'  => 1,
            'comment'           => null,
            'evaluate_score_id' => null,
            'evaluate_type'     => 3,
            'goal_id'           => null,
            'index_num'         => 0,
            'status'            => 0
        ];
        $Teams->Team->Evaluation->save($data);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $data['Team']['csv_file']['tmp_name'] = APP . 'Test' . DS . 'csv_upload_data' . DS . 'final_evaluations_csv_format_no_error.csv';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_upload_final_evaluations_csv/evaluate_term_id:1',
            ['method' => 'POST', 'data' => $data]);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetScoreElm()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_get_score_elm/index:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetTermStartEnd()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_get_term_start_end/1/6', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testToInactive()
    {
        $this->_getTeamsCommonMock(null, true);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/to_inactive_score/team_id:1', ['method' => 'POST']);
    }

    function testAjaxGetConfirmInactiveScoreModal()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_get_confirm_inactive_score_modal/team_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetConfirmInactiveGoalCategoryModal()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_get_confirm_inactive_goal_category_modal/team_id:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetGoalCategoryElm()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_get_goal_category_elm/index:1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testToInactiveGoalCategory()
    {
        $this->_getTeamsCommonMock(null, true);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/to_inactive_goal_category/team_id:1', ['method' => 'POST']);
    }

    function testAjaxGetTermStartEndByEdit()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/ajax_get_term_start_end_by_edit/1/1/1', ['method' => 'GET']);
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

    function testSaveEvaluationSettingFail()
    {
        $Teams = $this->_getTeamsCommonMock(null, true);
        $data = [
            'EvaluationSetting' => [
                'team_id'    => 1,
                'enable_flg' => 'test',
            ]
        ];
        $this->testAction('/teams/save_evaluation_setting', ['method' => 'POST', 'data' => $data]);
        $this->assertTrue(!empty($Teams->Team->EvaluationSetting->validationErrors));
    }

    function testSaveEvaluationSettingSuccess()
    {
        $Teams = $this->_getTeamsCommonMock(null, true);
        $data = [
            'EvaluationSetting' => [
                'team_id'    => 1,
                'enable_flg' => true,
            ],
            'EvaluateScore'     => [
                [
                    'name'        => 'test',
                    'index_num'   => 1,
                    'description' => 'desc'
                ]
            ]
        ];
        $this->testAction('/teams/save_evaluation_setting', ['method' => 'POST', 'data' => $data]);
        $this->assertTrue(empty($Teams->Team->EvaluationSetting->validationErrors));
    }

    function testEditTeamSuccess()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = [
            'Team' => [
                'name' => 'test'
            ]
        ];
        $this->testAction('/teams/edit_team', ['method' => 'POST', 'data' => $data]);
    }

    function testEditTeamFail()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = [
            'Team' => [
                'name' => null
            ]
        ];
        $this->testAction('/teams/edit_team', ['method' => 'POST', 'data' => $data]);
    }

    function testEditTermSuccess()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = [
            'Team' => [
                'change_from'      => '1',
                'start_term_month' => 1,
                'border_months'    => 1,
            ]
        ];
        $this->testAction('/teams/edit_term', ['method' => 'POST', 'data' => $data]);
    }

    function testEditTermFail()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = [
            'Team' => [
                'start_term_month' => null
            ]
        ];
        $this->testAction('/teams/edit_term', ['method' => 'POST', 'data' => $data]);
    }

    function testChangeFreezeStatusSuccess()
    {
        $Teams = $this->_getTeamsCommonMock(null, true);

        $Teams->Team->Term->addTermData(Term::TYPE_CURRENT);
        $termId = $Teams->Team->Term->getLastInsertID();
        $this->testAction('/teams/change_freeze_status/evaluate_term_id:' . $termId, ['method' => 'POST']);
    }

    function testChangeFreezeStatusFailed()
    {
        $this->_getTeamsCommonMock(null, true);
        $termId = 99999999;
        $this->testAction('/teams/change_freeze_status/evaluate_term_id:' . $termId, ['method' => 'POST']);
    }

    function testDownloadFinalEvaluationsCsv()
    {
        $this->_getTeamsCommonMock(null, true);
        $this->testAction('/teams/download_final_evaluations_csv/evaluate_term_id:1', ['method' => 'GET']);
    }

    function testSaveEvaluationScoresSuccess()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = [
            'EvaluateScore' => [
                [
                    'team_id'     => 1,
                    'name'        => 'test',
                    'index_num'   => 1,
                    'description' => 'desc'
                ]
            ]
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/save_evaluation_scores', ['method' => 'POST', 'data' => $data]);
    }

    function testSaveEvaluationScoresFail()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = ['EvaluateScore' => []];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/save_evaluation_scores', ['method' => 'POST', 'data' => $data]);
    }

    function testSaveGoalCategoriesSuccess()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = [
            'GoalCategory' => [
                [
                    'team_id'     => 1,
                    'name'        => 'test',
                    'description' => 'desc'
                ]
            ]
        ];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/save_goal_categories', ['method' => 'POST', 'data' => $data]);
    }

    function testSaveGoalCategoryFail()
    {
        $this->_getTeamsCommonMock(null, true);
        $data = ['GoalCategory' => []];
        /** @noinspection PhpUndefinedFieldInspection */
        $this->testAction('/teams/save_goal_categories', ['method' => 'POST', 'data' => $data]);
    }

    function testAjaxTeamAdminUserCheckReturnTrue()
    {
        $Teams = $this->_getTeamsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $admin_flg = 1;
        $data = [
            'user_id'   => $Teams->Team->TeamMember->uid,
            'team_id'   => $Teams->Team->TeamMember->current_team_id,
            'admin_flg' => $admin_flg
        ];
        $Teams->Team->TeamMember->save($data);
        $this->testAction('/teams/ajax_team_admin_user_check', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxTeamAdminUserCheckReturnFalse()
    {
        $Teams = $this->_getTeamsCommonMock();
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $admin_flg = 0;
        $data = [
            'user_id'   => $Teams->Team->TeamMember->uid,
            'team_id'   => $Teams->Team->TeamMember->current_team_id,
            'admin_flg' => $admin_flg
        ];
        $Teams->Team->TeamMember->save($data);
        $this->testAction('/teams/ajax_team_admin_user_check', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxDeleteTeamVision()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_delete_team_vision/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetTeamVision()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_team_vision/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSetTeamVisionArchive()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_set_team_vision_archive/1/0', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testMemberList()
    {
        $this->_getTeamsCommonMock(null, true);
        $this->testAction('/teams/main', ['method' => 'GET']);
    }

    function testAjaxGetTeamMemberInit()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_team_member_init', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetTeamMember()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_team_member', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetGroupMember()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_group_member', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCurrentTeamGroupList()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_current_team_group_list', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCurrentTeamAdminList()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_current_team_admin_list', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetCurrentNot2faStepUserList()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_current_not_2fa_step_user_list', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSetCurrentTeamActiveFlag()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_set_current_team_active_flag/1/0', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSetCurrentTeamAdminUserFlag()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_set_current_team_admin_user_flag/1/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSetCurrentTeamEvaluationFlag()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_set_current_team_evaluation_flag/1/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetInviteMemberList()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_invite_member_list', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetGroupVision()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_group_vision/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxSetGroupVisionArchive()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_set_group_vision_archive/1/0', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetLoginUserGroupId()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_login_user_group_id/1/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxDeleteGroupVision()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_delete_group_vision/1/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetTeamVisionDetail()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_team_vision_detail/1/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAjaxGetGroupVisionDetail()
    {
        $this->_getTeamsCommonMock(null, true);
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->testAction('/teams/ajax_get_group_vision_detail/1/1', ['method' => 'GET']);
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    function testAddTeamVisionNotAdmin()
    {
        $Teams = $this->_getTeamsCommonMock();
        $Teams->Team->TeamMember->updateAll(['admin_flg' => false],
            ['TeamMember.user_id' => 1, 'TeamMember.team_id' => 1]);
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));
        $this->testAction('/teams/add_team_vision', ['method' => 'GET']);
    }

    function testAddTeamVisionGet()
    {
        $Teams = $this->_getTeamsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));
        $this->testAction('/teams/add_team_vision', ['method' => 'GET']);
    }

    function testAddTeamVisionGetAlreadyExists()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'name'           => 'test',
            'team_id'        => 1,
            'create_user_id' => $Teams->Team->my_uid,
            'modify_user_id' => $Teams->Team->my_uid,
        ];
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));

        $Teams->Team->TeamVision->create();
        $Teams->Team->TeamVision->save($data);
        $this->testAction('/teams/add_team_vision', ['method' => 'GET']);
    }

    function testAddTeamVisionPostNoData()
    {
        $Teams = $this->_getTeamsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));
        $this->testAction('/teams/add_team_vision', ['method' => 'POST', 'data' => []]);
    }

    function testAddTeamVisionPostEmpty()
    {
        $Teams = $this->_getTeamsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));
        $this->testAction('/teams/add_team_vision',
            ['method' => 'POST', 'data' => ['TeamVision' => ['name' => null]]]);
    }

    function testAddTeamVisionPostSuccess()
    {
        $Teams = $this->_getTeamsCommonMock();
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Session->expects($this->any())->method('read')
                       ->will($this->returnValueMap([['current_team_id', 1]]));
        $Teams->Team->TeamMember->updateAll(['admin_flg' => true], ['user_id' => 1]);
        $data = [
            'TeamVision' => [
                'name' => 'test'
            ]
        ];
        $this->testAction('/teams/add_team_vision',
            ['method' => 'POST', 'data' => $data]);
    }

    function testEditTeamVisionNotAdmin()
    {
        $Teams = $this->_getTeamsCommonMock();
        $Teams->Team->TeamMember->updateAll(['admin_flg' => false],
            ['TeamMember.user_id' => 1, 'TeamMember.team_id' => 1]);
        $this->testAction('/teams/edit_team_vision', ['method' => 'GET']);
    }

    function testEditTeamVisionNotFound()
    {
        $this->_getTeamsCommonMock();
        $this->testAction('/teams/edit_team_vision', ['method' => 'GET']);
    }

    function testEditTeamVisionNotFound2()
    {
        $this->_getTeamsCommonMock();
        $this->testAction('/teams/edit_team_vision/team_vision_id:99999', ['method' => 'GET']);
    }

    function testEditTeamVisionGet()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test'
        ];
        $Teams->Team->TeamVision->save($data);
        $team_vision_id = $Teams->Team->TeamVision->getLastInsertID();
        $this->testAction("/teams/edit_team_vision/team_vision_id:{$team_vision_id}", ['method' => 'GET']);
    }

    function testEditTeamVisionPostNoData()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test'
        ];
        $Teams->Team->TeamVision->save($data);
        $team_vision_id = $Teams->Team->TeamVision->getLastInsertID();
        $this->testAction("/teams/edit_team_vision/team_vision_id:{$team_vision_id}",
            ['method' => 'POST', 'data' => []]);
    }

    function testEditTeamVisionPostEmpty()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test'
        ];
        $Teams->Team->TeamVision->save($data);
        $team_vision_id = $Teams->Team->TeamVision->getLastInsertID();
        $this->testAction("/teams/edit_team_vision/team_vision_id:{$team_vision_id}",
            ['method' => 'POST', 'data' => ['TeamVision' => ['name' => null]]]);
    }

    function testEditTeamVisionPostSuccess()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test'
        ];
        $Teams->Team->TeamVision->save($data);
        $team_vision_id = $Teams->Team->TeamVision->getLastInsertID();
        $Teams->Team->TeamMember->updateAll(['admin_flg' => true], ['user_id' => 1]);
        $data = [
            'TeamVision' => [
                'id'   => $team_vision_id,
                'name' => 'test',
            ]
        ];
        $this->testAction("/teams/edit_team_vision/team_vision_id:{$team_vision_id}",
            ['method' => 'POST', 'data' => $data]);
    }

    function _addMemberGroup($Teams)
    {
        $Teams->Team->Group->save(
            [
                'name'    => 'test',
                'team_id' => $Teams->Team->current_team_id,
            ]
        );
        $Teams->Team->Group->MemberGroup->save(
            [
                'team_id'  => $Teams->Team->current_team_id,
                'user_id'  => 1,
                'group_id' => $Teams->Team->Group->getLastInsertID()
            ]
        );
    }

    function testAddGroupVisionNoGroup()
    {
        $Teams = $this->_getTeamsCommonMock();
        $Teams->Team->Group->MemberGroup->deleteAll(['MemberGroup.user_id' => 1, 'MemberGroup.team_id' => 1]);
        $this->testAction('/teams/add_group_vision', ['method' => 'GET']);
    }

    function testAddGroupVisionGet()
    {
        $Teams = $this->_getTeamsCommonMock();
        $this->_addMemberGroup($Teams);
        $this->testAction('/teams/add_group_vision', ['method' => 'GET']);
    }

    function testAddGroupVisionPostNoData()
    {
        $Teams = $this->_getTeamsCommonMock();
        $this->_addMemberGroup($Teams);
        $this->testAction('/teams/add_group_vision', ['method' => 'POST', 'data' => []]);
    }

    function testAddGroupVisionPostEmpty()
    {
        $Teams = $this->_getTeamsCommonMock();
        $this->_addMemberGroup($Teams);
        $this->testAction('/teams/add_group_vision',
            ['method' => 'POST', 'data' => ['GroupVision' => ['name' => null]]]);
    }

    function testAddGroupVisionPostSuccess()
    {
        $Teams = $this->_getTeamsCommonMock();
        $this->_addMemberGroup($Teams);
        $Teams->Team->TeamMember->updateAll(['admin_flg' => true], ['user_id' => 1]);
        $data = [
            'GroupVision' => [
                'name' => 'test'
            ]
        ];
        $this->testAction('/teams/add_group_vision',
            ['method' => 'POST', 'data' => $data]);
    }

    function testEditGroupVisionNotFound()
    {
        $this->_getTeamsCommonMock();
        $this->testAction('/teams/edit_group_vision', ['method' => 'GET']);
    }

    function testEditGroupVisionNotFound2()
    {
        $this->_getTeamsCommonMock();
        $this->testAction('/teams/edit_group_vision/group_vision_id:9999999', ['method' => 'GET']);
    }

    function testEditGroupVisionGet()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test',
            'group_id'       => 1,
        ];
        $Teams->Team->GroupVision->save($data);
        $group_vision_id = $Teams->Team->GroupVision->getLastInsertID();
        $this->testAction("/teams/edit_group_vision/group_vision_id:{$group_vision_id}", ['method' => 'GET']);
    }

    function testEditGroupVisionPostNoData()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test',
            'group_id'       => 1,
        ];
        $Teams->Team->GroupVision->save($data);
        $group_vision_id = $Teams->Team->GroupVision->getLastInsertID();
        $this->testAction("/teams/edit_group_vision/group_vision_id:{$group_vision_id}",
            ['method' => 'POST', 'data' => []]);
    }

    function testEditGroupVisionPostEmpty()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test',
            'group_id'       => 1,
        ];
        $Teams->Team->GroupVision->save($data);
        $group_vision_id = $Teams->Team->GroupVision->getLastInsertID();
        $this->testAction("/teams/edit_group_vision/group_vision_id:{$group_vision_id}",
            ['method' => 'POST', 'data' => ['GroupVision' => ['name' => null]]]);
    }

    function testEditGroupVisionPostSuccess()
    {
        $Teams = $this->_getTeamsCommonMock();
        $data = [
            'team_id'        => $Teams->Team->current_team_id,
            'create_user_id' => 1,
            'modify_user_id' => 1,
            'name'           => 'test',
            'group_id'       => 1,
        ];
        $Teams->Team->GroupVision->save($data);
        $group_vision_id = $Teams->Team->GroupVision->getLastInsertID();
        $Teams->Team->TeamMember->updateAll(['admin_flg' => true], ['user_id' => 1]);
        $data = [
            'GroupVision' => [
                'id'   => $group_vision_id,
                'name' => 'test',
            ]
        ];
        $this->testAction("/teams/edit_group_vision/group_vision_id:{$group_vision_id}",
            ['method' => 'POST', 'data' => $data]);
    }

    function _getTeamsCommonMock(
        $value_map = null,
        $insert_team_data = false,
        $is_active = true,
        $is_admin = true,
        $referer = '/'
    ) {
        Configure::write('Config.language', 'jpn');

        /**
         * @var TeamsController $Teams
         */
        $Teams = $this->generate('Teams', [
            'components' => [
                'Security' => ['_validateCsrf', '_validatePost'],
                'Auth',
                'Session',
                'Csv'      => ['is_uploaded_file', 'move_uploaded_file'],
            ],
            'methods'    => [
                'referer'
            ]
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
        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Csv->expects($this->any())
                   ->method('is_uploaded_file')
                   ->will($this->returnValue(true));
        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Csv->expects($this->any())
                   ->method('move_uploaded_file')
                   ->will($this->returnCallback('copy'));
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->expects($this->any())->method('referer')->will($this->returnValue($referer));
        if (!$value_map) {
            $value_map = [
                [
                    null,
                    [
                        'id'         => '1',
                        'last_first' => true,
                        'language'   => 'jpn'
                    ]
                ],
                ['id', 1],
            ];
        }
        /** @noinspection PhpUndefinedMethodInspection */
        $Teams->Auth->staticExpects($this->any())->method('user')
                    ->will($this->returnValueMap($value_map)
                    );

        $team_id = 1;
        if ($insert_team_data) {
            /** @noinspection PhpUndefinedFieldInspection */
            $Teams->Team->TeamMember->myStatusWithTeam = null;
            $data = [
                'TeamMember' => [
                    [
                        'user_id'    => 1,
                        'active_flg' => $is_active,
                        'admin_flg'  => $is_admin,
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
            $team_id = $Teams->Team->getLastInsertId();
            /** @noinspection PhpUndefinedMethodInspection */
            $Teams->Auth->staticExpects($this->any())->method('user')
                        ->will($this->returnValueMap([['id', '1']])
                        );
            /** @noinspection PhpUndefinedMethodInspection */
            $Teams->Session->expects($this->any())->method('read')
                           ->will($this->returnValueMap($session_value_map)
                           );
        }
        $Teams->Team->TeamMember->csv_datas = [];
        $Teams->Team->current_team_id = 1;
        $Teams->Team->uid = $Teams->Team->my_uid = 1;
        $Teams->Team->TeamMember->current_team_id = 1;
        $Teams->Team->TeamMember->uid = $Teams->Team->TeamMember->my_uid = 1;
        $Teams->Team->TeamMember->User->MemberGroup->Group->current_team_id = 1;
        $Teams->Team->TeamMember->User->MemberGroup->Group->uid = $Teams->Team->TeamMember->User->MemberGroup->Group->my_uid = 1;
        $Teams->Team->TeamMember->MemberType->current_team_id = 1;
        $Teams->Team->TeamMember->MemberType->uid = $Teams->Team->TeamMember->MemberType->my_uid = 1;
        $Teams->Team->TeamMember->User->Email->current_team_id = 1;
        $Teams->Team->TeamMember->User->Email->uid = $Teams->Team->TeamMember->User->Email->my_uid = 1;
        $Teams->Team->Term->current_team_id = 1;
        $Teams->Team->Term->my_uid = 1;
        $Teams->Team->Evaluator->current_team_id = 1;
        $Teams->Team->Evaluator->my_uid = 1;
        $Teams->Team->EvaluationSetting->current_team_id = 1;
        $Teams->Team->EvaluationSetting->my_uid = 1;
        $Teams->Team->Evaluation->current_team_id = 1;
        $Teams->Team->Evaluation->my_uid = 1;
        $Teams->Team->TeamVision->current_team_id = $team_id;
        $Teams->Team->TeamVision->my_uid = 1;
        $Teams->Team->GroupVision->current_team_id = $team_id;
        $Teams->Team->GroupVision->my_uid = 1;
        $Teams->Team->Group->current_team_id = $team_id;
        $Teams->Team->Group->my_uid = 1;
        $Teams->Team->Group->MemberGroup->current_team_id = $team_id;
        $Teams->Team->Group->MemberGroup->my_uid = 1;
        $Teams->Team->Circle->current_team_id = 1;

        return $Teams;
    }
}
