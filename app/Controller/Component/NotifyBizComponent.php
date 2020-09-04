<?php
App::uses('ModelType', 'Model');
App::uses('Message', 'Model');
App::uses('TopicMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Post', 'Model');
App::uses('AppUtil', 'Util');
App::import('Service', 'PushService');
App::import('Service', 'SubscriptionService');

/**
 * TODO: 汎用的なコンポーネントにするために、業務ロジックはサービス層に移す
 *
 * @author daikihirakata
 * @property SessionComponent $Session
 * @property AuthComponent    $Auth
 * @property GlEmailComponent $GlEmail
 * @property MentionComponent $Mention
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
        'Mention'
    ];

    public $notify_option = [
        'old_gl'      => true,
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

    /**
     * Whether from_user_id should be check for null value
     *
     * @var bool
     */
    private $skipCheckFromUserId = false;

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
        $this->Subscription = ClassRegistry::init('Subscription');
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
     * @param int  $postId
     */
    function sendNotify(
        $notify_type,
        $model_id,
        $sub_model_id = null,
        $to_user_list = null,
        $user_id,
        $team_id,
        int $postId = null
    )
    {
        $this->notify_option['from_user_id'] = $user_id;
        $this->notify_option['options']['from_user_id'] = $user_id;
        $this->_setModelProperty($user_id, $team_id);

        switch ($notify_type) {
            case NotifySetting::TYPE_FEED_POST:
                $this->_setFeedPostOption($model_id);
                break;
            case NotifySetting::TYPE_MESSAGE:
                $this->_setMessageOption($model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST:
                $this->_setFeedCommentedOnMineOption(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_POST, $model_id,
                    $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST:
                $this->_setFeedCommentedOnMyCommentedOption(NotifySetting::TYPE_FEED_COMMENTED_ON_MY_COMMENTED_POST,
                    $model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT:
                $this->_setFeedMentionedOption(NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT,
                    $model_id, $sub_model_id, $to_user_list);
                break;
            case NotifySetting::TYPE_FEED_MENTIONED_IN_POST:
                $this->_setFeedMentionedOption(NotifySetting::TYPE_FEED_MENTIONED_IN_POST,
                    $model_id, $sub_model_id, $to_user_list);
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
            case NotifySetting::TYPE_TKR_EXCHANGED_BY_LEADER:
            case NotifySetting::TYPE_MY_GOAL_CHANGED_NEXT_TO_CURRENT_BY_LEADER:
                $this->_setMyGoalChangedOption($notify_type, $model_id, $user_id, $team_id);
                break;
            case NotifySetting::TYPE_MEMBER_CHANGE_KR:
                $this->_setMemberChangeKrOption($notify_type, $model_id, $user_id, $team_id);
                break;
            case NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_MY_GOAL_AS_LEADER_REQUEST_TO_CHANGE:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_COACHEE_CREATE_GOAL:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_COACHEE_COLLABORATE_GOAL:
                $this->_setCollaboApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_COACHEE_CHANGE_ROLE:
                $this->_setCollaboApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_COACHEE_CHANGE_GOAL:
            case NotifySetting::TYPE_COACHEE_EXCHANGE_TKR:
            case NotifySetting::TYPE_COACHEE_CHANGE_GOAL_NEXT_TO_CURRENT:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_COACHEE_WITHDRAW_APPROVAL:
                $this->_setApprovalOption($notify_type, $model_id, $to_user_list, $team_id);
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
            case NotifySetting::TYPE_APPROVAL_COMMENT:
                $this->_setApprovalCommentOption($model_id, $sub_model_id, $to_user_list, $team_id);
                break;
            case NotifySetting::TYPE_EXCHANGED_LEADER:
                $this->_setGoalLeaderChangedOption($notify_type, $model_id, $sub_model_id, $user_id, $team_id);
            case NotifySetting::TYPE_CHANGED_TEAM_BASIC_SETTING:
            case NotifySetting::TYPE_CHANGED_TERM_SETTING:
                $this->_setChangedTeamSetting($notify_type, $model_id, $user_id);
                break;
            case NotifySetting::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED:
                $this->_setTranscodeCompleted($model_id, $user_id, $team_id);
                break;
            case NotifySetting::TYPE_TRANSCODE_FAILED:
                $this->_setTranscodeFailed($user_id, $team_id);
                break;
            case NotifySetting::TYPE_EVALUATOR_SET_TO_COACH:
                $this->_setAddedEvaluatorToCoach($team_id, $user_id, $to_user_list);
                break;
            case NotifySetting::TYPE_EVALUATOR_SET_TO_EVALUATEE:
                $this->_setAddedEvaluatorToEvaluatee($team_id, $to_user_list, $user_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_GOAL:
                $this->_setFeedCommentedOnGoal($team_id, $user_id, $model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_FEED_COMMENTED_ON_COMMENTED_GOAL:
                $this->_setFeedCommentedOnCommentedGoal($team_id, $user_id, $model_id, $sub_model_id);
                break;
            case NotifySetting::TYPE_TRANSLATION_LIMIT_REACHED:
                $this->_setTranslationLimitReached($team_id, $to_user_list);
                break;
            case NotifySetting::TYPE_TRANSLATION_LIMIT_CLOSING:
                $this->_setTranslationLimitClosing($team_id, $to_user_list);
                break;
            default:
                break;
        }
        //通知するアイテムかどうかチェック
        if (!$this->_canNotify()) {
            return;
        }

        //Check if from_user_id is null
        if (!$this->skipCheckFromUserId && empty($this->notify_option['from_user_id'])) {
            GoalousLog::error("Missing from_user_id for notification type $notify_type");
        }

        //通常のアプリ通知データ保存
        $this->_saveNotifications();

        //通常の通知メール送信
        $this->_sendNotifyEmail();

        //通常のアプリ向けPUSH通知
        $this->_sendPushNotify();

        //send desktop notificaton
        $this->_sendDesktopNotify();
    }

    /**
     * Send Pusher
     *
     * @param           $socketId
     * @param           $share        string
     * @param int|null  $teamId
     * @param array     $optionValues optional data to send pusher
     */
    public function push($socketId, $share, $teamId = null, array $optionValues = [])
    {
        if (!$socketId) {
            return;
        }

        $targetTeamId = $this->Session->read('current_team_id') ?? $teamId;
        $channelName = $share . "_team_" . $targetTeamId;

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
                    $channelName = "team_all_" . $targetTeamId;
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
        if (!empty($optionValues)) {
            $data['options'] = $optionValues;
        }

        // push
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $pusher->trigger($channelName, 'post_feed', $data, $socketId);
    }

    /**
     * @param string   $socketId
     * @param string[] $share
     *      ['public', 'circle_1', ...]
     * @param int|null $teamId
     */
    public function pushUpdateCircleList($socketId, $share, $teamId = null)
    {
        if (!$socketId) {
            return;
        }

        $teamId = $this->Session->read('current_team_id') ?? $teamId;
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
     * @param $topic_id
     */
    public function msgNotifyPush($from_user_id, $flag_name, $topic_id)
    {
        $pusher = new Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_ID);
        $chunk_channels = array_chunk($this->push_channels, 100);
        $data = compact('from_user_id', 'flag_name', 'topic_id');
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
            = $this->Goal->GoalMember->my_uid
            = $this->Goal->Follower->my_uid
            = $this->Goal->Team->my_uid
            = $this->Goal->Team->Term->my_uid
            = $this->Goal->Team->Term->Team->my_uid
            = $this->NotifySetting->my_uid
            = $this->GlEmail->SendMail->my_uid
            = $this->GlEmail->SendMail->SendMailToUser->my_uid
            = $this->Team->my_uid
            = $this->Team->TeamMember->my_uid
            = $this->Team->Invite->my_uid
            = $this->Team->Invite->FromUser->my_uid
            = $this->Team->EvaluationSetting->my_uid
            = $user_id;

        $this->Post->current_team_id
            = $this->Post->Comment->current_team_id
            = $this->Post->PostShareCircle->current_team_id
            = $this->Post->PostShareUser->current_team_id
            = $this->Post->Team->TeamMember->current_team_id
            = $this->Post->User->CircleMember->current_team_id
            = $this->Goal->current_team_id
            = $this->Goal->GoalMember->current_team_id
            = $this->Goal->Follower->current_team_id
            = $this->Goal->Team->current_team_id
            = $this->Goal->Team->Term->current_team_id
            = $this->Goal->Team->Term->Team->current_team_id
            = $this->NotifySetting->current_team_id
            = $this->GlEmail->SendMail->current_team_id
            = $this->GlEmail->SendMail->SendMailToUser->current_team_id
            = $this->Team->current_team_id
            = $this->Team->TeamMember->current_team_id
            = $this->Team->Invite->current_team_id
            = $this->Team->Invite->FromUser->current_team_id
            = $this->Team->EvaluationSetting->current_team_id
            = $team_id;
    }

    private function _fixReceiver($team_id, $user_ids, $body)
    {
        $mentionedUsers = $this->Mention->getUserList($body, $team_id, null, true);
        foreach ($mentionedUsers as $user) {
            $index = array_search($user, $user_ids);
            if ($index !== false) {
                unset($user_ids[$index]);
            }
        }
        return $user_ids;
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

        // exclude mentioned
        $members = $this->_fixReceiver($this->Team->current_team_id, $members, $post['Post']['body']);
        if (!count($members)){
            return;
        }

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

        if ($post['Post']['type'] == Post::TYPE_NORMAL) {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        } else {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        }

        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($post['Post']['body']) ?
            json_encode([MentionComponent::replaceMentionToSimpleReadable($post['Post']['body'])]) : null;
        $this->notify_option['options']['share_user_list'] = $share_user_list;
        $this->notify_option['options']['share_circle_list'] = $share_circle_list;

        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $members);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_CIRCLE, $share_circle_list);
    }

    /**
     * 自分が閲覧可能なメッセージがあった場合
     *
     * @param $messageId
     */
    private function _setMessageOption(int $messageId)
    {
        /** @var Message $Message */
        $Message = ClassRegistry::init('Message');
        /** @var TopicMember $TopicMember */
        $TopicMember = ClassRegistry::init('TopicMember');
        $message = $Message->getById($messageId);

        if (empty($message)) {
            $this->log("Message doesn't exist. messageId:$messageId");
            $this->log(Debugger::trace());
            return;
        }

        // Customize the body if there are attached files and no body.
        if (!$message['body'] and $message['attached_file_count'] > 0) {
            // set language
            // TODO: This is not good. It should be translated to several member's setting ideally.
            $this->_setLangByUserId($message['sender_user_id']);
            $body = __('Sent file(s).');
        } else {
            $body = $message['body'];
        }

        $topicId = $message['topic_id'];
        $senderUserId = $message['sender_user_id'];

        // notify to members without sender.
        $members = $TopicMember->findMemberIdList($topicId, [$senderUserId]);

        // notify settings of target members
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($members,
            NotifySetting::TYPE_MESSAGE);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MESSAGE;
        $this->notify_option['url_data'] = ['controller' => 'topics', 'action' => $topicId, "detail"];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($body) ? json_encode([trim($body)]) : null;
        $this->notify_option['topic_id'] = $topicId;
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
        if (!Hash::get($invite, 'FromUser.id') || !Hash::get($invite, 'Team.name')) {
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
        //GoalMember
        $goalMembers = $this->Goal->GoalMember->findActiveByGoalId($goal_id);
        //Follower
        $followers = $this->Goal->Follower->getFollowerListByGoalId($goal_id);
        //Coach
        $coach_id = $this->Team->TeamMember->getCoachId($this->Team->my_uid, $this->Team->current_team_id);
        //通知先に指定されたユーザ
        $share_members = $this->Post->getShareAllMemberList($post['Post']['id']);

        $members = $share_members + $goalMembers + $followers + [$coach_id => $coach_id];
        //exclude inactive users
        $members = array_intersect($members, $this->Team->TeamMember->getActiveTeamMembersList());

        unset($members[$this->Team->my_uid]);

        //対象ユーザの通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($members,
            NotifySetting::TYPE_FEED_CAN_SEE_ACTION);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_CAN_SEE_ACTION;

        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => $post['Post']['id']
        ];
        $this->notify_option['old_gl'] = false;

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
        $goalMembers = $this->Goal->GoalMember->findActiveByGoalId($goal_id);
        //exclude inactive users
        $goalMembers = array_intersect($goalMembers, $this->Team->TeamMember->getActiveTeamMembersList());
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($goalMembers,
            NotifySetting::TYPE_MY_GOAL_FOLLOW);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_FOLLOW;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goal_id];
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
        $goalMembers = $this->Goal->GoalMember->findActiveByGoalId($goal_id);
        //exclude inactive users
        $goalMembers = array_intersect($goalMembers, $this->Team->TeamMember->getActiveTeamMembersList());
        //exclude me
        unset($goalMembers[$user_id]);
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($goalMembers,
            NotifySetting::TYPE_MY_GOAL_COLLABORATE);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_MY_GOAL_COLLABORATE;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goal_id];
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goal_id);
    }

    /**
     * 自分がオーナーのゴールがリーダーによって変更されたときのオプション
     *
     * @param $notify_type
     * @param $goal_id
     * @param $user_id
     * @param $team_id
     */
    private function _setMyGoalChangedOption($notify_type, $goal_id, $user_id, $team_id)
    {
        $goal = $this->Goal->getGoal($goal_id);
        if (empty($goal)) {
            return;
        }
        $goalMembers = $this->Goal->GoalMember->findActiveByGoalId($goal_id);
        //exclude inactive users
        $goalMembers = array_intersect($goalMembers, $this->Team->TeamMember->getActiveTeamMembersList());
        //exclude me
        unset($goalMembers[$user_id]);

        //exclude coach
        App::import('Service', 'GoalApprovalService');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $isApprovable = $GoalApprovalService->isApprovable($user_id, $team_id);
        $coachId = $this->Team->TeamMember->getCoachId($user_id);
        //チームの評価設定on かつ ユーザが評価対象 かつ コーチが存在している場合はコーチを通知対象から除外
        //コーチには別途、認定関連の通知が届くため。
        if ($isApprovable && !empty($goalMembers[$coachId])) {
            unset($goalMembers[$coachId]);
        }
        if (empty($goalMembers)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($goalMembers,
            $notify_type);
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goal_id];
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goal_id);
    }

    /**
     * 自分がオーナーのゴールがリーダーによって変更されたときのオプション
     *
     * @param $notifyType
     * @param $krId
     * @param $userId
     * @param $teamId
     */
    private function _setMemberChangeKrOption($notifyType, $krId, $userId, $teamId)
    {
        $kr = $this->Goal->KeyResult->getById($krId);
        if (empty($kr)) {
            return;
        }
        $goalId = Hash::get($kr, 'goal_id');
        $goalMembers = $this->Goal->GoalMember->findActiveByGoalId($goalId);
        //exclude inactive users
        $goalMembers = array_intersect($goalMembers, $this->Team->TeamMember->getActiveTeamMembersList());
        //exclude me
        unset($goalMembers[$userId]);

        //exclude coach
        App::import('Service', 'GoalApprovalService');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $isApprovable = $GoalApprovalService->isApprovable($userId, $teamId);
        $coachId = $this->Team->TeamMember->getCoachId($userId);
        //チームの評価設定on かつ ユーザが評価対象 かつ コーチが存在している場合はコーチを通知対象から除外
        //コーチには別途、認定関連の通知が届くため。
        if ($isApprovable && !empty($goalMembers[$coachId])) {
            unset($goalMembers[$coachId]);
        }
        if (empty($goalMembers)) {
            return;
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($goalMembers,
            $notifyType);
        $this->notify_option['notify_type'] = $notifyType;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goalId];
        $this->notify_option['model_id'] = $krId;
        $this->notify_option['item_name'] = json_encode([$kr['name']]);
        $this->notify_option['options']['kr_id'] = $krId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goalId);
    }

    /**
     * Option when changed team setting
     *
     * @param $notifyType
     * @param $teamId
     */
    private function _setChangedTeamSetting($notifyType, $teamId, $userId)
    {
        // Get all team member user id
        $teamMemberUserIds = $this->Team->TeamMember->getActiveTeamMembersList();
        unset($teamMemberUserIds[$userId]);

        $team = $this->Team->getById($teamId);

        // Notify setting
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($teamMemberUserIds,
            $notifyType);
        $this->notify_option['notify_type'] = $notifyType;
        $this->notify_option['url_data'] = ['controller' => 'teams', 'action' => 'index'];
        $this->notify_option['model_id'] = $teamId;
        $this->notify_option['item_name'] = json_encode([$team['name']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_ALL_TEAM);
    }

    private function _setTranscodeCompleted($postId, $userId, $teamId)
    {
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($userId,
            NotifySetting::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED;
        $this->notify_option['url_data'] = ['controller' => 'posts', 'action' => 'feed', 'post_id' => $postId];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode(['']);

        $this->notify_option['options'] = [
            'post_id' => $postId,
        ];
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $userId);
    }

    private function _setTranscodeFailed($userId, $teamId)
    {
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($userId,
            NotifySetting::TYPE_TRANSCODE_COMPLETED_AND_PUBLISHED);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_TRANSCODE_FAILED;
        $this->notify_option['url_data'] = ['controller' => 'pages', 'action' => 'home'];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode(['']);
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $userId);
    }

    /**
     * @param $teamId
     * @param $userId
     * @param $coachId
     */
    private function _setAddedEvaluatorToEvaluatee($teamId, $userId, $coachId)
    {
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($userId,
            NotifySetting::TYPE_EVALUATOR_SET_TO_EVALUATEE);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_EVALUATOR_SET_TO_EVALUATEE;
        $this->notify_option['url_data'] = [
            'controller' => 'evaluator_settings',
            'user_id'    => $userId[0],
            'action'     => 'detail',
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode(['']);
        $this->notify_option['force_notify'] = true;
        $this->notify_option['options'] = [
            'coach_user_id' => $coachId,
        ];
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $userId);
    }

    /**
     * @param $teamId
     * @param $userId
     * @param $coachId
     */
    private function _setAddedEvaluatorToCoach($teamId, $userId, $coachId)
    {
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($coachId,
            NotifySetting::TYPE_EVALUATOR_SET_TO_COACH);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_EVALUATOR_SET_TO_COACH;
        $this->notify_option['url_data'] = [
            'controller' => 'evaluator_settings',
            'user_id'    => $userId,
            'action'     => 'detail',
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode(['']);
        $this->notify_option['force_notify'] = true;
        $this->notify_option['options'] = [
            'coachee_user_id' => $userId,
        ];
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $coachId);
    }

    /**
     * @param int   $teamId
     * @param int   $commenterUserId
     * @param array $toUserList
     * @param int   $postId
     */
    private function _setFeedCommentedOnGoal(int $teamId, int $commenterUserId, int $postId, int $commentId)
    {
        $post = $this->Post->findById($postId);
        if (empty($post)) {
            return;
        }
        if ($post['Post']['user_id'] == $commenterUserId) {
            return;
        }

        if (!$this->Team->TeamMember->isActive($post['Post']['user_id'])) {
            return;
        }

        $comment = $this->Post->Comment->read(null, $commentId);
        $members = array($post['Post']['user_id']);
        $members = $this->_fixReceiver($teamId, $members, $comment['Comment']['body']);
        if (!count($members)) return;

        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($members,
            NotifySetting::TYPE_FEED_COMMENTED_ON_GOAL);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_COMMENTED_ON_GOAL;

        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => $postId
        ];
        $this->notify_option['old_gl'] = false;

        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($comment['Comment']['body']) ?
            json_encode([MentionComponent::replaceMentionToSimpleReadable($comment['Comment']['body'])]) : json_encode(['']);
        $this->notify_option['force_notify'] = true;
        $this->notify_option['options'] = [
            'commenter_user_id' => $commenterUserId,
        ];
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $members);
    }

    /**
     * @param int   $teamId
     * @param int   $commenterUserId
     * @param array $toUserList
     * @param int   $postId
     */
    private function _setFeedCommentedOnCommentedGoal(
        int $teamId,
        int $commenterUserId,
        int $postId,
        int $commentId
    )
    {
        // get unique users in comments
        $commentedUserList = $this->Post->Comment->getCommentedUniqueUsersList($postId);
        if (empty($commentedUserList)) {
            return;
        }

        // exclude inactive users
        $commentedUserList = array_intersect($commentedUserList,
            $this->Team->TeamMember->getActiveTeamMembersList());
        $post = $this->Post->findById($postId);
        if (empty($post)) {
            return;
        }

        // exclude post onwer
        unset($commentedUserList[$post['Post']['user_id']]);
        if (empty($commentedUserList)) {
            return;
        }
        $comment = $this->Post->Comment->read(null, $commentId);
        $commentedUserList = $this->_fixReceiver($teamId, $commentedUserList, $comment['Comment']['body']);
        if (!count($commentedUserList)) return;
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($commentedUserList,
            NotifySetting::TYPE_FEED_COMMENTED_ON_COMMENTED_GOAL);
        $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_COMMENTED_ON_COMMENTED_GOAL;

        $this->notify_option['url_data'] = [
            'controller' => 'posts',
            'action'     => $postId
        ];
        $this->notify_option['old_gl'] = false;

        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = !empty($comment['Comment']['body']) ?
            json_encode([MentionComponent::replaceMentionToSimpleReadable($comment['Comment']['body'])]) : json_encode(['']);

        $this->notify_option['force_notify'] = true;

        /** @var Post $Post */
        $Post = ClassRegistry::init('Post');
        $this->notify_option['options'] = [
            'commenter_user_id'  => $commenterUserId,
            'post_owner_user_id' => $Post->getById($postId)['user_id']
        ];
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $commentedUserList);
    }

    private function _setTranslationLimitReached(int $teamId, array $toUserList) {
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($toUserList,
            NotifySetting::TYPE_TRANSLATION_LIMIT_REACHED);

        $this->notify_option['from_user_id'] = null;
        $this->skipCheckFromUserId = true;
        $this->notify_option['notify_type'] = NotifySetting::TYPE_TRANSLATION_LIMIT_REACHED;
        $this->notify_option['url_data'] = [
            'controller' => 'teams',
            'action'     => 'settings',
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([__('Team settings')]);
        $this->notify_option['force_notify'] = true;
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $toUserList);
    }

    private function _setTranslationLimitClosing(int $teamId, array $toUserList) {
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($toUserList,
            NotifySetting::TYPE_TRANSLATION_LIMIT_CLOSING);

        $this->notify_option['from_user_id'] = null;
        $this->skipCheckFromUserId = true;
        $this->notify_option['notify_type'] = NotifySetting::TYPE_TRANSLATION_LIMIT_CLOSING;
        $this->notify_option['url_data'] = [
            'controller' => 'teams',
            'action'     => 'settings',
        ];
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([__('Team settings')]);
        $this->notify_option['force_notify'] = true;
        $this->NotifySetting->current_team_id = $teamId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $toUserList);
    }

    /**
     * ゴールのリーダーが変更されたときのオプション
     *
     * @param $notify_type
     * @param $goal_id
     * @param $user_id
     * @param $team_id
     */
    private function _setGoalLeaderChangedOption($notifyType, $goalId, $oldLeaderUserId, $userId, $teamId)
    {
        $goal = $this->Goal->getGoal($goalId);
        if (empty($goal)) {
            return;
        }
        $goalMembers = $this->Goal->GoalMember->findActiveByGoalId($goalId);
        //exclude inactive users
        $goalMembers = array_intersect($goalMembers, $this->Team->TeamMember->getActiveTeamMembersList());
        //exclude me
        unset($goalMembers[$userId]);

        App::import('Service', 'GoalApprovalService');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // 現リーダーのコーチを追加
        $isApprovable = $GoalApprovalService->isApprovable($userId, $teamId);
        $coachId = $this->Team->TeamMember->getCoachId(Hash::get($goal, 'Goal.user_id'));
        if ($isApprovable && empty($goalMembers[$coachId])) {
            $goalMembers[$coachId] = $coachId;
        }

        // 旧リーダーのコーチを追加
        if ($oldLeaderUserId) {
            $isApprovableOldLeader = $GoalApprovalService->isApprovable($oldLeaderUserId, $teamId);
            $oldLeaderCoachId = $this->Team->TeamMember->getCoachId($oldLeaderUserId);
            if ($isApprovableOldLeader && empty($goalMembers[$oldLeaderCoachId])) {
                $goalMembers[$oldLeaderCoachId] = $oldLeaderCoachId;
            }
            if (empty($goalMembers)) {
                return;
            }
        }

        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($goalMembers,
            $notifyType);
        $this->notify_option['notify_type'] = $notifyType;
        $this->notify_option['url_data'] = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goalId];
        $this->notify_option['model_id'] = $goalId;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goalId;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_GOAL, $goalId);
    }

    /**
     * 認定通知オプション
     *
     * @param $notify_type
     * @param $goal_id
     * @param $to_user_id
     * @param $team_id
     */
    private function _setApprovalOption($notify_type, $goal_id, $to_user_id, $team_id)
    {
        $goal = $this->Goal->getGoal($goal_id, $to_user_id);

        if (empty($goal)) {
            return;
        }
        $goalMember = Hash::get($goal, 'MyCollabo.0') ?
            Hash::get($goal, 'MyCollabo.0') : Hash::get($goal, 'Leader.0');
        if (empty($goalMember)) {
            return;
        }

        //inactive user
        if (!$this->Team->TeamMember->isActive($to_user_id)) {
            return;
        }

        //認定できないユーザの場合は処理しない
        App::import('Service', 'GoalApprovalService');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $isApprovable = $GoalApprovalService->isApprovable($goalMember['user_id'], $team_id);
        if (!$isApprovable) {
            return;
        }

        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($to_user_id, $notify_type);

        $url_goal_detail = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goal_id];
        $url_approval_list = ['controller' => 'goals', 'action' => 'approval', 'list'];
        $url_approval_detail = ['controller' => 'goals', 'action' => 'approval', 'detail', $goalMember['id']];

        // 認定希望していないゴールはゴール詳細へ
        if (!$goalMember['is_wish_approval']) {
            $url = $url_goal_detail;
            // 認定取り下げの場合は認定一覧へ
        } elseif ($notify_type == NotifySetting::TYPE_COACHEE_WITHDRAW_APPROVAL) {
            $url = $url_approval_list;
        } else {
            $url = $url_approval_detail;
        }
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['url_data'] = $url;
        $this->notify_option['model_id'] = $goal_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $to_user_id);
    }

    private function _setCollaboApprovalOption($notify_type, $goal_member_id, $to_user_id, $team_id)
    {
        $goalMember = $this->Goal->GoalMember->findById($goal_member_id);
        if (empty($goalMember)) {
            return;
        }

        $goal_id = $goalMember['GoalMember']['goal_id'];

        $goal = $this->Goal->getGoal($goal_id);

        //inactive user
        if (!$this->Team->TeamMember->isActive($to_user_id)) {
            return;
        }

        //認定できないユーザの場合は処理しない
        App::import('Service', 'GoalApprovalService');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $isApprovable = $GoalApprovalService->isApprovable($goalMember['GoalMember']['user_id'], $team_id);
        if (!$isApprovable) {
            return;
        }

        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($to_user_id, $notify_type);

        $url_goal_detail = ['controller' => 'goals', 'action' => 'view_krs', 'goal_id' => $goal_id];
        $url_goal_approval = ['controller' => 'goals', 'action' => 'approval', 'detail', $goal_member_id];

        //認定希望していないゴールはゴール詳細へ
        if (!$goalMember['GoalMember']['is_wish_approval']) {
            $url = $url_goal_detail;
        } else {
            $url = $url_goal_approval;
        }
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['url_data'] = $url;
        $this->notify_option['model_id'] = $goal_member_id;
        $this->notify_option['item_name'] = json_encode([$goal['Goal']['name']]);
        $this->notify_option['options']['goal_id'] = $goal_id;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $to_user_id);
    }

    /**
     * 認定コメント通知オプション
     *
     * @param $notify_type
     * @param $goal_id
     * @param $to_user_id
     * @param $team_id
     */
    private function _setApprovalCommentOption($goalMemberId, $commentId, $toUserId, $teamId)
    {
        $goalMember = $this->Goal->GoalMember->findById($goalMemberId);
        if (empty($goalMember)) {
            return;
        }

        //inactive user
        if (!$this->Team->TeamMember->isActive($toUserId)) {
            return;
        }

        //認定できないユーザの場合は処理しない
        App::import('Service', 'GoalApprovalService');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $isApprovable = $GoalApprovalService->isApprovable(Hash::get($goalMember, 'GoalMember.user_id'), $teamId);
        if (!$isApprovable) {
            return;
        }

        // TODO: この辺の処理は全部サービス層にうつす。
        //       副作用がこわいので、後ほど一括で移行。
        $approvalHistory = $this->Goal->GoalMember->ApprovalHistory->findById($commentId);
        if (empty($approvalHistory)) {
            return;
        }
        $comment = Hash::get($approvalHistory, 'ApprovalHistory.comment');

        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($toUserId,
            NotifySetting::TYPE_APPROVAL_COMMENT);

        $url_goal_approval = [
            'controller' => 'goals',
            'action'     => 'approval',
            'detail',
            Hash::get($goalMember, 'GoalMember.id')
        ];

        $this->notify_option['notify_type'] = NotifySetting::TYPE_APPROVAL_COMMENT;
        $this->notify_option['url_data'] = $url_goal_approval;
        $this->notify_option['model_id'] = $goalMemberId;
        $this->notify_option['item_name'] = json_encode([trim($comment)]);
        $this->notify_option['options']['goal_id'] = Hash::get($goalMember, 'GoalMember.goal_id');
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $toUserId);
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

        $evaluateeUserId = $evaluation['Evaluation']['evaluatee_user_id'];

        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($evaluation['Evaluation']['evaluator_user_id'],
            NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR);
        $evaluatee = $this->Goal->User->getUsersProf($evaluateeUserId);

        $url = [
            'controller'       => 'evaluations',
            'action'           => 'view',
            'evaluate_term_id' => $evaluation['Evaluation']['term_id'],
            'user_id'          => $evaluation['Evaluation']['evaluatee_user_id'],
            'team_id'          => $this->NotifySetting->current_team_id
        ];

        $this->notify_option['from_user_id'] = $evaluateeUserId;
        $this->notify_option['notify_type'] = NotifySetting::TYPE_EVALUATION_CAN_AS_EVALUATOR;
        $this->notify_option['url_data'] = $url;
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([$evaluatee[0]['User']['display_username']]);
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $evaluatee[0]['User']['id']);
    }

    /**
     * 評価関係者全員通知オプション
     *
     * @param $notifyType
     * @param $termId
     * @param $userId
     */
    private function _setForEvaluationAllUserOption($notifyType, $termId, $userId)
    {
        //対象ユーザはevaluatees
        $evaluatees = $this->Goal->Evaluation->getEvaluateeIdsByTermId($termId);
        $evaluators = $this->Goal->Evaluation->getEvaluatorIdsByTermId($termId);
        $toUserIds = $evaluatees + $evaluators;
        //exclude inactive users
        $toUserIds = array_intersect($toUserIds, $this->Team->TeamMember->getActiveTeamMembersList());
        if (isset($toUserIds[$userId])) {
            unset($toUserIds[$userId]);
        }
        //対象ユーザの通知設定
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($toUserIds,
            $notifyType);

        //通知のurlの元データ
        $notifyListUrl = [
            'controller' => 'evaluations',
            'action'     => 'index',
            'team_id'    => $this->NotifySetting->current_team_id,
            '?'          => ['term_id' => $termId]
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $teamName = $this->Goal->Team->findById($this->NotifySetting->current_team_id);

        $this->notify_option['from_user_id'] = null;
        $this->skipCheckFromUserId = true;
        $this->notify_option['notify_type'] = $notifyType;
        $this->notify_option['url_data'] = $notifyListUrl;
        $this->notify_option['model_id'] = null;
        $this->notify_option['item_name'] = json_encode([$teamName['Team']['name']]);
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
        $comment = $this->Post->Comment->read(null, $comment_id);
        $commented_user_list = $this->_fixReceiver($this->Team->current_team_id, $commented_user_list, $comment['Comment']['body']);
        if (!count($commented_user_list)) return;
        //通知対象者の通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($commented_user_list,
            $notify_type);

        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['count_num'] = count($commented_user_list);

        if ($post['Post']['type'] == Post::TYPE_NORMAL) {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        } else {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        }

        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([$this->Mention->replaceMention(trim($comment['Comment']['body']), [], true)]) : null;
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
        $comment = $this->Post->Comment->read(null, $comment_id);
        $members = array($post['Post']['user_id']);
        $members = $this->_fixReceiver($this->Team->current_team_id, $members, $comment['Comment']['body']);
        if (!count($members)) return;
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($post['Post']['user_id'],
            $notify_type);

        $this->notify_option['to_user_id'] = $post['Post']['user_id'];
        $this->notify_option['notify_type'] = $notify_type;
        $this->notify_option['count_num'] = $this->Post->Comment->getCountCommentUniqueUser($post_id,
            [$post['Post']['user_id']]);

        if ($post['Post']['type'] == Post::TYPE_NORMAL) {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        } else {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        }

        $this->notify_option['model_id'] = $post_id;
        $this->notify_option['item_name'] = !empty($comment) ?
            json_encode([$this->Mention->replaceMention(trim($comment['Comment']['body']), [], true)]) : null;
        $this->notify_option['app_notify_enable'] = $this->notify_settings[$post['Post']['user_id']]['app'];
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $post['Post']['user_id']);
    }

    /**
     * get notification options for the mention
     *
     * @param $notify_type
     * @param $post_id
     * @param $comment_id
     */
    private function _setFeedMentionedOption($notify_type, $post_id, $comment_id, $to_user_ids)
    {
        if (!in_array($notify_type, [NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT, NotifySetting::TYPE_FEED_MENTIONED_IN_POST])){
            return;
        }
        if (empty($to_user_ids)) return;
        $post = $this->Post->findById($post_id);
        if (empty($post)) {
            return;
        }
        //通知対象者の通知設定確認
        $this->notify_settings = $this->NotifySetting->getUserNotifySetting($to_user_ids,
            $notify_type);
        foreach ($to_user_ids as $toUserId) {
            $this->notify_settings[$toUserId]['app'] = true;
        }

        if ($notify_type == NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT){
            if (!is_null($comment_id)) {
                $comment = Hash::get($this->Post->Comment->read(null, $comment_id), 'Comment') ?? [];
            }
        }

        $this->notify_option['count_num'] = count($to_user_ids);
        if ($post['Post']['type'] == Post::TYPE_NORMAL) {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        } else {
            $this->notify_option['url_data'] = [
                'controller' => 'posts',
                'action'     => $post_id,
            ];
            $this->notify_option['old_gl'] = false;
        }
        if ($notify_type == NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT){
            $this->notify_option['model_id'] = $post_id;
            $this->notify_option['options']['post_user_id'] = $post['Post']['user_id'];
            if (!empty($post['Post']['action_result_id'])) {
                $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT_IN_ACTION;
                $actionResult = $this->Post->ActionResult->findById($post['Post']['action_result_id']);
            } else if (!empty($post['Post']['key_result_id'])) {
                $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT_IN_ACTION;
            } else {
                $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_MENTIONED_IN_COMMENT;
            }
            $mentionReplacedBody = MentionComponent::replaceMentionToSimpleReadable($comment['body']);
            $this->notify_option['item_name'] = json_encode([$mentionReplacedBody]);
            $this->notify_option['options']['mention_targets'] = $this->Mention->getTargetIdsEachType($comment['body'], $comment['team_id']);
        } else {
            $this->notify_option['model_id'] = $post_id;
            $this->notify_option['options']['post_user_id'] = $post['Post']['user_id'];
            $this->notify_option['notify_type'] = NotifySetting::TYPE_FEED_MENTIONED_IN_POST;
            $mentionReplacedBody = MentionComponent::replaceMentionToSimpleReadable($post['Post']['body']);
            $this->notify_option['item_name'] = json_encode([$mentionReplacedBody]);
            $this->notify_option['options']['mention_targets'] = $this->Mention->getTargetIdsEachType($post['Post']['body'], $post['Post']['team_id']);

        }

        $this->notify_option['force_notify'] = true;
        $this->setBellPushChannels(self::PUSHER_CHANNEL_TYPE_USER, $to_user_ids);
    }

    private function _saveNotifications()
    {
        //通知onのユーザを取得
        $notificationReceiverUserIds = [];
        foreach ($this->notify_settings as $user_id => $val) {
            if ($val['app']) {
                $notificationReceiverUserIds[] = $user_id;
            }
        }
        if (empty($notificationReceiverUserIds)) {
            return;
        }
        //to be short text
        $notificationBody = json_decode($this->notify_option['item_name']);
        foreach ($notificationBody as $k => $v) {
            $notificationBody[$k] = mb_strimwidth($v, 0, 40, "...");
        }
        $notificationBody = json_encode($notificationBody);

        $this->GlRedis->setSkipCheckMyId($this->skipCheckFromUserId);

        $this->GlRedis->setNotifications(
            $this->notify_option['notify_type'],
            $this->NotifySetting->current_team_id,
            $notificationReceiverUserIds,
            $this->notify_option['from_user_id'],
            $notificationBody,
            $this->notify_option['url_data'],
            microtime(true),
            $this->notify_option['topic_id'] ?? null,
            json_encode($this->notify_option['options']),
            $this->notify_option['old_gl']
        );
        $flag_name = $this->NotifySetting->getFlagPrefixByType($this->notify_option['notify_type']) . '_app_flg';
        if ($this->notify_option['notify_type'] == NotifySetting::TYPE_MESSAGE) {
            $this->msgNotifyPush($this->notify_option['from_user_id'], $flag_name, $this->notify_option['topic_id']);
        } else {
            if ($this->notify_option['force_notify'] ?? false) {
                $flag_name = 'force_notify';
            }
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
    /**
     * get desktop notification target user
     *
     * @return array プッシュ通知送信対象のユーザーのリスト
     */
    private function _getSendDesktopNotifyUserList()
    {
        $uids = [];
        foreach ($this->notify_settings as $user_id => $val) {
            $uids[] = $user_id;
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
    private function _sendPushNotify()
    {
        // Get list of users to send notifications
        $uids = $this->_getSendMobileNotifyUserList();
        if (empty($uids)) {
            return;
        }

        // Config language
        $this->notify_option['options']['style'] = 'plain';
        $original_lang = Configure::read('Config.language');

        // URL to be associated with the notification
        $postUrl = null;
        if (Hash::get($this->notify_option, 'url')) {
            $postUrl = $this->notify_option['url'];
        } else {
            $postUrl = Router::url($this->notify_option['url_data'], true);
        }
        // for switching team when user logged in other team.
        $postUrl = AppUtil::addQueryParamsToUrl($postUrl, ['team_id' => $this->NotifySetting->current_team_id]);

        /** @var PushService $PushService */
        $PushService = ClassRegistry::init('PushService');

        // Keep track of already sent notifications
        $sent_device_tokens = [];

        foreach ($uids as $to_user_id) {

            $deviceTokens = $this->Device->getDeviceTokens($to_user_id);
            if (empty($deviceTokens)) {
                //このユーザーはスマホ持ってないのでスキップ
                continue;
            }

            // ひとつのデバイスが複数のユーザーで登録されている可能性があるので
            // 一度送ったデバイスに対して2度はPUSH通知は送らない
            foreach ($deviceTokens as $key => $value) {
                if (array_search($value, $sent_device_tokens) !== false) {
                    unset($deviceTokens[$key]);
                }
            }
            $this->_setLangByUserId($to_user_id, $original_lang);
            $from_user = $this->NotifySetting->User->getUsersProf($this->notify_option['from_user_id']);
            $from_user_name = Hash::get($from_user, '0.User.display_username');

            // messageが設定されている場合は、それを優先して設定する。セットアップガイド用。
            $title = "";
            if (isset($this->notify_option['message'])) {
                $title = $this->notify_option['message'];
            } else {
                $title = $this->NotifySetting->getTitle($this->notify_option['notify_type'],
                    $from_user_name,
                    1,
                    $this->notify_option['item_name'],
                    array_merge(
                        $this->notify_option['options'],
                        ['to_user_id' => $to_user_id]
                    )
                );

                //メッセージの場合は本文も出ていたほうがいいので出してみる
                $item_name = json_decode($this->notify_option['item_name']);
                if (!empty($item_name)) {
                    $item_name = mb_strimwidth($item_name[0], 0, 40, "...");
                    $title .= " : " . $item_name;
                }
            }

            // Separate the tokens in two groups.
            // The ones with installation_id belongs to Nifty Cloud
            // The ones without installation_id belongs to Firebase
            $firebaseTokens = [];
            $ncmbTokens = [];
            foreach ($deviceTokens as $token) {
                if (empty($token['installation_id'])) {
                    $firebaseTokens[] = $token;
                } else {
                    $ncmbTokens[] = $token['device_token'];
                }
            }

            // Send to NCMB
            if (count($ncmbTokens) > 0) {
                $encTitle = json_encode($title, JSON_HEX_QUOT);
                $PushService->sendNCMBPushNotification($ncmbTokens, $encTitle, $postUrl);
            }

            // Send to Firebase
            if (count($firebaseTokens) > 0) {
                $PushService->sendFirebasePushNotification($firebaseTokens, $title, $postUrl);
            }
            $sent_device_tokens = array_merge($sent_device_tokens, $deviceTokens);
        }

        //変更したlangをログインユーザーのものに書き戻しておく
        $this->_setLang($original_lang);
    }

    /**
     * send desktop notification
     *
     * @param string $app_key
     * @param string $client_key
     */
    private function _sendDesktopNotify()
    {
        // Get list of users to send notifications
        $uids = $this->_getSendDesktopNotifyUserList();
        if (empty($uids)) {
            return;
        }

        // Config language
        $this->notify_option['options']['style'] = 'plain';
        $original_lang = Configure::read('Config.language');

        // URL to be associated with the notification
        $postUrl = null;
        if (Hash::get($this->notify_option, 'url')) {
            $postUrl = $this->notify_option['url'];
        } else {
            $postUrl = Router::url($this->notify_option['url_data'], true);
        }
        // for switching team when user logged in other team.
        $postUrl = AppUtil::addQueryParamsToUrl($postUrl, ['team_id' => $this->NotifySetting->current_team_id]);

        /** @var SubscriptionService $SubscriptionService */
        $SubscriptionService = ClassRegistry::init('SubscriptionService');

        // Keep track of already sent notifications
        $sent_device_tokens = [];

        foreach ($uids as $to_user_id) {

            $subscriptions = $SubscriptionService->getSubscriptionByUserId($to_user_id);
            if (empty($subscriptions)) {
                continue;
            }

            $this->_setLangByUserId($to_user_id, $original_lang);
            $from_user = $this->NotifySetting->User->getUsersProf($this->notify_option['from_user_id']);
            $from_user_name = Hash::get($from_user, '0.User.display_username');

            // messageが設定されている場合は、それを優先して設定する。セットアップガイド用。
            $title = "";
            if (isset($this->notify_option['message'])) {
                $title = $this->notify_option['message'];
            } else {
                $title = $this->NotifySetting->getTitle($this->notify_option['notify_type'],
                    $from_user_name,
                    1,
                    $this->notify_option['item_name'],
                    array_merge(
                        $this->notify_option['options'],
                        ['to_user_id' => $to_user_id]
                    )
                );

                //メッセージの場合は本文も出ていたほうがいいので出してみる
                $item_name = json_decode($this->notify_option['item_name']);
                if (!empty($item_name)) {
                    $item_name = mb_strimwidth($item_name[0], 0, 40, "...");
                    $title .= " : " . $item_name;
                }
            }

            $SubscriptionService->sendDesktopPushNotification($subscriptions, $title, $postUrl);
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
     * execコマンドにて通知を行う
     *
     * @param             $type
     * @param             $model_id
     * @param             $sub_model_id
     * @param array       $to_user_list json_encodeしてbase64_encodeする
     * @param int|null    $teamId
     * @param int|null    $userId
     * @param string|null $baseUrl      the base url of notification list url
     *                                  specify if execSendNotify called from externalAPI, batch shell
     */
    public function execSendNotify(
        $type,
        $model_id,
        $sub_model_id = null,
        $to_user_list = null,
        $teamId = null,
        $userId = null,
        $baseUrl = null
    )
    {
        $set_web_env = "";
        $nohup = "nohup ";
        if (ENV_NAME == 'local') {
            $php = 'php ';
        } else {
            $php = '/opt/phpbrew/php/php-' . phpversion() . '/bin/php ';
        }

        $cake_cmd = $php . APP . "Console" . DS . "cake.php";
        $cake_app = " -app " . APP;
        $cmd = " Operation.notify";
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
        $cmd .= " -b " . (is_null($baseUrl) ? Router::fullBaseUrl() : $baseUrl);
        $cmd .= " -i " . ($this->Auth->user('id') ?? $userId);
        $cmd .= " -o " . ($this->Session->read('current_team_id') ?? $teamId);
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
                    [
                        'from_user_id' => $user_id,
                        'to_user_id' => $this->NotifySetting->my_uid
                    ]));
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
     * @param int $topicId
     *
     * @return bool|void
     */
    function removeMessageNotification($topicId)
    {
        if (!$topicId) {
            // target none.
            return false;
        }
        return $this->GlRedis->deleteMessageNotify(
            $this->NotifySetting->current_team_id,
            $this->NotifySetting->my_uid,
            $topicId
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
     * @param $userId
     * @param $installationId
     * @param $version
     *
     * @return bool
     */
    function saveDeviceInfo($userId, $installationId, $version = null)
    {
        if (!$userId || !$installationId) {
            throw new RuntimeException(__('Parameters were wrong'));
        }
        // getting device token from NCMB
        $ncDeviceInfo = $this->getDeviceInfo($installationId);
        if (!isset($ncDeviceInfo['deviceToken'])) {
            throw new RuntimeException(__('Device Information not exists'));
        }
        $deviceToken = $ncDeviceInfo['deviceToken'];

        // find by installation_id
        // without user_id. cause, if another user logs in Goalous mobile app on same device, record should be updated.
        // installation id is uniq.
        $device = $this->Device->find('first', [
            'conditions' => [
                'installation_id' => $installationId,
            ]
        ]);
        $osType = $this->_getDeviceOsType($ncDeviceInfo['deviceType']);
        if (empty($device)) {
            // add new record

            $this->Device->create();
            $device = $this->Device->save([
                'Device' => [
                    'user_id'         => $userId,
                    'device_token'    => $deviceToken,
                    'os_type'         => $osType,
                    'version'         => $version,
                    'installation_id' => $installationId,
                ]
            ]);
        } else {
            // updating device info on DB
            $device['Device'] = am($device['Device'], [
                'user_id'      => $userId,
                'device_token' => $deviceToken,
                'os_type'      => $osType,
                'version'      => $version,
            ]);
            // updating modified.
            unset($device['Device']['modified']);
            $device = $this->Device->save($device);
        }

        // saving Device was failed..
        if (empty($device)) {
            // logging that saving Device was failure. In most cases Android
            $this->log(sprintf("Failed to save Device. userId: %s, installationId: %s, version: %s, osType: %s, requestData: %s, validationError: %s",
                $userId,
                $installationId,
                $version,
                $osType,
                AppUtil::varExportOneLine($this->Controller->request->data),
                AppUtil::varExportOneLine($this->Device->validationErrors)
            ));
            $this->log(Debugger::trace());
            throw new RuntimeException(__('Failed to save a Device Information.'));
        }

        // updating app version on Session
        $this->Session->write('app_version', $version);
        return $device;
    }

    /**
     * getting device os type
     *
     * @param string $deviceType
     *
     * @return int
     */
    private function _getDeviceOsType(string $deviceType): int
    {
        App::uses('Device', 'Model');
        $osType = Device::OS_TYPE_OTHER;
        if ($deviceType == "android") {
            $osType = Device::OS_TYPE_ANDROID;
        } elseif ($deviceType == "ios") {
            $osType = Device::OS_TYPE_IOS;
        }
        return $osType;
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
        /** @var PushService $PushService */
        $PushService = ClassRegistry::init('PushService');
        $timestamp = $PushService->getNCBTimestamp();
        $path = "/" . NCMB_REST_API_VER . "/" . NCMB_REST_API_GET_INSTALLATION . "/" . $installation_id;
        $signature = $PushService->getNCMBSignature($timestamp, NCMB_REST_API_GET_METHOD, $path, $app_key, $client_key);

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
