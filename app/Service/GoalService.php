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
App::uses('KeyResult', 'Model');
App::uses('EvaluateTerm', 'Model');
App::uses('GoalLabel', 'Model');
App::uses('ApprovalHistory', 'Model');
App::uses('GoalMember', 'Model');
App::uses('Post', 'Model');
App::uses('KrChangeLog', 'Model');
App::uses('KrProgressLog', 'Model');
App::uses('KrValuesDailyLog', 'Model');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'GoalApprovalService');
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');
// TODO:NumberExHelperだけimportではnot foundになってしまうので要調査
App::uses('NumberExHelper', 'View/Helper');

/**
 * Class GoalService
 */
class GoalService extends AppService
{
    const TERM_TYPE_CURRENT = 'current';
    const TERM_TYPE_PREVIOUS = 'previous';
    const TERM_TYPE_NEXT = 'next';

    public $goalValidateFields = [
        "name",
        "goal_category_id",
        "photo",
        "term_type",
        "description",
        "end_date",
        "priority",
    ];

    /* ゴールの拡張種別 */
    const EXTEND_GOAL_LABELS = "GOAL:EXTEND_GOAL_LABELS";
    const EXTEND_TOP_KEY_RESULT = "GOAL:EXTEND_TOP_KEY_RESULT";
    const EXTEND_GOAL_MEMBERS = "GOAL:EXTEND_GOAL_MEMBERS";

    /* グラフ設定 */
    const GRAPH_MAX_BUFFER_DAYS = 10;
    const GRAPH_TARGET_DAYS = 30;
    const GRAPH_SWEET_SPOT_MAX_TOP = 100;
    const GRAPH_SWEET_SPOT_MAX_BOTTOM = 60;

    /* ゴールキャッシュ */
    private static $cacheList = [];

    /**
     * idによる単体データ取得
     *
     * @param       $id
     * @param null  $userId
     * @param array $extends
     *
     * @return array|mixed
     */
    function get($id, $userId = null, $extends = [])
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
            $data = self::$cacheList[$id];
            return $this->extend($data, $userId, $extends);
        }

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");
        $TimeExHelper = new TimeExHelper(new View());

        $data = self::$cacheList[$id] = Hash::extract($Goal->findById($id), 'Goal');
        if (empty($data)) {
            return $data;
        }

        // 各サイズの画像URL追加
        $data = $Goal->attachImgUrl($data, 'Goal');

        // 評価期間の設定
        $currentTerm = $EvaluateTerm->getCurrentTermData();
        $nextTerm = $EvaluateTerm->getNextTermData();
        if ($currentTerm['start_date'] <= $data['start_date']
            && $data['start_date'] <= $currentTerm['end_date']
        ) {
            $data['term_type'] = 'current';
        } elseif ($nextTerm['start_date'] <= $data['start_date']
            && $data['start_date'] <= $nextTerm['end_date']
        ) {
            $data['term_type'] = 'next';
        } else {
            $data['term_type'] = 'current';
        }

        // 日付フォーマット
        $data['start_date'] = $TimeExHelper->dateFormat($data['start_date'], $currentTerm['timezone']);
        $data['end_date'] = $TimeExHelper->dateFormat($data['end_date'], $currentTerm['timezone']);

        // 認定可能フラグ追加
        $data['is_approvable'] = false;
        $goalLeaderId = $GoalMember->getGoalLeaderId($id);
        if ($goalLeaderId) {
            $data['is_approvable'] = $GoalMemberService->isApprovableGoalMember($goalLeaderId);
        }

        // キャッシュ変数に保存
        self::$cacheList[$id] = $data;

        // データ拡張
        return $this->extend($data, $userId, $extends);
    }

    /**
     * データ拡張
     *
     * @param $data
     * @param $userId
     * @param $extends
     *
     * @return mixed
     */
    function extend($data, $userId, $extends)
    {
        if (empty($data) || empty($extends)) {
            return $data;
        }

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        if (in_array(self::EXTEND_GOAL_LABELS, $extends)) {
            $data['goal_labels'] = Hash::extract($Goal->GoalLabel->findByGoalId($data['id']), '{n}.Label');
        }

        if (in_array(self::EXTEND_TOP_KEY_RESULT, $extends)) {
            /** @var KeyResultService $KeyResultService */
            $KeyResultService = ClassRegistry::init("KeyResultService");
            $kr = Hash::extract($Goal->KeyResult->getTkr($data['id']), 'KeyResult');
            $data['top_key_result'] = $KeyResultService->processKeyResult($kr);
        }
        if (in_array(self::EXTEND_GOAL_MEMBERS, $extends)) {
            $data['goal_member'] = Hash::extract($Goal->GoalMember->getUnique($userId, $data['id']), 'GoalMember');
        }
        return $data;
    }

    /**
     * ゴール更新
     *
     * @param $userId
     * @param $goalId
     * @param $requestData
     *
     * @return bool
     */
    function update($userId, $goalId, $requestData)
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init("GoalLabel");
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var KrChangeLog $KrChangeLog */
        $KrChangeLog = ClassRegistry::init("KrChangeLog");
        /** @var KrProgressLog $KrProgressLog */
        $KrProgressLog = ClassRegistry::init("KrProgressLog");

        try {
            // トランザクション開始
            $Goal->begin();

            // ゴール・TKR・コラボレーター取得
            $goal = $this->get($goalId, $userId, [
                self::EXTEND_TOP_KEY_RESULT,
                self::EXTEND_GOAL_MEMBERS
            ]);
            // 本来TKRやコラボレーターが存在しないことは有りえないが一応判定
            if (empty($goal['top_key_result'])) {
                throw new Exception(sprintf("Not exist tkr. goalId:%d", $goalId));
            }
            if (empty($goal['goal_member'])) {
                throw new Exception(sprintf("Not exist goal_member. goalId:%d userId:%d", $goalId, $userId));
            }

            // ゴール更新
            $updateGoal = $this->buildUpdateGoalData($goalId, $requestData);
            if (!$Goal->save($updateGoal, false)) {
                throw new Exception(sprintf("Failed update goal. data:%s"
                    , var_export($updateGoal, true)));
            }

            // 優先度更新
            $GoalMember->id = $goal['goal_member']['id'];
            if (!$GoalMember->saveField('priority', $requestData['priority'])) {
                throw new Exception(sprintf("Failed to update GoalMember priority. goalMemberId:%s priority:%s",
                    $goal['goal_member']['id'], $requestData['priority']));
            }

            // TKR更新
            $tkrId = $goal['top_key_result']['id'];
            $inputTkrData = Hash::get($requestData, 'key_result');
            $updateTkr = $KeyResultService->buildUpdateKr($tkrId, $inputTkrData, false);
            if (!$KeyResult->save($updateTkr, false)) {
                throw new Exception(sprintf("Failed update tkr. data:%s"
                    , var_export($updateTkr, true)));
            }

            // TKRの進捗単位を変更した場合は進捗リセット
            if ($goal['top_key_result']['value_unit'] != $updateTkr['value_unit']) {
                if (!$KrProgressLog->deleteByKrId($tkrId)) {
                    throw new Exception(sprintf("Failed reset kr progress log. krId:%s", $tkrId));
                }
            }

            // KR変更ログ保存
            if (!$KrChangeLog->saveSnapshot($userId, $tkrId, $KrChangeLog::TYPE_MODIFY)) {
                throw new Exception(sprintf("Failed save kr snapshot. krId:%s", $tkrId));
            }

            // ゴールラベル更新
            if (!$GoalLabel->saveLabels($goalId, $requestData['labels'])) {
                throw new Exception(sprintf("Failed save labels. goalId:%s labels:%s"
                    , $goalId, var_export($requestData['labels'], true)));
            }

            // 評価対象者かつ認定対象ゴールの場合のみ実行する処理
            if ($GoalMemberService->isApprovableByGoalId($goalId, $userId)) {
                // コラボレーター更新(再申請のステータスに変更)
                $updateGoalMember = [
                    'id'                   => $goal['goal_member']['id'],
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

                //コーチの認定件数を更新(キャッシュを削除)
                $coachId = $TeamMember->getCoachUserIdByMemberUserId($userId);
                if ($coachId) {
                    Cache::delete($Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId), 'user_data');
                }
            }

            // ダッシュボードのKRキャッシュ削除
            $KeyResultService->removeGoalMembersCacheInDashboard($goalId, false);

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
     * ゴール作成
     *
     * @param $userId
     * @param $requestData
     *
     * @return bool
     */
    function create($userId, $requestData)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init("GoalLabel");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        try {
            // トランザクション開始
            $Goal->begin();
            $data = [
                'Goal'      => $requestData,
                'KeyResult' => [Hash::get($requestData, 'key_result')],
                'Label'     => Hash::get($requestData, 'labels'),
            ];

            $data['Goal']['team_id'] = $Goal->current_team_id;
            $data['Goal']['user_id'] = $userId;

            $goal_term = $Goal->getGoalTermFromPost($data);

            $data = $Goal->convertGoalDateFromPost($data, $goal_term, $data['Goal']['term_type']);

            $data = $Goal->buildTopKeyResult($data, $goal_term);
            $data = $Goal->buildGoalMemberDataAsLeader($data);

            // setting default image if default image is chosen and image is not selected.
            if (Hash::get($data, 'Goal.img_url') && !Hash::get($data, 'Goal.photo')) {
                $data['Goal']['photo'] = $data['Goal']['img_url'];
                unset($data['Goal']['img_url']);
            }

            $Goal->create();
            $Goal->saveAll($data);

            $newGoalId = $Goal->getLastInsertID();
            if (!$newGoalId) {
                throw new Exception(sprintf("Failed create goal. data:%s"
                    , var_export($data, true)));
            }

            if (!$GoalLabel->saveLabels($newGoalId, $data['Label'])) {
                throw new Exception(sprintf("Failed save labels. data:%s"
                    , var_export($data, true)));
            }

            if (!$Post->addGoalPost(Post::TYPE_CREATE_GOAL, $newGoalId)) {
                throw new Exception(sprintf("Failed save labels. data:%s"
                    , var_export($data, true)));
            }

            // ダッシュボードのKRキャッシュ削除
            $KeyResultService->removeGoalMembersCacheInDashboard($newGoalId);

            $Goal->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Goal->rollback();
            return false;
        }
        return $newGoalId;
    }

    /**
     * ゴール更新データ作成
     *
     * @param $goalId
     * @param $requestData
     *
     * @return array
     */
    private function buildUpdateGoalData($goalId, $requestData)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        $updateData = [
            'id'          => $goalId,
            'name'        => $requestData['name'],
            'description' => $requestData['description'],
        ];

        if (!empty($requestData['goal_category_id'])) {
            $updateData['goal_category_id'] = $requestData['goal_category_id'];
        }
        if (!empty($requestData['end_date'])) {
            $goalTerm = $Goal->getGoalTermData($goalId);
            $updateData['end_date'] = AppUtil::getEndDateByTimezone($requestData['end_date'], $goalTerm['timezone']);
        }
        if (!empty($requestData['photo'])) {
            $updateData['photo'] = $requestData['photo'];
        }
        return $updateData;
    }

    /**
     * ゴール登録・更新のバリデーション
     *
     * @param array    $data
     * @param array    $fields
     * @param int|null $goalId
     *
     * @return array
     */
    function validateSave($data, $fields, $goalId = null)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var Label $Label */
        $Label = ClassRegistry::init("Label");
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");

        // 編集の場合評価期間の選択は無い為、既に登録されているゴールの開始日と終了日から評価期間を割り出し、入力した終了日のバリデーションに利用する
        if (!empty($goalId) && (empty($fields) || in_array('end_date', $fields))) {
            $goal = $this->get($goalId);
            $data['term_type'] = $EvaluateTerm->getTermType(strtotime($goal['start_date']),
                strtotime($goal['end_date']));
        }

        $goalFields = array_intersect($this->goalValidateFields, $fields);
        $validationErrors = $this->validationExtract(
            $Goal->validateGoalPOST($data, $goalFields, $goalId)
        );

        // ゴールラベル バリデーション
        if (empty($fields) || in_array('labels', $fields)) {
            $validationLabelsError = $Label->validationLabelNames($data);
            if (!empty($validationLabelsError)) {
                $validationErrors = array_merge(
                    $validationErrors,
                    ['labels' => $validationLabelsError]
                );
            }
        }

        // TKR バリデーション
        if (empty($fields) || in_array('key_result', $fields)) {
            $krValidation = $KeyResult->validateKrPOST($data['key_result'], false, $goalId);

            if ($krValidation !== true) {
                $validationErrors['key_result'] = $this->validationExtract($krValidation);
            }
        }

        // コメントバリデーション
        if (empty($fields) || in_array('approval_history', $fields)) {
            $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
            $ApprovalHistory->set(Hash::get($data, 'approval_history'));
            if (!$ApprovalHistory->validates()) {
                $validationErrors['approval_history'] = $this->validationExtract($ApprovalHistory->validationErrors);
            }
        }
        return $validationErrors;
    }

    /**
     * 対象のゴールが今季以降のゴールか
     *
     * @param int $goalId
     *
     * @return bool
     */
    function isGoalAfterCurrentTerm(int $goalId): bool
    {
        $goal = $this->get($goalId);
        if (empty($goal)) {
            return false;
        }

        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");

        $currentTerm = $EvaluateTerm->getCurrentTermData();
        return strtotime($goal['start_date']) >= $currentTerm['start_date'];
    }

    /**
     * ゴール一覧をビュー用に整形
     * TODO:引数に依存しすぎている。渡す側は＄goalsにKeyResultやTargetCollaboを含めなきゃいけないことがわからない
     *
     * @param  array $goals [description]
     *
     * @return array $goals [description]
     */
    function processGoals($goals)
    {
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        foreach ($goals as $key => $goal) {
            // 進捗を計算
            if (!empty($goal['KeyResult'])) {
                $sumPriorities = $this->sumPriorities($goal['KeyResult']);
                $goals[$key]['Goal']['progress'] = $this->getProgress($goal['KeyResult'], $sumPriorities);
            }
            // 認定有効フラグを追加
            if (!empty($goal['TargetCollabo'])) {
                $goals[$key]['TargetCollabo']['is_approval_enabled'] = $GoalApprovalService->isApprovable($goal['TargetCollabo']['user_id']);
            }
            // リーダー変更可能フラグを追加
            $goals[$key]['Goal']['can_change_leader'] = $GoalMemberService->canChangeLeader(Hash::get($goal,
                'Goal.id'));
        }
        return $goals;
    }

    /**
     * ゴールの進捗をキーリザルト一覧から取得
     * TODO:将来的にゴールIDのみを引数として渡し、そのゴールIDから各KR取得→KR進捗率計算→ゴール進捗率計算の順に処理を行うようにする。またキャッシュ化も必要。
     *
     * @param  array $keyResults      [description]
     * @param int    $sumKrPriorities 対象KRの重要度の合計
     *
     * @return float $res
     */
    function getProgress(array $keyResults, int $sumKrPriorities): float
    {
        $goalProgress = 0;
        $NumberExHelper = new NumberExHelper(new View());

        $errFlg = false;

        foreach ($keyResults as $keyResult) {
            if (!Hash::check($keyResult, 'start_value')
                || !Hash::check($keyResult, 'target_value')
                || !Hash::check($keyResult, 'current_value')
            ) {
                $errFlg = true;
                $progress = 0;
            } else {
                $progress = $NumberExHelper->calcProgressRate($keyResult['start_value'], $keyResult['target_value'],
                    $keyResult['current_value']);
            }
            if ($progress > 100) {
                //目標値を下げた場合に過去の進捗が100を超えるケースがあるので補正
                $progress = 100;
            } elseif ($progress < 0) {
                //開始値を上げた場合に過去の進捗が0を下回るケースがあるので補正(現状あり得ないが将来的にありうるので)
                $progress = 0;
            }
            $goalProgress += $progress * $keyResult['priority'] / $sumKrPriorities;
        }
        $goalProgress = round($goalProgress, 2);

        // 本メソッドを呼ぶ箇所が多いため抜け漏れを検知する為にtrycatchを入れる
        try {
            if ($errFlg) {
                throw new Exception(sprintf("Not found field to calc progress    %s",
                    var_export(compact('keyResults'), true)));
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        return $goalProgress;
    }

    /**
     * KRの重要度の合計を返す
     *
     * @param array $krs [[priority=>"",..],[priority=>"",..]]
     *
     * @return int
     */
    function sumPriorities(array $krs): int
    {
        return array_sum(array_column($krs, 'priority'));
    }

    /**
     * あてはまる評価期間をゴールごとに設定
     *
     * @param $goals
     * @param $loginUserId
     *
     * @return mixed
     */
    function extendTermType($goals, $loginUserId)
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");

        // 評価期間取得
        $currentTerm = $EvaluateTerm->getCurrentTermData();
        $nextTerm = $EvaluateTerm->getNextTermData();

        $goalIds = Hash::extract($goals, '{n}.Goal.id');
        $countKrEachGoal = $KeyResult->countEachGoalId($goalIds);

        // あてはまる評価期間をゴールごとに設定
        foreach ($goals as $k => &$v) {
            $goalId = $v['Goal']['id'];
            $v['Goal']['term_type'] = $this->getTermType($v['Goal']['start_date'], $currentTerm, $nextTerm);
            $v['Goal']['can_exchange_tkr'] = $this->canExchangeTkr($goalId, $v['Goal']['term_type'], $loginUserId);
            $v['Goal']['kr_count'] = $countKrEachGoal[$goalId];
        }

        return $goals;
    }

    /**
     * ゴールのあてはまる評価期間を取得
     *
     * @param $startDate
     * @param $currentTerm
     * @param $nextTerm
     *
     * @return string
     */
    function getTermType($startDate, $currentTerm = null, $nextTerm = null)
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");

        // 評価期間取得
        if (empty($currentTerm)) {
            $currentTerm = $EvaluateTerm->getCurrentTermData();
        }
        if (empty($nextTerm)) {
            $nextTerm = $EvaluateTerm->getNextTermData();
        }

        // あてはまる評価期間をゴールごとに設定
        if ($currentTerm['start_date'] <= $startDate
            && $startDate <= $currentTerm['end_date']
        ) {
            return self::TERM_TYPE_CURRENT;
        }

        if ($nextTerm['start_date'] <= $startDate
            && $startDate <= $nextTerm['end_date']
        ) {
            return self::TERM_TYPE_NEXT;
        }

        return self::TERM_TYPE_PREVIOUS;
    }

    /**
     * TKR変更可能か
     *
     * @param $goalId
     * @param $termType
     * @param $loginUserId
     *
     * @return string
     */
    function canExchangeTkr($goalId, $termType, $loginUserId)
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        // 前期
        if ($termType == GoalService::TERM_TYPE_PREVIOUS) {
            return false;
        }

        if ($termType == GoalService::TERM_TYPE_CURRENT) {
            $currentTermId = $EvaluateTerm->getCurrentTermId();
            // 評価開始中でないか
            if ($EvaluateTerm->isStartedEvaluation($currentTermId)) {
                return false;
            }
        }

        // KR数取得
        $krCount = $KeyResult->getKrCount($goalId);
        // TKRとして変更するKRが存在するか
        if ($krCount < 2) {
            return false;
        }

        // リーダーか
        if (!$GoalMemberService->isLeader($goalId, $loginUserId)) {
            return false;
        }
        return true;
    }

    /**
     * アクション可能なゴール取得
     *
     * @param int $userId
     *
     * @return array
     */
    function findCanAction(int $userId): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        // コラボも含めて自分のゴールリスト取得
        return $Goal->findCanAction($userId);
    }

    /**
     * ゴール完了
     *
     * @param $goalId
     *
     * @return bool
     */
    function complete(int $goalId): bool
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var Post $Post */
        $Post = ClassRegistry::init("Post");

        try {
            $Goal->begin();

            // ゴール完了
            $Goal->complete($goalId);
            // ゴール完了の投稿
            if (!$Post->addGoalPost(Post::TYPE_GOAL_COMPLETE, $goalId, null)) {
                throw new Exception("Create goal complete post. goalId:" . $goalId);
            }

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
     * 与えられた対象終了日と対象日数からグラフの開始日、終了日を求める
     * - グラフの開始日、終了日は必ず期内となる
     * - グラフ開始日にバッファ日数を加算した日が期の開始日以前になる場合は、グラフ開始日に期の開始日をセット
     * - 指定された終了日が期の終了日に近づいたら、グラフ終了日は期の終了日をセット
     * - それ以外は$targetDays前から本日までの日付を(バッファ日数を考慮)
     *
     * @param int $targetEndTimestamp
     * @param int $targetDays
     * @param int $maxBufferDays
     *
     * @return array ['graphStartDate'=>string|null,'graphEndDate'=>string|null,'plotDataEndDate'=>string|null]
     * @throws Exception
     */
    function getGraphRange(
        int $targetEndTimestamp,
        int $targetDays = self::GRAPH_TARGET_DAYS,
        int $maxBufferDays = 0
    ): array {
        //initialize variables
        $ret = [
            'graphStartDate'  => null,
            'graphEndDate'    => null,
            'plotDataEndDate' => null,
        ];

        //今期の情報取得
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTimestamp = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTimestamp = $EvaluateTerm->getCurrentTermData(true)['end_date'];

        //バリデーション
        $validOrErrorMsg = $this->validateGraphRange(
            $targetEndTimestamp,
            $targetDays,
            $maxBufferDays,
            $termStartTimestamp,
            $termEndTimestamp
        );
        if ($validOrErrorMsg !== true) {
            throw new Exception($validOrErrorMsg);
        }

        //期の開始日から指定グラフ終了日までの日数が少ない場合(以下がその定義)は、グラフ開始日に期の開始日をセット
        //期の開始日から指定グラフ終了日までの日数が最小プロット可能日数を下回る場合
        $daysFromTermStartToTargetEnd = AppUtil::diffDays($termStartTimestamp, $targetEndTimestamp);
        $daysMinPlot = $targetDays - $maxBufferDays;
        if ($daysFromTermStartToTargetEnd < $daysMinPlot) {
            $ret['graphStartDate'] = AppUtil::dateYmd($termStartTimestamp);
            $ret['graphEndDate'] = AppUtil::dateYmd($termStartTimestamp + (($targetDays - 1) * DAY));
            $ret['plotDataEndDate'] = AppUtil::dateYmd($targetEndTimestamp);
            return $ret;
        }

        if ($maxBufferDays > 0) {
            //指定グラフ終了日から期の終了日まで日数が少ない場合(以下がその定義)は、グラフ終了日は期の終了日をセット
            //指定グラフ終了日が期の終了日からバッファ日数を引いた日を超えた場合
            $termEndBeforeMaxBufferDaysTimestamp = $termEndTimestamp - $maxBufferDays * DAY;
            if ($targetEndTimestamp > $termEndBeforeMaxBufferDaysTimestamp) {
                $ret['graphStartDate'] = AppUtil::dateYmd($termEndTimestamp - (($targetDays - 1) * DAY));
                $ret['graphEndDate'] = AppUtil::dateYmd($termEndTimestamp);
                $ret['plotDataEndDate'] = $ret['graphEndDate'];
                return $ret;
            }
        }

        //$targetDays前から本日まで(バッファ日数を考慮)
        $targetStartTimestamp = $targetEndTimestamp - (($targetDays - 1) * DAY);
        $ret['graphStartDate'] = AppUtil::dateYmd($targetStartTimestamp + ($maxBufferDays * DAY));
        $ret['graphEndDate'] = AppUtil::dateYmd($targetEndTimestamp + ($maxBufferDays * DAY));
        $ret['plotDataEndDate'] = AppUtil::dateYmd($targetEndTimestamp);

        return $ret;
    }

    /**
     * グラフ範囲指定のバリデーション
     * okならtrue,ngならメッセージを返す
     *
     * @param int $targetEndTimestamp
     * @param int $targetDays
     * @param int $maxBufferDays
     * @param int $termStartTimestamp
     * @param int $termEndTimestamp
     *
     * @return true|string
     */
    function validateGraphRange(
        int $targetEndTimestamp,
        int $targetDays,
        int $maxBufferDays,
        int $termStartTimestamp,
        int $termEndTimestamp
    ) {
        //対象日数が1未満はありえない
        if ($targetDays < 1) {
            $this->log(sprintf("%s%s [method:%s] wrong target days. targetDays:%s",
                    __FILE__, __LINE__, __METHOD__, $targetDays)
            );
            return __('Wrong target days.');
        }
        //バッファ日数が0未満はありえない
        if ($maxBufferDays < 0) {
            $this->log(sprintf("%s%s [method:%s] wrong buffer days. maxBufferDays:%s",
                    __FILE__, __LINE__, __METHOD__, $maxBufferDays)
            );
            return __('Wrong buffer days.');
        }
        //指定日数からバッファ日数を引いたものが0以下はありえない
        if ($targetDays - $maxBufferDays <= 0) {
            $this->log(sprintf("%s%s [method:%s] wrong targetDays:%s or maxBufferDays:%s",
                    __FILE__, __LINE__, __METHOD__, $targetDays, $maxBufferDays)
            );
            return __('Wrong target days or buffer days.');
        }
        //$targetDaysが期の日数を超えていたらエラー
        $termTotalDays = AppUtil::diffDays($termStartTimestamp, $termEndTimestamp);
        if ($targetDays > $termTotalDays) {
            $this->log(sprintf("%s%s [method:%s] targetDays(%s days) over termTotalDays(%s days).",
                    __FILE__, __LINE__, __METHOD__, $targetDays, $termTotalDays)
            );
            return __('Wrong target days.');
        }
        //指定グラフ終了日は評価期間内でなければいけない
        if ($targetEndTimestamp < $termStartTimestamp || $targetEndTimestamp > $termEndTimestamp) {
            $this->log(sprintf("%s%s [method:%s] target end date(%s) not in evaluate term(%s - %s)",
                    __FILE__, __LINE__, __METHOD__, $targetEndTimestamp, $termStartTimestamp, $termEndTimestamp)
            );
            return __('Target end date should be in evaluate term');

        }

        return true;
    }

    /**
     * グラフ用ゴール進捗取得時のバリデーション
     *
     * @param string $graphStartDate
     * @param string $graphEndDate
     * @param string $plotDataEndDate
     *
     * @return true|string
     */
    function validateGetProgressDrawingGraph(
        string $graphStartDate,
        string $graphEndDate,
        string $plotDataEndDate
    ) {
        //不正な範囲指定か判定
        if ($graphStartDate >= $graphEndDate
            || $graphStartDate > $plotDataEndDate
            || $plotDataEndDate > $graphEndDate
        ) {
            $this->log(sprintf("%s%s [method:%s] Graph range is wrong. graphStartDate:%s, graphEndDate:%s, plotDataEndDate:%s",
                    __FILE__, __LINE__, __METHOD__, $graphStartDate, $graphEndDate, $plotDataEndDate)
            );
            return __('Graph range is wrong.');

        }
        return true;
    }

    /**
     * グラフ用のユーザの全ゴール進捗ログデータを取得
     * //日毎に集計済みのゴール進捗ログを取得
     * //当日の進捗を計算
     * //sweet spotを算出
     * //ログデータと当日の進捗をマージ
     * //グラフ用データに整形
     *
     * @param int    $userId
     * @param string $graphStartDate  Y-m-d形式のグラフ描画開始日
     * @param string $graphEndDate    Y-m-d形式のグラフ描画終了日
     * @param string $plotDataEndDate Y-m-d形式のデータプロット終了日
     * @param bool   $withSweetSpot
     *
     * @return array
     * @throws Exception
     */
    function getUserAllGoalProgressForDrawingGraph(
        int $userId,
        string $graphStartDate,
        string $graphEndDate,
        string $plotDataEndDate,
        bool $withSweetSpot = false
    ): array {
        //パラメータバリデーション
        $validOrErrorMsg = $this->validateGetProgressDrawingGraph($graphStartDate, $graphEndDate, $plotDataEndDate);
        if ($validOrErrorMsg !== true) {
            throw new Exception($validOrErrorMsg);
        }

        //当日がプロット対象に含まれるかどうか？
        $isIncludedTodayInPlotData = AppUtil::between(
            time(),
            strtotime($graphStartDate),
            strtotime($plotDataEndDate) + DAY
        );

        //日毎に集計済みのゴール進捗ログを取得
        $logStartDate = $graphStartDate;
        if ($isIncludedTodayInPlotData) {
            $logEndDate = AppUtil::dateYmd(strtotime('yesterday'));
        } else {
            $logEndDate = $plotDataEndDate;
        }

        //ゴール重要度のリスト key:goal_id,value:priority
        $goalPriorities = $this->findGoalPriorities($userId);
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init('KeyResultService');
        $goalIds = array_keys($goalPriorities);
        //ゴールごとにグルーピングされたkr一覧[goal_id=>[kr_id=>[],kr_id=>[]]]
        $latestKrValues = $KeyResultService->findValuesGroupByGoalId($goalIds);

        $progressLogs = $this->findSummarizedUserProgressesFromLog($goalPriorities, $latestKrValues, $logStartDate,
            $logEndDate);
        $progressLogs = $this->processProgressesToGraph($logStartDate, $logEndDate, $progressLogs);

        //範囲に当日が含まれる場合は当日の進捗を取得しログデータとマージ
        if ($isIncludedTodayInPlotData) {
            $latestTotalGoalProgress = $this->findLatestSummarizedGoalProgress($userId);
            if ($latestTotalGoalProgress <> 0) {
                array_push($progressLogs, $latestTotalGoalProgress);
            }
        }

        //sweetSpotを算出
        $sweetSpot = $withSweetSpot ? $this->getSweetSpot($graphStartDate, $graphEndDate) : [];

        //グラフ用データに整形
        $ret = $this->shapeDataForGraph($progressLogs, $sweetSpot, $graphStartDate, $graphEndDate);

        return $ret;
    }

    function findGoalPriorities($userId)
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTimestamp = $EvaluateTerm->getCurrentTermData()['start_date'];
        $termEndTimestamp = $EvaluateTerm->getCurrentTermData()['end_date'];

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');
        $goalPriorities = $GoalMember->findGoalPriorities($userId, $termStartTimestamp, $termEndTimestamp);
        return $goalPriorities;
    }

    /**
     * グラフ用の単一ゴール進捗ログデータを取得
     * //ゴール進捗ログを取得
     * //当日の進捗を計算
     * //sweet spotを算出
     * //ログデータと当日の進捗をマージ
     * //グラフ用データに整形
     *
     * @param int    $goalId
     * @param string $graphStartDate  Y-m-d形式のグラフ描画開始日
     * @param string $graphEndDate    Y-m-d形式のグラフ描画終了日
     * @param string $plotDataEndDate Y-m-d形式のデータプロット終了日
     * @param bool   $withSweetSpot
     *
     * @return array
     * @throws Exception
     */
    function getGoalProgressForDrawingGraph(
        int $goalId,
        string $graphStartDate,
        string $graphEndDate,
        string $plotDataEndDate,
        bool $withSweetSpot = false
    ): array {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        if (!$Goal->exists($goalId)) {
            throw new Exception(__('The goal is not exist.'));
        }
        //パラメータバリデーション
        $validOrErrorMsg = $this->validateGetProgressDrawingGraph($graphStartDate, $graphEndDate, $plotDataEndDate);
        if ($validOrErrorMsg !== true) {
            throw new Exception($validOrErrorMsg);
        }

        //当日がプロット対象に含まれるかどうか？
        $isIncludedTodayInPlotData = AppUtil::between(
            time(),
            strtotime($graphStartDate),
            strtotime($plotDataEndDate) + DAY
        );

        //日毎に集計済みのゴール進捗ログを取得
        $logStartDate = $graphStartDate;
        if ($isIncludedTodayInPlotData) {
            $logEndDate = AppUtil::dateYmd(strtotime('yesterday'));
        } else {
            $logEndDate = $plotDataEndDate;
        }
        $progressLogs = $this->findGoalProgressFromLog($goalId, $logStartDate, $logEndDate);
        $progressLogs = $this->processProgressesToGraph($logStartDate, $logEndDate, $progressLogs);

        //範囲に当日が含まれる場合は当日の進捗を取得しログデータとマージ
        if ($isIncludedTodayInPlotData) {
            $goal = $Goal->getGoal($goalId);
            $sumPriorities = $goal['KeyResult'];
            $latestGoalProgress = $this->getProgress($goal['KeyResult'], $sumPriorities);
            if ($latestGoalProgress <> 0) {
                array_push($progressLogs, $latestGoalProgress);
            }
        }

        //データが存在しない場合は空の配列を返す
        if (empty($progressLogs)) {
            return [];
        }

        //sweetSpotを算出
        $sweetSpot = $withSweetSpot ? $this->getSweetSpot($graphStartDate, $graphEndDate) : [];

        //グラフ用データに整形
        $ret = $this->shapeDataForGraph($progressLogs, $sweetSpot, $graphStartDate, $graphEndDate);
        return $ret;
    }

    /**
     * グラフ出力ライブラリ用にデータを整形
     *
     * @param array  $progressLogs
     * @param array  $sweetSpot
     * @param string $graphStartDate
     * @param string $graphEndDate
     *
     * @return array
     */
    function shapeDataForGraph(
        array $progressLogs,
        array $sweetSpot,
        string $graphStartDate,
        string $graphEndDate
    ): array {
        /** @noinspection PhpUndefinedVariableInspection */
        $ret[0] = array_merge(['sweet_spot_top'], $sweetSpot['top']??[]);
        $ret[1] = array_merge(['sweet_spot_bottom'], $sweetSpot['bottom']??[]);
        $ret[2] = array_merge(['data'], $progressLogs);
        $ret[3] = array_merge(['x'], $this->getFormatDatesEachGraphPoint($graphStartDate, $graphEndDate));
        return $ret;
    }

    /**
     * グラフ表示期間内のフォーマットした各日付を取得
     *
     * @param string $graphStartDate
     * @param string $graphEndDate
     *
     * @return array
     */
    function getFormatDatesEachGraphPoint(string $graphStartDate, string $graphEndDate): array
    {
        $TimeEx = new TimeExHelper(new View());
//        ()
//        $diffDays = (strtotime(date("Y-m-d", $dif)) - strtotime("1970-01-01")) / 86400;
        $ret = [];
        $timestamp = strtotime($graphStartDate);
        $graphEndTimestamp = strtotime($graphEndDate);
        while ($timestamp <= $graphEndTimestamp) {
            $ret[] = $TimeEx->dateLocalFormat($timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        return $ret;
    }

    /**
     * 最新のゴール進捗の合計を取得
     * - ゴールの重要度を掛けて合計
     *
     * @param int $userId
     *
     * @return float
     */
    function findLatestSummarizedGoalProgress(int $userId): float
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTimestamp = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTimestamp = $EvaluateTerm->getCurrentTermData(true)['end_date'];

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');
        $goalPriorities = $GoalMember->findGoalPriorities($userId, $termStartTimestamp, $termEndTimestamp);

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        $goalIds = array_keys($goalPriorities);
        $goals = $Goal->getGoalAndKr($goalIds, $userId);
        foreach ($goals as $key => $goal) {
            $sumPriorities = $this->sumPriorities($goal['KeyResult']);
            $goals[$key]['Goal']['progress'] = $this->getProgress($goal['KeyResult'], $sumPriorities);
        }
        $goalProgresses = Hash::combine($goals, '{n}.Goal.id', '{n}.Goal.progress');
        $ret = $this->sumGoalProgress($goalProgresses, $goalPriorities);
        return $ret;
    }

    /**
     * 集計済みのユーザのゴール進捗をログから取得
     * //キャッシュからデータを取得なければ以下処理
     * ///ログDBから自分の各ゴールの進捗データ取得(今期の開始日以降の過去30日分)
     * ///ゴールの重要度を掛け合わせる(例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75 )
     * ///ここまでのデータをキャッシュ
     *
     * @param array  $goalPriorities
     * @param array  $latestKrValues
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findSummarizedUserProgressesFromLog(
        array $goalPriorities,
        array $latestKrValues,
        string $startDate,
        string $endDate
    ): array {
        //キャッシュに保存されるデータ
        //TODO: キャッシュば別issue(GL-5549)で行う
        //$progressLogs = $this->getUserProgressFromCache($userId, $startDate, $endDate);
        //if ($progressLogs === false) {
        ///ログDBから自分の各ゴールの進捗データ取得
        /** @var KrValuesDailyLog $KrValuesDailyLog */
        $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");
        $goalIds = array_keys($goalPriorities);
        $krValueLogs = $KrValuesDailyLog->findLogs($startDate, $endDate, $goalIds);
        ///ゴールの重要度を掛け合わせて日次のゴール進捗の合計を計算(例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75 )
        $progressLogs = $this->sumDailyGoalProgress($krValueLogs, $latestKrValues, $goalPriorities);

        //キャッシュに保存
        //TODO: キャッシュば別issue(GL-5549)で行う
        //$this->writeUserProgressToCache($userId, $startDate, $endDate, $progressLogs);
        //}

        return $progressLogs;
    }

    /**
     * 集計済みの単一ゴール進捗をログから取得
     * - ログDBから自分の各ゴールの進捗データ取得(今期の開始日以降の過去30日分)
     * - キャッシュする
     *
     * @param int    $goalId
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findGoalProgressFromLog(int $goalId, string $startDate, string $endDate): array
    {
        //キャッシュに保存されるデータ
        //TODO: キャッシュば別issue(GL-5549)で行う
        //$goalProgressLogs = $this->getGoalProgressFromCache($goalId, $startDate, $endDate);
        //if ($goalProgressLogs === false) {
        ///ログDBから自分の各ゴールの進捗データ取得
        /** @var KrValuesDailyLog $KrValuesDailyLog */
        $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");

        $krValueLogs = $KrValuesDailyLog->findLogs($startDate, $endDate, [$goalId]);

        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        //最新のKR
        $latestKrValues = $KeyResult->findProgressBaseValues([$goalId]);
        $progressLogs = $this->getDailyGoalProgress($krValueLogs, $latestKrValues);

        //キャッシュに保存
        //TODO: キャッシュば別issue(GL-5549)で行う
        //$this->writeGoalProgressToCache($goalId, $startDate, $endDate, $goalProgressLogs);
        //}

        return $progressLogs;
    }

    /**
     * ゴール進捗をグラフ用に加工
     * - 先頭でログが存在しない日は0をセット
     * - 途中でログが存在しない場合はその直近のprogressをセット
     *
     * @param string $startDate
     * @param string $endDate
     * @param array  $progresses key:date,value:progress
     *
     * @return array progressの配列
     */
    function processProgressesToGraph(string $startDate, string $endDate, array $progresses): array
    {
        $currentProgress = null;
        $ret = [];

        $currentTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate);

        while ($currentTimestamp <= $endTimestamp) {
            $currentDate = AppUtil::dateYmd($currentTimestamp);
            if (isset($progresses[$currentDate])) {
                $currentProgress = $progresses[$currentDate];
            }
            $ret[] = $currentProgress;

            $currentTimestamp += DAY;
        }

        return $ret;
    }

    /**
     * ゴールの重要度を掛け合わせ日次のゴール進捗の合計を返す
     *
     * @param array $krLogs         including: goal_id,key_result_id, current_value, target_date
     * @param array $latestKrValues ゴールごとにグルーピングされたkr一覧[goal_id=>[kr_id=>[start_value,],kr_id=>[]]]
     * @param array $goalPriorities key:goal_id, value:priorityの配列
     *
     * @return array key:date, value:progress
     */
    function sumDailyGoalProgress(array $krLogs, array $latestKrValues, array $goalPriorities): array
    {
        //logsを日付でグルーピングする
        $krLogs = Hash::combine($krLogs, '{n}.key_result_id', '{n}', '{n}.target_date');

        $goalGroupedLogs = [];
        foreach ($krLogs as $date => $krs) {
            //ゴールでグルーピング [goal_id=>[kr_id=>current_value],]
            $goals = Hash::combine($krs, '{n}.key_result_id', '{n}.current_value', '{n}.goal_id');
            $goalGroupedLogs[$date] = $goals;
        }

        //最新のKRデータにログのKR現在値をマージして進捗率を計算
        $logGoalProgresses = [];
        foreach ($goalGroupedLogs as $date => $goals) {
            foreach ($goals as $goalId => $logKrs) {
                //ログのゴールが最新データに存在しない場合はスキップ
                if (!isset($latestKrValues[$goalId])) {
                    continue;
                }
                $logGoalProgresses[$date][$goalId] = $this->getProgressWithLog($logKrs, $latestKrValues[$goalId]);
            }
        }

        $ret = [];
        //日毎にゴールのプライオリティを掛け合わせる
        foreach ($logGoalProgresses as $date => $goals) {
            $ret[$date] = $this->sumGoalProgress($goals, $goalPriorities);
        }
        return $ret;
    }

    /**
     * 単一ゴールの日次の進捗を返す
     *
     * @param array $logs
     * @param array $latestKrValues
     *
     * @return array
     */
    function getDailyGoalProgress(array $logs, array $latestKrValues): array
    {
        //logsを日付でグルーピングする
        $logs = Hash::combine($logs, '{n}.key_result_id', '{n}.current_value', '{n}.target_date');
        //最新のKRデータにログのKR現在値をマージして進捗率を計算
        $ret = [];
        foreach ($logs as $date => $krs) {
            $ret[$date] = $this->getProgressWithLog($krs, $latestKrValues);
        }
        return $ret;
    }

    /**
     * ログのKR現在値と最新のKR(重要度、開始値、目標値)から進捗を算出
     *
     * @param array $logKrs    [kr_id=>[current_value=>""],kr_id=>[current_value=>""],]
     * @param array $latestKrs [kr_id=>[start_value,target_value,current_value,priority,...],kr_id=>[],]
     *
     * @return float
     */
    function getProgressWithLog(array $logKrs, array $latestKrs): float
    {
        $rebuildedKrs = [];
        foreach ($logKrs as $krId => $currentValue) {
            //ログのKRが最新データに存在しない場合はスキップ
            if (!isset($latestKrs[$krId])) {
                continue;
            }
            //最新KRデータにログの現在値をマージ
            $rebuildedKrs[] = array_merge(
                $latestKrs[$krId],
                ['current_value' => $currentValue]
            );
        }
        $sumLatestKrPriorities = $this->sumPriorities($latestKrs);
        $ret = $this->getProgress($rebuildedKrs, $sumLatestKrPriorities);
        return $ret;
    }

    /**
     * ゴール進捗の合計を取得
     * - 例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75
     *
     * @param array $goalProgresses key:goal_id, value:progress
     * @param array $goalPriorities key:goal_id, value:priority
     *
     * @return float
     */
    function sumGoalProgress(array $goalProgresses, array $goalPriorities): float
    {
        //summarize goal priorities
        $sumPriorities = array_sum($goalPriorities);
        $progresses = [];
        foreach ($goalProgresses as $goalId => $progress) {
            if (isset($goalPriorities[$goalId])) {
                $progresses[] = $progress * $goalPriorities[$goalId] / $sumPriorities;
            }
        }
        $ret = round(array_sum($progresses), 2);
        return $ret;
    }

    /**
     * 開始日、終了日の日毎のSweet Spotを返す
     * $start, $endが期を跨いだ場合は空の配列を返す
     *
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     * @param int    $maxTop
     * @param int    $maxBottom
     *
     * @return array 　e.g. [top=>[0,10,20,30...],bottom=>[0,10,20,30...]]
     */
    function getSweetSpot(
        string $startDate,
        string $endDate,
        int $maxTop = self::GRAPH_SWEET_SPOT_MAX_TOP,
        int $maxBottom = self::GRAPH_SWEET_SPOT_MAX_BOTTOM
    ): array {
        $startTimestamp = strtotime($startDate);
        $endTimestamp = strtotime($endDate) + DAY - 1;

        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTimestamp = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTimestamp = $EvaluateTerm->getCurrentTermData(true)['end_date'];

        //開始日、終了日のどちらかが期の範囲を超えていたら、何もしない
        if ($startTimestamp < $termStartTimestamp || $endTimestamp > $termEndTimestamp) {
            return [];
        }

        $termTotalDays = AppUtil::diffDays($termStartTimestamp, $termEndTimestamp);
        //sweetspotの上辺の一日で進む高さ(0が含まれるのでその分-1)
        $topStep = (float)($maxTop / ($termTotalDays - 1));
        //sweetspotの下辺の一日で進む高さ(0が含まれるのでその分-1)
        $bottomStep = (float)($maxBottom / ($termTotalDays - 1));

        //返り値
        $sweetSpot = [
            'top'    => [],
            'bottom' => [],
        ];

        //期の開始日からの日数を算出し、その日数分開始値を進める
        $daysFromTermStart = AppUtil::diffDays($termStartTimestamp, $startTimestamp);
        $top = (float)$daysFromTermStart * $topStep;
        $bottom = (float)$daysFromTermStart * $bottomStep;

        //一日ずつ値を格納
        $graphTotalDays = AppUtil::diffDays($startTimestamp, $endTimestamp);
        for ($i = 1; $i <= $graphTotalDays; $i++) {
            $sweetSpot['top'][] = round($top, 2);
            $sweetSpot['bottom'][] = round($bottom, 2);

            $top += $topStep;
            $bottom += $bottomStep;
        }

        return $sweetSpot;
    }

    /**
     * 集計済みユーザのゴール進捗をキャッシュから取得
     *
     * @param int    $userId
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     *
     * @return mixed
     */
    function getUserProgressFromCache(int $userId, string $startDate, string $endDate)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        return Cache::read($Goal->getCacheKey(CACHE_KEY_USER_GOAL_PROGRESS_LOG . ":start:$startDate:end:$endDate",
            true, $userId), 'user_data');
    }

    /**
     * 集計済みのユーザのゴール進捗をキャッシュに書き出す
     * 生存期間は当日の終わりまで(UTC)
     *
     * @param int    $userId
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     * @param array  $data      重要度を掛け合わせたもの
     */
    function writeUserProgressToCache(int $userId, string $startDate, string $endDate, array $data)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        $remainSecUntilEndOfTheDay = strtotime('tomorrow') - time();
        Cache::set('duration', $remainSecUntilEndOfTheDay, 'user_data');
        Cache::write($Goal->getCacheKey(CACHE_KEY_USER_GOAL_PROGRESS_LOG . ":start:$startDate:end:$endDate",
            true, $userId), $data, 'user_data');
    }

    /**
     * アクション可能なゴール一覧を返す
     * - フィードページで参照されるデータなのでキャッシュを使う
     * TODO:findCanActionと重複している
     *
     * @return array
     */
    function findActionables(): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        // キャッシュは一旦無効とする
//        $cachedActionableGoals = Cache::read($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
//        if ($cachedActionableGoals !== false) {
//            return $cachedActionableGoals;
//        }

        // キャッシュが存在しない場合はDBにqueryを投げてキャッシュに保存する
        $actionableGoals = $Goal->findActionables($Goal->my_uid);
        $actionableGoals = Hash::combine($actionableGoals, '{n}.id', '{n}.name');

//        Cache::write($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), $actionableGoals, 'user_data');
        return $actionableGoals;
    }

    /*
     * 単一のゴール進捗をキャッシュから取得
     *
     * @param int    $goalId
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     *
     * @return mixed
     */
    function getGoalProgressFromCache(int $goalId, string $startDate, string $endDate)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        return Cache::read(
            $Goal->getCacheKey(CACHE_KEY_GOAL_PROGRESS_LOG . ":goal_id:$goalId:start:$startDate:end:$endDate"),
            'team_info');
    }

    /**
     * 単一のゴール進捗をキャッシュに書き出す
     * 生存期間は当日の終わりまで(UTC)
     *
     * @param int    $goalId
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     * @param array  $data      重要度を掛け合わせたもの
     */
    function writeGoalProgressToCache(int $goalId, string $startDate, string $endDate, array $data): void
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        $remainSecUntilEndOfTheDay = strtotime('tomorrow') - time();
        Cache::set('duration', $remainSecUntilEndOfTheDay, 'team_info');
        Cache::write(
            $Goal->getCacheKey(CACHE_KEY_GOAL_PROGRESS_LOG . ":goal_id:$goalId:start:$startDate:end:$endDate"),
            $data,
            'team_info');
    }

    /**
     * ユーザーに紐づくゴール名一覧を返す
     * - TODO: feedページで呼ばれるメソッドのためキャッシュが必要
     *
     * @param  int $userId
     *
     * @return array
     */
    function findNameListAsMember(int $userId, int $startDateTime, int $endDateTime): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        $goalNameList = $Goal->findNameListAsMember($userId, $startDateTime, $endDateTime);
        return $goalNameList;
    }

    /**
     * ゴール毎の進捗ログデータをバルクで保存する
     *
     * @param int    $teamId
     * @param string $targetDate
     *
     * @return bool
     */
    function saveGoalProgressLogsAsBulk(int $teamId, string $targetDate): bool
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var GoalProgressDailyLog $GoalProgressDailyLog */
        $GoalProgressDailyLog = ClassRegistry::init('GoalProgressDailyLog');

        $targetTerm = $EvaluateTerm->getTermDataByTimeStamp(strtotime($targetDate));
        if (empty($targetTerm)) {
            //期間データが存在しない場合はログを採らない。期間データがない(ログインしているユーザがいない)なら進捗自体がないということなので。
            return false;
        }
        // 対象期間の全ゴールのIDリスト
        $goalIds = $Goal->findAllIdsByEndDateTimestamp($targetTerm['start_date'], $targetTerm['end_date']);
        if (empty($goalIds)) {
            return false;
        }
        $saveData = [];
        // 全ゴールを取得
        $goals = $Goal->getGoalAndKr($goalIds);
        //保存データの生成
        foreach ($goals as $goal) {
            $sumPriorities = $this->sumPriorities($goal['KeyResult']);
            $saveData[] = [
                'team_id'     => $teamId,
                'goal_id'     => $goal['Goal']['id'],
                //各ゴール毎にKRからゴール進捗を求める
                'progress'    => $this->getProgress($goal['KeyResult'], $sumPriorities),
                'target_date' => $targetDate,
            ];
        }
        $ret = $GoalProgressDailyLog->bulkInsert($saveData);

        return $ret;
    }
}
