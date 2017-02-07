<?php
App::import('Service', 'AppService');
App::uses('AppUtil', 'Util');
App::uses('EvaluateTerm', 'Model');
App::uses('KeyResult', 'Model');
App::uses('KrValuesDailyLog', 'Model');

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
                throw new Exception(sprintf("Term data does not exist. teamId: %s targetDate: %s", $teamId, $targetDate));
            }

            // 対象期間の全KRリスト取得
            $krs = $KeyResult->findAllForSavingDailyLog($teamId, $targetTerm['start_date'], $targetTerm['end_date']);
            if ($krs) {
                $krsWithTargetDate = Hash::insert($krs, '{n}.target_date', $targetDate);
                // ログ保存処理実行
                if (!$KrValuesDailyLog->bulkInsert($krsWithTargetDate)) {
                    throw new Exception(sprintf("Failed to save kr log data. teamId: %s targetDate: %s saveData: %s", $teamId, $targetDate, var_export($krsWithTargetDate, true)));
                }
            }
        } catch (PDOException $e) {
            //rollback transaction
            $KrValuesDailyLog->rollback();
            $this->log("PDOException occurred!");
            $this->log($e->getMessage());
            $this->log($e->queryString);
            return false;
        }

        //commit transaction
        $KrValuesDailyLog->commit();
        return true;
    }
}
