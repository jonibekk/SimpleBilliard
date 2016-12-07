<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::import('Service', 'AppService');
App::uses('GoalMember', 'Model');
App::uses('TeamMember', 'Model');
App::uses('User', 'Model');

class GoalMemberService extends AppService
{
    /* コラボレーターの拡張種別 */
    const EXTEND_COACH = "GOAL:EXTEND_COACH";
    const EXTEND_COACHEE = "GOAL:EXTEND_COACHEE";

    /* リーダー変更リクエストの種類 */
    const CHANGE_LEADER_WITH_COLLABORATION = 1;
    const CHANGE_LEADER_WITH_QUIT = 2;
    const CHANGE_LEADER_FROM_GOAL_MEMBER = 3;

    /**
     * idによる単体データ取得
     *
     * @param       $id
     * @param array $extends
     *
     * @return array|mixed
     */
    function get($id, $extends = [])
    {
        $GoalMember = ClassRegistry::init("GoalMember");

        $data = Hash::extract($GoalMember->findById($id), 'GoalMember');
        if (empty($data)) {
            return $data;
        }

        return $this->extend($data, $extends);
    }

    /**
     * データ拡張
     *
     * @param $data
     * @param $extends
     *
     * @return mixed
     */
    function extend($data, $extends)
    {
        if (empty($data) || empty($extends)) {
            return $data;
        }

        $TeamMember = ClassRegistry::init("TeamMember");
        $User = ClassRegistry::init("User");

        if (in_array(self::EXTEND_COACH, $extends)) {
            $coachId = $TeamMember->getCoachId($data['user_id']);
            $data['coach'] = Hash::extract($User->findById($coachId), 'User');
        }

        if (in_array(self::EXTEND_COACHEE, $extends)) {
            $data['coachee'] = Hash::extract($User->findById($data['user_id']), 'User');
        }
        return $data;
    }

    /**
     * ゴールに対するゴールメンバーが認定対象かどうか判定
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     * - ゴールメンバーの認定希望フラグON
     *
     * @param  $goalMemberId
     *
     * @return boolean
     */
    function isApprovableGoalMember($goalMemberId)
    {
        if(!$goalMemberId) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // ゴールメンバーのユーザIDを取得
        $goalMember = $GoalMember->findById($goalMemberId, ['user_id']);
        if(!$goalMember) {
            return false;
        }
        $goalMemberUserId = Hash::get($goalMember, 'GoalMember.user_id');

        $userIsApproval = $GoalApprovalService->isApprovable($goalMemberUserId);
        $goalIsApproval = $GoalMember->isWishGoalApproval($goalMemberId);

        return $userIsApproval && $goalIsApproval;
    }

    /**
     * ゴールに対するゴールメンバーが認定対象かどうか判定
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     * - ゴールメンバーの認定希望フラグON
     *
     * @param  $goalId
     * @param  $userId
     *
     * @return boolean
     */
    function isApprovableByGoalId($goalId, $userId)
    {
        if(empty($goalId) || empty($userId)) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");

        // ゴールメンバーのユーザIDを取得
        $goalMember = $GoalMember->findByGoalIdAndUserId($goalId, $userId);
        if(empty($goalMember)) {
            return false;
        }
        $goalMemberId = Hash::get($goalMember, 'GoalMember.id');

        $userIsApproval = $GoalApprovalService->isApprovable($userId);
        $goalIsApproval = $GoalMember->isWishGoalApproval($goalMemberId);

        return $userIsApproval && $goalIsApproval;
    }

    /**
     * ゴールに対するゴールメンバーが認定対象かどうか判定
     * # 条件
     * - チームの評価設定がon
     * - ユーザが評価対象
     * - コーチが存在する
     * - ゴールメンバーの認定希望フラグON
     *
     * @param  $goalId
     * @param  $userId
     *
     * @return boolean
     */
    function isLeader($goalId, $userId)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        return $GoalMember->isLeader($goalId, $userId);
    }

    /**
     * アクティブなゴールリーダーIDを取得
     * @param  int $goalId [description]
     * @return [type]         [description]
     */
    function getAcitiveLeaderId(int $goalId)
    {
        if (!$goalId) {
            return null;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");

        $res = $GoalMember->find('first', [
            'conditions' => [
                'GoalMember.goal_id'    => $goalId,
                'GoalMember.type'       => $GoalMember::TYPE_OWNER,
                'TeamMember.active_flg' => true,
                'User.active_flg'       => true
            ],
            'fields'     => [
                'GoalMember.id'
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id'
                    ],
                ],
            ]
        ]);
        if (empty($res)) {
            return null;
        }
        return Hash::get($res, 'GoalMember.id');
    }

    /**
     * ゴールメンバーがアクティブかどうか判定
     * @param  int  $goalMemberId
     * @return bool
     */
    function isActiveGoalMember(int $goalMemberId, int $goalId): bool
    {
        if (!$goalMemberId) {
            return false;
        }

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");

        $res = $GoalMember->find('first', [
            'conditions' => [
                'GoalMember.id'         => $goalMemberId,
                'GoalMember.type'       => $GoalMember::TYPE_COLLABORATOR,
                'Goal.id'               => $goalId,
                'TeamMember.active_flg' => true,
                'User.active_flg'       => true
            ],
            'fields'     => [
                'GoalMember.id'
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'goals',
                    'alias'      => 'Goal',
                    'conditions' => [
                        'Goal.id = GoalMember.goal_id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'team_members',
                    'alias'      => 'TeamMember',
                    'conditions' => [
                        'TeamMember.user_id = GoalMember.user_id',
                    ],
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'users',
                    'alias'      => 'User',
                    'conditions' => [
                        'User.id = GoalMember.user_id'
                    ],
                ],
            ]
        ]);

        return (boolean)$res;
    }

    public function validateChangeLeader(array $formData, int $changeType)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        // パラメータ存在チェック
        $goalId = Hash::get($formData, 'Goal.id');
        if (empty($goalId) || empty($changeType)) {
            return __("Invalid Request.");
        }

        // ゴールが存在するか
        $goal = $Goal->findById($goalId);
        if (empty($goal)) {
            return __("This goal doesn't exist.");
        }

        // 今期以降のゴールか
        $isAfterCurrentGoal = $GoalService->isAfterCurrentGoal($goalId);
        if(!$isAfterCurrentGoal) {
            return __("Can't change leader before current term's goal.");
        }

        // 評価開始前か
        $termType = $GoalService->getTermType(Hash::get($goal, 'Goal.start_date'));
        if ($termType == GoalService::TERM_TYPE_CURRENT) {
            $currentTermId = $EvaluateTerm->getCurrentTermId();
            $isStartedEvaluation = $EvaluateTerm->isStartedEvaluation($currentTermId);
            if ($isStartedEvaluation) {
                return __("You can't change the goal in the evaluation.");
            }
        }

        // リーダーがinactiveのケース
        if ($changeType === self::CHANGE_LEADER_FROM_GOAL_MEMBER) {
            // 自分がゴールメンバーかどうか
            if (!$GoalMember->isCollaborated($goalId)) {
                return __("You don't have a permission to edit this goal as member.");
            }

            // アクティブなリーダーが存在する場合は、ゴールメンバーである自分にはリーダー変更権限がない
            $goalLeaderId = $this->getAcitiveLeaderId($goalId);
            if ($goalLeaderId) {
                return __("You don't have a permission to edit this goal. Exist leader.");
            }
        // 自分がリーダーのケース
        } else {
            // 自分がリーダーかどうか
            $loginUserIsLeader = $GoalMember->isLeader($goalId, $GoalMember->my_uid);
            if (!$loginUserIsLeader) {
                return __("You don't have a permission to edit this goal because you aren't leader.");
            }

            // リーダーを変更して自分がコラボする場合
            if ($changeType === self::CHANGE_LEADER_WITH_COLLABORATION) {
                $GoalMember->set($formData);
                if (!$GoalMember->validates()) {
                    return __("Invalid value");
                }
            }
        }

        // 変更後のリーダーがアクティブなゴールメンバーかどうか
        $newLeaderId = Hash::get($formData, 'NewLeader.id');
        if (!$this->isActiveGoalMember($newLeaderId, $goalId)) {
            return __("Invalid member ID.");
        }

        return true;
    }

    /**
     * リーダー変更処理
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    function changeLeader(array $data, int $changeType): bool
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");

        try {
            // トランザクション開始
            $GoalMember->begin();

            // コラボレーター -> リーダー
            $newLeader = ['id' => Hash::get($data, 'NewLeader.id'), 'type' => $GoalMember::TYPE_OWNER, 'role' => null, 'description' => null];
            if (!$GoalMember->save($newLeader, false)) {
                throw new Exception(sprintf("Failed to change leader. data:%s"
                    , var_export($newLeader, true)));
            }

            // リーダー -> コラボレーター
            if ($changeType == self::CHANGE_LEADER_WITH_COLLABORATION) {
                $data['GoalMember']['type'] = $GoalMember::TYPE_COLLABORATOR;
                if (!$GoalMember->save($data['GoalMember'])) {
                    throw new Exception(sprintf("Failed to collaborate. data:%s"
                        , var_export($data['GoalMember'], true)));
                }
            }

            // ゴール脱退
            if ($changeType === self::CHANGE_LEADER_WITH_QUIT) {
                $goalMemberId = Hash::get($data, 'GoalMember.id');
                $GoalMember->delete($goalMemberId);
                // 論理削除している関係で必ずdelete()メソッドは必ずfalseを返す。
                // よって削除が成功してるかどうか泥臭くチェックしてる。泣
                if (!empty($GoalMember->findById($goalMemberId))) {
                    throw new Exception(sprintf("Failed to quit goal. data:%s"
                        , var_export($goalMemberId, true)));
                }

                // 認定対象の場合のみ未認定カウントキャッシュを削除
                $goalMemberId = Hash::get($data, 'GoalMember.id');
                if ($this->isApprovableGoalMember($goalMemberId)) {
                    $quitUserId = $GoalMember->getUserIdByGoalMemberId($goalMemberId);
                    $coachId = $TeamMember->getCoachId($quitUserId);
                    Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
                }
            }

            // Redisキャッシュ削除
            Cache::delete($GoalMember->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');
            Cache::delete($GoalMember->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');

            // トランザクション完了
            $GoalMember->commit();

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $GoalMember->rollback();
            return false;
        }
        return true;
    }

}
