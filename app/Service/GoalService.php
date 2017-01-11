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

    /* グラフ設定 */
    const GRAPH_MAX_BUFFER_DAYS = 10;
    const GRAPH_TARGET_DAYS = 30;
    const GRAPH_SWEET_SPOT_MAX_TOP = 100;
    const GRAPH_SWEET_SPOT_MIN_TOP = 60;

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

            // Redisキャッシュ削除
            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
            Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

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

            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
            Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

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
            $goals[$key]['Goal']['can_change_leader'] = $GoalMemberService->canChangeLeader(Hash::get($goal,
                'Goal.id'));
        }
        return $goals;
    }

    /**
     * ゴールの進捗をキーリザルト一覧から取得
     * TODO:将来的にゴールIDのみを引数として渡し、そのゴールIDから各KR取得→KR進捗率計算→ゴール進捗率計算の順に処理を行うようにする。またキャッシュ化も必要。
     *
     * @param  array $key_results [description]
     *
     * @return float|int $res
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
                || !Hash::check($key_result, 'current_value')
            ) {
                $errFlg = true;
                $progress = 0;
            } else {
                $progress = $NumberExHelper->calcProgressRate($key_result['start_value'], $key_result['target_value'],
                    $key_result['current_value']);
            }
            $current_progress_total += $key_result['priority'] * $progress;
        }

        // 本メソッドを呼ぶ箇所が多いため抜け漏れを検知する為にtrycatchを入れる
        try {
            if ($errFlg) {
                throw new Exception(sprintf("Not found field to calc progress    %s",
                    var_export(compact('key_results'), true)));
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
        Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        return true;

    }

    /**
     * 与えられた対象終了日と対象日数からグラフの開始日、終了日を求める
     * - グラフの開始日、終了日は必ず期内となる
     * - グラフ開始日が期の開始日より前になる場合はグラフ開始日は期の開始日と同一となる
     * - グラフ開始日が期の終了日-$targetDays日以内の場合は、グラフ終了日は期の終了日と同一となる
     *
     * @param int $targetEndTime
     * @param int $targetDays
     * @param int $bufferDays
     *
     * @return array ['start'=>'','end'=>'']
     */
    function getGraphRange(int $targetEndTime, int $targetDays = self::GRAPH_TARGET_DAYS, int $bufferDays = 0): array
    {
        $targetStartTime = $targetEndTime - $targetDays * DAY;

        $ret = [
            'start' => "",
            'end'   => "",
        ];
        //今期の情報取得
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTime = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTime = $EvaluateTerm->getCurrentTermData(true)['end_date'];

        //$targetDaysが期の日数を超えていたら期の開始日、終了日を返す
        $termTotalDays = AppUtil::getDiffDays($termStartTime, $termEndTime);
        if ($targetDays > $termTotalDays) {
            $ret['start'] = date('Y-m-d', $termStartTime);
            $ret['end'] = date('Y-m-d', $termEndTime);
            return $ret;
        }

        //グラフの開始日が期の開始日以前になる場合は、グラフ開始日に期の開始日をセット
        if ($targetStartTime < $termStartTime) {
            $ret['start'] = date('Y-m-d', $termStartTime);
            $ret['end'] = date('Y-m-d', $termStartTime + $targetDays * DAY);
            return $ret;
        }

        //指定された終了日が期の終了日に近づいたら、グラフ終了日は期の終了日をセット
        //「期の終了日に近づいた」の定義: 指定された終了日に指定日数を加算したものが期の終了日を超えた場合
        if ($targetEndTime + ($targetDays * DAY) > $termEndTime) {
            $ret['start'] = date('Y-m-d', $termEndTime - $targetDays * DAY);
            $ret['end'] = date('Y-m-d', $termEndTime);
            return $ret;
        }

        //$targetDays前から本日まで
        $ret['start'] = date('Y-m-d', $targetEndTime - $targetDays * DAY);
        $ret['end'] = date('Y-m-d', $targetEndTime);
        return $ret;
    }

    /**
     * TODO: 未実装
     * バッファの日付を求める
     * - 指定された終了日が期の開始日に近かった場合は、bufferは0
     * - 指定された終了日が期の終了日に近かった場合は、bufferは減少する
     * - それ以外の場合は、$maxBufferDaysを返す
     *
     * @param int $targetEndTime
     * @param int $targetDays
     * @param int $maxBufferDays
     *
     * @return int
     */
    function getBufferDays(
        int $targetEndTime,
        int $targetDays = self::GRAPH_TARGET_DAYS,
        int $maxBufferDays = self::GRAPH_MAX_BUFFER_DAYS
    ): int {

    }

    /**
     * グラフ用のゴール進捗ログデータを取得
     * //日毎に集計済みのゴール進捗ログを取得
     * //当日の進捗を計算
     * //sweetspotを算出(max60%で今期の開始日から今期の終了日までのdailyのtopとbottom)
     * //ログデータと当日の進捗をマージ
     * //グラフ用データに整形
     *
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     * @param int    $bufferDays
     *
     * @return array
     */
    function getAllMyProgressForDrawingGraph(string $startDate, string $endDate, int $bufferDays = 0): array
    {
        //日毎に集計済みのゴール進捗ログを取得
        $progressLogs = $this->findSummarizedGoalProgressesFromLog($startDate, $endDate);

        //範囲に当日が含まれる場合は当日の進捗を取得しログデータとマージ
        if (time() >= strtotime($startDate) && time() <= strtotime($endDate) + DAY) {
            $latestTotalGoalProgress = $this->findLatestTotalGoalProgress();
            array_push($progressLogs, $latestTotalGoalProgress);
        }

        ///sweetSpotを算出(max60%で今期の開始日から今期の終了日までのdailyのtopとbottom)
        $sweetSpot = $this->getSweetSpot($startDate, $endDate);

        //グラフ用データに整形
        $ret = [];
        $ret[0] = array_merge(['sweet_spot_top'], $sweetSpot['top']??[]);
        $ret[1] = array_merge(['data'], $progressLogs);
        $ret[2] = array_merge(['sweet_spot_bottom'], $sweetSpot['bottom']??[]);

        return $ret;
    }

    /**
     * 最新のトータルのゴール進捗を取得
     *
     * @return int
     */
    function findLatestTotalGoalProgress(): int
    {
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTime = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTime = $EvaluateTerm->getCurrentTermData(true)['end_date'];

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');
        $myGoalPriorities = $GoalMember->findMyGoalPriorities($termStartTime, $termEndTime);

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        $myGoalIds = array_keys($myGoalPriorities);
        $goals = $Goal->getGoalAndKr($myGoalIds);
        foreach ($goals as $key => $goal) {
            $goals[$key]['Goal']['progress'] = $this->getProgress($goal['KeyResult']);
        }
        $goalProgresses = Hash::combine($goals, '{n}.Goal.id', '{n}.Goal.progress');
        $ret = $this->sumGoalProgress($goalProgresses, $myGoalPriorities);
        return $ret;
    }

    /**
     * //キャッシュからデータを取得なければ以下処理
     * ///ログDBから自分の各ゴールの進捗データ取得(今期の開始日以降の過去30日分)
     * ///ゴールの重要度を掛け合わせる(例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75 )
     * ///ここまでのデータをキャッシュ
     *
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findSummarizedGoalProgressesFromLog(string $startDate, string $endDate): array
    {
        //今期の情報取得
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTime = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTime = $EvaluateTerm->getCurrentTermData(true)['end_date'];
        //キャッシュに保存されるデータ
        $progressLogs = $this->getProgressFromCache($startDate, $endDate);
        if ($progressLogs === false) {
            ///ログDBから自分の各ゴールの進捗データ取得
            /** @var GoalMember $GoalMember */
            $GoalMember = ClassRegistry::init('GoalMember');
            $myGoalPriorities = $GoalMember->findMyGoalPriorities($termStartTime, $termEndTime);
            /** @var GoalProgressDailyLog $GoalProgressDailyLog */
            $GoalProgressDailyLog = ClassRegistry::init("GoalProgressDailyLog");
            $goalProgressLogs = $GoalProgressDailyLog->findLogs($startDate, $endDate, array_keys($myGoalPriorities));

            ///ゴールの重要度を掛け合わせて日次のゴール進捗の合計を計算(例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75 )
            $progressLogs = $this->sumDailyGoalProgress($goalProgressLogs, $myGoalPriorities);
            //キャッシュに保存
            $this->writeProgressToCache($progressLogs, $startDate, $endDate);
        }
        return $progressLogs;
    }

    /**
     * ゴールの重要度を掛け合わせ日次のゴール進捗の合計を返す
     *
     * @param array $logs           including: goal_id, progress, target_date
     * @param array $goalPriorities key:goal_id, value:priorityの配列
     *
     * @return array key:date, value:progress
     */
    function sumDailyGoalProgress(array $logs, array $goalPriorities): array
    {
        //logsを日付でグルーピングする
        $logs = Hash::combine($logs, '{n}.goal_id', '{n}.progress', '{n}.target_date');

        $ret = [];
        //日毎にゴールのプライオリティを掛け合わせる
        foreach ($logs as $date => $goals) {
            $ret[] = $this->sumGoalProgress($goals, $goalPriorities);
        }
        return $ret;
    }

    /**
     * ゴール進捗の合計を取得
     * 例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75
     *
     * @param array $goalsProgresses key:goal_id, value:progress
     * @param array $goalPriorities  key:goal_id, value:priority
     *
     * @return int
     */
    function sumGoalProgress(array $goalsProgresses, array $goalPriorities): int
    {
        //summarize goal priorities
        $sumPriorities = array_sum($goalPriorities);
        $progresses = [];
        foreach ($goalsProgresses as $goalId => $progress) {
            if (isset($goalPriorities[$goalId])) {
                $progresses[] = $progress * $goalPriorities[$goalId] / $sumPriorities;
            }
        }
        return array_sum($progresses);
    }

    /**
     * 開始日、終了日の日毎のSweet Spotを返す
     * $start, $endが期を跨いだ場合は空の配列を返す
     *
     * @param string $startDate Y-m-d
     * @param string $endDate   Y-m-d
     * @param int    $maxTop
     * @param int    $minTop
     *
     * @return array 　e.g. [top=>[0,10,20,30...],bottom=>[0,10,20,30...]]
     */
    function getSweetSpot(string $startDate, string $endDate, int $maxTop = 100, int $minTop = 60): array
    {
        $startTime = strtotime($startDate);
        $endTime = strtotime($endDate);

        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('EvaluateTerm');
        $termStartTime = $EvaluateTerm->getCurrentTermData(true)['start_date'];
        $termEndTime = $EvaluateTerm->getCurrentTermData(true)['end_date'];

        //開始日、終了日のどちらかが期の範囲を超えていたら、何もしない
        if ($startTime < $termStartTime || $endTime > $termEndTime) {
            return [];
        }

        $termTotalDays = AppUtil::getDiffDays($termStartTime, $termEndTime);
        //sweetspotの上辺の一日で進む高さ
        $topStep = (float)($maxTop / $termTotalDays);
        //sweetspotの下辺の一日で進む高さ
        $bottomStep = (float)($minTop / $termTotalDays);

        //返り値
        $sweetSpot = [
            'top'    => [],
            'bottom' => [],
        ];

        //期の開始日からの日数を算出し、その日数分開始値を進める
        $daysFromTermStart = AppUtil::getDiffDays($termStartTime, $startTime);
        $top = (float)$daysFromTermStart * $topStep;
        $bottom = (float)$daysFromTermStart * $bottomStep;

        //一日ずつ値を格納
        $graphTotalDays = AppUtil::getDiffDays($startTime, $endTime);
        for ($i = 1; $i <= $graphTotalDays; $i++) {
            $sweetSpot['top'][] = round($top, 2);
            $sweetSpot['bottom'][] = round($bottom, 2);

            $top += $topStep;
            $bottom += $bottomStep;
        }

        return $sweetSpot;
    }

    /**
     * 集計済みゴール進捗をキャッシュから取得
     *
     * @param string $start Y-m-d
     * @param string $end   Y-m-d
     *
     * @return mixed
     */
    function getProgressFromCache(string $start, string $end)
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        return Cache::read($Goal->getCacheKey(CACHE_KEY_GOAL_PROGRESS_LOG . ":start:$start:end:$end", true),
            'user_data');
    }

    /**
     * 集計済みゴール進捗をキャッシュに書き出す
     * 生存期間は当日の終わりまで(UTC)
     *
     * @param array  $data  重要度を掛け合わせたもの
     * @param string $start Y-m-d
     * @param string $end   Y-m-d
     */
    function writeProgressToCache(array $data, string $start, string $end): void
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        $remainSecUntilEndOfTheDay = strtotime('tomorrow') - time();
        Cache::set('duration', $remainSecUntilEndOfTheDay, 'user_data');
        Cache::write($Goal->getCacheKey(CACHE_KEY_GOAL_PROGRESS_LOG . ":start:$start:end:$end", true), $data,
            'user_data');
    }

}
