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
        $Goal = ClassRegistry::init("Goal");
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
}
