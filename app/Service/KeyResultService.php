<?php
App::import('Service', 'AppService');
App::import('Service', 'GoalMemberService');
App::import('Service', 'ActionService');
App::uses('KeyResult', 'Model');
App::uses('Goal', 'Model');
App::uses('GoalMember', 'Model');
App::uses('KrChangeLog', 'Model');
App::uses('KrProgressLog', 'Model');
App::uses('TeamMember', 'Model');
App::uses('Watchlist', 'Model');
// TODO:NumberExHelperだけimportではnot foundになってしまうので要調査
App::uses('NumberExHelper', 'View/Helper');
App::uses('FindForKeyResultListRequest', 'Service/Request/KeyResults');

/**
 * Class KeyResultService
 */
class KeyResultService extends AppService
{

    /**
     * idによる単体データ取得
     *
     * @param       $id
     *
     * @return array
     */
    function get($id): array
    {
        $data = $this->_getWithCache($id, 'KeyResult');
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

    function appendWatchedToKeyResults(array $keyResults, int $userId, int $teamId)
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        $krIds = Hash::extract($keyResults, '{n}.KeyResult.id');
        $watchedKrs = $KeyResult->find("all", [
            "joins" => [
                [
                    'alias' => 'KrWatchlist',
                    'table' => 'kr_watchlists',
                    'conditions' => [
                        'KrWatchlist.key_result_id = KeyResult.id',
                        'KrWatchlist.key_result_id' => $krIds
                    ]
                ],
                [
                    'alias' => 'Watchlist',
                    'table' => 'watchlists',
                    'conditions' => [
                        'KrWatchlist.watchlist_id = Watchlist.id',
                        'Watchlist.user_id' => $userId,
                        'Watchlist.team_id' => $teamId
                    ]
                ]
            ],
            "fields" => "KeyResult.id"
        ]);
        $watchedKrsIds = Hash::extract($watchedKrs, "{n}.KeyResult.id");

        return array_map(function($kr) use ($watchedKrsIds) {
            $kr['KeyResult']['watched'] = in_array($kr['KeyResult']['id'], $watchedKrsIds);
            return $kr;
        }, $keyResults);
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
        // for error log in https://goalous.slack.com/archives/C0LV38PC6/p1496983479848440
        // TODO: I dont't know the cause of above error. So, logging it.
        if (empty($keyResult)) {
            /** @var TeamMember $TeamMember */
            $TeamMember = ClassRegistry::init('TeamMember');
            $this->log(sprintf("failed to find user! keyResultData: %s, teamId: %s, loggedIn user: %s",
                var_export($keyResult, true), $TeamMember->current_team_id, $TeamMember->my_uid));
            $this->log(Debugger::trace());
        }

        // 完了/未完了
        if ($keyResult['value_unit'] == KeyResult::UNIT_BINARY) {
            $keyResult['display_value'] = __('Complete/Incomplete');
            $keyResult['display_in_progress_bar'] = boolval($keyResult['completed'] ?? false) ? __('Completed') : __('Incomplete');
            $keyResult['progress_rate'] = boolval($keyResult['completed'] ?? false) ? 100 : 0;
            return $keyResult;
        }
        $NumberEx = new NumberExHelper(new View());
        // 少数の不要な0を取り除く
        // 桁数が多いと指数表記(111E+など)になるため、ここで数字をフォーマットする
        $keyResult['start_value'] = $this->formatBigFloat($keyResult['start_value']);
        $keyResult['target_value'] = $this->formatBigFloat($keyResult['target_value']);
        $keyResult['current_value'] = $this->formatBigFloat($keyResult['current_value']);
        $keyResult['progress_rate'] = $NumberEx->calcProgressRate($keyResult['start_value'], $keyResult['target_value'],
            $keyResult['current_value']);
        // 3桁区切りversion
        $keyResult['comma_start_value'] = AppUtil::formatThousand($keyResult['start_value']);
        $keyResult['comma_target_value'] = AppUtil::formatThousand($keyResult['target_value']);
        $keyResult['comma_current_value'] = AppUtil::formatThousand($keyResult['current_value']);
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
        $keyResult['start_value_with_unit'] = $headUnit . $keyResult['start_value'] . $tailUnit;
        $keyResult['target_value_with_unit'] = $headUnit . $keyResult['comma_target_value'] . $tailUnit;
        $keyResult['current_value_with_unit'] = $headUnit . $keyResult['comma_current_value'] . $tailUnit;
        $keyResult['display_value'] = "{$keyResult['start_value_with_unit']} {$symbol} {$keyResult['target_value_with_unit']}";
        $keyResult['display_in_progress_bar'] = "{$keyResult['current_value_with_unit']} {$symbol} {$keyResult['target_value_with_unit']}";
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
                Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
            }

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
                'start_value'   => 0,
                'target_value'  => 1,
                'current_value' => 0,
            ]);
        } else {
            $updateKr = am($updateKr, [
                'target_value'  => Hash::get($requestData, 'target_value'),
                'current_value' => Hash::get($requestData, 'current_value'),
            ]);
            // 単位変更している場合だけ開始値入力が有効になるので更新に含める
            if ($requestData['value_unit'] != $kr['value_unit']) {
                $updateKr['start_value'] = Hash::get($requestData, 'start_value');
            }
        }

        if (empty($kr['completed']) && $updateKr['target_value'] == $updateKr['current_value']) {
            // 未完了 && 進捗現在値が目標値に達してたら完了とする
            $updateKr['completed'] = time();
        } elseif (!empty($kr['completed']) && $updateKr['target_value'] != $updateKr['current_value']) {
            // 完了 && 進捗現在値が目標値に未達なら未完了とする
            $updateKr['completed'] = null;
        }

        if (Hash::get($requestData, 'start_date')) {
            $updateKr['start_date'] = $requestData['start_date'];
        }
        if (Hash::get($requestData, 'end_date')) {
            $updateKr['end_date'] = $requestData['end_date'];
        }
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
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var KrChangeLog $KrChangeLog */
        $KrChangeLog = ClassRegistry::init("KrChangeLog");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init("KrProgressLog");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        try {
            // トランザクション開始
            $KeyResult->begin();
            // KR更新
            $updateKr = $this->buildUpdateKr($krId, $requestData);
            if (!$KeyResult->save($updateKr, false)) {
                throw new Exception(sprintf("Failed update kr. data:%s"
                    , var_export($updateKr, true)));
            }
            // 進捗単位を変更した場合はKR進捗リセット(アクションによるKR進捗ログ削除)
            $kr = $this->get($krId);
            if ($requestData['value_unit'] != $kr['value_unit']) {
                if (!$KrProgressLog->deleteByKrId($krId)
                ) {
                    throw new Exception(sprintf("Failed reset kr progress log. krId:%s", $krId));
                }
                /** @var KrValuesDailyLogService $KrValuesDailyLogService */
                $KrValuesDailyLogService = ClassRegistry::init("KrValuesDailyLogService");
                /** @var KrValuesDailyLog $KrValuesDailyLog */
                $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");

                // KR進捗日次ログ削除
                if (!$KrValuesDailyLog->softDeleteAll(['key_result_id' => $krId])) {
                    throw new Exception(sprintf("Failed delete kr_values_daily_log. data:%s",
                        var_export(compact('krId'), true)));
                }

                // KR進捗日次ログキャッシュ削除(チーム単位)
                $KrValuesDailyLogService->deleteCache();

            }

            // KR変更ログ保存
            if (!$KrChangeLog->saveSnapshot($userId, $krId, $KrChangeLog::TYPE_MODIFY)) {
                throw new Exception(sprintf("Failed save kr snapshot. krId:%s", $krId));
            }

            // TKRかつ紐づくゴールが認定対象の場合、再申請のステータスに変更
            $goalId = Hash::get($kr, 'goal_id');
            if (Hash::get($kr, 'tkr_flg') && $GoalMemberService->isApprovableByGoalId($goalId, $userId)) {
                $goalMemberId = Hash::get($GoalMember->getUnique($userId, $goalId), 'GoalMember.id');
                if (empty($goalMemberId)) {
                    throw new Exception(sprintf("Not exist goal_member. data:%s"
                        , var_export(compact('goalId', 'userId'), true)));
                }
                $updateGoalMember = [
                    'id'                   => $goalMemberId,
                    'approval_status'      => GoalMember::APPROVAL_STATUS_REAPPLICATION,
                    'is_target_evaluation' => GoalMember::IS_NOT_TARGET_EVALUATION
                ];
                if (Hash::get($requestData, 'priority') !== null) {
                    $updateGoalMember['priority'] = $requestData['priority'];
                }
                if (!$GoalMember->save($updateGoalMember, false)) {
                    throw new Exception(sprintf("Failed update goal_member. data:%s"
                        , var_export($updateGoalMember, true)));
                }
                $coachId = $TeamMember->getCoachUserIdByMemberUserId($userId);
                //コーチの認定件数キャッシュを削除
                Cache::delete($GoalMember->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
            }

            // ダッシュボードのKRキャッシュ削除
            $this->removeGoalMembersCacheInDashboard($goalId, false);

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

    /**
     * トップページ右カラムに初期表示するKR数を取得
     * - キャッシュが存在する場合はキャッシュを返す
     *
     * @return int
     */
    function countMine($goalId = null, bool $includeComplete = false, $userId = null): int
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        $resCount = $KeyResult->countMine($goalId, $includeComplete, $userId);
        return $resCount;

        // キャッシュ管理がなされてないためコメントアウト
//        // キャッシュ検索
//        $resCount = 0;
//        $cachedCount = Cache::read($KeyResult->getCacheKey(CACHE_KEY_MY_KR_COUNT, true), 'user_data');
//        if ($cachedCount !== false) {
//            $resCount = $cachedCount;
//        } else {
//            // キャッシュが存在しない場合はquery投げて結果をキャッシュに保存
//            $resCount = $KeyResult->countMine();
//            Cache::write($KeyResult->getCacheKey(CACHE_KEY_MY_KR_COUNT, true), $resCount, 'user_data');
//        }
//        return $resCount;
    }

    /**
     * 最新アクション日時を更新
     * - 存在する中で一番新しいアクションのcreatedをlatest_actionedとして登録
     * - アクションが存在しなければnullを登録
     *
     * @param  $krId
     *
     * @return bool
     */
    function updateLatestActioned(int $krId): bool
    {
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        //　最新アクション日時取得
        $latestAction = $ActionResult->getLatestAction($krId);
        $latestActioned = $latestAction['ActionResult']['created'] ?? null;

        // KR更新
        $KeyResult->id = $krId;
        $saved = $KeyResult->saveField('latest_actioned', $latestActioned);

        return (bool)$saved;
    }

    /**
     * 全ゴールメンバーのダッシュボードのキャッシュを削除
     * - $withCountがtrueの場合はKRカウントキャッシュも削除する
     *
     * @param      int 　$goalId
     * @param bool $withCount
     */
    function removeGoalMembersCacheInDashboard(int $goalId, bool $withCount = true)
    {
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");

        $memberUserids = $GoalMember->findAllMemberUserIds($goalId);
        foreach ($memberUserids as $userId) {
            Cache::delete($GoalMember->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true,
                $userId), 'user_data');
            if ($withCount) {
                Cache::delete($GoalMember->getCacheKey(CACHE_KEY_MY_KR_COUNT, true,
                    $userId), 'user_data');
            }
        }
    }

    /**
     * ダッシュボード表示用にKR一覧を整形
     *
     * @param  array $krs
     *
     * @return array
     */
    function processInDashboard(array $krs): array
    {
        /** @var ActionService $ActionService */
        $ActionService = ClassRegistry::init("ActionService");

        $krs = Hash::map($krs, '', function ($kr) use ($ActionService) {
            $kr['action_results'] = $ActionService->groupByUser($kr['action_results']);
            $kr['key_result']['action_message'] = $this->generateActionMessage($kr);
            // 先頭から3番目までのデータを返す
            $kr['action_results'] = array_slice($kr['action_results'], 0, 3);
            return $kr;
        });

        return $krs;
    }

    /**
     * アクションを促すメッセージを生成する
     *
     * @param  $kr
     *
     * @return string
     */
    function generateActionMessage(array $kr): string
    {
        App::import('View', 'Helper/TimeExHelper');
        $TimeEx = new TimeExHelper(new View());

        $actionCount = count($kr['action_results']);
        $latestActioned = $kr['key_result']['latest_actioned'];
        $completed = $kr['key_result']['completed'];

        if ($completed) {
            return __('Completed this on %s.', $TimeEx->formatDateI18n($completed));
        } elseif ($actionCount > 0) {
            return __('%s member(s) actioned recently.',
                '<span class="font_verydark font_bold">' . $actionCount . '</span>');
        } elseif ($latestActioned) {
            return __("Take action since %s !", $TimeEx->formatDateI18n($latestActioned));
        } else {
            return __('Take first action to this !');
        }
    }

    /**
     * KR削除
     * TODO:削除ポリシー決定後、削除処理が不足していたら対応
     *
     * @param $krId
     *
     * @return bool
     */
    function delete(int $krId): bool
    {
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var KrValuesDailyLogService $KrValuesDailyLogService */
        $KrValuesDailyLogService = ClassRegistry::init("KrValuesDailyLogService");
        /** @var KrValuesDailyLog $KrValuesDailyLog */
        $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");

        try {
            // トランザクション開始
            $KeyResult->begin();

            // KR削除
            // TODO:将来的にコメントアウトを外す
            // コメントアウト理由：deleteメソッドはSoftDeleteBehaviorのbeforeDeleteメソッドが原因で成功失敗に関わらずfalseを返しているため
//            if (!$KeyResult->delete($krId)) {
//                throw new Exception(sprintf("Failed delete kr. data:%s", var_export(compact('krId'), true)));
//            }
            $KeyResult->delete($krId);

            //関連アクションの紐付け解除
            if (!$ActionResult->releaseKr($krId)) {
                throw new Exception(sprintf("Failed release action_result. data:%s",
                    var_export(compact('krId'), true)));
            }
            // KR進捗日次ログ削除
            if (!$KrValuesDailyLog->softDeleteAll(['key_result_id' => $krId])) {
                throw new Exception(sprintf("Failed delete kr_values_daily_log. data:%s",
                    var_export(compact('krId'), true)));
            }

            $KeyResult->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $KeyResult->rollback();
            return false;
        }
        // KR進捗日次ログキャッシュ削除(チーム単位)
        $KrValuesDailyLogService->deleteCache();
        // アクション可能ゴール一覧キャッシュ削除
        Cache::delete($KeyResult->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
        // ユーザページのマイゴール一覧キャッシュ削除
        Cache::delete($KeyResult->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

        return true;
    }

    public function findForKeyResultList(FindForKeyResultListRequest $request): array
    {
        $now = GoalousDateTime::now();
        $options = [
            'conditions' => [
                'GoalMember.user_id'    => $request->getUserId(),
                'KeyResult.end_date >=' => $request->getCurrentTermModel()['start_date'],
                'KeyResult.end_date <=' => $request->getCurrentTermModel()['end_date'],
                'KeyResult.team_id'     => $request->getTeamId(),
                'GoalMember.del_flg'    => false,
                'Goal.end_date >='      => $now->format('Y-m-d'),
            ],
            'order'      => [
                'KeyResult.latest_actioned' => 'desc',
                'KeyResult.priority'        => 'desc',
                'KeyResult.created'         => 'desc'
            ],
            'fields'     => [
                'KeyResult.*',
                'Goal.*',
                'GoalMember.priority'
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'goal_members',
                    'alias'      => 'GoalMember',
                    'conditions' => [
                        'GoalMember.goal_id = KeyResult.goal_id'
                    ]
                ],
            ],
            'contain'    => [
                'Goal',
                'ActionResult' => [
                    'fields'     => ['user_id'],
                    'order'      => [
                        'ActionResult.created' => 'desc'
                    ],
                    'User'
                ]
            ]
        ];
        if ($request->getGoalIdSelected()) {
            $options['conditions']['Goal.id'] = $request->getGoalIdSelected();
        }
        if ($request->getLimit()) {
            $options['limit'] = $request->getLimit();
        }
        if ($request->getOnlyKrIncomplete()) {
            $options['conditions']['KeyResult.completed'] = null;
        }

        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        return $KeyResult->useType()->find('all', $options);
    }
}
