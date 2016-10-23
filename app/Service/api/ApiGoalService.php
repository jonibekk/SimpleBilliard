<?php
App::import('Service/Api', 'ApiService');

/**
 * Class AppService
 */
class ApiGoalService extends ApiService
{
    /**
     * ゴール検索
     *
     * @param $conditions
     * @param $offset
     * @param $limit
     * @param $order
     *
     * @return array
     */
    function search($userId, $conditions, $offset, $limit, $order = "")
    {
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");

        // デフォルト値定義
        $ret = [
            'data'   => [],
            'count'  => 0,
            'paging' => ['next' => null]
        ];

        // 検索条件抽出(余分な条件が入り込まないようにする)
        $conditions = $this->extractConditions($conditions);

        // ゴール件数取得
        $count = $Goal->countSearch($conditions);
        $this->log($Goal->getDataSource()->getLog());
        if ($count == 0) {
            return $ret;
        }
        $ret['count'] = $count;

        // ゴール検索
        $goals = $Goal->search($conditions, $offset, $limit + 1, $order);
        if (empty($goals)) {
            return $ret;
        }

        // APIレスポンス用に整形
        $ret['data'] = $this->formatResponseData($goals);

        // レスポンスデータ拡張
        $ret['data'] = $this->extend($ret['data'], $userId);

        // ページング情報設定
        $this->setPaging($ret, $conditions, $offset, $limit, $order);

        return $ret;
    }

    /**
     * データ拡張
     *
     * @param $goals
     *
     * @return array
     * @internal param $params
     */
    private function extend($goals, $loginUserId)
    {
        // TODO：AppModel.attachImgUrlをService層に移す
        // 画像URLの取得を行うAppModel.attachImgUrlをService層に移したいが、
        // UploadHelper内で行っているUploadBehaviorを認識しない問題がある。
        // (そもそもHelperでBehaviorを使用しているのが間違い)

        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var GoalMember $GoalMember */
        $GoalMember = ClassRegistry::init("GoalMember");
        /** @var GoalLabel $GoalLabel */
        $GoalLabel = ClassRegistry::init("GoalLabel");
        /** @var KeyResult $KeyResult */
        $KeyResult = ClassRegistry::init("KeyResult");
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init("ActionResult");
        /** @var Follower $Follower */
        $Follower = ClassRegistry::init("Follower");

        // ゴール関連情報を取得する為にゴールIDを抽出
        $goalIds = Hash::extract($goals, '{n}.id');

        /* ゴール関連情報を取得 */
        /* ループの中で1件ずつ取得するのはパフォーマンスに影響があるのであらかじめまとめて取得しておく */

        // ゴールラベル
        $goalLabels = $GoalLabel->findByGoalId($goalIds);
        $goalLabelsEachGoalId = [];
        foreach($goalLabels as $goalLabel) {
            $goalId = Hash::get($goalLabel, 'GoalLabel.goal_id');
            $goalLabelsEachGoalId[$goalId][] = Hash::get($goalLabel, 'Label');
        }

        // リーダー
        $leaders = $GoalMember->findLeaders($goalIds);
        $leadersEachGoalId = Hash::combine($leaders, '{n}.goal_id', '{n}');

        // KR数、アクション数、フォロワー数、ゴールメンバー数
        $krCountEachGoalId = $KeyResult->countEachGoalId($goalIds);
        $actionCountEachGoalId = $ActionResult->countEachGoalId($goalIds);
        $followerCountEachGoalId = $Follower->countEachGoalId($goalIds);
        $goalMemberCountEachGoalId = $GoalMember->countEachGoalId($goalIds);

        // フォロー、コラボしているか
        $isFollowingCountEachGoalId = $Follower->isFollowingEachGoalId($goalIds, $loginUserId);
        $isMemberCountEachGoalId = $GoalMember->isMemberCountEachGoalId($goalIds, $loginUserId);

        // ゴール毎に関連情報を設定
        foreach ($goals as &$goal) {
            $goalId = $goal['id'];

            $goal = $Goal->attachImgUrl($goal, 'Goal');
            $goal['goal_labels'] = $goalLabelsEachGoalId[$goalId];
            $goal['leader'] = $leadersEachGoalId[$goalId];
            $goal['kr_count'] = (int)$krCountEachGoalId[$goalId];
            $goal['action_count'] = (int)$actionCountEachGoalId[$goalId];
            $goal['follower_count'] = (int)$followerCountEachGoalId[$goalId];
            $goal['goal_member_count'] = (int)$goalMemberCountEachGoalId[$goalId];
            $goal['is_follow'] = (boolean)$isFollowingCountEachGoalId[$goalId];
            $goal['is_member'] = (boolean)$isMemberCountEachGoalId[$goalId];
        }
        return $goals;
    }

    /**
     * 検索条件抽出
     * 余分な条件が入り込まないようにする
     *
     * @param $params
     *
     * @return array
     */
    private function extractConditions($params)
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

    /**
     * ページング情報設定
     *
     * @param $data
     * @param $conditions
     * @param $offset
     * @param $limit
     * @param $order
     */
    private function setPaging(&$data, $conditions, $offset, $limit, $order)
    {
        // 次回のデータが無い場合はページング情報は空で返す
        if ($limit + 1 > count($data['data'])) {
            return;
        }

        $newOffset = $offset + $limit;
        $queryParams = array_merge(
            $conditions,
            ['offset' => $newOffset],
            compact('order')
        );

        $data['paging']['next'] = '/api/v1/goals/search?' . http_build_query($queryParams);
    }
}
