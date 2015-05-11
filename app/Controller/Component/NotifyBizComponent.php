<?php
App::uses('ModelType', 'Model');

/**
 * @author daikihirakata
 * @property SessionComponent $Session
 * @property RedisComponent   $Redis
 * @property AuthComponent    $Auth
 * @property GlEmailComponent $GlEmail
 * @property NotifySetting    $NotifySetting
 * @property Post             $Post
 * @property Goal             $Goal
 */
class NotifyBizComponent extends Component
{

    public $name = "NotifyBiz";

    public $components = [
        'Auth',
        'Session',
        'GlEmail',
        'Redis',
    ];

    public $notify_option = [
        'url_data'    => null,
        'count_num'   => 1,
        'notify_type' => null,
        'model_id'    => null,
        'item_name'   => null,
    ];
    public $notify_settings = [];

    private $initialized = false;

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        parent::__construct($collection, $settings);

    }

    public function initialize(Controller $controller)
    {
        $this->startup($controller);
        $this->initialized = true;
    }

    public function startup(Controller $controller)
    {
        if (!$this->initialized) {
            CakeSession::start();
            $this->NotifySetting = ClassRegistry::init('NotifySetting');
            $this->Post = ClassRegistry::init('Post');
            $this->Goal = ClassRegistry::init('Goal');
            $this->GlEmail->startup($controller);
        }
    }

    /**
     * @param      $notify_type
     * @param      $model_id
     * @param null $sub_model_id
     * @param null $to_user_list
     * @param      $user_id
     * @param      $team_id
     */
    function sendNotify($notify_type, $model_id, $sub_model_id = null, $to_user_list = null, $user_id, $team_id)
    {
        $this->notify_option['from_user_id'] = $user_id;
        $this->_setModelProperty($user_id, $team_id);

        switch ($notify_type) {
            case NotifySetting::TYPE_FEED_POST:
                $this->_setFeedPostOption($model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST:
                $this->_setFeedCommentedOnMyPostOption($model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $this->_setFeedCommentedOnMyCommentedPostOption($model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_CIRCLE_USER_JOIN:
                $this->_setCircleUserJoinOption($model_id);
                break;
            case NotifySetting::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING:
                $this->_setCircleChangePrivacyOption($model_id);
                break;
            case NotifySetting::TYPE_CIRCLE_ADD_USER:
                $this->_setCircleAddUserOption($model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_MY_GOAL_FOLLOW:
                $this->_setMyGoalFollowOption($model_id);
                break;
            case NotifySetting::TYPE_MY_GOAL_COLLABORATE:
                $this->_setMyGoalCollaborateOption($model_id, $user_id);
                break;
            case NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER:
                $this->_setMyGoalChangedOption($model_id, $user_id);
                break;
            case NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION:
                $this->_setMyGoalTargetFroEvaluationOption($model_id, $to_user_list);
                break;
            default:
                break;
        }
        //通常のアプリ通知データ保存
        $this->_saveNotifications();

        //通常の通知メール送信
        $this->_sendNotifyEmail();
    }

    public function push($socketId, $share)
    {
        if (!$socketId) {
            return;
        }

        $teamId = $this->Session->read('current_team_id');
        $channelName = $share . "_team_" . $teamId;

        // アクション投稿のケース
        if (strpos($share, "goal") !== false) {
            $feedType = "goal";
        }
        // サークル投稿のケース
        else {
            if (strpos($share, "circle") !== false) {
                $feedType = $share;
                // ユーザー向け投稿のケース
            }
            else {
                if (strpos($share, "user") !== false) {
                    $feedType = "all";
                }
                // その他
                else {
                    $channelName = "team_all_" . $teamId;
                    $feedType = "all";
                }
            }
        }

        // レスポンスデータの定義
        $notifyId = Security::hash(time());
        $data = [
            'is_feed_notify' => true,
            'feed_type'      => $feedType,
            'notify_id'      => $notifyId
        ];

        // push
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger($channelName, 'post_feed', $data, $socketId);
    }

    public function bellPush($socketId, $channelName, $data)
    {
        // push
        if (!$socketId || !$channelName || !$data) {
            return;
        }
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger($channelName, 'post_feed', $data, $socketId);
    }

    public function commentPush($socketId, $data)
    {
        // push
        if (!$socketId || !$data) {
            return;
        }
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger("team_all_" . $this->Session->read('current_team_id'), 'post_feed', $data, $socketId);
    }

    private function _setModelProperty($user_id, $team_id)
    {
        $this->Post->my_uid
            = $this->Post->Comment->my_uid
            = $this->Post->PostShareCircle->my_uid
            = $this->Post->PostShareUser->my_uid
            = $this->Post->Team->TeamMember->my_uid
            = $this->Post->User->CircleMember->my_uid
            = $this->Goal->my_uid
            = $this->Goal->Collaborator->my_uid
            = $this->NotifySetting->my_uid
            = $this->NotifySetting->my_uid
            = $this->GlEmail->SendMail->my_uid
            = $this->GlEmail->SendMail->SendMailToUser->my_uid
            = $user_id;

        $this->Post->current_team_id
            = $this->Post->Comment->current_team_id
            = $this->Post->PostShareCircle->current_team_id
            = $this->Post->PostShareUser->current_team_id
            = $this->Post->Team->TeamMember->current_team_id
            = $this->Post->User->CircleMember->current_team_id
            = $this->Goal->current_team_id
            = $this->Goal->Collaborator->current_team_id
            = $this->NotifySetting->current_team_id
            = $this->NotifySetting->current_team_id
            = $this->GlEmail->SendMail->current_team_id
            = $this->GlEmail->SendMail->SendMailToUser->current_team_id
            = $team_id;
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
                                                                                NotifySetting::TYPE_FEED_POST);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_POST;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($post['Post']['body']) ?
            json_encode([trim($post['Post']['body'])]) : null;
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
        //宛先は自分以外のサークル管理者
        $circle_member_list = $this->Post->User->CircleMember->getAdminMemberList($circle_id);
        if (empty($circle_member_list)) {
            return;
        }
        $circle = $this->Post->User->CircleMember->Circle->findById($circle_id);
        if (empty($circle)) {
            return;
        }
        //サークルメンバーの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($circle_member_list,
                                                                                NotifySetting::TYPE_CIRCLE_USER_JOIN);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_CIRCLE_USER_JOIN;
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
                                                                                NotifySetting::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING;
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
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($user_id,
                                                                                NotifySetting::TYPE_CIRCLE_ADD_USER);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_CIRCLE_ADD_USER;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name']]);
    }

    /**
     * 自分がオーナーのゴールがフォローされたときのオプション
     *
     * @param $goal_id
     */
    private function _setMyGoalFollowOption($goal_id)
    {
        $goal = $this->Goal->getGoal($goal_id);
        if (empty($goal)) {
            return;
        }
        $collaborators = $this->Goal->Collaborator->getCollaboratorListByGoalId($goal_id);
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($collaborators,
                                                                                NotifySetting::TYPE_MY_GOAL_FOLLOW);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_FOLLOW;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'index', 'team_id' => $this->NotifySetting->current_team_id];//TODO In the future, goal detail page.
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
    }

    /**
     * 自分がオーナーのゴールがコラボされたときのオプション
     *
     * @param $goal_id
     * @param $user_id
     */
    private function _setMyGoalCollaborateOption($goal_id, $user_id)
    {
        $goal = $this->Goal->getGoal($goal_id);
        if (empty($goal)) {
            return;
        }
        $collaborators = $this->Goal->Collaborator->getCollaboratorListByGoalId($goal_id);
        //exclude me
        unset($collaborators[$user_id]);
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($collaborators,
                                                                                NotifySetting::TYPE_MY_GOAL_COLLABORATE);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_COLLABORATE;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'index', 'team_id' => $this->NotifySetting->current_team_id];//TODO In the future, goal detail page.
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
    }

    /**
     * 自分がオーナーのゴールがリーダーによって変更されたときのオプション
     *
     * @param $goal_id
     * @param $user_id
     */
    private function _setMyGoalChangedOption($goal_id, $user_id)
    {
        $goal = $this->Goal->getGoal($goal_id);
        if (empty($goal)) {
            return;
        }
        $collaborators = $this->Goal->Collaborator->getCollaboratorListByGoalId($goal_id);
        //exclude me
        unset($collaborators[$user_id]);
        if (empty($collaborators)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($collaborators,
                                                                                NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'index', 'team_id' => $this->NotifySetting->current_team_id];//TODO In the future, goal detail page.
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
    }

    /**
     * 自分がオーナーのゴールが評価対象となったときのオプション
     *
     * @param $goal_id
     * @param $to_user_id
     */
    private function _setMyGoalTargetFroEvaluationOption($goal_id, $to_user_id)
    {
        $goal = $this->Goal->getGoal($goal_id);
        if (empty($goal)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($to_user_id,
                                                                                NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION;
        $this->notify_option['url_data'] = ['controller' => 'goal_approval', 'action' => 'done', 'team_id' => $this->NotifySetting->current_team_id];
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
    }

    /**
     * 自分のコメントした投稿にコメントがあった場合のオプション取得
     *
     * @param $post_id
     * @param $comment_id
     */
    private function _setFeedCommentedOnMyCommentedPostOption($post_id, $comment_id)
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
        //通知対象者の通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($commented_user_list,
                                                                                NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST);
        $comment = $this->Post->Comment->read(null, $comment_id);

        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST;
        $this->notify_option['count_num'] = count($commented_user_list);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([trim($comment['Comment']['body'])]) : null;
    }

    /**
     * 自分の投稿にコメントがあった場合のオプション取得
     *
     * @param $post_id
     * @param $comment_id
     */
    private function _setFeedCommentedOnMyPostOption($post_id, $comment_id)
    {
        //宛先は投稿主
        $post = $this->Post->findById($post_id);
        if (empty($post)) {
            return;
        }
        //自分の投稿へのコメントの場合は処理しない
        if ($post['Post']['user_id'] == $this->NotifySetting->my_uid) {
            return;
        }
        //通知対象者の通知設定確認
        $this->notify_settings = $this->NotifySetting->getAppEmailNotifySetting($post['Post']['user_id'],
                                                                                NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST);
        $comment = $this->Post->Comment->read(null, $comment_id);

        $this->notify_option['to_user_id'] = $post['Post']['user_id'];
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST;
        $this->notify_option['count_num'] = $this->Post->Comment->getCountCommentUniqueUser($post_id,
                                                                                            [$post['Post']['user_id']]);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([trim($comment['Comment']['body'])]) : null;
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
        //to be short text
        $item = json_decode($this->notify_option['item_name']);
        foreach ($item as $k => $v) {
            $item[$k] = mb_strimwidth($v, 0, 40, "...");
        }
        $item = json_encode($item);
        //TODO save to redis.
        $this->Redis->setNotifications(
            $this->notify_option['notify_type'],
            $this->NotifySetting->current_team_id,
            $uids,
            $this->NotifySetting->my_uid,
            $item,
            $this->notify_option['url_data'],
            microtime(true)
        );
        return true;
    }

    private function _getSendNotifyUserList()
    {
        //メール通知onのユーザを取得
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['email']) {
                $uids[] = $user_id;
            }
        }
        return $uids;
    }

    private function _sendNotifyEmail()
    {
        $uids = $this->_getSendNotifyUserList();
        $this->GlEmail->sendMailNotify($this->notify_option, $uids);
    }

    /**
     * execコマンドにて通知を行う
     *
     * @param       $type
     * @param       $model_id
     * @param       $sub_model_id
     * @param array $to_user_list json_encodeしてbase64_encodeする
     */
    public function execSendNotify($type, $model_id, $sub_model_id = null, $to_user_list = null)
    {
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
        if ($sub_model_id) {
            $cmd .= " -n " . $sub_model_id;
        }
        if ($to_user_list) {
            $to_user_list = base64_encode(json_encode($to_user_list));
            $cmd .= " -u " . $to_user_list;
        }
        $cmd .= " -b " . Router::fullBaseUrl();
        $cmd .= " -i " . $this->Auth->user('id');
        $cmd .= " -o " . $this->Session->read('current_team_id');
        $cmd_end = " > /dev/null &";
        $all_cmd = $set_web_env . $nohup . $cake_cmd . $cake_app . $cmd . $cmd_end;
        exec($all_cmd);
    }

    /**
     * get notifications form redis.
     * return value like this.
     * $array = [
     * [
     * 'User'         => [
     * 'id'               => 1,
     * 'display_username' => 'test taro',
     * 'photo_file_name'  => null,
     * ],
     * 'Notification' => [
     * 'title'      => 'test taroさんがあなたの投稿にコメントしました。',
     * 'url'        => 'http://192.168.50.4/post_permanent/1/from_notification:1',
     * 'unread_flg' => false,
     * 'created'    => '1429643033',
     * ]
     * ],
     * [
     * 'User'         => [
     * 'id'               => 2,
     * 'display_username' => 'test jiro',
     * 'photo_file_name'  => null,
     * ],
     * 'Notification' => [
     * 'title'      => 'test jiroさんがあなたの投稿にコメントしました。',
     * 'url'        => 'http://192.168.50.4/post_permanent/2/from_notification:1',
     * 'unread_flg' => false,
     * 'created'    => '1429643033',
     * ]
     * ],
     * ];
     *
     * @param null|int $limit
     * @param null|int $from_date
     *
     * @return array
     */
    function getNotification($limit = null, $from_date = null)
    {
        $notify_from_redis = $this->Redis->getNotifications(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid,
            $limit,
            $from_date
        );
        if (empty($notify_from_redis)) {
            return [];
        }
        $data = [];
        foreach ($notify_from_redis as $v) {
            $data[]['Notification'] = $v;
        }
        //fetch User
        $user_list = Hash::extract($notify_from_redis, '{n}.user_id');
        $users = Hash::combine($this->NotifySetting->User->getUsersProf($user_list), '{n}.User.id', '{n}');
        //merge users to notification data
        foreach ($data as $k => $v) {
            $data[$k] = array_merge($data[$k], $users[$v['Notification']['user_id']]);
            //get title
            $title = $this->NotifySetting->getTitle($data[$k]['Notification']['type'],
                                                    $data[$k]['User']['display_username'], 1,
                                                    $data[$k]['Notification']['body']);
            $data[$k]['Notification']['title'] = $title;
        }
        return $data;
    }

    /**
     * set notifications
     *
     * @param array|int $to_user_ids
     * @param int       $type
     * @param string    $url
     * @param string    $body
     *
     * @return bool
     */
    function setNotifications($to_user_ids, $type, $url, $body = null)
    {
        $this->Redis->setNotifications(
            $type,
            $this->NotifySetting->current_team_id,
            $to_user_ids,
            $this->NotifySetting->my_uid,
            $body,
            $url
        );
        return true;
    }

    /**
     * get count of new notifications from redis.
     *
     * @return int
     */
    function getCountNewNotification()
    {
        return $this->Redis->getCountOfNewNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );
    }

    /**
     * delete count of new notifications form redis.
     *
     * @return bool
     */
    function resetCountNewNotification()
    {
        return $this->Redis->deleteCountOfNewNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );
    }

    /**
     * change read status of notification.
     *
     * @param int $notify_id
     *
     * @return bool
     */
    function changeReadStatusNotification($notify_id)
    {
        return $this->Redis->changeReadStatusOfNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid,
            $notify_id
        );
    }
}
