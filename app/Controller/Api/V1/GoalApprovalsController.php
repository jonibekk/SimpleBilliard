<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'GoalApprovalService');

/**
 * Class GoalApprovalsController
 * @property PnotifyComponent $Pnotify
 */
class GoalApprovalsController extends ApiController
{

    public $components = [
        'Pnotify',
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
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        $userId = $this->Auth->user('id');

        // コーチとして管理している評価対象のコーチーのユーザーID取得
        $coacheeIds = $this->Team->TeamMember->getMyMembersList($userId);
        // 自分のコーチのユーザーIDを取得
        $coachId = $this->Team->TeamMember->getCoachUserIdByMemberUserId($userId);

        // コーチとコーチーがいない場合はForbidden
        if(empty($coachId) && empty($coacheeIds)) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // コーチとしてのゴール認定未処理件数取得
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $applicationCount = $GoalApprovalService->countUnapprovedGoal($userId);

        // レスポンスの基となるゴール認定リスト取得
        $collaborators = $this->_findCollabrators(
            $userId,
            $coachId,
            $coacheeIds
        );

        // レスポンス用に整形
        $teamId = $this->Session->read('current_team_id');
        $collaborators = $this->_processCollaborators($userId, $teamId, $collaborators);

        $res = [
            'application_count' => $applicationCount,
            'collaborators' => $collaborators
        ];
        return $this->_getResponseSuccess($res);
    }

    /**
     * ゴール認定リストをレスポンス用に整形
     * @param $userId
     * @param $teamId
     * @param $baseData
     *
     * @return array
     */
    private function _processCollaborators($userId, $teamId, $baseData)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        // 自分が評価対象か
        $myEvaluationFlg = $this->Team->TeamMember->getEvaluationEnableFlg($userId, $teamId);

        $res = [];
        foreach ($baseData as $k => $v) {
            $collaborator = $v['Collaborator'];
            $collaborator['is_mine'] = false;
            if ($userId === $v['User']['id']) {
                $collaborator['is_mine'] = true;
                if ($myEvaluationFlg === false) {
                    continue;
                }
            }
            /* コーチー情報設定 */
            $user = $v['User'];
            $user['original_img_url'] = $Upload->uploadUrl($v, 'User.photo');
            $user['small_img_url'] = $Upload->uploadUrl($v, 'User.photo', ['style' => 'small']);
            $user['large_img_url'] = $Upload->uploadUrl($v, 'User.photo', ['style' => 'large']);
            $collaborator['user'] = $user;

            /* ゴール情報設定 */
            $goal = $v['Goal'];
            $goal['original_img_url'] = $Upload->uploadUrl($v, 'Goal.photo');
            $goal['small_img_url'] = $Upload->uploadUrl($v, 'Goal.photo', ['style' => 'small']);
            $goal['large_img_url'] = $Upload->uploadUrl($v, 'Goal.photo', ['style' => 'large']);

            $collaborator['goal'] = $goal;
            $res[] = $collaborator;
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
    private function _findCollabrators($userId, $coachId, $coacheeIds)
    {
        $isCoach = !empty($coachId);
        $isMember = !empty($coacheeIds);

        $res = [];
        // コーチはいるがコーチーがいない
        if ($isCoach === true && $isMember === false) {
            $res = $this->Goal->Collaborator->findActive([$userId]);
        }
        // コーチとコーチーどちらもいる
        elseif ($isCoach === true && $isMember === true) {
            $coacheeCollabos = $this->Goal->Collaborator->findActive($coacheeIds);
            $coachCollabos = $this->Goal->Collaborator->findActive([$userId]);
            // コーチとコーチーのゴール認定リストを結合
            $res = array_merge($coacheeCollabos, $coachCollabos);
        }
        // コーチはいないがコーチーがいる
        elseif ($isCoach === false && $isMember === true) {
            $res = $this->Goal->Collaborator->findActive($coacheeIds);
        }

        return $res;
    }

    /**
     * ゴール認定に関するコメント取得
     * TODO:閲覧権限チェック追加
     */
    function get_histories()
    {
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $collaboratorId = $this->request->query('collaborator_id');
        $histories = $GoalApprovalService->findHistories($collaboratorId);
        return $this->_getResponseSuccess($histories);
    }

    /**
     * ゴール認定対象化API
     * - アクセス権限チェック
     * - 保存データ定義
     * - バリデーション(失敗したらレスポンス返す)
     * - Collaborator, ApprovalHisotry保存(失敗したらレスポンス返す)
     * - コーチーへ通知
     * - Mixpanelでトラッキング
     * - 認定ヒストリーIDをレスポンスに含めて返却
     *
     * @return CakeResponse
     */
    function post_set_as_target()
    {
        App::uses('ApprovalHistory', 'Model');
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $data = $this->request->data;
        $collaboratorId = Hash::get($data,'collaborator.id');

        // アクセス権限チェック
        $canAccess = $GoalApprovalService->haveAccessAuthoriyOnApproval($collaboratorId, $myUserId);
        if(!$canAccess) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // 保存データ定義
        $saveData = $GoalApprovalService->generateSaveData(Collaborator::IS_TARGET_EVALUATION, $data, $myUserId);

        // 保存処理
        $response = $this->_postApproval($saveData);
        if($response !== true) {
            return $response;
        }

        // コーチーへ通知
        $this->_sendNotifyToCoachee($collaboratorId, NotifySetting::TYPE_MY_GOAL_TARGET_FOR_EVALUATION);

        // Mixpanelのトラッキング
        $this->_trackApprovalToMixpanel(
            MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_EVALUABLE,
            MixpanelComponent::PROP_APPROVAL_MEMBER_MEMBER,
            $collaboratorId
        );

        // リストページに表示する通知カード
        $this->Pnotify->outSuccess(__("Set as approval"));

        //コーチーと自分の認定未処理件数を更新(キャッシュを削除
        $coachee = $this->Goal->Collaborator->findById($collaboratorId);
        $coacheeUserId = Hash::get($coachee,'Collaborator.user_id');
        $GoalApprovalService->deleteUnapprovedCountCache([$this->my_uid, $coacheeUserId]);

        // レスポンス
        $newApprovalHistoryId = $this->Goal->Collaborator->ApprovalHistory->getLastInsertID();
        return $this->_getResponseSuccess(['approval_history_id' => $newApprovalHistoryId]);
    }

    /**
     * ゴール認定対象外化API
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
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $data = $this->request->data;
        $collaboratorId = Hash::get($data,'collaborator.id');

        // アクセス権限チェック
        $canAccess = $GoalApprovalService->haveAccessAuthoriyOnApproval($collaboratorId, $myUserId);
        if(!$canAccess) {
            $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        // 保存データ定義
        $saveData = $GoalApprovalService->generateSaveData(Collaborator::IS_NOT_TARGET_EVALUATION, $data, $myUserId);

        // 保存処理
        $response = $this->_postApproval($saveData);
        if($response !== true) {
            return $response;
        }

        // コーチーへ通知
        $this->_sendNotifyToCoachee($collaboratorId, NotifySetting::TYPE_MY_GOAL_NOT_TARGET_FOR_EVALUATION);

        // Mixpanelのトラッキング
        $this->_trackApprovalToMixpanel(
            MixpanelComponent::PROP_APPROVAL_STATUS_APPROVAL_INEVALUABLE,
            MixpanelComponent::PROP_APPROVAL_MEMBER_MEMBER,
            $collaboratorId
        );

        // リストページに表示する通知カード
        $this->Pnotify->outSuccess(__("remove from approval"));

        //コーチーと自分の認定未処理件数を更新(キャッシュを削除
        $coachee = $this->Goal->Collaborator->findById($collaboratorId);
        $coacheeUserId = Hash::get($coachee,'Collaborator.user_id');
        $GoalApprovalService->deleteUnapprovedCountCache([$this->my_uid, $coacheeUserId]);

        // レスポンス
        $newApprovalHistoryId = $this->Goal->Collaborator->ApprovalHistory->getLastInsertID();
        return $this->_getResponseSuccess(['approval_history_id' => $newApprovalHistoryId]);
    }

    /**
     * Goal認定詳細ページの初期データ取得API
     *
     * @param  integer collaborator_id クエリパラメータにて送られる
     * @return CakeResponse
     */
    public function get_detail()
    {
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->my_uid;
        $collaboratorId = $this->request->query('collaborator_id');

        // パラメータが存在しない場合はNotFound
        if(!$collaboratorId) {
            $this->Pnotify->outError(__("Ooops, Not Found."));
            return $this->_getResponseNotFound();
        }

        // アクセス権限チェック
        $canAccess = $GoalApprovalService->haveAccessAuthoriyOnApproval($collaboratorId, $myUserId);
        if(!$canAccess) {
            // TODO: モーダルでコラボを抜けた場合のために一時期的にここでエラーを吐かないようにする
            //       Reactでコラボ編集が実装されたらコメントアウトを外す
            // $this->Pnotify->outError(__("You don't have access right to this page."));
            return $this->_getResponseForbidden();
        }

        $res = $this->Goal->Collaborator->getCollaboratorForApproval($collaboratorId);
        return $this->_getResponseSuccess($GoalApprovalService->formatGoalApprovalForResponse($res, $myUserId));
    }

    /**
     * 認定詳細ページPOSTの共通処理
     * @param  $saveData
     * @return true|CakeResponse
     */
    function _postApproval($saveData)
    {
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // バリデーション
        $validateResult = $GoalApprovalService->validateApprovalPost($saveData);
        if ($validateResult !== true) {
            return $this->_getResponseBadFail(__('Validation failed.'), $validateResult);
        }

        // 保存処理
        $isSaveSuccess = $GoalApprovalService->saveApproval($saveData);
        if ($isSaveSuccess === false) {
            return $this->_getResponseBadFail(__('Failed to save.'));
        }

        return true;
    }

    function _trackApprovalToMixpanel($trackType, $memberType, $collaboratorId)
    {
        $collaborator = $this->Goal->Collaborator->findById($collaboratorId);
        $goalId = Hash::get($collaborator,'Collaborator.goal_id');
        if (!$goalId) {
            return;
        }

        return $this->Mixpanel->trackApproval(
            $trackType,
            $memberType,
            $goalId
        );
    }
}
