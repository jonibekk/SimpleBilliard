<?php
App::import('Service', 'AppService');
App::uses('AppUtil', 'Util');
App::uses('EvaluateTerm', 'Model');
App::uses('KeyResult', 'Model');
App::uses('KrValuesDailyLog', 'Model');
App::uses('GlRedis', 'Model');

/**
 * Class KrValuesDailyLogService
 */
class KrValuesDailyLogService extends AppService
{
    /**
     * KRログデータをバルクで保存する
     *
     * @param int    $teamId
     * @param string $targetDate
     *
     * @return bool
     */
    function saveAsBulk(int $teamId, string $targetDate): bool
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        /** @var KrValuesDailyLog $KrValuesDailyLog */
        $KrValuesDailyLog = ClassRegistry::init('KrValuesDailyLog');

        $existingLog = $KrValuesDailyLog->existTeamLog($teamId, $targetDate);
        if ($existingLog) {
            $this->log(sprintf("Already exists kr log data. teamId: %s targetDate: %s", $teamId, $targetDate));
            return true;
        }

        //start transaction
        $KrValuesDailyLog->begin();

        try {
            $targetTerm = $EvaluateTerm->getTermDataByTimeStamp(strtotime($targetDate));
            if (empty($targetTerm)) {
                //期間データが存在しない場合はログを採らない。期間データがない(ログインしているユーザがいない)なら進捗自体がないということなので。
                throw new PDOException(sprintf("Term data does not exist. teamId: %s targetDate: %s", $teamId,
                    $targetDate));
            }

            // 対象期間の全KRリスト取得
            $krs = $KeyResult->findAllForSavingDailyLog($teamId, $targetTerm['start_date'], $targetTerm['end_date']);
            if ($krs) {
                $krsWithTargetDate = Hash::insert($krs, '{n}.target_date', $targetDate);
                // ログ保存処理実行
                if (!$KrValuesDailyLog->bulkInsert($krsWithTargetDate)) {
                    throw new PDOException(sprintf("Failed to save kr log data. teamId: %s targetDate: %s saveData: %s",
                        $teamId, $targetDate, var_export($krsWithTargetDate, true)));
                }
            }
        } catch (PDOException $e) {
            //rollback transaction
            $KrValuesDailyLog->rollback();
            $this->log("PDOException occurred!");
            $this->log($e->getMessage());
            if (isset($e->queryString)) {
                $this->log($e->queryString);
            }
            return false;
        }

        //commit transaction
        $KrValuesDailyLog->commit();
        return true;
    }

    /**
     * ゴールに紐づくKR進捗日次ログのDBレコード＆キャッシュ削除
     */
    function deleteCache()
    {
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init('GlRedis');
        // 本来ならlocal:cache_user_data:{KEY}:*の形で指定したいが、なぜか前方一致だと取得できないのでやむなく部分一致にする。
        // ユーザーごとのKR進捗ログキャッシュ削除
        $key = '*:' . $GlRedis->getCacheKey(CACHE_KEY_USER_GOAL_KR_VALUES_DAILY_LOG . ":" . date('Y-m-d')).':user:*';
        $GlRedis->deleteKeys($key);
        
        // ゴールごとのKR進捗ログキャッシュ削除
        $key = '*:' . $GlRedis->getCacheKey(CACHE_KEY_GOAL_KR_VALUES_DAILY_LOG . ":*:" . date('Y-m-d'));
        $GlRedis->deleteKeys($key);
    }

    /**
     * KR日次ログをキャッシュから取得
     * msgpack利用
     *
     * @param int    $userId
     * @param string $date Y-m-d
     *
     * @return mixed
     */
    function getKrValueDailyLogFromCache(int $userId, string $date)
    {
        $data = Cache::read($this->getCacheKeyUserGoalKrValuesDailyLog($userId, $date), 'user_data');
        if($data === false){
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $data = msgpack_unpack($data);

        return $data;
    }

    /**
     * KR日次ログをキャッシュに書き出す
     * 生存期間は当日の終わりまで(UTC)
     * msgpack利用
     *
     * @param int    $userId
     * @param string $date Y-m-d
     * @param array  $data 重要度を掛け合わせたもの
     */
    function writeKrValueDailyLogToCache(int $userId, string $date, array $data)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        $data = msgpack_pack($data);

        $remainSecUntilEndOfTheDay = strtotime('tomorrow') - time();
        Cache::set('duration', $remainSecUntilEndOfTheDay, 'user_data');
        $cacheKey = $this->getCacheKeyUserGoalKrValuesDailyLog($userId, $date);
        Cache::write($cacheKey, $data, 'user_data');
    }

    /**
     * ユーザゴールのキャッシュのキーを取得
     *
     * @param int    $userId
     * @param string $date
     *
     * @return mixed
     */
    function getCacheKeyUserGoalKrValuesDailyLog(int $userId, string $date)
    {
        $Goal = ClassRegistry::init("Goal");
        return $Goal->getCacheKey(CACHE_KEY_USER_GOAL_KR_VALUES_DAILY_LOG . ":" . $date, true, $userId);
    }

    /**
     * 単一ゴールのキャッシュのキーを取得
     *
     * @param int    $goalId
     * @param string $date
     *
     * @return mixed
     */
    function getCacheKeyGoalKrValuesDailyLog(int $goalId, string $date)
    {
        $Goal = ClassRegistry::init("Goal");
        return $Goal->getCacheKey(CACHE_KEY_GOAL_KR_VALUES_DAILY_LOG . ":goal_id:$goalId:$date");
    }

    /**
     * 単一ゴールのKR日次ログをキャッシュから取得
     * msgpack利用
     *
     * @param int    $goalId
     * @param string $date Y-m-d
     *
     * @return mixed
     */
    function getGoalKrValueDailyLogFromCache(int $goalId, string $date)
    {
        $data = Cache::read($this->getCacheKeyGoalKrValuesDailyLog($goalId, $date), 'team_info');
        if($data === false){
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $data = msgpack_unpack($data);
        return $data;
    }

    /**
     * 単一ゴールのKR日次ログをキャッシュに書き出す
     * 生存期間は当日の終わりまで(UTC)
     * msgpack利用
     *
     * @param int    $goalId
     * @param string $date Y-m-d
     * @param array  $data
     */
    function writeGoalKrValueDailyLogToCache(int $goalId, string $date, array $data)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        $data = msgpack_pack($data);

        $remainSecUntilEndOfTheDay = strtotime('tomorrow') - time();
        Cache::set('duration', $remainSecUntilEndOfTheDay, 'team_info');
        $cacheKey = $this->getCacheKeyGoalKrValuesDailyLog($goalId, $date);
        Cache::write($cacheKey, $data, 'team_info');
    }
}
