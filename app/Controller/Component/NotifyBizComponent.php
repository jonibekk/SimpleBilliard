<?php
App::uses('ModelType', 'Model');

/**
 * @author daikihirakata
 * @property SessionComponent $Session
 * @property AuthComponent    $Auth
 * @property GlEmailComponent $GlEmail
 * @property Notification     $Notification
 * @property NotifySetting    $NotifySetting
 * @property Post             $Post
 */
class NotifyBizComponent extends Component
{

    public $name = "NotifyBiz";

    public $components = [
        'Auth',
        'Session',
        'GlEmail'
    ];

    public $notify_option = [
        'url_data'    => null,
        'count_num' => 1,
        'notify_type' => null,
        'model_id'    => null,
        'item_name'   => null,
    ];
    public $notify_settings = [];

    public $has_send_mail_interval_time = true;

    public $is_one_on_one_notify = false;

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $this->Notification = ClassRegistry::init('Notification');
        $this->NotifySetting = ClassRegistry::init('NotifySetting');
        $this->Post = ClassRegistry::init('Post');

        parent::__construct($collection, $settings);
    }

    function sendNotify($notify_type, $model_id, $to_user_list = null)
    {
        $this->notify_option['from_user_id'] = $this->Auth->user('id');
        switch ($notify_type) {
            case Notification::TYPE_FEED_POST:
                $this->is_one_on_one_notify = true;
                $this->_setFeedPostOption($model_id);
                break;
            case Notification::TYPE_FEED_COMMENTED_ON_MY_POST:
                $this->_setFeedCommentedOnMyPostOption($model_id);
                break;
            case Notification::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $this->_setFeedCommentedOnMyCommentedPostOption($model_id);
                break;
            case Notification::TYPE_CIRCLE_USER_JOIN:
                $this->_setCircleUserJoinOption($model_id);
                break;
            case Notification::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                $this->is_one_on_one_notify = true;
                $this->has_send_mail_interval_time = false;
                $this->_setCircleChangePrivacyOption($model_id);
                break;
            case Notification::TYPE_CIRCLE_ADD_USER:
                $this->has_send_mail_interval_time = false;
                $this->is_one_on_one_notify = true;
                $this->_setCircleAddUserOption($model_id, $to_user_list);
                break;
            default:
                break;
        }
        if ($this->is_one_on_one_notify) {
            //ユーザ個別ののアプリ通知データ保存
            $notify_ids = $this->_saveOneOnOneNotifications();
            //ユーザ個別の通知メール送信
            $this->_sendOneOnOneNotifyEmail($notify_ids);
        }
        else {
            //通常のアプリ通知データ保存
            $this->_saveNotifications();
            //通常の通知メール送信
            $this->_sendNotifyEmail();
        }
    }

    /**
     * 自分が閲覧可能な投稿があった場合
     *
     * @param $post_id
     *
     * @throws RuntimeException
     */
    private function _setFeedPostOption($post_id)
    {
        $post = $this->Post->findById($post_id);
        if (empty($post)) {
            return;
        }
        //宛先は閲覧可能な全ユーザ
        $members = $this->Post->getShareAllMemberList($post_id);

        //対象ユーザの通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($members,
                                                                                NotifySetting::TYPE_FEED);
        $this->notify_option['notify_type'] = Notification::TYPE_FEED_POST;
        $this->notify_option['url_data'] = "/";
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($post['Post']['body']) ?
            json_encode([mb_strimwidth($post['Post']['body'], 0, 40, "...")]) : null;
    }

    /**
     * 自分の所属するサークルにメンバーが参加した時の通知
     *
     * @param $circle_id
     *
     * @internal param $post_id
     */
    private function _setCircleUserJoinOption($circle_id)
    {
        //宛先は自分以外のサークルメンバー
        $circle_member_list = $this->Post->User->CircleMember->getMemberList($circle_id, true, false);
        if (empty($circle_member_list)) {
            return;
        }
        $circle = $this->Post->User->CircleMember->Circle->findById($circle_id);
        if (empty($circle)) {
            return;
        }
        //サークルメンバーの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($circle_member_list,
                                                                                NotifySetting::TYPE_CIRCLE);
        $this->notify_option['notify_type'] = Notification::TYPE_CIRCLE_USER_JOIN;
        //通知先ユーザ分を-1
        $this->notify_option['count_num'] = count($circle_member_list);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name']]);
    }

    /**
     * 自分の所属するのプライバシー設定が変更になったとき
     *
     * @param $circle_id
     *
     * @internal param $post_id
     */
    private function _setCircleChangePrivacyOption($circle_id)
    {
        //宛先は自分以外のサークルメンバー
        $circle_member_list = $this->Post->User->CircleMember->getMemberList($circle_id, true, false);
        if (empty($circle_member_list)) {
            return;
        }
        $circle = $this->Post->User->CircleMember->Circle->findById($circle_id);
        if (empty($circle)) {
            return;
        }
        $privacy_name = Circle::$TYPE_PUBLIC[$circle['Circle']['public_flg']];
        //サークルメンバーの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($circle_member_list,
                                                                                NotifySetting::TYPE_CIRCLE);
        $this->notify_option['notify_type'] = Notification::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name'], $privacy_name]);
    }

    /**
     * 管理者が自分をサークルに参加させたときのオプション
     *
     * @param $circle_id
     * @param $user_id
     *
     * @internal param $post_id
     */
    private function _setCircleAddUserOption($circle_id, $user_id)
    {
        $circle = $this->Post->User->CircleMember->Circle->findById($circle_id);
        if (empty($circle)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($user_id, NotifySetting::TYPE_CIRCLE);
        $this->notify_option['notify_type'] = Notification::TYPE_CIRCLE_ADD_USER;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name']]);
    }

    /**
     * 自分のコメントした投稿にコメントがあった場合のオプション取得
     *
     * @param $post_id
     *
     * @throws RuntimeException
     */
    private function _setFeedCommentedOnMyCommentedPostOption($post_id)
    {
        //宛先は自分以外のコメント主(投稿主ものぞく)
        $commented_user_list = $this->Post->Comment->getCommentedUniqueUsersList($post_id);
        if (empty($commented_user_list)) {
            return;
        }
        $post = $this->Post->findById($post_id);
        if (empty($post)) {
            return;
        }
        //投稿主を除外
        unset($commented_user_list[$post['Post']['user_id']]);
        if (empty($commented_user_list)) {
            return;
        }
        //コメント主の通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($commented_user_list,
                                                                                NotifySetting::TYPE_FEED);
        $comment = $this->Post->Comment->read();

        $this->notify_option['notify_type'] = Notification::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST;
        $this->notify_option['count_num'] = count($commented_user_list);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([mb_strimwidth($comment['Comment']['body'], 0, 40, "...")]) : null;
    }

    /**
     * 自分の投稿にコメントがあった場合のオプション取得
     *
     * @param $post_id
     *
     * @throws RuntimeException
     */
    private function _setFeedCommentedOnMyPostOption($post_id)
    {
        //宛先は投稿主
        $post = $this->Post->findById($post_id);
        if (empty($post)) {
            return;
        }
        //自分の投稿へのコメントの場合は処理しない
        if ($post['Post']['user_id'] == $this->Notification->me['id']) {
            return;
        }
        //投稿主の通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($post['Post']['user_id'],
                                                                                NotifySetting::TYPE_FEED);
        $comment = $this->Post->Comment->read();

        $this->notify_option['to_user_id'] = $post['Post']['user_id'];
        $this->notify_option['notify_type'] = Notification::TYPE_FEED_COMMENTED_ON_MY_POST;
        $this->notify_option['count_num'] = $this->Post->Comment->getCountCommentUniqueUser($post_id,
                                                                                            [$post['Post']['user_id']]);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([mb_strimwidth($comment['Comment']['body'], 0, 40, "...")]) : null;
        $this->notify_option['app_notify_enable'] = $this->notify_settings[$post['Post']['user_id']]['app'];
    }

    private function _saveNotifications()
    {
        //通知onのユーザを取得
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['app']) {
                $uids[] = $user_id;
            }
        }
        if (empty($uids)) {
            return;
        }
        $data = [
            'team_id'   => $this->Notification->current_team_id,
            'type'      => $this->notify_option['notify_type'],
            'model_id'  => $this->notify_option['model_id'],
            'url_data'  => json_encode($this->notify_option['url_data']),
            'count_num' => $this->notify_option['count_num'],
            'item_name' => $this->notify_option['item_name'],
        ];
        $this->Notification->saveNotify($data, $uids);
    }

    private function _saveOneOnOneNotifications()
    {
        //通知onのユーザを取得
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['app']) {
                $uids[] = $user_id;
            }
        }
        if (empty($uids)) {
            return [];
        }
        $data = [
            'team_id'   => $this->Notification->current_team_id,
            'type'      => $this->notify_option['notify_type'],
            'model_id'  => $this->notify_option['model_id'],
            'count_num' => $this->notify_option['count_num'],
            'url_data'  => json_encode($this->notify_option['url_data']),
            'item_name' => $this->notify_option['item_name'],
        ];
        $res = $this->Notification->saveNotifyOneOnOne($data, $uids);
        return $res;
    }

    private function _getSendNotifyUserList($notify_ids)
    {
        //メール通知onのユーザを取得
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['email']) {
                $uids[] = $user_id;
            }
        }
        if (empty($uids)) {
            return $uids;
        }
        //インターバルありの場合
        if ($this->has_send_mail_interval_time) {
            //送信できないユーザIDリスト
            $invalid_uids = $this->GlEmail->SendMail->SendMailToUser->getInvalidSendUserList($notify_ids);
            //送信できないユーザを除外
            foreach ($uids as $key => $val) {
                if (in_array($val, $invalid_uids)) {
                    unset($uids[$key]);
                }
            }
        }
        return $uids;
    }

    private function _sendNotifyEmail()
    {
        $uids = $this->_getSendNotifyUserList($this->Notification->id);
        $this->notify_option['notification_id'] = $this->Notification->id;
        $this->GlEmail->sendMailNotify($this->notify_option, $uids);
    }

    private function _sendOneOnOneNotifyEmail($notify_ids)
    {
        $uids = $this->_getSendNotifyUserList($notify_ids);

        $notify_to_users = $this->Notification->NotifyToUser->getNotifyIdUserIdList($notify_ids);
        foreach ($notify_to_users as $notification_id => $user_id) {
            if (!in_array($user_id, $uids)) {
                continue;
            }
            $this->notify_option['notification_id'] = $notification_id;
            $this->GlEmail->sendMailNotify($this->notify_option, $user_id);
        }
    }

    /**
     * execコマンドにて通知を行う
     *
     * @param       $type
     * @param       $model_id
     * @param array $to_user_list json_encodeしてbase64_encodeする

     *
*@internal param $id
     */
    public function execSendNotify($type, $model_id, $to_user_list = null)
    {
        $session_id = $this->Session->id();
        $set_web_env = "";
        $nohup = "nohup ";
        $php = "/usr/bin/php ";
        $cake_cmd = $php . APP . "Console" . DS . "cake.php";
        $cake_app = " -app " . APP;
        $cmd = " notify";
        $cmd .= " -t " . $type;
        if ($model_id) {
            $cmd .= " -m " . $model_id;
        }
        if ($to_user_list) {
            $to_user_list = base64_encode(json_encode($to_user_list));
            $cmd .= " -u " . $to_user_list;
        }
        $cmd .= " -b " . Router::fullBaseUrl();
        $cmd .= " -s " . $session_id;
        $cmd_end = " > /dev/null &";
        $all_cmd = $set_web_env . $nohup . $cake_cmd . $cake_app . $cmd . $cmd_end;
        exec($all_cmd);
    }

}
