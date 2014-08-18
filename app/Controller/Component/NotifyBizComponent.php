<?php
App::uses('ModelType', 'Model');

/**
 * @author daikihirakata
 */

/** @noinspection PhpDocSignatureInspection */
class NotifyBizComponent extends Object
{

    public $name = "NotifyBiz";

    /**
     * @var AppController
     */
    var $Controller;

    /**
     * @var SessionComponent
     */
    var $Session;

    /**
     * @var AuthComponent
     */
    var $Auth;

    /**
     * @var Notification
     */
    var $Notification;

    /**
     * @var NotifySetting
     */
    var $NotifySetting;
    /**
     * @var Post
     */
    var $Post;

    public $notify_option = [
        'from_user_id' => null,
        'url_data'     => null,
        'count_num'    => null,
        'notify_type'  => null,
        'model_id'     => null,
        'item_name'    => null,
    ];

    public $notify_options = [];

    public $notify_settings = [];

    function initialize()
    {
    }

    function startup(&$controller)
    {
        $this->Controller = $controller;
        $this->Auth = $this->Controller->Auth;
        $this->Session = $this->Controller->Session;

        ClassRegistry::init('Notification');
        $this->Notification = new Notification();
        ClassRegistry::init('NotifySetting');
        $this->NotifySetting = new NotifySetting();
        ClassRegistry::init('Post');
        $this->Post = new Post();
    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    function sendNotify($notify_type, $model_id)
    {
        $this->notify_option['from_user_id'] = $this->Auth->user('id');

        switch ($notify_type) {
            case Notification::TYPE_FEED_POST:
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
                break;
            default:
                break;
        }
        //アプリ通知データ保存
        $this->_saveNotifications();
        //メール送信
        $this->_sendNotifyEmail();
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
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($post['Post']['body']) ?
            mb_strimwidth($post['Post']['body'], 0, 40, "...") : null;
    }

    /**
     * 自分の所属するサークルにメンバーが参加した時の通知
     *
     * @param $post_id
     *
     * @throws RuntimeException
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
        //コメント主の通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($circle_member_list,
                                                                                NotifySetting::TYPE_CIRCLE);
        $this->notify_option['notify_type'] = Notification::TYPE_CIRCLE_USER_JOIN;
        //通知先ユーザ分を-1
        $this->notify_option['count_num'] = count($circle_member_list) - 1;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = $circle['Circle']['name'];
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
        $this->notify_option['count_num'] = count($commented_user_list) - 1;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            mb_strimwidth($comment['Comment']['body'], 0, 40, "...") : null;
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
        if ($post['Post']['user_id'] == $this->Auth->user('id')) {
            return;
        }
        //投稿主の通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($post['Post']['user_id'],
                                                                                NotifySetting::TYPE_FEED);
        $comment = $this->Post->Comment->read();

        $this->notify_option['to_user_id'] = $post['Post']['user_id'];
        $this->notify_option['notify_type'] = Notification::TYPE_FEED_COMMENTED_ON_MY_POST;
        $this->notify_option['count_num'] = $this->Post->Comment->getCountCommentUniqueUser($post_id,
                                                                                            [$this->Post->me['id'], $post['Post']['user_id']]);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            mb_strimwidth($comment['Comment']['body'], 0, 40, "...") : null;
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
            'team_id'      => $this->Notification->current_team_id,
            'type'         => $this->notify_option['notify_type'],
            'from_user_id' => $this->notify_option['from_user_id'],
            'model_id'     => $this->notify_option['model_id'],
            'url_data'     => json_encode($this->notify_option['url_data']),
            'count_num'    => $this->notify_option['count_num'],
            'item_name'    => $this->notify_option['item_name'],
        ];
        $this->Notification->saveNotify($data, $uids);
    }

    private function _sendNotifyEmail()
    {
        //メール通知onのユーザを取得
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['email']) {
                $uids[] = $user_id;
            }
        }
        if (empty($uids)) {
            return;
        }

        //送信できないユーザIDリスト
        $invalid_uids = $this->Controller->GlEmail->SendMail->SendMailToUser->getInvalidSendUserList($this->Notification->id);
        foreach ($uids as $key => $val) {
            if (in_array($val, $invalid_uids)) {
                unset($uids[$key]);
            }
        }
        if (empty($uids)) {
            return;
        }
        $this->notify_option['notification_id'] = $this->Notification->id;
        $this->Controller->GlEmail->sendMailNotify($this->notify_option, $uids);
    }

}
