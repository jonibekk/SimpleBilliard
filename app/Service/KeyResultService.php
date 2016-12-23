<?php
App::import('Service', 'AppService');
App::import('Service', 'GoalMemberService');
App::uses('KeyResult', 'Model');
App::uses('Goal', 'Model');
App::uses('KrChangeLog', 'Model');
App::uses('KrProgressLog', 'Model');
App::uses('TeamMember', 'Model');

/**
 * Class KeyResultService
 */
class KeyResultService extends AppService
{

    /* KRキャッシュ */
    private static $cacheList = [];

    /**
     * idによる単体データ取得
     *
     * @param       $id
     *
     * @return array
     */
    function get($id)
    {
        if (empty($id)) {
            return [];
        }

        // 既にDBからのデータ取得は行っているがゴール情報が存在しなかった場合
        if (array_key_exists($id, self::$cacheList) && empty(self::$cacheList[$id])) {
            return [];
        }

        // 既にDBからのデータ取得は行っていて、かつゴール情報が存在している場合
        if (!empty(self::$cacheList[$id])) {
            // キャッシュから取得
            return self::$cacheList[$id];
        }

        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        // キャッシュ変数に保存
        $data = self::$cacheList[$id] = $KeyResult->getById($id);
        return $data;
    }

    /**
     * KRのValueUnitセレクトボックス値の生成
     *
     * @return array $unit_select_list
     */
    function buildKrUnitsSelectList(bool $isShort = false): array
    {
        $units_config = Configure::read("label.units");
        $unit_select_list = [];
        foreach ($units_config as $v) {
            $unit = $isShort ? $v['unit'] : "{$v['label']}({$v['unit']})";
            $unit_select_list[$v['id']] = $unit;
        }
        return $unit_select_list;
    }

    /**
     * キーリザルト一覧を表示用に整形するためのラッパー
     *
     * @param  array  $key_results
     * @param  string $model_alias
     * @param string  $symbol
     *
     * @return array $key_results
     */
    function processKeyResults($key_results, $model_alias = 'KeyResult', $symbol = '→')
    {
        foreach ($key_results as $k => $v) {
            $key_results[$k][$model_alias] = $this->processKeyResult($v[$model_alias], $symbol);
        }
        return $key_results;
    }

    /**
     * キーリザルトを表示用に整形
     *
     * @param $keyResult
     * @param $symbol
     *
     * @return array $key_result
     * @internal param array $key_result
     */
    function processKeyResult($keyResult, $symbol = '→')
    {
        // 完了/未完了
        if ($keyResult['value_unit'] == KeyResult::UNIT_BINARY) {
            $keyResult['display_value'] = __('Complete/Incomplete');
            return $keyResult;
        }

        // 少数の不要な0を取り除く
        // 桁数が多いと指数表記(111E+など)になるため、ここで数字をフォーマットする
        $keyResult['start_value'] = $this->formatBigFloat($keyResult['start_value']);
        $keyResult['target_value'] = $this->formatBigFloat($keyResult['target_value']);
        $keyResult['current_value'] = $this->formatBigFloat($keyResult['current_value']);

        // 単位を文頭におくか文末に置くか決める
        $unitName = KeyResult::$UNIT[$keyResult['value_unit']];
        $headUnit = '';
        $tailUnit = '';
        if (in_array($keyResult['value_unit'], KeyResult::$UNIT_HEAD)) {
            $headUnit = $unitName;
        }
        if (in_array($keyResult['value_unit'], KeyResult::$UNIT_TAIL)) {
            $tailUnit = $unitName;
        }

        $keyResult['display_value'] = "{$headUnit}{$keyResult['start_value']}{$tailUnit} {$symbol} {$headUnit}{$keyResult['target_value']}{$tailUnit}";

        return $keyResult;
    }

    /**
     * 指定したゴールのKRリスト取得
     *
     * @param $goalId
     *
     * @return array $key_result
     */
    function findByGoalId($goalId)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        $krs = $KeyResult->findAllByGoalId($goalId);
        return Hash::extract($krs, '{n}.KeyResult');
    }

    /**
     * TKR交換
     * 別のKRをTKRとして変更
     *
     * @param $krId
     * @param $leaderUserId
     *
     * @return bool
     */
    function exchangeTkr($krId, $leaderUserId)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        try {
            // トランザクション開始
            $Goal->begin();

            $kr = $this->get($krId);
            if (empty($kr)) {
                throw new Exception(sprintf("Not exist kr by id. id:%d", $krId));
            }

            $goalId = Hash::get($kr, 'goal_id');
            // ゴール・TKR・コラボレーター取得
            $tkr = $KeyResult->find('first', [
                'conditions' => [
                    'tkr_flg' => true,
                    'goal_id' => $goalId
                ]
            ]);
            $tkrId = Hash::get($tkr, 'KeyResult.id');
            if (empty($tkrId)) {
                throw new Exception(sprintf("Not exist tkr. goal_id:%d", $kr['goal_id']));
            }

            // TKR→KRに更新
            $dropTkr = ['id' => $tkrId, 'tkr_flg' => false];
            if (!$KeyResult->save($dropTkr, false)) {
                throw new Exception(sprintf("Failed change tkr to kr. data:%s"
                    , var_export($dropTkr, true)));
            }

            // KR→TKRに更新(重要度も自動的に最も高いレベルに変更)
            $addTkr = ['id' => $krId, 'tkr_flg' => true, 'priority' => 5];
            if (!$KeyResult->save($addTkr, false)) {
                throw new Exception(sprintf("Failed change kr to tkr. data:%s"
                    , var_export($addTkr, true)));
            }

            // 認定対象の場合のみゴールの認定ステータスを更新
            $goalLeaderId = $GoalMember->getGoalLeaderId($goalId);
            if ($GoalMemberService->isApprovableGoalMember($goalLeaderId)) {
                // コーチに再申請(リーダーのみでコラボレーターのコーチには再申請は起こらない)
                $updateGoalMember = [
                    'id'                   => $goalLeaderId,
                    'approval_status'      => GoalMember::APPROVAL_STATUS_REAPPLICATION,
                    'is_target_evaluation' => GoalMember::IS_NOT_TARGET_EVALUATION
                ];
                if (!$GoalMember->save($updateGoalMember, false)) {
                    throw new Exception(sprintf("Failed reapply goal. data:%s"
                        , var_export($updateGoalMember, true)));
                }

                // 認定に関するRedisキャッシュ削除
                $coachId = $TeamMember->getCoachId($leaderUserId,
                    Hash::get($kr, 'team_id'));
                Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
                Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
            }

            // Redisキャッシュ削除
            Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');
            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');

            // トランザクション完了
            $Goal->commit();

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Goal->rollback();
            return false;
        }
        return true;
    }

    /**
     * 少数/整数を表示用にフォーマットする
     * 1234.123000 -> 1234.123
     * 1234567890 -> 1234567890
     *
     * @param  $floatNum
     *
     * @return $float_deleted_right_zero
     */
    public function formatBigFloat($floatNum)
    {
        $floatDeletedIndex = sprintf("%.3f", $floatNum);
        $floatDeletedRightZero = preg_replace("/\.?0*$/", '', $floatDeletedIndex);
        return $floatDeletedRightZero;
    }

    /**
     * 未完了KRリスト取得
     *
     * @param int $goalId
     *
     * @return array
     */
    public function findIncomplete(int $goalId): array
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        $krs = $KeyResult->findIncomplete($goalId);
        if (empty($krs)) {
            return [];
        }
        foreach ($krs as &$kr) {
            $kr['start_value'] = $this->formatBigFloat($kr['start_value']);
            $kr['target_value'] = $this->formatBigFloat($kr['target_value']);
            $kr['hash_current_value'] = Security::hash($kr['current_value']);
            $kr['current_value'] = $this->formatBigFloat($kr['current_value']);

        }
        return $krs;
    }

    /**
     * 未完了KR数取得
     *
     * @param int $goalId
     *
     * @return int
     */
    public function countIncomplete(int $goalId): int
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        return $KeyResult->countIncomplete($goalId);
    }

    /**
     * 更新バリデーション
     *
     * @param int $userId
     * @param int $krId
     * @param     $data
     *
     * @return array
     */
    public function validateUpdate(int $userId, int $krId, $data): array
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        // KRが存在するか
        $kr = $this->get($krId);
        if (empty($kr)) {
            return ["status_code" => 400, "message" => __("Not exist")];
        }

        // ゴールメンバーか
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");
        if (!$GoalMemberService->isMember($kr['goal_id'], $userId)) {
            return ["status_code" => 403, "message" => __("You have no permission.")];
        }

        // KRが既に完了していないか
        if ($KeyResult->isCompleted($krId)) {
            return ["status_code" => 400, "message" => __("You can't edit achieved KR.")];
        }

        // フォームバリデーション
        $KeyResult->validate = am($KeyResult->validate, $KeyResult->updateValidate);
        $KeyResult->set($data);
        if (!$KeyResult->validates()) {
            return [
                "status_code"       => 400,
                "validation_errors" => $this->validationExtract($KeyResult->validationErrors)
            ];
        }
        return [];
    }

    /**
     * 更新データ作成
     *
     * @param int   $krId
     * @param array $requestData
     *
     * @return array|bool
     * @throws Exception
     * @internal param $goalId
     */
    function buildUpdateKr(int $krId, array $requestData): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        $kr = $this->get($krId);
        if (empty($kr)) {
            throw new Exception(sprintf("Not exist kr. krId:%d", $krId));
        }

        $updateKr = [
            'id'          => $krId,
            'name'        => Hash::get($requestData, 'name'),
            'description' => Hash::get($requestData, 'description'),
            'value_unit'  => Hash::get($requestData, 'value_unit'),
        ];

        // 完了/未完了の場合は固定値をセット
        if ($requestData['value_unit'] == KeyResult::UNIT_BINARY) {
            $updateKr = am($updateKr, [
                'start_value'  => 0,
                'target_value'  => 1,
                'current_value'  => 0,
            ]);
        } else {
            $updateKr = am($updateKr, [
                'start_value'  => Hash::get($requestData, 'start_value'),
                'target_value'  => Hash::get($requestData, 'target_value'),
            ]);
            if (Hash::check($requestData, 'current_value')) {
                $updateKr['current_value']  = Hash::get($requestData, 'current_value');
                $updateKr['completed'] = ($updateKr['target_value'] == $updateKr['current_value']) ? time() : null;
            }
        }

        // ゴールが属している評価期間データ
        $goalTerm = $Goal->getGoalTermData($kr['goal_id']);
        // 開始日・終了日設定
        $updateKr['start_date'] = strtotime($requestData['start_date']) - $goalTerm['timezone'] * HOUR;
        $updateKr['end_date'] = strtotime('+1 day -1 sec',
                strtotime($requestData['end_date'])) - $goalTerm['timezone'] * HOUR;

        return $updateKr;
    }

    /**
     * 更新
     *
     * @param int   $userId
     * @param int   $krId
     * @param array $requestData
     *
     * @return bool
     */
    function update(int $userId, int $krId, array $requestData): bool
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var KrChangeLog $KrChangeLog */
        $KrChangeLog = ClassRegistry::init("KrChangeLog");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init("KrProgressLog");

        try {
            // トランザクション開始
            $KeyResult->begin();
            // KR更新
            $updateKr = $this->buildUpdateKr($krId, $requestData);
            if (!$KeyResult->save($updateKr, false)) {
                throw new Exception(sprintf("Failed update kr. data:%s"
                    , var_export($updateKr, true)));
            }
            // KR進捗リセット(アクションによるKR進捗ログ削除)
            if (!$KrProgressLog->deleteAll(['KrProgressLog.key_result_id' => $krId, 'KrProgressLog.del_flg' => false])) {
                throw new Exception(sprintf("Failed reset kr progress log. krId:%s", $krId));
            }

            // KR変更ログ保存
            if (!$KrChangeLog->saveSnapshot($userId, $krId, $KrChangeLog::TYPE_MODIFY)) {
                throw new Exception(sprintf("Failed save kr snapshot. krId:%s", $krId));
            }

            // Redisキャッシュ削除
            Cache::delete($KeyResult->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');

            // トランザクション完了
            $KeyResult->commit();

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $KeyResult->rollback();
            return false;
        }
        return true;
    }

}
