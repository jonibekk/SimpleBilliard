<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::uses('Goal', 'Model');
class GoalApprovalService
{
    function countUnapprovedGoal($userId)
    {
        $Goal = new Goal();
        // Redisのキャッシュデータ取得
        $count = Cache::read($Goal->Collaborator->getCacheKey(CACHE_UNAPPROVED_GOAL_COUNT, true), 'user_data');
        // Redisから無ければDBから取得してRedisに保存
        if ($count === false) {
            $count = $Goal->Collaborator->countUnapprovedGoal($userId);
            Cache::set('duration', 60 * 1, 'user_data');//1 minute
            Cache::write($Goal->Collaborator->getCacheKey(CACHE_UNAPPROVED_GOAL_COUNT, true), $count, 'user_data');
        }
        return $count;
    }

}
