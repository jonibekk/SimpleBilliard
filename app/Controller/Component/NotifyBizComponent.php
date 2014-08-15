<?php
App::uses('ModelType', 'Model');

/**
 * @author daikihirakata
 * @property GlEmailComponent $GlEmail
 */
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

    public $components = [
        'GlEmailComponent',
    ];

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

    public $notify_option_default = [
        'from_user_id' => null,
        'to_user_id'   => null,
        'url_data'     => null,
        'count_num'    => null,
        'notify_type'  => null,
        'model_id'  => null,
        'item_name' => null,
    ];

    public $notify_options = [];

    function sendNotify($notify_type, $model_id)
    {
        $this->notify_option_default['from_user_id'] = $this->Auth->user('id');

        switch ($notify_type) {
            case Notification::TYPE_FEED_COMMENTED_ON_MY_POST:
                $this->setFeedCommentedOnMyPostOption($model_id);
                break;
            case Notification::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                break;
            case Notification::TYPE_CIRCLE_USER_JOIN:
                break;
            case Notification::TYPE_CIRCLE_POSTED_ON_MY_CIRCLE:
                break;
            case Notification::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                break;
            default:
                break;
        }
        //アプリ通知データ保存

        //メール送信
    }

    /**
     * 自分の投稿にコメントがあった場合のオプション取得
     *
     * @param $post_id
     *
     * @throws RuntimeException
     */
    function setFeedCommentedOnMyPostOption($post_id)
    {
        //宛先は投稿主
        $post = $this->Post->findById($post_id);
        if (empty($post)) {
            throw new RuntimeException();
        }
        $comment = $this->Post->Comment->read();
        $item_name = null;
        if (!empty($comment)) {
            $item_name = mb_strimwidth($comment['Comment']['body'], 0, 40, "...");
        }

        $notify_option = $this->notify_option_default;
        $notify_option['to_user_id'] = $post['Post']['user_id'];
        $notify_option['notify_type'] = Notification::TYPE_FEED_COMMENTED_ON_MY_POST;
        $notify_option['count_num'] = $post['Post']['comment_count'] - 1;
        $notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $notify_option['model_id'] = $post_id;
        $notify_option['item_name'] = $item_name;
        $this->notify_options[] = $notify_option;
    }

    function saveNotifications()
    {
        if (empty($this->notify_options)) {
            return;
        }
        //Notification用データに変換
        $datas = [];
        foreach ($this->notify_options as $option) {
            $data = [
                'user_id'      => $option['to_user_id'],
                'team_id'      => $this->Notification->current_team_id,
                'type'         => $option['notify_type'],
                'from_user_id' => $option['from_user_id'],
                'model_id'     => $option['model_id'],
                'url_data'     => json_encode($option['url_data']),
                'count_num'    => $option['count_num'],
                'item_name' => $option['item_name'],
            ];
            $datas[] = $data;
        }
        if ($this->Notification->saveAll($datas)) {

        }
    }

}
