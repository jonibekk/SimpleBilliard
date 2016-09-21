<?php
App::uses('ApiController', 'Controller/Api');

/**
 * Class GoalApprovalsController
 */
class GoalApprovalsController extends ApiController
{
    /*
     * ログインしているユーザータイプ
     * 1: コーチのみ存在
     * 2: コーチとメンバーが存在
     * 3: メンバーのみ存在
     */
    const USER_TYPE_NOT_AVAILABLE = 0;
    const USER_TYPE_ONLY_COACH = 1;
    const USER_TYPE_COACH_AND_MEMBER = 2;
    const USER_TYPE_ONLY_MEMBER = 3;

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
        $teamId = $this->Session->read('current_team_id');

        // コーチとして管理している評価対象のメンバーID取得
        $memberIds = $this->Team->TeamMember->getMyMembersList($userId);
        // 自分のコーチのユーザーIDを取得
        $coachId = $this->Team->TeamMember->getCoachUserIdByMemberUserId($userId);
        $userType = $this->_getUserType($coachId, $memberIds);

        // コーチとしてのゴール認定未処理件数取得
        $applicationCount = $this->Goal->Collaborator->countUnapprovedGoal($teamId, $userId);

        // レスポンスの基となるゴール認定リスト取得
        $collaborators = $this->_findCollabrators(
            $userId,
            $teamId,
            $userType,
            $memberIds
        );

        // レスポンス用に整形
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
        $this->Upload = new UploadHelper(new View());

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
            $user['original_img_url'] = $this->Upload->uploadUrl($v, 'User.photo');
            $user['small_img_url'] = $this->Upload->uploadUrl($v, 'User.photo', ['style' => 'small']);
            $user['large_img_url'] = $this->Upload->uploadUrl($v, 'User.photo', ['style' => 'large']);
            $collaborator['user'] = $user;

            /* ゴール情報設定 */
            $goal = $v['Goal'];
            $goal['original_img_url'] = $this->Upload->uploadUrl($v, 'Goal.photo');
            $goal['small_img_url'] = $this->Upload->uploadUrl($v, 'Goal.photo', ['style' => 'small']);
            $goal['large_img_url'] = $this->Upload->uploadUrl($v, 'Goal.photo', ['style' => 'large']);

            $collaborator['goal'] = $goal;
            $res[] = $collaborator;
        }
        return $res;
    }
    /*
     * リストに表示するゴールのUserIDを取得
     */
    public function _getCollaboratorUserId($userId, $userType, $memberIds)
    {
        $goalUserIds = [];
        if ($userType === self::USER_TYPE_ONLY_COACH) {
            $goalUserIds = [$userId];
        } elseif ($userType === self::USER_TYPE_COACH_AND_MEMBER) {
            $goalUserIds = array_merge([$userId], $memberIds);
        } elseif ($userType === self::USER_TYPE_ONLY_MEMBER) {
            $goalUserIds = $memberIds;
        }
        return $goalUserIds;
    }

    /**
     * ゴール認定リスト取得
     * @param $userId
     * @param $teamId
     * @param $userType
     * @param $memberIds
     *
     * @return array|null
     */
    public function _findCollabrators($userId, $teamId, $userType, $memberIds)
    {
        $res = [];
        if ($userType === self::USER_TYPE_ONLY_COACH) {
            $res = $this->Goal->Collaborator->findActive(
                $teamId, [$userId]);

        } elseif ($userType === self::USER_TYPE_COACH_AND_MEMBER) {
            $member_goal_info = $this->Goal->Collaborator->findActive(
                $teamId, $memberIds);

            $my_goal_info = $this->Goal->Collaborator->findActive(
                $teamId, [$userId]);

            $res = array_merge($member_goal_info, $my_goal_info);

        } elseif ($userType === self::USER_TYPE_ONLY_MEMBER) {
            $res = $this->Goal->Collaborator->findActive(
                $teamId, $memberIds);
        }

        return $res;
    }

    /*
     * コーチ認定機能を使えるユーザーか判定
     * 1: コーチがいる、メンバーいない
     * 2: コーチいる、メンバーがいる
     * 3: コーチがいない、メンバーがいる
     */
    public function _getUserType($coachId, $memberIds)
    {
        $isCoach = !empty($coachId);
        $isMember = !empty($memberIds);
        if ($isCoach === true && $isMember === false) {
            return self::USER_TYPE_ONLY_COACH;
        }

        if ($isCoach === true && $isMember === true) {
            return self::USER_TYPE_COACH_AND_MEMBER;
        }

        if ($isCoach === false && $isMember === true) {
            return self::USER_TYPE_ONLY_MEMBER;
        }

        return self::USER_TYPE_NOT_AVAILABLE;
    }


}
