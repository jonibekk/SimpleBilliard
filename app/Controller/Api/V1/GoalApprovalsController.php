<?php
App::uses('ApiController', 'Controller/Api');
App::import('Service', 'GoalApprovalService');

/**
 * Class GoalApprovalsController
 */
class GoalApprovalsController extends ApiController
{
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
        // チームの評価設定が無効であれば404
        if (!$this->Team->EvaluationSetting->isEnabled()) {
            throw new NotFoundException();
        }

        $userId = $this->Auth->user('id');

        // コーチとして管理している評価対象のコーチーのユーザーID取得
        $coacheeIds = $this->Team->TeamMember->getMyMembersList($userId);
        // 自分のコーチのユーザーIDを取得
        $coachId = $this->Team->TeamMember->getCoachUserIdByMemberUserId($userId);

        // コーチとコーチーがいない場合は404
        if(empty($coachId) && empty($coacheeIds)) {
            throw new NotFoundException();
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
    public function _processCollaborators($userId, $teamId, $baseData)
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
    public function _findCollabrators($userId, $coachId, $coacheeIds)
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
     * ゴール認定対象化API
     * - アクセス権限チェック
     * - 保存データ定義
     * - バリデーション(失敗したらレスポンス返す)
     * - Collaborator, ApprovalHisotry保存(失敗したらレスポンス返す)
     * - コーチーへ通知
     * - Mixpanelでトラッキング
     * - 認定ヒストリーIDをレスポンスに含めて返却
     *
     * @return true|CakeResponse
     */
    function post_set_as_target()
    {
        App::uses('ApprovalHistory', 'Model');
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $data = $this->request->data;
        $collaboratorId = viaIsSet($data['collaborator']['id']);

        // アクセス権限チェック
        $canAccess = $GoalApprovalService->haveAccessAuthoriyOnApproval($collaboratorId, $myUserId);
        if(!$canAccess) {
            throw new NotFoundException();
        }

        // 保存データ定義
        $data = $this->request->data;
        $saveData = [
            'Collaborator' => [
                'id' => $collaboratorId,
                'is_target_evaluation' => true,
                'approval_status' => Collaborator::APPROVAL_STATUS_DONE
            ],
            'ApprovalHistory' => [
                'select_clear_status' => ApprovalHistory::STATUS_IS_CLEAR,
                'select_important_status' => ApprovalHistory::STATUS_IS_IMPORTANT,
                'collaborator_id' => $collaboratorId,
                'user_id' => $this->my_uid,
                'comment' => viaIsSet($data['approval_history']['comment'])
            ]
        ];

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
        $this->Pnotify = $this->Components->load('Pnotify');
        $this->Pnotify->outSuccess(__("Set as approval"));

        //コーチーと自分の認定未処理件数を更新(キャッシュを削除
        $coacheeId = viaIsSet($this->Goal->Collaborator->findById($data['collaborator_id'])['Collaborator']['user_id']);
        $GoalApprovalService->deleteUnapprovedCountCache([$this->my_uid, $coacheeId]);

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
     * @return true|CakeResponse
     */
    function post_remove_from_target()
    {
        App::uses('ApprovalHistory', 'Model');
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->Auth->user('id');
        $data = $this->request->data;
        $collaboratorId = viaIsSet($data['collaborator']['id']);

        // アクセス権限チェック
        $canAccess = $GoalApprovalService->haveAccessAuthoriyOnApproval($collaboratorId, $myUserId);
        if(!$canAccess) {
            throw new NotFoundException();
        }

        // 保存データ定義
        $saveData = [
            'Collaborator' => [
                'id' => $data['collaborator']['id'],
                'is_target_evaluation' => false,
                'approval_status' => Collaborator::APPROVAL_STATUS_DONE
            ],
            'ApprovalHistory' => [
                'select_clear_status' => $data['approval_history']['clear_or_not'],
                'select_important_status' => $data['approval_history']['important_or_not'],
                'collaborator_id' => $collaboratorId,
                'user_id' => $this->my_uid,
                'comment' => viaIsSet($data['approval_history']['comment'])
            ]
        ];

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
        $this->Pnotify = $this->Components->load('Pnotify');
        $this->Pnotify->outSuccess(__("remove from approval"));

        //コーチーと自分の認定未処理件数を更新(キャッシュを削除
        $coacheeId = viaIsSet($this->Goal->Collaborator->findById($data['collaborator_id'])['Collaborator']['user_id']);
        $GoalApprovalService->deleteUnapprovedCountCache([$this->my_uid, $coacheeId]);

        // レスポンス
        $newApprovalHistoryId = $this->Goal->Collaborator->ApprovalHistory->getLastInsertID();
        return $this->_getResponseSuccess(['approval_history_id' => $newApprovalHistoryId]);
    }

    /**
     * Goal認定詳細ページの初期データ取得API
     *
     * @param  integer $collaboratorId
     * @return true | CakeResponse
     */
    public function get_detail($collaboratorId)
    {
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $myUserId = $this->my_uid;

        // アクセス権限チェック
        $canAccess = $GoalApprovalService->haveAccessAuthoriyOnApproval($collaboratorId, $myUserId);
        if(!$canAccess) {
            throw new NotFoundException();
        }

        $res = $this->Goal->Collaborator->getCollaboratorForApproval($collaboratorId);
        return $this->_getResponseSuccess($this->_formatGoalApprovalForResponse($res, $myUserId));
    }

    public function _formatGoalApprovalForResponse($resByModel, $myUserId)
    {
        App::uses('UploadHelper', 'View/Helper');
        $Upload = new UploadHelper(new View());

        $res = Hash::extract($resByModel, 'Collaborator');

        // モデル名整形(大文字->小文字)
        $res['user'] = Hash::extract($resByModel, 'User');
        $res['goal'] = Hash::extract($resByModel, 'Goal');
        $res['goal']['category'] = Hash::extract($resByModel, 'Goal.GoalCategory');
        $res['goal']['leader'] = Hash::extract($resByModel, 'Goal.Leader.0');
        $res['goal']['leader']['user'] = Hash::extract($resByModel, 'Goal.Leader.0.User');
        $res['goal']['top_key_result'] = Hash::extract($resByModel, 'Goal.TopKeyResult');
        $res['approval_histories'] = Hash::map($resByModel, 'ApprovalHistory', function($value) {
            $value['user'] = Hash::extract($value, 'User');
            unset($value['User']);
            return $value;
        });

        // 画像パス追加
        $res['user']['original_img_url'] = $Upload->uploadUrl($resByModel, 'User.photo');
        $res['user']['small_img_url'] = $Upload->uploadUrl($resByModel, 'User.photo', ['style' => 'small']);
        $res['user']['large_img_url'] = $Upload->uploadUrl($resByModel, 'User.photo', ['style' => 'large']);
        $res['goal']['original_img_url'] = $Upload->uploadUrl($resByModel, 'Goal.photo');
        $res['goal']['small_img_url'] = $Upload->uploadUrl($resByModel, 'Goal.photo', ['style' => 'small']);
        $res['goal']['large_img_url'] = $Upload->uploadUrl($resByModel, 'Goal.photo', ['style' => 'large']);

        // マッピング
        $res['is_leader'] = (boolean)$res['type'];
        $res['is_mine'] = $res['user']['id'] == $myUserId;
        $res['type'] = Collaborator::$TYPE[$res['type']];

        // 不要な要素の削除
        unset($res['User'], $res['Goal'], $res['ApprovalHistory'], $res['goal']['GoalCategory'], $res['goal']['Leader'], $res['goal']['TopKeyResult'], $res['goal']['leader']['User']);

        return $res;
    }

    function _postApproval($saveData)
    {
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // バリデーション
        $validateResult = $this->_validateApprovalPost($saveData);
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

    /**
     * ゴール認定POSTのバリデーション
     * @param  array $data 検証するデータ
     * @return true|CakeResponse
     */
    function _validateApprovalPost($data)
    {
        $validation = [];

        // collaborator validation
        $this->Goal->Collaborator->set($data['Collaborator']);
        $collaborator_validation = $this->Goal->Collaborator->validates();
        if ($collaborator_validation !== true) {
            $validation['collaborator'] = $this->_validationExtract($this->Goal->Collaborator->validationErrors);
        }

        // approval_history validation
        $this->Goal->Collaborator->ApprovalHistory->set($data['ApprovalHistory']);
        $approval_history_validation = $this->Goal->Collaborator->ApprovalHistory->validates();
        if ($approval_history_validation !== true) {
            $validation['approval_history'] = $this->_validationExtract($this->Goal->Collaborator->ApprovalHistory->validationErrors);
        }

        if (!empty($validation)) {
            return $validation;
        }
        return true;
    }

    function _trackApprovalToMixpanel($trackType, $memberType, $collaboratorId)
    {
        $collaborator = $this->Goal->Collaborator->findById($collabo_id);
        if (!viaIsSet($collaborator['Collaborator'])) {
            return;
        }
        $goalId = $collaborator['Collaborator']['goal_id'];

        return $this->Mixpanel->trackApproval(
            $trackType,
            $memberType,
            $goalId
        );
    }

}
