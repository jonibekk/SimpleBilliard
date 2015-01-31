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
        $this->Security->unlockedActions = ['ajax_upload_new_members_csv'];
    }

    public function add()
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        if (!$this->request->is('post')) {
            return $this->render();
        }

        if ($this->Team->add($this->request->data, $this->Auth->user('id'))) {
            $this->_refreshAuth($this->Auth->user('id'));
            $this->Session->write('current_team_id', $this->Team->getLastInsertID());
            $this->Pnotify->outSuccess(__d('gl', "チームを作成しました。"));
            $this->redirect(['action' => 'invite']);
        }
        else {
            $this->Pnotify->outError(__d('gl', "チームに失敗しました。"));
        }
        return $this->render();
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
        $th = $this->_getCsvHeading(true);
        $td = [];
        $this->set(compact('filename', 'th', 'td'));
    }

    function ajax_upload_new_members_csv()
    {
        $result = [
            'error' => false,
            'css'   => 'alert-success',
            'title' => __d('gl', "正常に登録が完了しました。"),
            'msg'   => '',
        ];
        $this->_ajaxPreProcess('post');
        return $this->_ajaxGetResponse($result);
    }

    function download_team_members_csv()
    {
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $this->layout = false;
        $filename = 'team_members_' . date('YmdHis');

        //見出し
        $th = $this->_getCsvHeading(false);

        $dummy_datas = [
            0 => [
                'a' => 'abc',
                'b' => 'abc',
            ],
        ];
        $td = [];
        foreach ($dummy_datas as $k => $v) {
            $record = [];
            $record['last_name'] = $v['a'];

            $td[] = $record;
        }

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

    /**
     * get CSV heading
     *
     * @param bool $new
     *
     * @return array
     */
    function _getCsvHeading($new = true)
    {
        if ($new) {
            return [
                __d('gl', "メール(*)"),
                __d('gl', "メンバーID(*)"),
                __d('gl', "ローマ字名(*)"),
                __d('gl', "ローマ字姓(*)"),
                __d('gl', "メンバーのアクティブ状態(*)"),
                __d('gl', "管理者(*)"),
                __d('gl', "メンバータイプ(*)"),
                __d('gl', "評価対象(*)"),
                __d('gl', "グループ"),
                __d('gl', "ローカル姓名の言語コード"),
                __d('gl', "ローカル名"),
                __d('gl', "ローカル姓"),
                __d('gl', "電話"),
                __d('gl', "性別"),
                __d('gl', "誕生年"),
                __d('gl', "誕生月"),
                __d('gl', "誕生日"),
                __d('gl', "コーチID"),
                __d('gl', "評価者1"),
                __d('gl', "評価者2"),
                __d('gl', "評価者3"),
            ];
        }
        return [
            __d('gl', "メンバーID(*)"),
            __d('gl', "メール(*, 変更できません)"),
            __d('gl', "ローマ字名(*, 変更できません)"),
            __d('gl', "ローマ字姓(*, 変更できません)"),
            __d('gl', "管理者(*)"),
            __d('gl', "メンバータイプ(*)"),
            __d('gl', "評価対象(*)"),
            __d('gl', "グループ"),
            __d('gl', "コーチID"),
            __d('gl', "評価者1"),
            __d('gl', "評価者2"),
            __d('gl', "評価者3"),
        ];

    }

}
