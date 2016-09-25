<?php
/**
 * Created by PhpStorm.
 * User: yoshidam2
 * Date: 2016/09/21
 * Time: 17:57
 */

App::uses('Goal', 'Model');
App::uses('EvaluateTerm', 'Model');
App::uses('GoalLabel', 'Model');
App::uses('ApprovalHistory', 'Model');
App::import('View', 'Helper/TimeExHelper');
App::import('View', 'Helper/UploadHelper');

class GoalService extends Object
{
    const EXTEND_GOAL_LABELS = "GOAL:EXTEND_GOAL_LABELS";
    const EXTEND_TOP_KEY_RESULT = "GOAL:EXTEND_TOP_KEY_RESULT";
    const EXTEND_COLLABORATOR = "GOAL:EXTEND_COLLABORATOR";

    private static $cacheList = [];

    function get($id, $userId = null, $extends =[])
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

        $Goal = ClassRegistry::init("Goal");
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
        } elseif($nextTerm['start_date'] <= $data['start_date']
            && $data['start_date'] <= $nextTerm['end_date']
        ) {
            $data['term_type'] = 'next';
        } else {
            $data['term_type'] = 'current';
        }

        // 日付フォーマッット
        $data['start_date'] = $TimeExHelper->dateFormat($data['start_date'], $currentTerm['timezone']);
        $data['end_date'] = $TimeExHelper->dateFormat($data['end_date'], $currentTerm['timezone']);

        // キャッシュ変数に保存
        self::$cacheList[$id] = $data;

        // データ拡張
        return $this->extend($data, $userId, $extends);
    }

    function extend($data, $userId, $extends)
    {
        if (empty($data) || empty($extends)) {
            return $data;
        }
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
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init("GoalLabel");
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");

        try {
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

            $requestData['id'] = $goalId;
            // 保存するTKR情報
            $keyResult = Hash::get($requestData, 'key_result');
            $keyResult['id'] = $goal['top_key_result']['id'];

            // トランザクション開始
            $Goal->begin();

            $data = [
                'Goal'      => $requestData,
                'KeyResult' => [$keyResult],
                'Label'     => Hash::get($requestData, 'labels'),
            ];

            $goalTerm = $Goal->getGoalTermFromPost($data);

            $data = $Goal->convertGoalDateFromPost($data, $goalTerm);
            $data = $Goal->buildTopKeyResult($data, $goalTerm, false);

            // setting default image if default image is chosen and image is not selected.
            if (Hash::get($data, 'Goal.img_url') && !Hash::get($data, 'Goal.photo')) {
                $data['Goal']['photo'] = $data['Goal']['img_url'];
                unset($data['Goal']['img_url']);
            }

            // ゴール・TKR更新
            if (!$Goal->saveAll($data)) {
                throw new Exception(sprintf("Failed save goal. data:%s", var_export($data, true)));
            }
            // ゴールラベル更新
            if (!$GoalLabel->saveLabels($data['Goal']['id'], $data['Label'])) {
                throw new Exception(sprintf("Failed save labels. data:%s", var_export($data, true)));
            }
            // 認定についてのコメント記載があれば登録
            if (!empty($requestData['approval_history'])) {
                $approvalHistory = [
                    'collaborator_id' => $goal['collaborator']['id'],
                    'user_id' => $userId,
                    'comment' => $requestData['approval_history']['comment'],
                ];
                if (!$ApprovalHistory->save($approvalHistory)) {
                    throw new Exception(sprintf("Failed save approvalHistory. data:%s" , var_export($approvalHistory, true)));
                }
            }

            // Redisキャッシュ削除
            Cache::delete($Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
            Cache::delete($Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

            // トランザクション完了
            $Goal->commit();

            // TODO:通知関連実装
            //通知
    //        $this->NotifyBiz->push(Hash::get($data, 'socket_id'), "all");
    //        $this->_sendNotifyToCoach($goalId, NotifySetting::TYPE_MY_MEMBER_CREATE_GOAL);
    //
    //        $this->updateSetupStatusIfNotCompleted();
    //        //コーチと自分の認定件数を更新(キャッシュを削除)
    //        $coach_id = $this->User->TeamMember->getCoachUserIdByMemberUserId($this->my_uid);
    //        if ($coach_id) {
    //            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true), 'user_data');
    //            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coach_id), 'user_data');
    //        }

        } catch (Exception $e) {
            $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            $this->log($e->getTraceAsString());
            $Goal->rollback();
            return false;
        }
        return true;
    }
}
