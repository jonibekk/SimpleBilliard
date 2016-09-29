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
App::uses('Collaborator', 'Model');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');

/**
 * Class GoalService
 */
class GoalService extends AppService
{
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
    const EXTEND_COLLABORATOR = "GOAL:EXTEND_COLLABORATOR";

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
            $data['top_key_result'] = Hash::extract($Goal->KeyResult->getTkr($data['id']), 'KeyResult');
        }
        if (in_array(self::EXTEND_COLLABORATOR, $extends)) {
            $data['collaborator'] = Hash::extract($Goal->Collaborator->getUnique($userId, $data['id']), 'Collaborator');
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
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init("GoalLabel");
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        /** @var Collaborator $Collaborator */
        $Collaborator = ClassRegistry::init("Collaborator");

        try {
            // トランザクション開始
            $Goal->begin();

            // ゴール・TKR・コラボレーター取得
            $goal = $this->get($goalId, $userId, [
                self::EXTEND_TOP_KEY_RESULT,
                self::EXTEND_COLLABORATOR
            ]);
            // 本来TKRやコラボレーターが存在しないことは有りえないが一応判定
            if (empty($goal['top_key_result'])) {
                throw new Exception(sprintf("Not exist tkr. goalId:%d", $goalId));
            }
            if (empty($goal['collaborator'])) {
                throw new Exception(sprintf("Not exist collaborator. goalId:%d userId:%d", $goalId, $userId));
            }

            // ゴール更新
            $updateGoal = $this->buildUpdateGoalData($goalId, $requestData);
            if (!$Goal->save($updateGoal, false)) {
                throw new Exception(sprintf("Failed update goal. data:%s"
                    , var_export($updateGoal, true)));
            }

            // TKR更新
            $updateTkr = $this->buildUpdateTkrData($goal['top_key_result']['id'], $requestData);
            if (!$KeyResult->save($updateTkr, false)) {
                throw new Exception(sprintf("Failed update tkr. data:%s"
                    , var_export($updateTkr, true)));
            }

            // ゴールラベル更新
            if (!$GoalLabel->saveLabels($goalId, $requestData['labels'])) {
                throw new Exception(sprintf("Failed save labels. goalId:%s labels:%s"
                    , $goalId, var_export($requestData['labels'], true)));
            }

            // コラボレーター更新(再申請のステータスに変更)
            $updateCollaborator = [
                'id'              => $goal['collaborator']['id'],
                'approval_status' => Collaborator::APPROVAL_STATUS_REAPPLICATION,
                'priority'        => $requestData['priority']
            ];
            if (!$Collaborator->save($updateCollaborator, false)) {
                throw new Exception(sprintf("Failed update collaborator. data:%s"
                    , var_export($updateCollaborator, true)));
            }

            // 認定についてのコメント記載があれば登録
            if (!empty($requestData['approval_history']) && !empty($requestData['approval_history']['comment'])) {
                $approvalHistory = [
                    'collaborator_id' => $goal['collaborator']['id'],
                    'user_id'         => $userId,
                    'comment'         => $requestData['approval_history']['comment'],
                ];
                if (!$ApprovalHistory->save($approvalHistory, false)) {
                    throw new Exception(sprintf("Failed save approvalHistory. data:%s"
                        , var_export($approvalHistory, true)));
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
        /** @var EvaluateTerm $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("EvaluateTerm");

        $updateData = [
            'id'          => $goalId,
            'name'        => $requestData['name'],
            'description' => $requestData['description'],
        ];

        if (!empty($requestData['goal_category_id'])) {
            $updateData['goal_category_id'] = $requestData['goal_category_id'];
        }
        if (!empty($requestData['end_date'])) {
            $goalTerm = $EvaluateTerm->getTermDataByDatetime($requestData['end_date']);
            $updateData['end_date'] = AppUtil::getEndDateByTimezone($requestData['end_date'], $goalTerm['timezone']);
        }
        if (!empty($requestData['photo'])) {
            $updateData['photo'] = $requestData['photo'];
        }
        return $updateData;
    }

    /**
     * TKR更新データ作成
     *
     * @param $tkrId
     * @param $requestData
     *
     * @return array
     */
    private function buildUpdateTkrData($tkrId, $requestData)
    {
        $inputTkrData = Hash::get($requestData, 'key_result');
        if (empty($inputTkrData)) {
            return [];
        }

        $updateData = [
            'id'           => $tkrId,
            'name'         => $inputTkrData['name'],
            'description'  => $inputTkrData['description'],
            'value_unit'   => $inputTkrData['value_unit'],
            'start_value'  => $inputTkrData['start_value'],
            'target_value' => $inputTkrData['target_value'],
        ];
        return $updateData;
    }

    /**
     * ゴール登録・更新のバリデーション
     *
     * @param array        $data
     * @param array        $fields
     * @param integer|null $goalId
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
            $krValidation = $KeyResult->validateKrPOST($data['key_result']);

            if ($krValidation !== true) {
                $validationErrors['key_result'] = $this->validationExtract($krValidation);
            }
        }

        // コメントバリデーション
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        $ApprovalHistory->set($data['approval_history']);
        if (!$ApprovalHistory->validates()) {
            $validationErrors['approval_history'] = $this->validationExtract($ApprovalHistory->validationErrors);
        }

        return $validationErrors;
    }

    /**
     * 対象のゴールが今季以降のゴールか
     *
     * @param $goalId
     *
     * @return bool
     */
    function isGoalAfterCurrentTerm($goalId)
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
}
