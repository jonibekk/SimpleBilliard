<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'GoalService');
App::uses('TimeExHelper', 'View/Helper');

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
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        // デフォルト値定義
        $ret = [
            'data'   => [],
            'count'  => 0,
            'paging' => ['next' => null]
        ];

        // 検索条件抽出(余分な条件が入り込まないようにする)
        $conditions = $GoalService->extractConditions($conditions);

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
        $ret['data'] = $this->extend($ret['data'], $userId, $conditions);

        return $ret;
    }

    /**
     * データ拡張
     *
     * @param array $goals
     * @param int   $loginUserId
     * @param array $conditionParams
     *
     * @return array
     */
    private function extend(array $goals, int $loginUserId, array $conditionParams): array
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
        foreach ($goalLabels as $goalLabel) {
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

        // if not current term, everyone cannot follow all goals.
        // if term param has not set, default is present
        $termParam = Hash::get($conditionParams, 'term');
        if (empty($termParam) || $termParam == 'present') {
            $isCurrentTerm = true;
        } else {
            $isCurrentTerm = false;
        }

        $followConditionGoalIds = [];
        // フォローのアクションを無効にするか
        foreach ($goals as &$goal) {
            if ($isCurrentTerm === false) {
                $goal['can_follow'] = false;
                continue;
            }
            if ($goal['user_id'] == $loginUserId) {
                $goal['can_follow'] = false;
                continue;
            }
            if ($goal['completed']) {
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

    /**
     * @param int $limit
     *
     * @return array
     */
    public function findDashboardFirstViewResponse(int $limit): array
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var ApiKeyResultService $ApiKeyResultService */
        $ApiKeyResultService = ClassRegistry::init("ApiKeyResultService");
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var Term $EvaluateTerm */
        $EvaluateTerm = ClassRegistry::init("Term");

        $TimeEx = new TimeExHelper(new View());

        // レスポンスデータ定義
        $ret = [
            'data'   => [
                'progress_graph' => [],
                'krs'            => [],
                'goals'          => []
            ],
            'paging' => [
                'next' => ''
            ],
            'count'  => 0
        ];

        // KR一覧レスポンスデータ取得
        // Paging目的で1つ多くデータを取得する
        // ※キャッシュは1次リリースでは使わない。今後パフォーマンスで問題があれば使用検討
        $krs = $ApiKeyResultService->findInDashboard($limit + 1);

        //KRが一件もない場合はdataキーを空で返す
        if (empty($krs)) {
            $ret['data'] = [];
            return $ret;
        }

        // ページング情報セット
        if (count($krs) > $limit) {
            $ret['paging'] = $ApiKeyResultService->generatePagingInDashboard($limit);
            array_pop($krs);
        }

        // カウント数をセット
        $ret['count'] = $KeyResultService->countMine();
        // KRデータセット
        $ret['data']['krs'] = $krs;
        // Goalデータセット
        $currentTerm = $EvaluateTerm->getCurrentTermData();
        $ret['data']['goals'] = $GoalService->findNameListAsMember($Goal->my_uid, $currentTerm['start_date'],
            $currentTerm['end_date']);

        // TODO: 後ほどweightが0のゴールはグラフの計算を行わないように実装する必要がある。これはあくまで例外が発生しないための緊急対応。
        //       ここでは初期データに含まれるゴールすべてのweightが0の場合にグラフの計算をさせないようにしている
        //       詳しくはここ https://jira.goalous.com/browse/GL-5713
        if (count($krs) == count(Hash::extract($krs, '{n}.goal_member[priority=0]'))) {
            return $ret;
        }

        //グラフデータのセット
        $todayDate = AppUtil::dateYmd(REQUEST_TIMESTAMP + $currentTerm['timezone'] * HOUR);
        $graphRange = $GoalService->getGraphRange(
            $todayDate,
            GoalService::GRAPH_TARGET_DAYS,
            GoalService::GRAPH_MAX_BUFFER_DAYS
        );
        /** @var User $User */
        $User = ClassRegistry::init("User");
        $progressGraph = $GoalService->getUserAllGoalProgressForDrawingGraph(
            $User->my_uid,
            $graphRange['graphStartDate'],
            $graphRange['graphEndDate'],
            $graphRange['plotDataEndDate'],
            true
        );
        $ret['data']['progress_graph'] = [
            'data'       => $progressGraph,
            'start_date' => $TimeEx->formatDateI18n(strtotime($graphRange['graphStartDate']), false),
            'end_date'   => $TimeEx->formatDateI18n(strtotime($graphRange['graphEndDate']), false),
        ];

        return $ret;
    }

    /**
     * Create a CSV file for downloading, based on set filters
     *
     * @param int   $teamId
     * @param array $conditions
     *
     * @return string
     */
    public function createCsvFile(int $teamId, array $conditions): string {

        // Threshold of 100 MB (100 * 1024 * 1024)
        $fd = fopen('php://temp/maxmemory:104857600', 'w');
        if($fd === false) {
            throw new RuntimeException('Failed to open temporary file');
        }

        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init('GoalService');

        $headers = $GoalService->createCsvHeader();
        $records = $GoalService->createCsvContent($teamId,  $conditions);

        fputcsv($fd, $headers);
        foreach($records as $record) {
            fputcsv($fd, $record);
        }

        rewind($fd);
        $csv = stream_get_contents($fd);
        fclose($fd);

        return $csv;
    }

}
