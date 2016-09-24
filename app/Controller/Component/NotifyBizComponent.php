<?php
App::uses('ModelType', 'Model');

/**
 * @author daikihirakata
 * @property SessionComponent $Session
 * @property AuthComponent    $Auth
 * @property GlEmailComponent $GlEmail
 * @property NotifySetting    $NotifySetting
 * @property Post             $Post
 * @property Device           $Device
 * @property Comment          $Comment
 * @property Goal             $Goal
 * @property GlRedis          $GlRedis
 * @property Team             $Team
 * @property AppController    $Controller
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
        'post_id'     => null,
        'options'     => [],
    ];
    public $notify_settings = [];
    private $push_channels = [];

    private $initialized = false;

    const PUSHER_CHANNEL_TYPE_ALL_TEAM = 'team_all';
    const PUSHER_CHANNEL_TYPE_USER = 'user';
    const PUSHER_CHANNEL_TYPE_CIRCLE = 'circle';
    const PUSHER_CHANNEL_TYPE_GOAL = 'goal';

    private $pusher_channel_types = [
        self::PUSHER_CHANNEL_TYPE_ALL_TEAM,
        self::PUSHER_CHANNEL_TYPE_USER,
        self::PUSHER_CHANNEL_TYPE_CIRCLE,
        self::PUSHER_CHANNEL_TYPE_GOAL
    ];

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        parent::__construct($collection, $settings);

    }

    public function initialize(Controller $controller)
    {
        $this->Controller = $controller;
        $this->NotifySetting = ClassRegistry::init('NotifySetting');
        $this->Post = ClassRegistry::init('Post');
        $this->Comment = ClassRegistry::init('Comment');
        $this->Goal = ClassRegistry::init('Goal');
        $this->Team = ClassRegistry::init('Team');
        $this->Device = ClassRegistry::init('Device');
        $this->GlRedis = ClassRegistry::init('GlRedis');
        $this->initialized = true;
    }

    public function startup(Controller $controller)
    {
        if (!CakeSession::started()) {
            CakeSession::start();
        }
        if (!$this->initialized) {
            $this->initialize($controller);
        }
        $this->GlEmail->startup($controller);
    }

    /**
     * セットアップガイドの通知を送る
     *
     * @param $user_id
     * @param $messages
     * @param $urls
     */
    function sendSetupNotify($user_id, $messages, $urls)
    {
        // User notify settings
        $settings = $this->NotifySetting->getUserNotifySetting($user_id, NotifySetting::TYPE_SETUP_GUIDE);

        // Send by mail
        if ($user_allow_send_mail = $settings[$user_id]['email']) {
            $this->GlEmail->sendMailSetup($user_id, $messages['mail'], null);
        }

        // Send by push notification
        if ($user_allow_push_mobile_notify = $settings[$user_id]['mobile']) {
            $this->notify_settings = [$user_id => ['mobile' => true]];
            $this->notify_option['url'] = $urls['push'];
            $this->notify_option['message'] = $messages['push'];
            $this->notify_option['from_user_id'] = $user_id; // dummy
            $this->_sendPushNotify();
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
        $this->notify_option['options']['from_user_id'] = $user_id;
        $this->_setModelProperty($user_id, $team_id);

        switch ($notify_type) {
            case NotifySetting::TYPE_FEED_POST:
                $this->_setFeedPostOption($model_id);
                break;
            case NotifySetting::TYPE_FEED_MESSAGE:
                $this->_setFeedMessageOption($model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST:
                $this->_setFeedCommentedOnMineOption(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST, $model_id,
                    $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $this->_setFeedCommentedOnMyCommentedOption(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                    $model_id, $sub_model_id);
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
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_COACHEE_CREATE_GOAL:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_COACHEE_COLLABORATE_GOAL:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_COACHEE_CHANGE_ROLE:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_COACHEE_CHANGE_GOAL:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_EVALUATION_START:
                $this->_setForEvaluationAllUserOption($notify_type, $model_id, $user_id);
                break;
            case NotifySetting::TYPE_EVALUATION_FREEZE:
                $this->_setForEvaluationAllUserOption($notify_type, $model_id, $user_id);
                break;
            case NotifySetting::TYPE_EVALUATION_START_CAN_ONESELF:
                break;
            case NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR:
                $this->_setForNextEvaluatorOption($model_id);
                break;
            case NotifySetting::TYPE_EVALUATION_DONE_FINAL:
                $this->_setForEvaluationAllUserOption($notify_type, $model_id, $user_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION:
                $this->_setFeedCommentedOnMineOption(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_ACTION, $model_id,
                    $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION:
                $this->_setFeedCommentedOnMyCommentedOption(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_ACTION,
                    $model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_CAN_SEE_ACTION:
                $this->_setFeedActionOption($model_id);
                break;
            case NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM:
                $this->_setTeamJoinOption($model_id);
                break;
            default:
                break;
        }

        //通知するアイテムかどうかチェック
        if (!$this->_canNotify()) {
            return;
        }

        //通常のアプリ通知データ保存
        $this->_saveNotifications();

        //通常の通知メール送信
        $this->_sendNotifyEmail();

        //通常のアプリ向けPUSH通知
        $this->_sendPushNotify();
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
        } // サークル投稿のケース
        else {
            if (strpos($share, "circle") !== false) {
                $feedType = $share;
                // ユーザー向け投稿のケース
            } else {
                if (strpos($share, "user") !== false) {
                    $feedType = "all";
                } // その他
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

    public function pushUpdateCircleList($socketId, $share)
    {
        if (!$socketId) {
            return;
        }

        $teamId = $this->Session->read('current_team_id');
        $channelName = "team_" . $teamId;
        $circle_ids = [];
        if (in_array("public", $share)) {
            /** @var Circle $Circle */
            $Circle = ClassRegistry::init('Circle');
            $circle_ids[] = $Circle->getTeamAllCircleId();
        } else {
            // それ以外の場合は共有先の数だけ回す
            foreach ($share as $val) {
                if (strpos($val, "circle_") !== false) {
                    $circle_ids[] = str_replace("circle_", "", $val);
                }
            }
        }

        // レスポンスデータの定義
        $data = [
            'circle_ids' => $circle_ids,
        ];

        // push
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger($channelName, 'circle_list_update', $data, $socketId);
    }

    /**
     * 通知ベルPushのチャンネルをセット
     *
     * @param string        $channel_type
     * @param array|integer $item_ids
     */
    public function setBellPushChannels($channel_type, $item_ids = null)
    {
        //チャンネルタイプがチーム以外の場合は$item_idsが必須
        if ($channel_type != self::PUSHER_CHANNEL_TYPE_ALL_TEAM && !$item_ids) {
            return;
        }
        if (!is_array($item_ids)) {
            $item_ids = [$item_ids];
        }
        //チャンネルタイプが未定義だった場合はなにもしない
        if (!in_array($channel_type, $this->pusher_channel_types)) {
            return;
        }
        if ($channel_type == self::PUSHER_CHANNEL_TYPE_ALL_TEAM) {
            $this->push_channels[] = $channel_type . '_' . $this->NotifySetting->current_team_id;
        } else {
            foreach ($item_ids as $id) {
                $this->push_channels[] = $channel_type . '_' . $id . '_team_' . $this->NotifySetting->current_team_id;
            }
        }
    }

    /**
     * 通知ベルPush
     *
     * @param $from_user_id
     * @param $flag_name
     */
    public function bellPush($from_user_id, $flag_name)
    {
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $chunk_channels = array_chunk($this->push_channels, 100);
        $data = compact('from_user_id', 'flag_name');
        foreach ($chunk_channels as $channels) {
            $pusher->trigger($channels, 'bell_count', $data);
        }
    }

    /**
     * 通知ベルPush
     *
     * @param $from_user_id
     * @param $flag_name
     * @param $post_id
     */
    public function msgNotifyPush($from_user_id, $flag_name, $post_id)
    {
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $chunk_channels = array_chunk($this->push_channels, 100);
        $data = compact('from_user_id', 'flag_name', 'post_id');
        foreach ($chunk_channels as $channels) {
            $pusher->trigger($channels, 'msg_count', $data);
        }
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
            = $this->Goal->Follower->my_uid
            = $this->Goal->Team->my_uid
            = $this->Goal->Team->EvaluateTerm->my_uid
            = $this->Goal->Team->EvaluateTerm->Team->my_uid
            = $this->NotifySetting->my_uid
            = $this->GlEmail->SendMail->my_uid
            = $this->GlEmail->SendMail->SendMailToUser->my_uid
            = $this->Team->my_uid
            = $this->Team->TeamMember->my_uid
            = $this->Team->Invite->my_uid
            = $this->Team->Invite->FromUser->my_uid
            = $user_id;

        $this->Post->current_team_id
            = $this->Post->Comment->current_team_id
            = $this->Post->PostShareCircle->current_team_id
            = $this->Post->PostShareUser->current_team_id
            = $this->Post->Team->TeamMember->current_team_id
            = $this->Post->User->CircleMember->current_team_id
            = $this->Goal->current_team_id
            = $this->Goal->Collaborator->current_team_id
            = $this->Goal->Follower->current_team_id
            = $this->Goal->Team->current_team_id
            = $this->Goal->Team->EvaluateTerm->current_team_id
            = $this->Goal->Team->EvaluateTerm->Team->current_team_id
            = $this->NotifySetting->current_team_id
            = $this->GlEmail->SendMail->current_team_id
            = $this->GlEmail->SendMail->SendMailToUser->current_team_id
            = $this->Team->current_team_id
            = $this->Team->TeamMember->current_team_id
            = $this->Team->Invite->current_team_id
            = $this->Team->Invite->FromUser->current_team_id
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
        //exclude inactive users
        $members = array_intersect($members, $this->Team->TeamMember->getActiveTeamMembersList());

        // 共有した個人一覧
        $share_user_list = $this->Post->PostShareUser->getShareUserListByPost($post_id);
        //exclude inactive users
        $share_user_list = array_intersect($share_user_list, $this->Team->TeamMember->getActiveTeamMembersList());

        // 共有したサークル一覧
        $share_circle_list = $this->Post->PostShareCircle->getShareCircleList($post_id);

        // 共有されたサークルの通知設定が全てオフになっている場合は通知対象から外す
        if ($share_circle_list) {
            $enable_user_list = $this->Post->Circle->CircleMember->getNotificationEnableUserList($share_circle_list);
            foreach ($members as $k => $uid) {
                // 個人として共有されている場合は通知対象とするのでスルー
                if (isset($share_user_list[$uid])) {
                    continue;
                }
                // サークル通知設定がオンでない場合は、通知対象から外す
                if (!isset($enable_user_list[$uid])) {
                    unset($members[$k]);
                }
            }
        }

        //対象ユーザの通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($members,
            NotifySetting::TYPE_FEED_POST);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_POST;
        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => 'feed',
            'post_id'    => $post['Post']['id']
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($post['Post']['body']) ?
            json_encode([trim($post['Post']['body'])]) : null;
        $this->notify_option['options']['share_user_list'] = $share_user_list;
        $this->notify_option['options']['share_circle_list'] = $share_circle_list;

        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $share_user_list);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_CIRCLE, $share_circle_list);
    }

    /**
     * 自分が閲覧可能なメッセージがあった場合
     *
     * @param $post_id
     *
     * @throws RuntimeException
     */
    private function _setFeedMessageOption($post_id, $comment_id)
    {
        $post = $this->Post->findById($post_id);

        if (empty($post)) {
            return;
        }

        //基本的にnotifyにはメッセージについたコメントを表示するが、コメントが無ければ最初のメッセージ
        $body = $post['Post']['body'];
        if ($comment_id) {
            $comment = $this->Comment->findById($comment_id);
            if (!empty($comment_body = $comment['Comment']['body'])) {
                $body = $comment_body;
            }
        }

        //宛先は閲覧可能な全ユーザ
        $members = $this->Post->getShareAllMemberList($post_id);
        //exclude inactive users
        $members = array_intersect($members, $this->Team->TeamMember->getActiveTeamMembersList());

        //対象ユーザの通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($members,
            NotifySetting::TYPE_FEED_MESSAGE);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_MESSAGE;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'message#', $post['Post']['id']];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($body) ? json_encode([trim($body)]) : null;
        $this->notify_option['post_id'] = $post_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $members);

    }

    /**
     * 招待したユーザがチーム参加したときのオプション
     *
     * @param $invite_id
     */
    private function _setTeamJoinOption($invite_id)
    {
        //宛先は招待した人
        $invite = $this->Team->Invite->getInviteById($invite_id);
        if (!viaIsSet($invite['FromUser']['id']) || !viaIsSet($invite['Team']['name'])) {
            return;
        }
        //inactive user
        if (!$this->Team->TeamMember->isActive($invite['FromUser']['id'])) {
            return;
        }

        //対象ユーザの通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($invite['FromUser']['id'],
            NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_USER_JOINED_TO_INVITED_TEAM;
        $this->notify_option['url_data'] = [
            'controller' => 'users',
            'action'     => 'view_info',
            'user_id'    => $invite['ToUser']['id']
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([$invite['Team']['name']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $invite['FromUser']['id']);
    }

    /**
     * 自分が閲覧可能なアクションがあった場合
     *
     * @param $action_result_id
     */
    private function _setFeedActionOption($action_result_id)
    {
        $action = $this->Goal->ActionResult->findById($action_result_id);
        /** @noinspection PhpUndefinedMethodInspection */
        $post = $this->Post->findByActionResultId($action_result_id);
        if (empty($action)) {
            return;
        }
        $goal_id = $action['ActionResult']['goal_id'];
        //宛先は閲覧可能な全ユーザ
        //Collaborator
        $collaborators = $this->Goal->Collaborator->getCollaboratorListByGoalId($goal_id);
        //Follower
        $followers = $this->Goal->Follower->getFollowerListByGoalId($goal_id);
        //Coach
        $coach_id = $this->Team->TeamMember->getCoachId($this->Team->my_uid, $this->Team->current_team_id);
        //通知先に指定されたユーザ
        $share_members = $this->Post->getShareAllMemberList($post['Post']['id']);

        $members = $share_members + $collaborators + $followers + [$coach_id => $coach_id];
        //exclude inactive users
        $members = array_intersect($members, $this->Team->TeamMember->getActiveTeamMembersList());

        unset($members[$this->Team->my_uid]);

        //対象ユーザの通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($members,
            NotifySetting::TYPE_FEED_CAN_SEE_ACTION);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_CAN_SEE_ACTION;
        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => 'feed',
            'post_id'    => $post['Post']['id']
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($action['ActionResult']['name']) ?
            json_encode([trim($action['ActionResult']['name'])]) : null;
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goal_id);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $share_members);
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
        //exclude inactive users
        $circle_member_list = array_intersect($circle_member_list, $this->Team->TeamMember->getActiveTeamMembersList());
        $circle = $this->Post->User->CircleMember->Circle->findById($circle_id);
        if (empty($circle)) {
            return;
        }
        //サークルメンバーの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($circle_member_list,
            NotifySetting::TYPE_CIRCLE_USER_JOIN);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_CIRCLE_USER_JOIN;
        //通知先ユーザ分を-1
        $this->notify_option['count_num'] = count($circle_member_list);
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $circle_member_list);
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
        //exclude inactive users
        $circle_member_list = array_intersect($circle_member_list, $this->Team->TeamMember->getActiveTeamMembersList());
        $circle = $this->Post->User->CircleMember->Circle->findById($circle_id);
        if (empty($circle)) {
            return;
        }
        $privacy_name = Circle::$TYPE_PUBLIC[$circle['Circle']['public_flg']];
        //サークルメンバーの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($circle_member_list,
            NotifySetting::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_CIRCLE_CHANGED_PRIVACY_SETTING;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name'], $privacy_name]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_CIRCLE, $circle_id);

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
        //if inactive user
        if (!$this->Team->TeamMember->isActive($user_id)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($user_id,
            NotifySetting::TYPE_CIRCLE_ADD_USER);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_CIRCLE_ADD_USER;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'circle_id' => $circle_id];
        $this->notify_option['model_id'] = $circle_id;
        $this->notify_option['item_name'] = json_encode([$circle['Circle']['name']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $user_id);
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
        //exclude inactive users
        $collaborators = array_intersect($collaborators, $this->Team->TeamMember->getActiveTeamMembersList());
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($collaborators,
            NotifySetting::TYPE_MY_GOAL_FOLLOW);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_FOLLOW;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_info', 'goal_id' => $goal_id];
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goal_id);
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
        //exclude inactive users
        $collaborators = array_intersect($collaborators, $this->Team->TeamMember->getActiveTeamMembersList());
        //exclude me
        unset($collaborators[$user_id]);
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($collaborators,
            NotifySetting::TYPE_MY_GOAL_COLLABORATE);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_COLLABORATE;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_info', 'goal_id' => $goal_id];
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goal_id);
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
        //exclude inactive users
        $collaborators = array_intersect($collaborators, $this->Team->TeamMember->getActiveTeamMembersList());
        //exclude me
        unset($collaborators[$user_id]);
        if (empty($collaborators)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($collaborators,
            NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_CHANGED_BY_LEADER;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_info', 'goal_id' => $goal_id];
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goal_id);

    }

    /**
     * 認定通知オプション
     *
     * @param $notify_type
     * @param $goal_id
     * @param $to_user_id
     */
    private function _setApprovalOption($notify_type, $goal_id, $to_user_id)
    {
        $goal = $this->Goal->getGoal($goal_id);

        if (empty($goal)) {
            return;
        }
        if (isset($goal['MyCollabo'][0])) {
            $collaborator = $goal['MyCollabo'][0];
        } elseif (isset($goal['Leader'][0])) {
            $collaborator = $goal['Leader'][0];
        } else {
            return;
        }

        //inactive user
        if (!$this->Team->TeamMember->isActive($to_user_id)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($to_user_id, $notify_type);

        $url_goal_detail = ['controller' => 'goals', 'action' => 'view_info', 'goal_id' => $goal_id];
        $url_goal_approval = ['controller' => 'goals', 'action' => 'approval', 'detail', $collaborator['id']];

        //認定希望していないゴールはゴール詳細へ
        if (!$collaborator['is_wish_approval']) {
            $url = $url_goal_detail;
        } else {
            $url = $url_goal_approval;
        }
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['url_data'] = $url;
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $to_user_id);
    }

    /**
     * 次の評価者への通知オプション
     *
     * @param $evaluate_id
     */
    private function _setForNextEvaluatorOption($evaluate_id)
    {
        $evaluation = $this->Goal->Evaluation->findById($evaluate_id);
        //inactive user
        if (!$this->Team->TeamMember->isActive($evaluation['Evaluation']['evaluator_user_id'])) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($evaluation['Evaluation']['evaluator_user_id'],
            NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR);
        $evaluatee = $this->Goal->User->getUsersProf($evaluation['Evaluation']['evaluatee_user_id']);

        $url = [
            'controller'       => 'evaluations',
            'action'           => 'view',
            'evaluate_term_id' => $evaluation['Evaluation']['evaluate_term_id'],
            'user_id'          => $evaluation['Evaluation']['evaluatee_user_id'],
            'team_id'          => $this->NotifySetting->current_team_id
        ];

        $this->notify_option['from_user_id'] = null;
        $this->notify_option['notify_type'] = NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR;
        $this->notify_option['url_data'] = $url;
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([$evaluatee[0]['User']['display_username']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $evaluatee[0]['User']['id']);
    }

    /**
     * 評価関係者全員通知オプション
     *
     * @param $notify_type
     * @param $term_id
     * @param $user_id
     */
    private function _setForEvaluationAllUserOption($notify_type, $term_id, $user_id)
    {
        //対象ユーザはevaluatees
        $evaluatees = $this->Goal->Evaluation->getEvaluateeIdsByTermId($term_id);
        $evaluators = $this->Goal->Evaluation->getEvaluatorIdsByTermId($term_id);
        $to_user_ids = $evaluatees + $evaluators;
        //exclude inactive users
        $to_user_ids = array_intersect($to_user_ids, $this->Team->TeamMember->getActiveTeamMembersList());
        if (isset($to_user_ids[$user_id])) {
            unset($to_user_ids[$user_id]);
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($to_user_ids,
            $notify_type);

        $notify_list_url = [
            'controller' => 'evaluations',
            'action'     => 'index',
            'term'       => 'present',
            'team_id'    => $this->NotifySetting->current_team_id
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $team_name = $this->Goal->Team->findById($this->NotifySetting->current_team_id);

        $this->notify_option['from_user_id'] = null;
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['url_data'] = $notify_list_url;
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([$team_name['Team']['name']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_ALL_TEAM);
    }

    /**
     * 自分のコメントした投稿、アクションその他にコメントがあった場合のオプション取得
     *
     * @param $notify_type
     * @param $post_id
     * @param $comment_id
     */
    private function _setFeedCommentedOnMyCommentedOption($notify_type, $post_id, $comment_id)
    {
        //宛先は自分以外のコメント主(投稿主ものぞく)
        $commented_user_list = $this->Post->Comment->getCommentedUniqueUsersList($post_id);
        if (empty($commented_user_list)) {
            return;
        }
        //exclude inactive users
        $commented_user_list = array_intersect($commented_user_list,
            $this->Team->TeamMember->getActiveTeamMembersList());
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
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($commented_user_list,
            $notify_type);
        $comment = $this->Post->Comment->read(null, $comment_id);

        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['count_num'] = count($commented_user_list);
        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => 'feed',
            'post_id'    => $post['Post']['id']
        ];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([trim($comment['Comment']['body'])]) : null;
        $this->notify_option['options']['post_user_id'] = $post['Post']['user_id'];
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $commented_user_list);
    }

    /**
     * 自分の投稿、アクション、その他にコメントがあった場合のオプション取得
     *
     * @param $notify_type
     * @param $post_id
     * @param $comment_id
     */
    private function _setFeedCommentedOnMineOption($notify_type, $post_id, $comment_id)
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
        // if inactive user
        if (!$this->Team->TeamMember->isActive($post['Post']['user_id'])) {
            return;
        }
        //通知対象者の通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($post['Post']['user_id'],
            $notify_type);
        $comment = $this->Post->Comment->read(null, $comment_id);

        $this->notify_option['to_user_id'] = $post['Post']['user_id'];
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['count_num'] = $this->Post->Comment->getCountCommentUniqueUser($post_id,
            [$post['Post']['user_id']]);
        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => 'feed',
            'post_id'    => $post['Post']['id']
        ];
        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([trim($comment['Comment']['body'])]) : null;
        $this->notify_option['app_notify_enable'] = $this->notify_settings[$post['Post']['user_id']]['app'];
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $post['Post']['user_id']);
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
        $this->GlRedis->setNotifications(
            $this->notify_option['notify_type'],
            $this->NotifySetting->current_team_id,
            $uids,
            $this->notify_option['from_user_id'],
            $item,
            $this->notify_option['url_data'],
            microtime(true),
            $this->notify_option['post_id'],
            json_encode($this->notify_option['options'])
        );
        $flag_name = $this->NotifySetting->getFlagPrefixByType($this->notify_option['notify_type']) . '_app_flg';
        if ($this->notify_option['notify_type'] == NotifySetting::TYPE_FEED_MESSAGE) {
            $this->msgNotifyPush($this->notify_option['from_user_id'], $flag_name, $this->notify_option['post_id']);
        } else {
            $this->bellPush($this->notify_option['from_user_id'], $flag_name);
        }
        return true;
    }

    private function _getSendEmailNotifyUserList()
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

    /**
     * アプリプッシュ通知送信対象のユーザを取得
     *
     * @return array プッシュ通知送信対象のユーザーのリスト
     */
    private function _getSendMobileNotifyUserList()
    {
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['mobile']) {
                $uids[] = $user_id;
            }
        }
        return $uids;
    }

    private function _sendNotifyEmail()
    {
        $uids = $this->_getSendEmailNotifyUserList();
        $this->notify_option['style'] = 'plain';
        $this->GlEmail->sendMailNotify($this->notify_option, $uids);
    }

    /**
     * アプリ向けプッシュ通知送信
     *
     * @param string $app_key
     * @param string $client_key
     */
    private function _sendPushNotify($app_key = NCMB_APPLICATION_KEY, $client_key = NCMB_CLIENT_KEY)
    {
        if (!$app_key) {
            return;
        }
        $timestamp = $this->_getTimestamp();
        $signature = $this->_getNCMBSignature($timestamp, null, null, $app_key, $client_key);

        $header = array(
            'X-NCMB-Application-Key: ' . $app_key,
            'X-NCMB-Signature: ' . $signature,
            'X-NCMB-Timestamp: ' . $timestamp,
            'Content-Type: application/json'
        );

        $options = array(
            'http' => array(
                'ignore_errors' => true,    // APIリクエストの結果がエラーでもレスポンスボディを取得する
                'max_redirects' => 0,       // リダイレクトはしない
                'method'        => NCMB_REST_API_PUSH_METHOD
            )
        );

        $uids = $this->_getSendMobileNotifyUserList();
        if (empty($uids)) {
            return;
        }

        $this->notify_option['options']['style'] = 'plain';
        $original_lang = Configure::read('Config.language');

        $post_url = null;
        if (viaIsSet($this->notify_option['url'])) {
            $post_url = $this->notify_option['url'];
        } else {
            $post_url = Router::url($this->notify_option['url_data'], true);
        }

        $sent_device_tokens = [];

        foreach ($uids as $to_user_id) {

            $device_tokens = $this->Device->getDeviceTokens($to_user_id);
            if (empty($device_tokens)) {
                //このユーザーはスマホ持ってないのでスキップ
                continue;
            }

            // ひとつのデバイスが複数のユーザーで登録されている可能性があるので
            // 一度送ったデバイスに対して2度はPUSH通知は送らない
            foreach ($device_tokens as $key => $value) {
                if (array_search($value, $sent_device_tokens) !== false) {
                    unset($device_tokens[$key]);
                }
            }

            $this->_setLangByUserId($to_user_id, $original_lang);
            $from_user = $this->NotifySetting->User->getUsersProf($this->notify_option['from_user_id']);
            $from_user_name = $from_user[0]['User']['display_username'];

            // messageが設定されている場合は、それを優先して設定する。セットアップガイド用。
            $title = "";
            if (isset($this->notify_option['message'])) {
                $title = $this->notify_option['message'];
            } else {
                $title = $this->NotifySetting->getTitle($this->notify_option['notify_type'],
                    $from_user_name,
                    1,
                    $this->notify_option['item_name'],
                    $this->notify_option['options']);

                //メッセージの場合は本文も出ていたほうがいいので出してみる
                $item_name = json_decode($this->notify_option['item_name']);
                if (!empty($item_name)) {
                    $item_name = mb_strimwidth($item_name[0], 0, 40, "...");
                    $title .= " : " . $item_name;
                }
            }
            $title = json_encode($title, JSON_HEX_QUOT);

            $body = '{
                "immediateDeliveryFlag" : true,
                "target":["ios","android"],
                "searchCondition":{"deviceToken":{ "$inArray":["' . implode('","', $device_tokens) . '"]}},
                "message":' . $title . ',
                "userSettingValue":{"url":"' . $post_url . '"}},
                "deliveryExpirationTime":"1 day"
            }';
            $options['http']['content'] = $body;

            $header['content-length'] = 'Content-Length: ' . strlen($body);
            $options['http']['header'] = implode("\r\n", $header);

            $url = "https://" . NCMB_REST_API_FQDN . "/" . NCMB_REST_API_VER . "/" . NCMB_REST_API_PUSH;
            $ret = file_get_contents($url, false, stream_context_create($options));
            $sent_device_tokens = array_merge($sent_device_tokens, $device_tokens);
        }

        //変更したlangをログインユーザーのものに書き戻しておく
        $this->_setLang($original_lang);
    }

    /**
     *  指定されたuseridのlangをグローバルに設定する
     *  注意：使い終わったら元のlangに書き戻すこと
     *
     * @param        $user_id
     * @param string $default_lang 指定されたuser_idに言語設定が存在しない場合に設定されるlang
     */
    private function _setLangByUserId($user_id, $default_lang = "eng")
    {
        $to_user = $this->NotifySetting->User->getProfileAndEmail($user_id);
        if (isset($to_user['User']['language'])) {
            $lang = $to_user['User']['language'];
        } else {
            $lang = $default_lang;
        }
        $this->_setLang($lang);
    }

    /**
     * 指定されたlangをグローバルに設定する
     *
     * @param $lang
     */
    private function _setLang($lang)
    {
        //こっちはメッセージ本体の言語に効く
        Configure::write('Config.language', $lang);
        if ($lang == "eng") {
            $lang = null;
        }
        //こっちは送信元の名前の言語に効く
        $this->NotifySetting->User->me['language'] = $lang;
    }

    /**
     * NOWなタイムスタンプを生成する。
     *
     * @return string
     */
    private function _getTimestamp()
    {
        $now = microtime(true);
        $msec = sprintf("%03d", ($now - floor($now)) * 1000);
        return gmdate('Y-m-d\TH:i:s.', floor($now)) . $msec . 'Z';
    }

    /**
     * push通知に必要なパラメータ
     * X-NCMB-SIGNATUREを生成する
     * デフォルトではpush通知用のシグネチャ生成
     *
     * @param        $timestamp  シグネチャを生成する時に使うタイムスタンプ
     * @param        $method     シグネチャを生成する時に使うメソッド
     * @param        $path       シグネチャを生成する時に使うパス
     * @param string $app_key    NCMB用 application key
     * @param string $client_key NCMB用 client key
     *
     * @return string X-NCMB-SIGNATUREの値
     */
    private function _getNCMBSignature(
        $timestamp,
        $method = null,
        $path = null,
        $app_key = NCMB_APPLICATION_KEY,
        $client_key = NCMB_CLIENT_KEY
    ) {
        $header_string = "SignatureMethod=HmacSHA256&";
        $header_string .= "SignatureVersion=2&";
        $header_string .= "X-NCMB-Application-Key=" . $app_key . "&";
        $header_string .= "X-NCMB-Timestamp=" . $timestamp;

        $signature_string = (($method) ? $method : NCMB_REST_API_PUSH_METHOD) . "\n";
        $signature_string .= NCMB_REST_API_FQDN . "\n";
        if ($path) {
            $signature_string .= $path . "\n";
        } else {
            $signature_string .= "/" . NCMB_REST_API_VER . "/" . NCMB_REST_API_PUSH . "\n";
        }
        $signature_string .= $header_string;

        return base64_encode(hash_hmac("sha256", $signature_string, $client_key, true));
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
    function getNotifyIds($limit = null, $from_date = null)
    {
        $notify_ids = $this->GlRedis->getNotifyIds(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid,
            $limit,
            $from_date
        );
        return $notify_ids;
    }

    function getNotification($limit = null, $from_date = null)
    {
        $notify_from_redis = $this->GlRedis->getNotifications(
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
            $v['options'] = json_decode($v['options'], true);
            $data[]['Notification'] = $v;
        }
        //fetch User
        $user_list = Hash::extract($notify_from_redis, '{n}.user_id');
        $user_list = array_merge($user_list, Hash::extract($data, '{n}.Notification.options.post_user_id'));
        $users = Hash::combine($this->NotifySetting->User->getUsersProf($user_list), '{n}.User.id', '{n}');
        //merge users to notification data

        foreach ($data as $k => $v) {
            $user_id = null;
            $user_name = null;

            if (isset($users[$v['Notification']['user_id']])) {
                $data[$k] = array_merge($data[$k], $users[$v['Notification']['user_id']]);
                $user_id = $v['Notification']['user_id'];
                $user_name = $data[$k]['User']['display_username'];
            }
            //get title
            $title = $this->NotifySetting->getTitle($data[$k]['Notification']['type'],
                $user_name, 1,
                $data[$k]['Notification']['body'],
                array_merge($data[$k]['Notification']['options'],
                    ['from_user_id' => $user_id]));
            $data[$k]['Notification']['title'] = $title;
        }
        return $data;
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
    function getMessageNotification($limit = null, $from_date = null)
    {
        $notify_from_redis = $this->GlRedis->getMessageNotifications(
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
            $user_name = null;
            if (isset($users[$v['Notification']['user_id']])) {
                $data[$k] = array_merge($data[$k], $users[$v['Notification']['user_id']]);
                $user_name = $data[$k]['User']['display_username'];
            }
            //送信対象のユーザー数：2人以上に送る場合+2と表示したい。getTitle内の処理での関係で前処理する
            $to_user_count = $data[$k]['Notification']['to_user_count'];
            if ($to_user_count > 1) {
                $to_user_count++;
            }
            //get title
            $title = $this->NotifySetting->getTitle($data[$k]['Notification']['type'],
                $user_name, $to_user_count,
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
        $this->GlRedis->setNotifications(
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
        return $this->GlRedis->getCountOfNewNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );
    }

    /**
     * get count of new notifications from redis. on the basis of team_id
     *
     * @return int
     */
    function _getCountNewNotificationForTeams($team_id)
    {
        return $this->GlRedis->getCountOfNewNotification(
            $team_id,
            $this->NotifySetting->my_uid
        );
    }

    /**
     * get count of new notifications from redis.
     *
     * @return int
     */
    function getCountNewMessageNotification()
    {
        $res = $this->GlRedis->getCountOfNewMessageNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );

        return $res;
    }

    function getUnreadMessagePostIds()
    {
        $unread_msgs = $this->GlRedis->getMessageNotifications(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );
        $res = Hash::extract($unread_msgs, '{n}.id');
        return $res;
    }

    /**
     * delete count of new notifications form redis.
     *
     * @return bool
     */
    function resetCountNewNotification()
    {
        return $this->GlRedis->deleteCountOfNewNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );
    }

    /**
     * delete count of new notifications form redis.
     *
     * @return bool
     */
    function resetCountNewMessageNotification()
    {
        return $this->GlRedis->deleteCountOfNewMessageNotification(
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
        return $this->GlRedis->changeReadStatusOfNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid,
            $notify_id
        );
    }

    /**
     * remove message notification.
     *
     * @param int $notify_id
     *
     * @return bool|void
     */
    function removeMessageNotification($notify_id)
    {
        if (!$notify_id) {
            // target none.
            return false;
        }
        return $this->GlRedis->deleteMessageNotify(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid,
            $notify_id
        );
    }

    /**
     * update count of message notification.
     *
     * @return bool|void
     */
    function updateCountNewMessageNotification()
    {
        return $this->GlRedis->updateCountOfNewMessageNotification(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid
        );
    }

    /**
     * installation_idでNCMBからdevice_tokenをとってきて
     * Deviceに保存する
     *
     * @param $user_id
     * @param $installation_id
     * @param $version
     *
     * @return bool
     */
    function saveDeviceInfo($user_id, $installation_id, $version = null)
    {
        if (!$user_id || !$installation_id) {
            throw new RuntimeException(__('Parameters were wrong'));
        }
        //まずinstallation_idとuser_idをキーにしてdbからデータとってくる
        $device = $this->Device->find('first',
            ['conditions' => ['user_id' => $user_id, 'installation_id' => $installation_id]]);
        if (empty($device)) {
            //もしなければNifty CloudからDeviceTokenを取得
            $nc_device_info = $this->getDeviceInfo($installation_id);
            if (!isset($nc_device_info['deviceToken'])) {
                throw new RuntimeException(__('Device Information not exists'));
            }
            //device_tokenとuser_idをキーにしてdbからデータ取ってくる
            $device = $this->Device->find('first',
                ['conditions' => ['user_id' => $user_id, 'device_token' => $nc_device_info['deviceToken']]]);
            if (!empty($device)) {
                //見つかった場合はinstallation_idを保存
                $this->Device->id = $device['Device']['id'];
                $device = Hash::merge($device, $this->Device->saveField('installation_id', $installation_id));
            } else {
                //見つからない場合は、新規でdevice tokenを保存
                $device_type = $nc_device_info['deviceType'];
                App::uses('Device', 'Model');
                $os_type = Device::OS_TYPE_OTHER;
                if ($device_type == "android") {
                    $os_type = Device::OS_TYPE_ANDROID;
                } elseif ($device_type == "ios") {
                    $os_type = Device::OS_TYPE_IOS;
                }

                $this->Device->create();
                $device = $this->Device->save([
                    'Device' => [
                        'user_id'         => $user_id,
                        'device_token'    => $nc_device_info['deviceToken'],
                        'os_type'         => $os_type,
                        'version'         => $version,
                        'installation_id' => $installation_id,
                    ]
                ]);
                if (empty($device)) {
                    throw new RuntimeException(__('Failed to save a Device Information.'));
                }
            }
        }

        //DBに保存されているバージョンとcurrent_versionが一致しない場合は、dbの情報を更新
        if ($version !== null && $device['Device']['version'] !== $version) {
            $this->Device->id = $device['Device']['id'];
            $device = Hash::merge($device, $this->Device->saveField('version', $version));
        }
        //sessionのアプリバージョン情報を更新
        $this->Session->write('app_version', $version);
        return $device;
    }

    /**
     * installation_idでNCMBからデバイス情報をとってくる
     *
     * @param        $installation_id
     * @param string $app_key
     * @param string $client_key
     *
     * @return bool
     */
    function getDeviceInfo($installation_id, $app_key = NCMB_APPLICATION_KEY, $client_key = NCMB_CLIENT_KEY)
    {
        if (!$app_key) {
            return false;
        }
        $timestamp = $this->_getTimestamp();
        $path = "/" . NCMB_REST_API_VER . "/" . NCMB_REST_API_GET_INSTALLATION . "/" . $installation_id;
        $signature = $this->_getNCMBSignature($timestamp, NCMB_REST_API_GET_METHOD, $path, $app_key, $client_key);

        $header = array(
            'X-NCMB-Application-Key: ' . $app_key,
            'X-NCMB-Signature: ' . $signature,
            'X-NCMB-Timestamp: ' . $timestamp,
            'Content-Type: application/json'
        );

        $options = array(
            'http' => array(
                'ignore_errors' => true,    // APIリクエストの結果がエラーでもレスポンスボディを取得する
                'max_redirects' => 0,       // リダイレクトはしない
                'method'        => NCMB_REST_API_GET_METHOD
            )
        );

        $options['http']['header'] = implode("\r\n", $header);

        $url = "https://" . NCMB_REST_API_FQDN . $path;
        $ret = file_get_contents($url, false, stream_context_create($options));
        $ret_array = json_decode($ret, true);

        if (!array_key_exists('deviceToken', $ret_array)) {
            return false;
        }

        return $ret_array;
    }

    function _canNotify()
    {
        // 通知先ユーザが存在しない場合　
        if (count($this->notify_settings) === 0) {
            return false;
        }
        return true;
    }

}
