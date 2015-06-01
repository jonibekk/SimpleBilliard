<?php
App::uses('AppController', 'Controller');

/**
 * Teams Controller
 *
 * @property Team $Team
 */
class TeamsController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function add()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        $border_months_options = $this->Team->getBorderMonthsOptions();
        $start_term_month_options = $this->Team->getMonths();
        $this->set(compact('border_months_options', 'start_term_month_options'));

        if (!$this->request->is('post')) {
            return $this->render();
        }

        if (!$this->Team->add($this->request->data, $this->Auth->user('id'))) {
            $this->Pnotify->outError(__d('gl', "チーム作成に失敗しました。"));
            return $this->render();
        }
        $this->_refreshAuth($this->Auth->user('id'));
        $this->Session->write('current_team_id', $this->Team->getLastInsertID());
        $this->Pnotify->outSuccess(__d('gl', "チームを作成しました。"));
        return $this->redirect(['action' => 'invite']);
    }

    public function settings()
    {
        $this->layout = LAYOUT_SETTING;
        $team_id = $this->Session->read('current_team_id');
        try {
            $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e);
            $this->redirect($this->referer());
        }
        $team = $this->Team->findById($team_id);
        $term_start_date = $this->Team->getCurrentTermStartDate();
        $term_end_date = $this->Team->getCurrentTermEndDate();
        $term_end_date = $term_end_date - 1;
        //get evaluation setting
        $eval_enabled = $this->Team->EvaluationSetting->isEnabled();
        $eval_setting = $this->Team->EvaluationSetting->getEvaluationSetting();
        $eval_scores = $this->Team->Evaluation->EvaluateScore->getScore($team_id);
        $this->request->data = array_merge($this->request->data, $eval_setting, $eval_scores);

        $current_term_id = $this->Team->EvaluateTerm->getCurrentTermId();
        $previous_term_id = $this->Team->EvaluateTerm->getPreviousTermId();
        $latest_term_id = $this->Team->EvaluateTerm->getLatestTermId();

        $eval_start_button_enabled = true;
        if (!is_null($current_term_id) &&
            !is_null($latest_term_id) &&
            !$this->Team->EvaluateTerm->isAbleToStartEvaluation($current_term_id) &&
            $current_term_id === $latest_term_id
        ) {
            $eval_start_button_enabled = false;
        }
        $this->set(compact('team', 'term_start_date', 'term_end_date', 'eval_enabled', 'eval_start_button_enabled',
                           'eval_scores'));
        $statuses = $this->Team->Evaluation->getAllStatusesForTeamSettings($latest_term_id);

        // 全体progressカウント
        $all_cnt = array_sum(Hash::extract($statuses, "{s}.all_num"));
        $incomplete_cnt = array_sum(Hash::extract($statuses, "{s}.incomplete_num"));
        $complete_cnt = (int)$all_cnt - (int)$incomplete_cnt;
        if ($complete_cnt == 0) {
            $progress_percent = 0;
        }
        else {
            $progress_percent = round(((int)$complete_cnt / (int)$all_cnt) * 100, 1);
        }

        // Get term info
        $current_eval_is_frozen = $this->Team->EvaluateTerm->checkFrozenEvaluateTerm($current_term_id);
        $current_eval_is_available = $this->Team->EvaluateTerm->checkTermAvailable($current_term_id);
        $current_term_start_date = $this->Team->getCurrentTermStartDate();
        $current_term_end_date = $this->Team->getCurrentTermEndDate() - 1;
        $previous_eval_is_frozen = $this->Team->EvaluateTerm->checkFrozenEvaluateTerm($previous_term_id);
        $previous_eval_is_available = $this->Team->EvaluateTerm->checkTermAvailable($previous_term_id);
        $previous_term = $this->Team->getBeforeTermStartEnd();
        $previous_term_start_date = $previous_term['start'];
        $previous_term_end_date = $previous_term['end'] - 1;

        $this->set(compact(
                       'statuses',
                       'all_cnt',
                       'incomplete_cnt',
                       'progress_percent',
                       'eval_is_frozen',
                       'current_term_id',
                       'current_eval_is_frozen',
                       'current_eval_is_available',
                       'current_term_start_date',
                       'current_term_end_date',
                       'previous_term_id',
                       'previous_eval_is_frozen',
                       'previous_eval_is_available',
                       'previous_term_start_date',
                       'previous_term_end_date'
                   ));

        return $this->render();
    }

    function save_evaluation_setting()
    {
        $this->request->allowMethod(['post', 'put']);
        $this->Team->begin();
        if ($this->Team->EvaluationSetting->save($this->request->data['EvaluationSetting'])) {
            $this->Team->commit();
            $this->Pnotify->outSuccess(__d('gl', "評価設定を保存しました。"));
        }
        else {
            $this->Team->rollback();
            $this->Pnotify->outError(__d('gl', "評価設定が保存できませんでした。"));
        }
        return $this->redirect($this->referer());
    }

    function save_evaluation_scores()
    {
        $this->request->allowMethod(['post', 'put']);
        $this->Team->begin();
        if ($this->Team->Evaluation->EvaluateScore->saveScores($this->request->data['EvaluateScore'],
                                                               $this->Session->read('current_team_id'))
        ) {
            $this->Team->commit();
            $this->Pnotify->outSuccess(__d('gl', "評価スコア設定を保存しました。"));
        }
        else {
            $this->Team->rollback();
            $this->Pnotify->outError(__d('gl', "評価スコア設定が保存できませんでした。"));
        }
        return $this->redirect($this->referer());

    }

    function to_inactive($id)
    {
        $this->request->allowMethod(['post']);
        $this->Team->Evaluation->EvaluateScore->setToInactive($id);
        $this->Pnotify->outSuccess(__d('gl', "スコア定義を削除しました。"));
        return $this->redirect($this->referer());
    }

    function ajax_get_confirm_inactive_score_modal($id)
    {
        $this->_ajaxPreProcess();
        $this->set(compact('id'));
        $response = $this->render('Team/confirm_to_inactive_score_modal');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function ajax_get_score_elm()
    {
        $this->_ajaxPreProcess();
        if (viaIsSet($this->request->params['named']['index'])) {
            $this->set(['index' => $this->request->params['named']['index']]);
        }
        $response = $this->render('Team/eval_score_form_elm');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function ajax_get_term_start_end($start_term_month, $border_months)
    {
        $this->_ajaxPreProcess();
        $res = $this->Team->getTermStrStartEndFromParam($start_term_month, $border_months, REQUEST_TIMESTAMP);
        return $this->_ajaxGetResponse($res);
    }

    function start_evaluation()
    {
        $this->request->allowMethod('post');
        try {
            if (!$this->Team->EvaluationSetting->isEnabled()) {
                throw new RuntimeException(__d('gl', "評価設定が有効ではありません。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e);
            return $this->redirect($this->referer());
        }
        //start evaluation process
        $this->Team->Evaluation->begin();
        if (!$this->Team->Evaluation->startEvaluation()) {
            $this->Team->Evaluation->rollback();
            $this->Pnotify->outError(__d('gl', "評価を開始できませんでした。"));
            return $this->redirect($this->referer());
        }
        $this->Team->Evaluation->commit();
        $this->Pnotify->outSuccess(__d('gl', "評価を開始しました。"));
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_START,
                                         $this->Team->EvaluateTerm->getCurrentTermId());
        return $this->redirect($this->referer());
    }

    public function invite()
    {
        $from_setting = false;
        if (strstr($this->referer(), "/settings")) {
            $from_setting = true;
        }
        $this->set(compact('from_setting'));

        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $team = $this->Team->findById($team_id);
        $this->set(compact('team'));

        if (!$this->request->is('post')) {
            $this->layout = LAYOUT_ONE_COLUMN;
            return $this->render();
        }

        $data = $this->request->data;
        //convert mail-address to array
        $email_list = $this->Team->getEmailListFromPost($data);

        //not exists correct email address.
        if (!$email_list) {
            $this->Pnotify->outError(__d('gl', "メールアドレスが正しくありません。"));
            return $this->redirect($this->referer());
        }

        $alreadyBelongTeamEmails = [];
        $sentEmails = [];
        //generate token and send mail one by one.
        foreach ($email_list as $email) {
            //don't process in case of exists in team.
            if ($this->User->Email->isBelongTeamByEmail($email, $team_id)) {
                $alreadyBelongTeamEmails[] = $email;
                continue;
            }
            //save invite mail data
            $invite = $this->Team->Invite->saveInvite(
                $email,
                $team_id,
                $this->Auth->user('id'),
                !empty($data['Team']['comment']) ? $data['Team']['comment'] : null
            );
            //send invite mail
            $team_name = $this->Team->TeamMember->myTeams[$this->Session->read('current_team_id')];
            $this->GlEmail->sendMailInvite($invite, $team_name);
            $sentEmails[] = $email;
        }

        $already_joined_usr_msg = null;
        if (!empty($alreadyBelongTeamEmails)) {
            $already_joined_usr_msg .= __d('gl', "%s人は既にチームに参加しているユーザの為、メール送信をキャンセルしました。",
                                           count($alreadyBelongTeamEmails));
        }

        if (empty($sentEmails)) {
            $this->Pnotify->outError($already_joined_usr_msg);
            return $this->redirect($this->referer());
        }

        $msg = __d('gl', "%s人に招待メールを送信しました。", count($sentEmails)) . "\n" . $already_joined_usr_msg;
        $this->Pnotify->outSuccess($msg);

        if (!$from_setting) {
            return $this->redirect('/');
        }

        return $this->redirect($this->referer());
    }

    function download_add_members_csv_format()
    {
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));

        $this->layout = false;
        $filename = 'add_member_csv_format';
        //heading
        $th = $this->Team->TeamMember->_getCsvHeading(true);
        $td = [];
        $this->set(compact('filename', 'th', 'td'));
    }

    function ajax_upload_update_members_csv()
    {
        $this->request->allowMethod('post');
        $result = [
            'error' => false,
            'css'   => 'alert-success',
            'title' => __d('gl', "正常に更新が完了しました。"),
            'msg'   => '',
        ];
        $this->_ajaxPreProcess('post');
        $csv = $this->Csv->convertCsvToArray($this->request->data['Team']['csv_file']['tmp_name']);
        $this->Team->TeamMember->begin();
        $save_res = $this->Team->TeamMember->updateMembersFromCsv($csv);
        if ($save_res['error']) {
            $this->Team->TeamMember->rollback();
            $result['error'] = true;
            $result['css'] = 'alert-danger';
            $result['msg'] = $save_res['error_msg'];
            if ($save_res['error_line_no'] == 0) {
                $result['title'] = __d('gl', "更新データに誤りがあります。");
            }
            else {
                $result['title'] = __d('gl', "%s行目でエラーがあります(行番号は見出し含む)。", $save_res['error_line_no']);
            }
        }
        else {
            $this->Team->TeamMember->commit();
            $result['msg'] = __d('gl', "%s人のメンバーを更新しました。", $save_res['success_count']);
        }
        return $this->_ajaxGetResponse($result);
    }

    function ajax_upload_new_members_csv()
    {
        $this->request->allowMethod('post');
        $result = [
            'error' => false,
            'css'   => 'alert-success',
            'title' => __d('gl', "正常に登録が完了しました。"),
            'msg'   => '',
        ];
        $this->_ajaxPreProcess('post');
        $csv = $this->Csv->convertCsvToArray($this->request->data['Team']['csv_file']['tmp_name']);
        $this->Team->TeamMember->begin();
        $save_res = $this->Team->TeamMember->saveNewMembersFromCsv($csv);
        if ($save_res['error']) {
            $this->Team->TeamMember->rollback();
            $result['error'] = true;
            $result['css'] = 'alert-danger';
            $result['msg'] = $save_res['error_msg'];
            if ($save_res['error_line_no'] == 0) {
                $result['title'] = __d('gl', "エラーがあります。");
            }
            else {
                $result['title'] = __d('gl', "%s行目でエラーがあります(行番号は見出し含む)。", $save_res['error_line_no']);
            }
        }
        else {
            $this->Team->TeamMember->commit();
            $team = $this->Team->findById($this->Session->read('current_team_id'));
            //send invite mail
            foreach ($this->Team->TeamMember->csv_datas as $data) {
                //save invite mail data
                $invite = $this->Team->Invite->saveInvite(
                    $data['Email']['email'],
                    $this->Team->current_team_id,
                    $this->Auth->user('id'),
                    null
                );
                //send invite mail
                $this->GlEmail->sendMailInvite($invite, $team['Team']['name']);
            }

            $result['msg'] = __d('gl', "%s人のメンバーを追加しました。", $save_res['success_count']);
        }
        return $this->_ajaxGetResponse($result);
    }

    function download_team_members_csv()
    {
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $this->layout = false;
        $filename = 'team_members_' . date('YmdHis');

        //見出し
        $th = $this->Team->TeamMember->_getCsvHeading(false);
        $td = $this->Team->TeamMember->getAllMembersCsvData();

        $this->set(compact('filename', 'th', 'td'));
    }

    function ajax_upload_final_evaluations_csv($term_id)
    {
        $this->request->allowMethod('post');
        $result = [
            'error' => false,
            'css'   => 'alert-success',
            'title' => __d('gl', "正常に最終評価が完了しました。"),
            'msg'   => '',
        ];
        $this->_ajaxPreProcess('post');
        $csv = $this->Csv->convertCsvToArray($this->request->data['Team']['csv_file']['tmp_name']);
        $this->Team->TeamMember->begin();
        $save_res = $this->Team->TeamMember->updateFinalEvaluationFromCsv($csv, $term_id);
        if ($save_res['error']) {
            $this->Team->TeamMember->rollback();
            $result['error'] = true;
            $result['css'] = 'alert-danger';
            $result['msg'] = $save_res['error_msg'];
            if ($save_res['error_line_no'] == 0) {
                $result['title'] = __d('gl', "更新データに誤りがあります。");
            }
            else {
                $result['title'] = __d('gl', "%s行目でエラーがあります(行番号は見出し含む)。", $save_res['error_line_no']);
            }
        }
        else {
            $this->Team->TeamMember->commit();
            $result['msg'] = __d('gl', "%s人の最終評価を更新しました。", $save_res['success_count']);
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_DONE_FINAL,
                                             $this->Team->EvaluateTerm->getCurrentTermId());
        }
        return $this->_ajaxGetResponse($result);
    }

    function download_final_evaluations_csv($term_id)
    {
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $this->layout = false;
        $filename = 'final_evaluations_' . date('YmdHis');

        //見出し
        $th = $this->Team->TeamMember->_getCsvHeadingEvaluation();
        $td = $this->Team->TeamMember->getAllEvaluationsCsvData($term_id, $team_id);

        $this->set(compact('filename', 'th', 'td'));
    }

    public function ajax_switch_team($team_id = null)
    {
        $this->layout = 'ajax';
        Configure::write('debug', 0);
        $redirect_url = Router::url("/", true);
        $this->set(compact("redirect_url"));
        if (!$team_id || !$this->request->is('ajax')) {
            $this->Pnotify->outError(__d('gl', "不正なアクセスです"));
            return $this->render();
        }
        //チーム所属チェック
        $my_teams = $this->Team->TeamMember->getActiveTeamList($this->Auth->user('id'));
        if (!array_key_exists($team_id, $my_teams)) {
            $this->Pnotify->outError(__d('gl', "このチームには所属していません"));
            return $this->render();
        }
        $this->_switchTeam($team_id, $this->Auth->user('id'));
        $this->Pnotify->outSuccess(__d('gl', "チームを「%s」に切り換えました。", $my_teams[$team_id]));
        return $this->render();
    }

    function change_freeze_status()
    {
        $this->request->allowMethod('post');
        $termId = viaIsSet($this->request->data['evaluate_term_id']);
        try {
            $this->Team->EvaluateTerm->changeFreezeStatus($termId);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e);
            return $this->redirect($this->referer());
        }
        $this->Pnotify->outSuccess(__d('gl', "評価を凍結しました。"));
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EVALUATION_FREEZE,
                                         $this->Team->EvaluateTerm->getCurrentTermId());
        return $this->redirect($this->referer());
    }
}
