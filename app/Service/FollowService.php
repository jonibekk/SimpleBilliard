<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::import('Service', 'AppService');
App::uses('AppUtil', 'Util');
App::uses('Goal', 'Model');
App::uses('Follow', 'Model');
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
     *
     * @return bool|int
     */
    function add($goalId, $userId)
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
            if (!$Follower->add($goalId, $userId)) {
                $data = [
                    'goal_id' => $goalId,
                    'user_id' => $userId,
                    'team_id' => $Follower->current_team_id,
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
            $this->log($Follower->getDataSource()->getLog());

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
