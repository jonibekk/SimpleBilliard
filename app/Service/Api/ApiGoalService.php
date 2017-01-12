<?php
App::import('Service/Api', 'ApiService');

/**
 * Class AppService
 */
class ApiGoalService extends ApiService
{
    // ゴール検索デフォルト取得件数
    const GOAL_SEARCH_DEFAULT_LIMIT = 10;

    /**
     * ゴール検索
     *
     * @param        $userId
     * @param        $conditions
     * @param        $offset
     * @param        $limit
     * @param string $order
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

        // ページング情報設定
        $this->setPaging($ret, $conditions, $offset, $limit, $order);

        // レスポンスデータ拡張
        $ret['data'] = $this->extend($ret['data'], $userId);

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

        $followConditionGoalIds = [];
        // フォローのアクションを無効にするか
        foreach ($goals as &$goal) {
            if ($goal['user_id'] == $loginUserId) {
                $goal['can_follow'] = false;
                continue;
            }
            if ($goal['completed']){
                $goal['can_follow'] = false;
                continue;
            }
            $goal['can_follow'] = true;
            $followConditionGoalIds[] = $goal['id'];
        }

        // フォローしているか
        $isFollowingEachGoalId = $Follower->isFollowingEachGoalId($followConditionGoalIds, $loginUserId);

        // ゴール毎に関連情報を設定
        foreach ($goals as &$goal) {
            $goalId = $goal['id'];

            $goal = $Goal->attachImgUrl($goal, 'Goal');
            $goal['goal_labels'] = empty($goalLabelsEachGoalId[$goalId]) ? [] : $goalLabelsEachGoalId[$goalId];
            $goal['leader'] = $leadersEachGoalId[$goalId];
            $goal['kr_count'] = (int)$krCountEachGoalId[$goalId];
            $goal['action_count'] = (int)$actionCountEachGoalId[$goalId];
            $goal['follower_count'] = (int)$followerCountEachGoalId[$goalId];
            $goal['goal_member_count'] = (int)$goalMemberCountEachGoalId[$goalId];
            $goal['is_follow'] = !empty($isFollowingEachGoalId[$goalId]);
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
        array_pop($data['data']);
        $newOffset = $offset + $limit;
        $queryParams = array_merge(
            $conditions,
            ['offset' => $newOffset],
            compact('order')
        );

        $data['paging']['next'] = '/api/v1/goals/search?' . http_build_query($queryParams);
    }

    public function findDashboardFirstViewResponse($queryParams)
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var ApiKeyResultService $ApiKeyResultService */
        $ApiKeyResultService = ClassRegistry::init("ApiKeyResultService");

        // レスポンスデータ定義
        $ret = [
            'data'   => [
                'progress_graph' => [],
                'krs'            => []
            ],
            'paging' => [
                'next' => ''
            ],
            'count' => 0
        ];

        // パラメータ展開
        list('limit' => $limit) = $queryParams;

        // KR一覧レスポンスデータ取得
        // Paging目的で1つ多くデータを取得する
        $krs = $KeyResultService->findInDashboardFirstView($limit + 1);

        // ページング情報セット
        if (count($krs) > $limit) {
            $ret['paging'] = $ApiKeyResultService->generatePagingInDashboard($limit);
            array_pop($krs);
        }

        // カウント数をセット
        $ret['count'] = $KeyResultService->countMine();

        // KRデータセット
        $ret['data']['krs'] = $krs;

        return $ret;
    }
}
