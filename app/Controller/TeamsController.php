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
        if ($this->request->is('post') && !empty($this->request->data)) {
            if ($this->Team->add($this->request->data, $this->Auth->user('id'))) {
                $this->_refreshAuth($this->Auth->user('id'));
                $this->Session->write('current_team_id', $this->Team->getLastInsertID());
                $this->Pnotify->outSuccess(__d('gl', "チームを作成しました。"));
                $this->redirect(['action' => 'invite']);
            }
            else {
                $this->Pnotify->outError(__d('gl', "チームに失敗しました。"));
            }
        }
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
        if ($this->request->is('post') && !empty($this->request->data)) {
            $data = $this->request->data;
            //複数のメールアドレスを配列に抜き出す
            if ($email_list = $this->Team->getEmailListFromPost($data)) {
                $allReadyBelongTeamEmails = [];
                $sentEmails = [];
                //１件ずつtokenを発行し、メール送信
                foreach ($email_list as $email) {
                    //既にチームに所属している場合は処理しない
                    if ($this->User->Email->isBelongTeamByEmail($email, $team_id)) {
                        $allReadyBelongTeamEmails[] = $email;
                        continue;
                    }
                    //招待メールデータの登録
                    $invite = $this->Team->Invite->saveInvite(
                        $email,
                        $team_id,
                        $this->Auth->user('id'),
                        !empty($data['Team']['comment']) ? $data['Team']['comment'] : null
                    );
                    //招待メール送信
                    $team_name = $this->Team->TeamMember->myTeams[$this->Session->read('current_team_id')];
                    $this->GlEmail->sendMailInvite($invite, $team_name);
                    $sentEmails[] = $email;
                }
                if (!empty($sentEmails)) {
                    //１件以上メール送信している場合はホームリダイレクト
                    $msg = __d('gl', "%s人に招待メールを送信しました。", count($sentEmails)) . "\n";
                    if (!empty($allReadyBelongTeamEmails)) {
                        $msg .= __d('gl', "%s人は既にチームに参加しているユーザの為、メール送信をキャンセルしました。", count($allReadyBelongTeamEmails));
                    }
                    $this->Pnotify->outSuccess($msg);
                    if ($from_setting) {
                        $this->redirect($this->referer());
                    }
                    else {
                        /** @noinspection PhpVoidFunctionResultUsedInspection */
                        $this->redirect('/');
                    }
                }
                else {
                    //１件も送信していない場合は既にチームに参加済みのユーザの為、再入力
                    $this->Pnotify->outError(__d('gl', "入力した全てのメールアドレスのユーザは既にチームに参加している為、メール送信をキャンセルしました。"));
                }
            }
            else {
                $this->Pnotify->outError(__d('gl', "メールアドレスが正しくありません。"));
            }
            $this->redirect($this->referer());
        }
        else {
            $this->layout = LAYOUT_ONE_COLUMN;
        }
    }

    function download_add_members_csv_format()
    {
        $this->request->allowMethod('post');
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));

        $this->layout = false;
        $filename = 'add_member_csv_format';
        //見出し
        $th = [
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
        $td = [];
        $this->set(compact('filename', 'th', 'td'));
    }

    function download_team_members_csv()
    {
        $this->request->allowMethod('post');
        $team_id = $this->Session->read('current_team_id');
        $this->Team->TeamMember->adminCheck($team_id, $this->Auth->user('id'));
        $this->layout = false;
        $filename = 'team_members_' . date('YmdHis');

        //見出し
        $th = [
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

}
