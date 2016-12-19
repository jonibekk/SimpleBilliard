<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'GoalApprovalService');
App::import('Service/Api', 'ApiGoalApprovalService');

/**
 * Class GoalApprovalsController
 *
 * @property PnotifyComponent $Pnotify
 */
class GoalApprovalsController extends ApiController
{

    public $components = [
        'Pnotify',
        'NotifyBiz'
    ];

    /*
     * オーバーライド
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * 認定対象のゴールリスト取得
     */
    function get_list()
    {
        // チームの評価設定が無効であればForbidden
        if (!$this->Team->EvaluationSetting->isEnabled()) {
            // TODO: 認定ページを使用する必要が無い場合は、単純にリストを0件にして表示する。（ここでPnotify設定すると不自然な動きになる）
            //       API経由でのエラーメッセージ表示は別途一括で設定する必要がある。
            // $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        $userId = $this->Auth->user('id');

        // コーチとして管理している評価対象のコーチーのユーザーID取得
        $coacheeIds = $this->Team->TeamMember->getMyMembersList($userId);
        // 自分のコーチのユーザーIDを取得
        $coachId = $this->Team->TeamMember->getCoachUserIdByMemberUserId($userId);

        // コーチとコーチーがいない場合はForbidden
        if (empty($coachId) && empty($coacheeIds)) {
            // TODO: 認定ページを使用する必要が無い場合は、単純にリストを0件にして表示する。（ここでPnotify設定すると不自然な動きになる）
            //       API経由でのエラーメッセージ表示は別途一括で設定する必要がある。
            // $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // コーチとしてのゴール認定未処理件数取得
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $applicationCount = $GoalApprovalService->countUnapprovedGoal($userId);
        $applicationInfo = ($applicationCount > 0) ? __("Complete the approval of %d goal(s).", $applicationCount) : "";

        // レスポンスの基となるゴール認定リスト取得
        $goalMembers = $this->_findGoalMembers(
            $userId,
            $coachId,
            $coacheeIds
        );

        // レスポンス用に整形
        $teamId = $this->Session->read('current_team_id');
        $goalMembers = $this->_processGoalMembers($userId, $teamId, $goalMembers);

        // 認定リスト全件数を取得
        $allApprovalCount = count($goalMembers);

        $res = [
            'application_count'  => $applicationCount,
            'application_info'   => $applicationInfo,
            'all_approval_count' => $allApprovalCount,
            'goal_members'      => $goalMembers
        ];
        return $this->_getResponseSuccess($res);
    }

    /**
     * ゴール認定リストをレスポンス用に整形
     *
     * @param $userId
     * @param $teamId
     * @param $baseData
     *
     * @return array
     */
    private function _processGoalMembers($userId, $teamId, $baseData)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        // 自分が評価対象か
        $myEvaluationFlg = $this->Team->TeamMember->getEvaluationEnableFlg($userId);

        $res = [];
        foreach ($baseData as $k => $v) {
            $goalMember = $v['GoalMember'];
            $goalMember['is_mine'] = false;
            if ($userId === $v['User']['id']) {
                $goalMember['is_mine'] = true;
                if ($myEvaluationFlg === false) {
                    continue;
                }
            }
            /* コーチー情報設定 */
            $user = $v['User'];
            $user['original_img_url'] = $Upload->uploadUrl($v, 'User.photo');
            $user['small_img_url'] = $Upload->uploadUrl($v, 'User.photo', ['style' => 'small']);
            $user['large_img_url'] = $Upload->uploadUrl($v, 'User.photo', ['style' => 'large']);
            $goalMember['user'] = $user;

            /* ゴール情報設定 */
            $goal = $v['Goal'];
            $goal['original_img_url'] = $Upload->uploadUrl($v, 'Goal.photo');
            $goal['small_img_url'] = $Upload->uploadUrl($v, 'Goal.photo', ['style' => 'small']);
            $goal['large_img_url'] = $Upload->uploadUrl($v, 'Goal.photo', ['style' => 'large']);

            $goalMember['goal'] = $goal;
            $res[] = $goalMember;
        }
        return $res;
    }

    /**
     * ゴール認定リスト取得
     *
     * @param $userId
     * @param $coachId
     * @param $coacheeIds
     *
     * @return array|null
     * @internal param $userType
     */
    private function _findGoalMembers($userId, $coachId, $coacheeIds)
    {
        $isCoach = !empty($coachId);
        $isMember = !empty($coacheeIds);

        $res = [];
        // コーチはいるがコーチーがいない
        if ($isCoach === true && $isMember === false) {
            $res = $this->Goal->GoalMember->findActive([$userId]);
        } // コーチとコーチーどちらもいる
        elseif ($isCoach === true && $isMember === true) {
            $coacheeCollabos = $this->Goal->GoalMember->findActive($coacheeIds);
            $coachCollabos = $this->Goal->GoalMember->findActive([$userId]);
            // コーチとコーチーのゴール認定リストを結合
            $res = array_merge($coacheeCollabos, $coachCollabos);
        } // コーチはいないがコーチーがいる
        elseif ($isCoach === false && $isMember === true) {
            $res = $this->Goal->GoalMember->findActive($coacheeIds);
        }

        return $res;
    }

    /**
     * ゴール認定対象化API
     * - IDチェック
     * - アクセス権限チェック
     * - 保存データ定義
     * - バリデーション(失敗したらレスポンス返す)
     * - GoalMember, ApprovalHisotry保存(失敗したらレスポンス返す)
     * - コーチーへ通知
     * - Mixpanelでトラッキング
     * - 認定ヒストリーIDをレスポンスに含めて返却
     *
     * @return CakeResponse
     */
    function post_set_as_target()
    {
        App::uses('ApprovalHistory', 'Model');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $data = $this->request->data;
        $goalMemberId = Hash::get($data, 'goal_member.id');

        // IDが存在しない場合はNotFound
        if (!$goalMemberId) {
            $this->Pnotify->outError(__("Ooops, Not Found."));
            return $this->_getResponseNotFound();
        }

        // アクセス権限チェック
        $haveAccessAuthority = $GoalApprovalService->haveAccessAuthorityOnApproval($goalMemberId, $myUserId);
        if (!$haveAccessAuthority) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // 保存データ定義
        $saveData = $GoalApprovalService->generateApprovalSaveData(GoalMember::IS_TARGET_EVALUATION, $data, $myUserId);

        // 保存処理
        $response = $this->_postApproval($saveData);
        if ($response !== true) {
            return $response;
        }

        // コーチーへ通知
        $this->_sendNotifyToCoachee($goalMemberId, NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION);

        // Mixpanelのトラッキング
        $this->_trackApprovalToMixpanel(
            MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_EVALUABLE,
            MixpanelComponent::PROP_APPROVAL_MEMBER_MEMBER,
            $goalMemberId
        );

        // リストページに表示する通知カード
        $this->Pnotify->outSuccess(__("Set as target"));

        // レスポンス
        return $this->_getResponseSuccess(['goal_member_id' => $goalMemberId]);
    }

    /**
     * ゴール認定対象外化API
     * - IDチェック
     * - アクセス権限チェック
     * - バリデーション(失敗したらレスポンス返す)
     * - 認定ヒストリー新規登録(失敗したらレスポンス返す)
     * - コーチーへ通知
     * - Mixpanelでトラッキング
     * - 認定ヒストリーIDをレスポンスに含めて返却
     *
     * @return CakeResponse
     */
    function post_remove_from_target()
    {
        App::uses('ApprovalHistory', 'Model');
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $data = $this->request->data;
        $goalMemberId = Hash::get($data, 'goal_member.id');

        // IDが存在しない場合はNotFound
        if (!$goalMemberId) {
            $this->Pnotify->outError(__("Ooops, Not Found."));
            return $this->_getResponseNotFound();
        }

        // アクセス権限チェック
        $haveAccessAuthority = $GoalApprovalService->haveAccessAuthorityOnApproval($goalMemberId, $myUserId);
        if (!$haveAccessAuthority) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // 保存データ定義
        $saveData = $GoalApprovalService->generateApprovalSaveData(GoalMember::IS_NOT_TARGET_EVALUATION, $data, $myUserId);

        // 保存処理
        $response = $this->_postApproval($saveData);
        if ($response !== true) {
            return $response;
        }

        // コーチーへ通知
        $this->_sendNotifyToCoachee($goalMemberId, NotifySetting::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION);

        // Mixpanelのトラッキング
        $this->_trackApprovalToMixpanel(
            MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_INEVALUABLE,
            MixpanelComponent::PROP_APPROVAL_MEMBER_MEMBER,
            $goalMemberId
        );

        // リストページに表示する通知カード
        $this->Pnotify->outSuccess(__("Removed from target"));

        // レスポンス
        return $this->_getResponseSuccess(['goal_member_id' => $goalMemberId]);
    }

    /**
     * ゴール認定申請取り消しAPI
     * - IDチェック
     * - アクセス権限チェック
     * - ステータス変更保存処理
     * - コーチへ通知
     * - Mixpanelでトラッキング
     * - コラボIDを返却
     *
     * @return CakeResponse
     */
    public function post_withdraw()
    {
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $goalMemberId = Hash::get($this->request->data, 'goal_member.id');

        // IDが存在しない場合はNotFound
        if (!$goalMemberId) {
            return $this->_getResponseBadFail();
        }

        // アクセス権限チェック
        $haveAccessAuthority = $GoalApprovalService->haveAccessAuthorityOnApproval($goalMemberId, $myUserId);
        if (!$haveAccessAuthority) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // 保存データ定義
        $saveData = $GoalApprovalService->generateWithdrawSaveData($goalMemberId);

        // 保存処理
        $response = $this->_postApproval($saveData);
        if ($response !== true) {
            return $response;
        }

        // コーチへ通知
        $goalId = Hash::get($this->Goal->GoalMember->findById($goalMemberId), 'GoalMember.goal_id');
        $this->_sendNotifyToCoach($goalId, NotifySetting::TYPE_COACHEE_WITHDRAW_APPROVAL);

        // Mixpanelのトラッキング
        // TODO: 現状、Mixpanelのトラッキングに関して実装の抜け漏れが結構あるため、後ほど他の箇所と合わせて一括で整備する
        //       このコードは後ほどパラメータを変えた上でコメントアウトを外す
        // $this->_trackApprovalToMixpanel(
        //     MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_INEVALUABLE,
        //     MixpanelComponent::PROP_APPROVAL_MEMBER_MEMBER,
        //     $goalMemberId
        // );

        // リストページに表示する通知カード
        $this->Pnotify->outSuccess(__("Has withdrawn"));

        // レスポンス
        return $this->_getResponseSuccess(['goal_member_id' => $goalMemberId]);
    }

    /**
     * Goal認定詳細ページの初期データ取得API
     *
     * @param  integer goal_member_id クエリパラメータにて送られる
     *
     * @return CakeResponse
     */
    public function get_detail()
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        /** @var ApiGoalApprovalService $ApiGoalApprovalService */
        $ApiGoalApprovalService = ClassRegistry::init("ApiGoalApprovalService");

        $myUserId = $this->Auth->user('id');
        $goalMemberId = $this->request->query('goal_member_id');

        // パラメータが存在しない場合はNotFound
        if (!$goalMemberId) {
            $this->Pnotify->outError(__("Ooops, Not Found."));
            return $this->_getResponseNotFound();
        }

        // ゴールが今期以外のものならNotFound
        $goalId = $GoalMember->getGoalIdById($goalMemberId);
        if (!$goalId || !$Goal->isPresentTermGoal($goalId)) {
            $this->Pnotify->outError(__("Ooops, Not Found."));
            return $this->_getResponseNotFound();
        }

        // アクセス権限チェック
        $haveAccessAuthority = $GoalApprovalService->haveAccessAuthorityOnApproval($goalMemberId, $myUserId);
        if (!$haveAccessAuthority) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        $goalMember = $this->Goal->GoalMember->getGoalMemberForApproval($goalMemberId);
        $res = $ApiGoalApprovalService->processGoalApprovalForResponse($goalMember, $myUserId);
        return $this->_getResponseSuccess($res);
    }

    /**
     * 認定コメント登録API
     * - IDチェック
     * - アクセス権限チェック
     * - 保存データ定義
     * - バリデーション(失敗したらレスポンス返す)
     * - ApprovalHisotry保存(失敗したらレスポンス返す)
     * - コーチ/コーチーへ通知
     * - 認定ヒストリーオブジェクトを返す
     *
     * @return [type] [description]
     */
    public function post_comment()
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        /** @var ApiService $ApiService */
        $ApiService = ClassRegistry::init("ApiService");

        $data = $this->request->data;
        $goalMemberId = Hash::get($data, 'goal_member.id');
        $myUserId = $this->Auth->user('id');

        // IDが存在しない場合はNotFound
        if (!$goalMemberId) {
            return $this->_getResponseBadFail();
        }

        // アクセス権限チェック
        $haveAccessAuthority = $GoalApprovalService->haveAccessAuthorityOnApproval($goalMemberId, $myUserId);
        if (!$haveAccessAuthority) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // 保存データ定義
        $saveData = $GoalApprovalService->generateCommentSaveData($data, $myUserId);

        // 保存処理
        $response = $this->_postApproval($saveData);
        if ($response !== true) {
            return $response;
        }

        // 通知
        $this->sendCommentNotify($goalMemberId, $myUserId, $ApprovalHistory->getLastInsertId());

        $res = $ApprovalHistory->findByIdWithUser($ApprovalHistory->getLastInsertId());
        $approval_history = $ApiService->formatResponseData($res);

        // レスポンス
        // コメント投稿が成功したらフロントで投稿したコメントを表示するため、
        // 表示用にコメントを整形して返す
        return $this->_getResponseSuccess(['approval_history' => $approval_history]);
    }

    /**
     * 認定詳細ページPOSTの共通処理
     *
     * @param  $saveData
     *
     * @return true|CakeResponse
     */
    function _postApproval($saveData)
    {
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // バリデーション
        $validateResult = $GoalApprovalService->validateApprovalPost($saveData);
        if ($validateResult !== true) {
            return $this->_getResponseBadFail(__('Validation failed.'), $validateResult);
        }

        // 保存処理
        $isSaveSuccess = $GoalApprovalService->saveApproval($saveData);
        if ($isSaveSuccess === false) {
            return $this->_getResponseInternalServerError(__('Failed to save.'));
        }

        return true;
    }

    /**
     * ゴール認定系のMixpanelトラッキング
     *
     * @param  integer $trackType
     * @param  integer $memberType
     * @param  integer $goalMemberId
     */
    function _trackApprovalToMixpanel($trackType, $memberType, $goalMemberId)
    {
        $goalMember = $this->Goal->GoalMember->findById($goalMemberId);
        $goalId = Hash::get($goalMember, 'GoalMember.goal_id');
        if (!$goalId) {
            return;
        }

        return $this->Mixpanel->trackApproval(
            $trackType,
            $memberType,
            $goalId
        );
    }

    /**
     * 認定コメントの通知を送信する
     *
     * @param  $goalMemberId
     * @param  $myUserId
     * @param  $commentId
     */
    function sendCommentNotify($goalMemberId, $myUserId, $commentId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        $goalMemberUserId = $GoalMember->getUserIdByGoalMemberId($goalMemberId);
        $toUser = null;

        // コーチーの場合、コーチに通知
        if($goalMemberUserId == $myUserId) {
            /** @var TeamMember $TeamMember */
            $TeamMember = ClassRegistry::init("TeamMember");
            $coachUserId = $TeamMember->getCoachUserIdByMemberUserId();
            $toUser = $coachUserId;
        // コーチの場合、コーチーに通知
        } else {
            $toUser = $goalMemberUserId;
        }

        return $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_APPROVAL_COMMENT, $goalMemberId, $commentId, $toUser);
    }
}
