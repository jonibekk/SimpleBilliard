<?php

use Goalous\Exception\Follow\ValidationToFollowException;

App::import('Service', 'AppService');
App::uses('AppUtil', 'Util');
App::uses('Goal', 'Model');
App::uses('Follow', 'Model');
App::uses('TeamMember', 'Model');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');

/**
 * Class FollowService
 */
class FollowService extends AppService
{
    /**
     * フォローする
     * 既にフォロー済みの場合は失敗ではなく単に処理をせずにフォローIDを返す
     *
     * @param $goalId
     * @param $userId
     * @param $teamId
     *
     * @return bool|int
     */
    function add($goalId, $userId, $teamId = null)
    {
        /** @var Follower $Follower */
        $Follower = ClassRegistry::init("Follower");
        try {
            //既にフォロー済みの場合は処理しない
            $follow = $Follower->getUnique($goalId, $userId);
            if (!empty($follow)) {
                return (int)$follow['id'];
            }

            // フォローする
            if ($teamId) {
                $Follower->current_team_id = $teamId;
            }
            if (!$Follower->add($goalId, $userId)) {
                $data = [
                    'goal_id' => $goalId,
                    'user_id' => $userId,
                    'team_id' => $teamId ? $teamId : $Follower->current_team_id,
                ];
                throw new Exception(sprintf("Failed follow. data:%s"
                    , var_export($data, true)));
            }

            // キャッシュリセット
            Cache::delete($Follower->getCacheKey(CACHE_KEY_CHANNEL_FOLLOW_GOALS, true), 'user_data');
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * フォロー解除
     *
     * @param $goalId
     * @param $userId
     *
     * @return bool
     */
    function delete($goalId, $userId)
    {
        /** @var Follower $Follower */
        $Follower = ClassRegistry::init("Follower");

        try {
            // フォロー解除
            if (!$Follower->del($goalId, $userId)) {
                $conditions = [
                    'goal_id' => $goalId,
                    'user_id' => $userId,
                    'team_id' => $Follower->current_team_id,
                ];
                throw new Exception(sprintf("Failed follow. conditions:%s"
                    , var_export($conditions, true)));
            }

            // キャッシュリセット
            Cache::delete($Follower->getCacheKey(CACHE_KEY_CHANNEL_FOLLOW_GOALS, true), 'user_data');

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * @param int $teamId
     * @param int $goalId
     * @param int $userId
     */
    public function validateToFollow(int $teamId, int $goalId, int $userId): void
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init('TeamMember');

        // Check if goal exists
        $goal = $Goal->findById($goalId);
        if (
            empty($goal) ||
            (int) Hash::get($goal, 'Goal.team_id') !== (int) $teamId ||
            (bool) Hash::get($goal, 'Goal.del_flg')
        ) {
            throw new ValidationToFollowException(__("The Goal doesn't exist."));
        }

        // Check if the goal is completed
        if ($Goal->isCompleted($goalId)) {
            throw new ValidationToFollowException(__("You cannot follow or collaborate with a completed Goal."));
        }

        // Check if it is an old goal
        if ($Goal->isFinished($goalId, $teamId)) {
            throw new ValidationToFollowException(__("You cannot follow or collaborate with a past Goal."));
        }

        // Check participating in collaboration
        $myCollaborationGoalIds = $Goal->GoalMember->getCollaborationGoalIds([$goalId], $userId);
        if (in_array($goalId, $myCollaborationGoalIds)) {
            throw new ValidationToFollowException(__("You cannot follow because you are participating in collaboration."));
        }

        // Check coaching the goal.
        $coachingGoalIds = $TeamMember->getCoachingGoalList($userId);
        if (isset($coachingGoalIds[$goalId])) {
            throw new ValidationToFollowException(__("You cannot follow because you are coaching this goal."));
        }
    }

    /**
     * ユニークのフォロー情報取得
     *
     * @param $goalId
     * @param $userId
     *
     * @return array
     */
    function getUnique($goalId, $userId)
    {
        /** @var Follower $Follower */
        $Follower = ClassRegistry::init("Follower");
        return $Follower->getUnique($goalId, $userId);
    }
}
