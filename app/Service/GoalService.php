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
App::uses('Team', 'Model');
App::uses('Term', 'Model');
App::uses('GoalLabel', 'Model');
App::uses('GoalCategory', 'Model');
App::uses('ApprovalHistory', 'Model');
App::uses('GoalMember', 'Model');
App::uses('Post', 'Model');
App::uses('KrChangeLog', 'Model');
App::uses('KrProgressLog', 'Model');
App::uses('Follower', 'Model');
App::uses('KrValuesDailyLog', 'Model');
App::uses('TimeExHelper', 'View/Helper');
App::uses('UploadHelper', 'View/Helper');
App::import('Service', 'GoalApprovalService');
App::import('Service', 'GoalMemberService');
App::import('Service', 'KeyResultService');
App::import('Service', 'KrValuesDailyLogService');
// TODO:NumberExHelperだけimportではnot foundになってしまうので要調査
App::uses('NumberExHelper', 'View/Helper');

use Goalous\Enum\Csv\GoalAndKrs as GoalAndKrs;
/**
 * Class GoalService
 */
class GoalService extends AppService
{
    const TERM_TYPE_CURRENT = 'current';
    const TERM_TYPE_PREVIOUS = 'previous';
    const TERM_TYPE_NEXT = 'next';

    const CSV_DATE_FORMAT = 'Y/m/d';

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
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");
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

            // ゴール変更前のtermType退避
            $preUpdatedTerm = $Goal->getTermTypeById($goalId);

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
            $updateTkr = $KeyResultService->buildUpdateKr($tkrId, $inputTkrData);
            if (!$KeyResult->save($updateTkr, false)) {
                throw new Exception(sprintf("Failed update tkr. data:%s"
                    , var_export($updateTkr, true)));
            }

            // KR更新
            // 来期のゴールを今期に期変更した場合のみ
            $afterUpdatedTerm = $Goal->getTermTypeById($goalId);
            if ($preUpdatedTerm == Term::TERM_TYPE_NEXT && $afterUpdatedTerm == Term::TERM_TYPE_CURRENT) {
                if (!$KeyResult->updateTermByGoalId($goalId, Term::TYPE_CURRENT)) {
                    throw new Exception(sprintf("Failed to update krs case goal move term from nest to current. goal_id:%s"
                        , $goalId));
                }
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
     * @return int|bool
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
        /** @var Team $Team */
        $Team = ClassRegistry::init("Team");

        $updateData = [
            'id'          => $goalId,
            'name'        => $requestData['name'],
            'description' => $requestData['description']
        ];

        if (!empty($requestData['goal_category_id'])) {
            $updateData['goal_category_id'] = $requestData['goal_category_id'];
        }
        if (!empty($requestData['end_date'])) {
            $updateData['end_date'] = $requestData['end_date'];

            // 来期から今期へ期間変更する場合のみstart_dateを今日に設定
            $preUpdatedTerm = $Goal->getTermTypeById($goalId);
            $isNextToCurrentUpdate = ($preUpdatedTerm == Term::TERM_TYPE_NEXT) && ($requestData['term_type'] == Term::TERM_TYPE_CURRENT);
            if ($isNextToCurrentUpdate) {
                $updateData['start_date'] = AppUtil::todayDateYmdLocal($Team->getTimezone());
            }
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

        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");

        $currentTerm = $EvaluateTerm->getCurrentTermData();
        return $goal['start_date'] >= $currentTerm['start_date'];
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
                $goals[$key]['Goal']['progress'] = $this->calcProgressByOwnedPriorities($goal['KeyResult']);
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
     * ゴール進捗をKR一覧から計算して返す(渡したKRが持っている重要度を利用)
     *
     * @param array $keyResults
     *
     * @return float
     */
    function calcProgressByOwnedPriorities(array $keyResults): float
    {
        $sumPriorities = $this->sumPriorities($keyResults);
        return $this->calcProgressByOtherPriorities($keyResults, $sumPriorities);
    }

    /**
     * ゴールの進捗をキーリザルト一覧と渡した重要度から計算して返す
     * TODO:将来的にゴールIDのみを引数として渡し、そのゴールIDから各KR取得→KR進捗率計算→ゴール進捗率計算の順に処理を行うようにする。またキャッシュ化も必要。
     *
     * @param  array $keyResults      [description]
     * @param int    $sumKrPriorities 対象KRの重要度の合計
     *
     * @return float $res
     */
    function calcProgressByOtherPriorities(array $keyResults, int $sumKrPriorities): float
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
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");
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
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");

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
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");
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
     * @param string $targetEndDate
     * @param int    $targetDays
     * @param int    $maxBufferDays
     *
     * @return array ['graphStartDate'=>string|null,'graphEndDate'=>string|null,'plotDataEndDate'=>string|null]
     * @throws Exception
     */
    function getGraphRange(
        string $targetEndDate,
        int $targetDays = self::GRAPH_TARGET_DAYS,
        int $maxBufferDays = 0
    ): array
    {
        //initialize variables
        $ret = [
            'graphStartDate'  => null,
            'graphEndDate'    => null,
            'plotDataEndDate' => null,
        ];

        //今期の情報取得
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('Term');
        $term = $EvaluateTerm->getCurrentTermData();
        $termStartDate = $term['start_date'];
        $termEndDate = $term['end_date'];

        //バリデーション
        $validOrErrorMsg = $this->validateGraphRange(
            $targetEndDate,
            $targetDays,
            $maxBufferDays,
            $termStartDate,
            $termEndDate
        );
        if ($validOrErrorMsg !== true) {
            throw new Exception($validOrErrorMsg);
        }

        //期の開始日から指定グラフ終了日までの日数が少ない場合(以下がその定義)は、グラフ開始日に期の開始日をセット
        //期の開始日から指定グラフ終了日までの日数が最小プロット可能日数を下回る場合
        $daysFromTermStartToTargetEnd = AppUtil::totalDays($termStartDate, $targetEndDate);
        $daysMinPlot = $targetDays - $maxBufferDays;
        if ($daysFromTermStartToTargetEnd < $daysMinPlot) {
            $ret['graphStartDate'] = $termStartDate;
            $ret['graphEndDate'] = AppUtil::dateAfter($termStartDate, $targetDays - 1);;
            $ret['plotDataEndDate'] = $targetEndDate;
            return $ret;
        }

        if ($maxBufferDays > 0) {
            //指定グラフ終了日から期の終了日まで日数が少ない場合(以下がその定義)は、グラフ終了日は期の終了日をセット
            //指定グラフ終了日が期の終了日からバッファ日数を引いた日を超えた場合
            $termEndDateBeforeMaxBufferDays = AppUtil::dateBefore($termEndDate, $maxBufferDays);
            if ($targetEndDate > $termEndDateBeforeMaxBufferDays) {
                $ret['graphStartDate'] = AppUtil::dateBefore($termEndDate, $targetDays - 1);
                $ret['graphEndDate'] = $termEndDate;
                $ret['plotDataEndDate'] = $ret['graphEndDate'];
                return $ret;
            }
        }

        //$targetDays前から本日まで(バッファ日数を考慮)
        $targetStartDate = AppUtil::dateBefore($targetEndDate, $targetDays - 1);
        $ret['graphStartDate'] = AppUtil::dateAfter($targetStartDate, $maxBufferDays);
        $ret['graphEndDate'] = AppUtil::dateAfter($targetEndDate, $maxBufferDays);
        $ret['plotDataEndDate'] = $targetEndDate;

        return $ret;
    }

    /**
     * グラフ範囲指定のバリデーション
     * okならtrue,ngならメッセージを返す
     *
     * @param string $targetEndDate
     * @param int    $targetDays
     * @param int    $maxBufferDays
     * @param string $termStartDate
     * @param string $termEndDate
     *
     * @return string|true
     */
    function validateGraphRange(
        string $targetEndDate,
        int $targetDays,
        int $maxBufferDays,
        string $termStartDate,
        string $termEndDate
    )
    {
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
        $termTotalDays = AppUtil::totalDays($termStartDate, $termEndDate);
        if ($targetDays > $termTotalDays) {
            $this->log(sprintf("%s%s [method:%s] targetDays(%s days) over termTotalDays(%s days).",
                    __FILE__, __LINE__, __METHOD__, $targetDays, $termTotalDays)
            );
            return __('Wrong target days.');
        }
        //指定グラフ終了日は評価期間内でなければいけない
        if ($targetEndDate < $termStartDate || $targetEndDate > $termEndDate) {
            $this->log(sprintf("%s%s [method:%s] target end date(%s) not in evaluate term(%s - %s)",
                    __FILE__, __LINE__, __METHOD__, $targetEndDate, $termStartDate, $termEndDate)
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
    )
    {
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
    ): array
    {
        //パラメータバリデーション
        $validOrErrorMsg = $this->validateGetProgressDrawingGraph($graphStartDate, $graphEndDate, $plotDataEndDate);
        if ($validOrErrorMsg !== true) {
            throw new Exception($validOrErrorMsg);
        }
        //今期の情報取得
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('Term');
        $termTimezone = $EvaluateTerm->getCurrentTermData()['timezone'];

        //当日がプロット対象に含まれるかどうか？
        $isIncludedTodayInPlotData = AppUtil::between(
            time(),
            strtotime($graphStartDate) - ($termTimezone * HOUR),
            strtotime($plotDataEndDate) - ($termTimezone * HOUR) + DAY
        );
        //日毎に集計済みのゴール進捗ログを取得
        $logStartDate = $graphStartDate;
        if ($isIncludedTodayInPlotData) {
            $logEndDate = AppUtil::dateYmdLocal(REQUEST_TIMESTAMP - DAY, $termTimezone);
        } else {
            $logEndDate = $plotDataEndDate;
        }
        //ゴール重要度のリスト key:goal_id,value:priority
        $goalPriorities = $this->findGoalPriorities($userId);
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        $goalIds = array_keys($goalPriorities);

        //最新のKRの値を取得
        $latestKrValues = $KeyResult->findProgressBaseValues($goalIds);
        $progressLogs = $this->findSummarizedUserProgressesFromLog(
            $userId,
            $goalPriorities,
            $latestKrValues,
            $logStartDate,
            $logEndDate
        );
        $progressLogs = $this->processProgressesToGraph($logStartDate, $logEndDate, $progressLogs);
        //ゴールが存在し、範囲に当日が含まれる場合は当日の進捗を取得しログデータとマージ
        if (!empty($goalIds) && $isIncludedTodayInPlotData) {
            $latestTotalGoalProgress = $this->findLatestSummarizedGoalProgress($latestKrValues, $goalPriorities);
            array_push($progressLogs, $latestTotalGoalProgress);
        }

        //sweetSpotを算出
        $sweetSpot = $withSweetSpot ? $this->getSweetSpot($graphStartDate, $graphEndDate) : [];

        //グラフ用データに整形
        $ret = $this->shapeDataForGraph($progressLogs, $sweetSpot, $graphStartDate, $graphEndDate);

        return $ret;
    }

    /**
     * ゴール重要度のリストを返す
     * key:goal_id,value:priority
     *
     * @param $userId
     *
     * @return array
     */
    function findGoalPriorities(int $userId): array
    {
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('Term');
        $termStartDate = $EvaluateTerm->getCurrentTermData()['start_date'];
        $termEndDate = $EvaluateTerm->getCurrentTermData()['end_date'];

        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');
        $goalPriorities = $GoalMember->findGoalPriorities($userId, $termStartDate, $termEndDate);
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
    ): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        if (!$Goal->exists($goalId)) {
            throw new Exception(__('The Goal is not exist.'));
        }
        //パラメータバリデーション
        $validOrErrorMsg = $this->validateGetProgressDrawingGraph($graphStartDate, $graphEndDate, $plotDataEndDate);
        if ($validOrErrorMsg !== true) {
            throw new Exception($validOrErrorMsg);
        }
        //今期の情報取得
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('Term');
        $termTimezone = $EvaluateTerm->getCurrentTermData()['timezone'];

        //当日がプロット対象に含まれるかどうか？
        $isIncludedTodayInPlotData = AppUtil::between(
            time(),
            strtotime($graphStartDate) - ($termTimezone * HOUR),
            strtotime($plotDataEndDate) - ($termTimezone * HOUR) + DAY
        );

        //日毎に集計済みのゴール進捗ログを取得
        $logStartDate = $graphStartDate;
        if ($isIncludedTodayInPlotData) {
            $logEndDate = AppUtil::dateYmdLocal(REQUEST_TIMESTAMP - DAY, $termTimezone);
        } else {
            $logEndDate = $plotDataEndDate;
        }

        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init('KeyResult');
        //最新のKRの値を取得
        $latestKrValues = $KeyResult->findProgressBaseValues([$goalId]);

        $progressLogs = $this->findGoalProgressFromLog($goalId, $latestKrValues, $logStartDate, $logEndDate);
        $progressLogs = $this->processProgressesToGraph($logStartDate, $logEndDate, $progressLogs);

        //範囲に当日が含まれる場合は当日の進捗を取得しログデータとマージ
        if ($isIncludedTodayInPlotData) {
            $latestGoalProgress = $this->calcProgressByOwnedPriorities($latestKrValues);
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
    ): array
    {
        /** @noinspection PhpUndefinedVariableInspection */
        $ret[0] = array_merge(['sweet_spot_top'], $sweetSpot['top'] ?? []);
        $ret[1] = array_merge(['sweet_spot_bottom'], $sweetSpot['bottom'] ?? []);
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
            $ret[] = $TimeEx->formatDateI18n($timestamp, false);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        return $ret;
    }

    /**
     * 最新のゴール進捗の合計を取得
     * - ゴールの重要度を掛けて合計
     *
     * @param array $latestKrValues
     * @param array $goalPriorities
     *
     * @return float
     */
    function findLatestSummarizedGoalProgress(array $latestKrValues, array $goalPriorities): float
    {
        //ゴールIDでグルーピング[goal_id=>[[kr],[kr]],]
        $latestKrValues = Hash::combine($latestKrValues, '{n}.id', '{n}', '{n}.goal_id');
        $goalProgresses = [];
        foreach ($latestKrValues as $goalId => $krs) {
            $goalProgresses[$goalId] = $this->calcProgressByOwnedPriorities($krs);
        }
        $ret = $this->sumGoalProgress($goalProgresses, $goalPriorities);
        return $ret;
    }

    /**
     * ユーザのゴール進捗をKRログを元に集計
     * //キャッシュからデータを取得なければ以下処理
     * ///ログDBから自分の各ゴールの進捗データ取得(今期の開始日以降の過去30日分)
     * ///ゴールの重要度を掛け合わせる(例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75 )
     *
     * @param int    $userId
     * @param array  $goalPriorities
     * @param array  $latestKrValues
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findSummarizedUserProgressesFromLog(
        int $userId,
        array $goalPriorities,
        array $latestKrValues,
        string $startDate,
        string $endDate
    ): array
    {
        $goalIds = array_keys($goalPriorities);
        $today = date('Y-m-d');
        ///ログDBからユーザの各ゴールのKR現在値のログを取得
        /** @var KrValuesDailyLogService $KrValuesDailyLogService */
        $KrValuesDailyLogService = ClassRegistry::init('KrValuesDailyLogService');
        $krValueLogs = $KrValuesDailyLogService->getKrValueDailyLogFromCache($userId, $today);
        if ($krValueLogs === false) {
            /** @var KrValuesDailyLog $KrValuesDailyLog */
            $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");
            $krValueLogs = $KrValuesDailyLog->findLogs($startDate, $endDate, $goalIds);
            $KrValuesDailyLogService->writeKrValueDailyLogToCache($userId, $today, $krValueLogs);
        }
        ///ゴールの重要度を掛け合わせて日次のゴール進捗の合計を計算(例:ゴールA[30%,重要度3],ゴールB[60%,重要度5]なら30*3/8 + 60*5/8 = 48.75 )
        $progressLogs = $this->sumDailyGoalProgress($krValueLogs, $latestKrValues, $goalPriorities);
        return $progressLogs;
    }

    /**
     * 単一ゴールの進捗をKRログを元に算出
     * - ログDBから自分の各ゴールの進捗データ取得(今期の開始日以降の過去30日分)
     * - キャッシュする
     *
     * @param int    $goalId
     * @param array  $latestKrValues [[id =>"",start_value =>"",target_value =>"",priority =>""],]
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findGoalProgressFromLog(int $goalId, array $latestKrValues, string $startDate, string $endDate): array
    {
        $today = date('Y-m-d');
        /** @var KrValuesDailyLogService $KrValuesDailyLogService */
        $KrValuesDailyLogService = ClassRegistry::init('KrValuesDailyLogService');
        $krValueLogs = $KrValuesDailyLogService->getGoalKrValueDailyLogFromCache($goalId, $today);
        if ($krValueLogs === false) {
            /** @var KrValuesDailyLog $KrValuesDailyLog */
            $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");
            $krValueLogs = $KrValuesDailyLog->findLogs($startDate, $endDate, [$goalId]);
            $KrValuesDailyLogService->writeGoalKrValueDailyLogToCache($goalId, $today, $krValueLogs);
        }

        $progressLogs = $this->getDailyGoalProgress($krValueLogs, $latestKrValues);
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
     * KR日次ログを日付とゴールでグルーピングする
     *
     * @param array $krLogs including: goal_id,key_result_id, current_value, target_date
     *
     * @return array [goal_id=>[kr_id=>current_value],]
     */
    function groupingKrLogsByDateGoal(array $krLogs): array
    {
        //logsを日付でグルーピングする
        $krLogs = Hash::combine($krLogs, '{n}.key_result_id', '{n}', '{n}.target_date');

        $goalGroupedLogs = [];
        foreach ($krLogs as $date => $krs) {
            //ゴールでグルーピング [goal_id=>[kr_id=>current_value],]
            $goals = Hash::combine($krs, '{n}.key_result_id', '{n}.current_value', '{n}.goal_id');
            $goalGroupedLogs[$date] = $goals;
        }

        return $goalGroupedLogs;
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
        //最新のKRをゴールでグルーピング[goal_id=>[kr_id=>[...]],]
        $latestKrValues = Hash::combine($latestKrValues, '{n}.id', '{n}', '{n}.goal_id');

        //KR日次ログを日付とゴールでグルーピングする
        $goalGroupedLogs = $this->groupingKrLogsByDateGoal($krLogs);

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
     * @param array $latestKrs KRの配列
     *
     * @return float
     */
    function getProgressWithLog(array $logKrs, array $latestKrs): float
    {
        //最新KRリストのkey_result_idを配列のキーに置き換える
        $latestKrs = Hash::combine($latestKrs, '{n}.id', '{n}');
        $rebuildedKrs = [];
        $sumLatestKrPriorities = $this->sumPriorities($latestKrs);

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
        $ret = $this->calcProgressByOtherPriorities($rebuildedKrs, $sumLatestKrPriorities);
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
        $ret = AppUtil::floor(array_sum($progresses), 1);
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
    ): array
    {
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('Term');
        $term = $EvaluateTerm->getCurrentTermData();
        $termStartDate = $term['start_date'];
        $termEndDate = $term['end_date'];

        //開始日、終了日のどちらかが期の範囲を超えていたら、何もしない
        if ($startDate < $termStartDate || $endDate > $termEndDate) {
            return [];
        }

        $termTotalDays = AppUtil::totalDays($termStartDate, $termEndDate);
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
        $daysFromTermStart = AppUtil::diffDays($termStartDate, $startDate);
        $top = (float)$daysFromTermStart * $topStep;
        $bottom = (float)$daysFromTermStart * $bottomStep;

        //一日ずつ値を格納
        $graphTotalDays = AppUtil::totalDays($startDate, $endDate);
        for ($i = 1; $i <= $graphTotalDays; $i++) {
            $sweetSpot['top'][] = round($top, 2);
            $sweetSpot['bottom'][] = round($bottom, 2);

            $top += $topStep;
            $bottom += $bottomStep;
        }

        return $sweetSpot;
    }

    /**
     * getting goal filter menu
     *
     * @param int      $userId
     * @param null|int $termId if null, fetching all goals
     * @param bool     $withAllOption
     *
     * @return array
     */
    function getFilterMenu(int $userId, $termId = null, bool $withAllOption = true): array
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        $termStart = null;
        $termEnd = null;
        if ($termId) {
            $term = $Term->findById($termId)['Term'];
            $termStart = $term['start_date'];
            $termEnd = $term['end_date'];
        }

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        $goals = $Goal->findCollaboratedGoals($userId, $termStart, $termEnd);
        $goalFilter = [];
        if ($withAllOption) {
            $goalFilter[null] = __('All');
        }
        foreach ($goals as $goal) {
            $goalFilter[$goal['id']] = $goal['name'];
        }
        return $goalFilter;
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

    /**
     * ユーザーに紐づくゴール名一覧を返す
     * - TODO: feedページで呼ばれるメソッドのためキャッシュが必要
     *
     * @param  int   $userId
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    function findNameListAsMember(int $userId, string $startDate, string $endDate): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        $goalNameList = $Goal->findNameListAsMember($userId, $startDate, $endDate);
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
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init('Term');
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');
        /** @var GoalProgressDailyLog $GoalProgressDailyLog */
        $GoalProgressDailyLog = ClassRegistry::init('GoalProgressDailyLog');

        $targetTerm = $EvaluateTerm->getTermDataByDate($targetDate);
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
            $saveData[] = [
                'team_id'     => $teamId,
                'goal_id'     => $goal['Goal']['id'],
                //各ゴール毎にKRからゴール進捗を求める
                'progress'    => $this->calcProgressByOwnedPriorities($goal['KeyResult']),
                'target_date' => $targetDate,
            ];
        }
        $ret = $GoalProgressDailyLog->bulkInsert($saveData);

        return $ret;
    }

    /**
     * ゴール削除
     * TODO:削除ポリシー決定後、削除処理が不足していたら対応(KRやアクション等)
     *
     * @param $goalId
     *
     * @return bool
     */
    function delete(int $goalId): bool
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init("GoalLabel");
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var KrValuesDailyLogService $KrValuesDailyLogService */
        $KrValuesDailyLogService = ClassRegistry::init("KrValuesDailyLogService");
        /** @var KrValuesDailyLog $KrValuesDailyLog */
        $KrValuesDailyLog = ClassRegistry::init("KrValuesDailyLog");

        try {
            // トランザクション開始
            $Goal->begin();

            // ゴール削除
            // TODO:将来的にコメントアウトを外す
            // コメントアウト理由：deleteメソッドはSoftDeleteBehaviorのbeforeDeleteメソッドが原因で成功失敗に関わらずfalseを返しているため
//            if (!$Goal->delete($goalId)) {
//                throw new Exception(sprintf("Failed delete goal. data:%s", var_export(compact('goalId'), true)));
//            }
            $Goal->delete($goalId);

            // ゴールラベル削除
            if (!$GoalLabel->softDeleteAll(['goal_id' => $goalId])) {
                throw new Exception(sprintf("Failed delete goal_label. data:%s", var_export(compact('goalId'), true)));
            }
            // ゴールとアクションの紐付けを解除
            if (!$ActionResult->releaseGoal($goalId)) {
                throw new Exception(sprintf("Failed release action_result. data:%s",
                    var_export(compact('goalId'), true)));
            }
            // KR進捗日次ログ削除
            if (!$KrValuesDailyLog->softDeleteAll(['goal_id' => $goalId])) {
                throw new Exception(sprintf("Failed delete kr_values_daily_log. data:%s",
                    var_export(compact('goalId'), true)));
            }

            $Goal->commit();
        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Goal->rollback();
            return false;
        }

        // KR進捗日次ログキャッシュ削除(チーム単位)
        $KrValuesDailyLogService->deleteCache();
        // アクション可能ゴール一覧キャッシュ削除
        Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
        // ユーザページのマイゴール一覧キャッシュ削除
        Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

        return true;
    }

    /**
     * finding Ids by termId and userId
     *
     * @param int $termId
     * @param int $userId
     *
     * @return array
     */
    function findIdsByTermIdUserId(int $termId, int $userId): array
    {
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        $term = $Term->getById($termId);
        if (empty($term)) {
            return [];
        }

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init('Goal');

        $startDate = $term['start_date'];
        $endDate = $term['end_date'];
        $goalIds = $Goal->findCollaboratedGoals($userId, $startDate, $endDate, ['id']);
        $goalIds = Hash::extract($goalIds, '{n}.id');
        return $goalIds;
    }

    /**
     * Create headers for csv file
     *
     * @return array
     */
    public function createCsvHeader(): array
    {
        $csvHeader[GoalAndKrs::GOAL_ID] = __("GOAL ID");
        $csvHeader[GoalAndKrs::GOAL_NAME] = __("GOAL NAME");
        $csvHeader[GoalAndKrs::GOAL_DESCRIPTION] = __("GOAL DESCRIPTION");
        $csvHeader[GoalAndKrs::GOAL_CATEGORY] = __("GOAL CATEGORY");
        $csvHeader[GoalAndKrs::GOAL_LABELS] = __("GOAL_LABELS");
        $csvHeader[GoalAndKrs::GOAL_MEMBERS_COUNT] = __("GOAL MEMBERS COUNT");
        $csvHeader[GoalAndKrs::FOLLOWERS_COUNT] = __("FOLLOWERS COUNT");
        $csvHeader[GoalAndKrs::KRS_COUNT] = __("KRS COUNT");
        $csvHeader[GoalAndKrs::TERM] = __("TERM");
        $csvHeader[GoalAndKrs::GOAL_START_DATE] = __("GOAL START DATE");
        $csvHeader[GoalAndKrs::GOAL_END_DATE] = __("GOAL END DATE");
        $csvHeader[GoalAndKrs::LEADER_USER_ID] = __("LEADER USER ID");
        $csvHeader[GoalAndKrs::LEADER_NAME] = __("LEADER NAME");
        $csvHeader[GoalAndKrs::GOAL_PROGRESS] = __("GOAL PROGRESS(%)");
        $csvHeader[GoalAndKrs::GOAL_CREATED] = __("GOAL CREATED");
        $csvHeader[GoalAndKrs::GOAL_EDITED] = __("GOAL EDITED");
        $csvHeader[GoalAndKrs::KR_ID] = __("KR ID");
        $csvHeader[GoalAndKrs::KR_NAME] = __("KR NAME");
        $csvHeader[GoalAndKrs::KR_DESCRIPTION] = __("KR DESCRIPTION");
        $csvHeader[GoalAndKrs::KR_TYPE] = __("KR TYPE");
        $csvHeader[GoalAndKrs::KR_WEIGHT] = __("KR WEIGHT");
        $csvHeader[GoalAndKrs::KR_START_DATE] = __("KR START DATE");
        $csvHeader[GoalAndKrs::KR_END_DATE] = __("KR END DATE");
        $csvHeader[GoalAndKrs::KR_PROGRESS] = __("KR PROGRESS(%)");
        $csvHeader[GoalAndKrs::KR_UNIT] = __("KR UNIT");
        $csvHeader[GoalAndKrs::KR_INITIAL] = __("KR INITIAL");
        $csvHeader[GoalAndKrs::KR_TARGET] = __("KR TARGET");
        $csvHeader[GoalAndKrs::KR_CURRENT] = __("KR CURRENT");
        $csvHeader[GoalAndKrs::KR_CREATED] = __("KR CREATED");
        $csvHeader[GoalAndKrs::KR_EDITED] = __("KR EDITED");

        return $csvHeader;
    }

    /**
     * Create content of CSV file to be downloaded
     *
     * @param int   $teamId
     * @param array $conditions
     *
     * @return array
     */
    public function createCsvContent(int $teamId, array $conditions): array
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        $conditions = $this->extractConditions($conditions);

        // Search goals
        $goals = $Goal->searchForDownload($teamId, $conditions);

        return $this->processCsvContentFromGoals($teamId, $goals);
    }

    /**
     * process csv content from goals
     * @param int $teamId
     * @param array $goals
     * @return array
     */
    public function processCsvContentFromGoals(int $teamId, array $goals): array
    {
        /** @var Follower $Follower */
        $Follower = ClassRegistry::init('Follower');
        /** @var GoalCategory $GoalCategory */
        $GoalCategory = ClassRegistry::init('GoalCategory');
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init('GoalLabel');
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init('GoalMember');
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        /** @var Term $Term */
        $Term = ClassRegistry::init('Term');
        /** @var User $User */
        $User = ClassRegistry::init('User');

        $team = $Team->getCurrentTeam();
        $timezoneTeam = floatval($team['Team']['timezone']);

        $result = [];
        $csvDateFormat = self::CSV_DATE_FORMAT;
        foreach ($goals as $goal) {

            $krs = $KeyResult->getAllByGoalId($goal['id'], true);

            $goalCategoryName = '-';
            if (!empty($goal['goal_category_id'])) {
                $goalCategory = $GoalCategory->getById($goal['goal_category_id'], ["name"]);
                $goalCategoryName = empty($goalCategory['name']) ? '-' : $goalCategory['name'];
            }
            $goalLabels = implode(", ", array_values($GoalLabel->getLabelList($goal['id'])));
            $goalMemberCount = $GoalMember->countEachGoalId([$goal['id']])[$goal['id']];
            $goalFollowerCount = $Follower->countEachGoalId([$goal['id']])[$goal['id']];
            $goalTerm = $Term->getTermByDate($teamId, $goal['start_date']);
            $termStartDate = GoalousDateTime::createFromFormat('Y-m-d', $goalTerm['start_date'] )->format($csvDateFormat);
            $termEndDate = GoalousDateTime::createFromFormat('Y-m-d', $goalTerm['end_date'] )->format($csvDateFormat);

            $goalCreated = GoalousDateTime::createFromTimestamp($goal['created'])->setTimeZoneByHour($timezoneTeam)->format('Y/m/d');
            $goalEdited = empty($goal['modified']) ? '-' : GoalousDateTime::createFromTimestamp($goal['modified'])->setTimeZoneByHour($timezoneTeam)->format($csvDateFormat);
            $goalLeader = $User->getById($goal['user_id']);
            $goalProgress = AppUtil::formatBigFloat($this->calcProgressByOwnedPriorities($krs));

            // Set goal information
            $baseRow = [];
            $baseRow[GoalAndKrs::GOAL_ID] = $goal['id'];
            $baseRow[GoalAndKrs::GOAL_NAME] = $goal['name'];
            $baseRow[GoalAndKrs::GOAL_DESCRIPTION] = empty($goal['description']) ? '-' : $goal['description'];
            $baseRow[GoalAndKrs::GOAL_CATEGORY] = $goalCategoryName;
            $baseRow[GoalAndKrs::GOAL_LABELS] = $goalLabels;
            $baseRow[GoalAndKrs::GOAL_MEMBERS_COUNT] = $goalMemberCount;
            $baseRow[GoalAndKrs::FOLLOWERS_COUNT] = $goalFollowerCount;
            $baseRow[GoalAndKrs::KRS_COUNT] = count($krs);

            $baseRow[GoalAndKrs::TERM] = $termStartDate . " - " . $termEndDate;
            $baseRow[GoalAndKrs::GOAL_START_DATE] = GoalousDateTime::createFromFormat('Y-m-d', $goal['start_date'])->format($csvDateFormat);
            $baseRow[GoalAndKrs::GOAL_END_DATE] = GoalousDateTime::createFromFormat('Y-m-d', $goal['end_date'])->format($csvDateFormat);
            $baseRow[GoalAndKrs::LEADER_USER_ID] = $goal['user_id'];
            $baseRow[GoalAndKrs::LEADER_NAME] = $goalLeader['display_username'];
            $baseRow[GoalAndKrs::GOAL_PROGRESS] = $goalProgress;
            $baseRow[GoalAndKrs::GOAL_CREATED] = $goalCreated;
            $baseRow[GoalAndKrs::GOAL_EDITED] = $goalEdited;

            foreach ($krs as $kr) {
                $row = $baseRow;

                $krCreated = GoalousDateTime::createFromTimestamp($kr['created'])->setTimeZoneByHour($timezoneTeam)->format($csvDateFormat);
                $krEdited = empty($goal['modified']) ? '-' : GoalousDateTime::createFromTimestamp($kr['modified'])->setTimeZoneByHour($timezoneTeam)->format($csvDateFormat);

                // Set KR information
                $row[GoalAndKrs::KR_ID] = $kr['id'];
                $row[GoalAndKrs::KR_NAME] = $kr['name'];
                $row[GoalAndKrs::KR_DESCRIPTION] = empty($kr['description']) ? '-' : $kr['description'];
                $row[GoalAndKrs::KR_TYPE] = ($kr['tkr_flg']) ? 'TKR' : 'KR';
                $row[GoalAndKrs::KR_WEIGHT] = $kr['priority'];

                $row[GoalAndKrs::KR_START_DATE] = GoalousDateTime::createFromFormat('Y-m-d', $kr['start_date'])->format($csvDateFormat);
                $row[GoalAndKrs::KR_END_DATE] = GoalousDateTime::createFromFormat('Y-m-d', $kr['end_date'])->format($csvDateFormat);
                $row[GoalAndKrs::KR_PROGRESS] = AppUtil::calcProgressRate($kr['start_value'], $kr['target_value'], $kr['current_value']);
                $kr['value_unit'] = (int)$kr['value_unit'];
                if ($kr['value_unit'] === KeyResult::UNIT_BINARY) {
                    $row[GoalAndKrs::KR_UNIT] = '-';
                    $row[GoalAndKrs::KR_INITIAL] = '-';
                    $row[GoalAndKrs::KR_TARGET] = '-';
                    $row[GoalAndKrs::KR_CURRENT] = ($kr['current_value'] == 1) ? __('Completed') : __('Incomplete');
                } elseif ($kr['value_unit'] === KeyResult::UNIT_NUMBER) {
                    $row[GoalAndKrs::KR_UNIT] = '#';
                    $row[GoalAndKrs::KR_INITIAL] = $kr['start_value'];
                    $row[GoalAndKrs::KR_TARGET] = $kr['target_value'];
                    $row[GoalAndKrs::KR_CURRENT] = $kr['current_value'];
                } else {
                    $row[GoalAndKrs::KR_UNIT] = KeyResult::$UNIT[$kr['value_unit']];
                    $row[GoalAndKrs::KR_INITIAL] = $kr['start_value'];
                    $row[GoalAndKrs::KR_TARGET] = $kr['target_value'];
                    $row[GoalAndKrs::KR_CURRENT] = $kr['current_value'];
                }
                $row[GoalAndKrs::KR_CREATED] = $krCreated;
                $row[GoalAndKrs::KR_EDITED] = $krEdited;
                $result[] = $row;
            }
        }
        return $result;
    }


    /**
     * 検索条件抽出
     * 余分な条件が入り込まないようにする
     *
     * @param $params
     *
     * @return array
     */
    public function extractConditions($params)
    {
        $conditions = [];
        $conditionFields = ['keyword', 'term', 'category', 'progress', 'labels'];
        foreach ($conditionFields as $field) {
            if (!empty($params[$field])) {
                $conditions[$field] = $params[$field];
            }
        }
        return $conditions;
    }
}
