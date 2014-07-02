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

    public function invite()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
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
                    /** @noinspection PhpVoidFunctionResultUsedInspection */
                    $this->redirect('/');
                }
                else {
                    //１件も送信していない場合は既にチームに参加済みのユーザの為、再入力
                    $this->Pnotify->outError(__d('gl', "入力した全てのメールアドレスのユーザは既にチームに参加している為、メール送信をキャンセルしました。"));
                }
            }
        }
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
        $this->_switchTerm($team_id, $this->Auth->user('id'));
        $this->Pnotify->outSuccess(__d('gl', "チームを「%s」に切り換えました。", $my_teams[$team_id]));
        return $this->render();
    }

}
