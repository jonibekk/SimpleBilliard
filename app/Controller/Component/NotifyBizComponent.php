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

    public $notify_option_default = [
        'from_user_id'      => null,
        'to_user_id'        => null,
        'url_data'          => null,
        'count_num'         => null,
        'notify_type'       => null,
        'model_id'          => null,
        'item_name'         => null,
        'app_notify_enable' => true,
        'notify_id'         => null,
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
        $this->notify_option_default['from_user_id'] = $this->Auth->user('id');

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
        $notify_option = $this->notify_option_default;
        $notify_option['notify_type'] = Notification::TYPE_FEED_POST;
        $notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $notify_option['model_id'] = null;
        $notify_option['item_name'] = !empty($post['Post']['body']) ?
            mb_strimwidth($post['Post']['body'], 0, 40, "...") : null;

        foreach ($members as $user_id) {
            $notify_option['app_notify_enable'] = $this->notify_settings[$user_id]['app'];
            $notify_option['to_user_id'] = $user_id;
            $this->notify_options[] = $notify_option;
        }
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
        $notify_option = $this->notify_option_default;
        $notify_option['notify_type'] = Notification::TYPE_CIRCLE_USER_JOIN;
        //通知先ユーザ分を-1
        $notify_option['count_num'] = count($circle_member_list) - 1;
        $notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $notify_option['model_id'] = $circle_id;
        $notify_option['item_name'] = $circle['Circle']['name'];

        foreach ($circle_member_list as $user_id) {
            $notify_option['app_notify_enable'] = $this->notify_settings[$user_id]['app'];
            $notify_option['to_user_id'] = $user_id;
            $this->notify_options[] = $notify_option;
        }
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

        $notify_option = $this->notify_option_default;
        $notify_option['notify_type'] = Notification::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST;
        $notify_option['count_num'] = count($commented_user_list) - 1;
        $notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $notify_option['model_id'] = $post_id;
        $notify_option['item_name'] = !empty($comment) ?
            mb_strimwidth($comment['Comment']['body'], 0, 40, "...") : null;

        foreach ($commented_user_list as $user_id) {
            $notify_option['app_notify_enable'] = $this->notify_settings[$user_id]['app'];
            $notify_option['to_user_id'] = $user_id;
            $this->notify_options[] = $notify_option;
        }
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

        $notify_option = $this->notify_option_default;
        $notify_option['to_user_id'] = $post['Post']['user_id'];
        $notify_option['notify_type'] = Notification::TYPE_FEED_COMMENTED_ON_MY_POST;
        $notify_option['count_num'] = $this->Post->Comment->getCountCommentUniqueUser($post_id,
                                                                                      [$this->Post->me['id'], $post['Post']['user_id']]);
        $notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $notify_option['model_id'] = $post_id;
        $notify_option['item_name'] = !empty($comment) ?
            mb_strimwidth($comment['Comment']['body'], 0, 40, "...") : null;
        $notify_option['app_notify_enable'] = $this->notify_settings[$post['Post']['user_id']]['app'];
        $this->notify_options[] = $notify_option;
    }

    private function _saveNotifications()
    {
        if (empty($this->notify_options)) {
            return;
        }
        //Notification用データに変換して保存
        foreach ($this->notify_options as $key => $option) {
            $data = [
                'user_id'      => $option['to_user_id'],
                'team_id'      => $this->Notification->current_team_id,
                'type'         => $option['notify_type'],
                'from_user_id' => $option['from_user_id'],
                'model_id'     => $option['model_id'],
                'url_data'     => json_encode($option['url_data']),
                'count_num'    => $option['count_num'],
                'item_name'    => $option['item_name'],
                'enable_flg'   => $option['app_notify_enable'],
            ];
            $res = $this->Notification->saveNotify($data);
            $this->notify_options[$key]['notification_id'] = $res['Notification']['id'];
        }
    }

    private function _sendNotifyEmail()
    {
        if (empty($this->notify_settings) || empty($this->notify_options)) {
            return;
        }
        $this->log($this->notify_options);
        foreach ($this->notify_options as $option) {
            //メール送信offの場合は処理しない
            if (!$this->notify_settings[$option['to_user_id']]['email']) {
                continue;
            }
            $this->Controller->GlEmail->sendMailNotify($option);
        }
    }

}
