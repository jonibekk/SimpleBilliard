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
}
