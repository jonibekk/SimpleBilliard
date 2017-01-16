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
App::import('Service', 'GoalApprovalService');
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');
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
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
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
                $coachId = $TeamMember->getCoachUserIdByMemberUserId($this->my_uid);
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

            // アクション可能ゴール一覧キャッシュ削除
            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');

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
     * @param array        $data
     * @param array        $fields
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
        /** @var TeamMember $TeamMember */
        $TeamMember = ClassRegistry::init("TeamMember");
        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        foreach ($goals as $key => $goal) {
            // 進捗を計算
            if (!empty($goal['KeyResult'])) {
                $goals[$key]['Goal']['progress'] = $this->getProgress($goal['KeyResult']);
            }
            // 認定有効フラグを追加
            if (!empty($goal['TargetCollabo'])) {
                $goals[$key]['TargetCollabo']['is_approval_enabled'] = $GoalApprovalService->isApprovable($goal['TargetCollabo']['user_id']);
            }
            // リーダー変更可能フラグを追加
            $goals[$key]['Goal']['can_change_leader'] = $GoalMemberService->canChangeLeader(Hash::get($goal, 'Goal.id'));
        }
        return $goals;
    }

    /**
     * ゴールの進捗をキーリザルト一覧から取得
     *
     * TODO:将来的にゴールIDのみを引数として渡し、そのゴールIDから各KR取得→KR進捗率計算→ゴール進捗率計算の順に処理を行うようにする。またキャッシュ化も必要。
     *
     * @param  array $key_results [description]
     *
     * @return array $res
     */
    function getProgress($key_results)
    {
        $res = 0;
        $target_progress_total = 0;
        $current_progress_total = 0;
        $NumberExHelper = new NumberExHelper(new View());

        $errFlg = false;
        foreach ($key_results as $key_result) {
            $target_progress_total += $key_result['priority'] * 100;
            if (!Hash::check($key_result, 'start_value')
                || !Hash::check($key_result, 'target_value')
                || !Hash::check($key_result, 'current_value'))
            {
                $errFlg = true;
                $progress = 0;
            } else {
                $progress = $NumberExHelper->calcProgressRate($key_result['start_value'], $key_result['target_value'], $key_result['current_value']);
            }
            $current_progress_total += $key_result['priority'] * $progress;
        }

        // 本メソッドを呼ぶ箇所が多いため抜け漏れを検知する為にtrycatchを入れる
        try {
            if ($errFlg) {
                throw new Exception(sprintf("Not found field to calc progress    %s", var_export(compact('key_results'), true)));
            }
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
        }

        if ($target_progress_total != 0) {
            $res = round($current_progress_total / $target_progress_total, 2) * 100;
        }
        return $res;
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
     * ゴール完了
     *
     * @param $goalId
     *
     * @return bool
     */
    function complete(int $goalId) : bool
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
                throw new Exception("Create goal complete post. goalId:".$goalId);
            }

            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');

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
     * アクション可能なゴール一覧を返す
     * - フィードページで参照されるデータなのでキャッシュを使う
     *
     * @return array
     */
    function findActionables(): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        $cachedActionableGoals = Cache::read($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
        if ($cachedActionableGoals !== false) {
            return $cachedActionableGoals;
        }

        // キャッシュが存在しない場合はDBにqueryを投げてキャッシュに保存する
        $actionableGoals = $Goal->findActionables($Goal->my_uid);
        $actionableGoals = Hash::combine($actionableGoals, '{n}.id', '{n}.name');

        Cache::write($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, $actionableGoals, true), 'user_data');
        return $actionableGoals;
    }

}
