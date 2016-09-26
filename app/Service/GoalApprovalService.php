<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::uses('Goal', 'Model');
App::uses('ApprovalHistory', 'Model');
App::uses('Collaborator', 'Model');
App::import('Service', 'CollaboratorService');

class GoalApprovalService extends Object
{
    /**
     * コーチとしての未対応認定件数取得
     * @param $userId
     *
     * @return mixed
     */
    function countUnapprovedGoal($userId)
    {
        $Collaborator = ClassRegistry::init("Collaborator");
        // Redisのキャッシュデータ取得
        $count = Cache::read($Collaborator->getCacheKey(CACHE_UNAPPROVED_GOAL_COUNT, true), 'user_data');
        // Redisから無ければDBから取得してRedisに保存
        if ($count === false) {
            $count = $Collaborator->countUnapprovedGoal($userId);
            Cache::set('duration', 60 * 1, 'user_data');//1 minute
            Cache::write($Collaborator->getCacheKey(CACHE_UNAPPROVED_GOAL_COUNT, true), $count, 'user_data');
        }
        return $count;
    }

    /**
     * 認定コメントリスト取得
     * @param $collaboratorId
     *
     * @return array
     */
    function findHistories($collaboratorId)
    {
        if (empty($collaboratorId)) {
            return [];
        }
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        $CollaboratorService = ClassRegistry::init("CollaboratorService");

        // 認定コメントリスト取得
        $histories = Hash::extract($ApprovalHistory->findByCollaboratorId($collaboratorId), '{n}.ApprovalHistory');

        $collaborator = $CollaboratorService->get($collaboratorId, [
            CollaboratorService::EXTEND_COACH,
            CollaboratorService::EXTEND_COACHEE,
        ]);

        foreach($histories as &$v) {
            $v['user'] = ($v['user_id'] == $collaborator['user_id']) ?
                $collaborator['coachee'] : $collaborator['coach'];
        }
        return $histories;
    }

    /**
     * 認定ページアクセス権限チェック
     * 認定ページにおいてユーザーがコラボレーターの情報にアクセスできるかチェック
     * @param  integer $collaboratorId
     * @param  integer $userId
     * @return boolean
     */
    function haveAccessAuthoriyOnApproval($collaboratorId, $userId)
    {
        $Collaborator = ClassRegistry::init("Collaborator");
        $Team = ClassRegistry::init("Team");
        $TeamMember = ClassRegistry::init("TeamMember");
        $EvaluationSetting = ClassRegistry::init("EvaluationSetting");

        // チームの評価設定が有効かチェック
        if (!$EvaluationSetting->isEnabled()) {
            return false;
        }

        if (!($collaboratorId && $userId)) {
            return false;
        }

        // コーチとして管理している評価対象のコーチーのユーザーID取得
        $coacheeUserIds = $TeamMember->getMyMembersList($userId);

        // ユーザーのコーチのユーザーIDを取得
        $coachUserId = $TeamMember->getCoachUserIdByMemberUserId($userId);

        // コーチとしてのアクセス権限
        $collaboratorUserId = $Collaborator->getUserIdByCollaboratorId($collaboratorId);
        $haveAuthoriyAsCoach = in_array($collaboratorUserId, $coacheeUserIds);

        // コーチーとしてのアクセス権限
        $haveAuthoriyAsCoachee = $userId == $collaboratorUserId;

        return $haveAuthoriyAsCoach || $haveAuthoriyAsCoachee;
    }

    /**
     * 認定処理未着手カウントのキャッシュ削除
     * @param  array|integer $userIds integerで渡ってきたら内部で配列に変換
     * @return array $deletedCacheUserIds
     */
    function deleteUnapprovedCountCache($userIds)
    {
        $Goal = ClassRegistry::init("Goal");

        if(getType($userIds) === "integer") $userIds = [$userIds];
        $deletedCacheUserIds = [];
        foreach($userIds as $userId) {
            $successDelete = Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $userId), 'user_data');
            if($successDelete) $deletedCacheUserIds[] = $userId;
        }
        return $deletedCacheUserIds;
    }

    /**
     * コラボレーター情報の更新と認定履歴の保存
     * @param  array $saveData
     * @return boolean
     */
    function saveApproval($saveData)
    {
        $Collaborator = ClassRegistry::init("Collaborator");
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");

        $Collaborator->begin();

        $isSaveSuccessCollaborator = $Collaborator->save($saveData);
        if (!$isSaveSuccessCollaborator) {
            $Collaborator->rollback();
            return false;
        }

        $isSaveSuccessApprovalHistory = $ApprovalHistory->add($saveData);
        if (!$isSaveSuccessApprovalHistory) {
            $Collaborator->rollback();
            return false;
        }

        $Collaborator->commit();
        return true;
    }

    /**
     * 認定詳細ページの初期データレスポンスのためにモデルデータをフォーマット
     * @param  $resByModel
     * @param  $myUserId
     * @return $res
     */
    public function formatGoalApprovalForResponse($resByModel, $myUserId)
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

    /**
     * ゴール認定POSTのバリデーション
     * @param  array $data 検証するデータ
     * @return true|CakeResponse
     */
    function validateApprovalPost($data)
    {
        $Collaborator = ClassRegistry::init("Collaborator");
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");

        $validation = [];

        // collaborator validation
        $Collaborator->set($data['Collaborator']);
        $collaborator_validation = $Collaborator->validates();
        if ($collaborator_validation !== true) {
            // TODO: _validationExtractがService基底クラスに移行されたらここの呼び出し元も変える
            $validation['collaborator'] = $Collaborator->_validationExtract($Collaborator->validationErrors);
        }

        // approval_history validation
        $ApprovalHistory->set($data['ApprovalHistory']);
        $approval_history_validation = $ApprovalHistory->validates();
        if ($approval_history_validation !== true) {
            // TODO: _validationExtractがService基底クラスに移行されたらここの呼び出し元も変える
            $validation['approval_history'] = $ApprovalHistory->_validationExtract($ApprovalHistory->validationErrors);
        }

        if (!empty($validation)) {
            return $validation;
        }
        return true;
    }

    /**
     * ゴール認定POSTデータを保存用に整形
     *
     * @param  array $requestData
     * @param  integer $user_id
     * @return array $saveData
     */
    function generateSaveData($approvalType, $requestData, $userId)
    {
        $collaboratorId = Hash::get($requestData, 'collaborator.id');
        $selectClearStatus = ApprovalHistory::STATUS_IS_CLEAR;
        $selectImportantStatus = ApprovalHistory::STATUS_IS_IMPORTANT;
        if($approvalType === Collaborator::IS_NOT_TARGET_EVALUATION) {
            $selectClearStatus = Hash::get($requestData, 'approval_history.select_clear_status');
            $selectImportantStatus = Hash::get($requestData, 'approval_history.select_important_status');
        }

        $saveData =  [
            'Collaborator' => [
                'id' => $collaboratorId,
                'is_target_evaluation' => $approvalType,
                'approval_status' => Collaborator::APPROVAL_STATUS_DONE
            ],
            'ApprovalHistory' => [
                'select_clear_status' => $selectClearStatus,
                'select_important_status' => $selectImportantStatus,
                'collaborator_id' => $collaboratorId,
                'user_id' => $userId,
                'comment' => Hash::get($requestData,'approval_history.comment')
            ]
        ];

        return $saveData;
    }
}
