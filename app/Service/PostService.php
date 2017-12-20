<?php
App::import('Service', 'AppService');

/**
 * Class PostService
 */
class PostService extends AppService
{

    /**
     * 月のインデックスからフィードの取得期間を取得
     *
     * @param int $monthIndex
     *
     * @return array ['start'=>unixtimestamp,'end'=>unixtimestamp]
     */
    function getRangeByMonthIndex(int $monthIndex): array
    {
        $start_month_offset = $monthIndex + 1;
        $ret['end'] = strtotime("-{$monthIndex} months", REQUEST_TIMESTAMP);
        $ret['start'] = strtotime("-{$start_month_offset} months", REQUEST_TIMESTAMP);
        return $ret;
    }

    /**
     * Save favorite post
     *
     * @param int $postId
     * @param int $userId
     * @param int $teamId
     *
     * @return bool
     */
    function saveItem(int $postId, int $userId, int $teamId): bool
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init("SavedPost");

        try {
            $SavedPost->create();
            $SavedPost->save([
                'post_id' => $postId,
                'user_id' => $userId,
                'team_id' => $teamId,
            ]);
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            return false;
        }
        return true;
    }

    /**
     * Delete favorite post
     *
     * @param int $postId
     * @param int $userId
     *
     * @return bool
     */
    function deleteItem(int $postId, int $userId): bool
    {
        /** @var SavedPost $SavedPost */
        $SavedPost = ClassRegistry::init("SavedPost");

        try {
            $SavedPost->deleteAll([
                'post_id' => $postId,
                'user_id' => $userId,
            ]);
        } catch (Exception $e) {
            CakeLog::error(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::error($e->getTraceAsString());
            return false;
        }
        return true;
    }
}
