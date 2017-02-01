<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'KeyResultService');
APP::import('Service/Api', 'ApiApprovalHistoryService');

/**
 * Class ApiGoalApprovalService
 */
class ApiGoalApprovalService extends ApiService
{
    /**
     * 認定詳細ページの初期データレスポンスのためにモデルデータをフォーマット
     *
     * @param  array $resByModel
     * @param  int   $myUserId
     *
     * @return array
     */
    public function process(array $resByModel, int $myUserId): array
    {
        /** @var GoalCategory $GoalCategory */
        $GoalCategory = ClassRegistry::init("GoalCategory");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var User $User */
        $User = ClassRegistry::init("User");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        // モデル名整形(大文字->小文字)
        $goalMember = Hash::get($resByModel, 'GoalMember');
        if(empty($goalMember)){
            return [];
        }
        $res = $this->formatResponseData($resByModel);
        $res = Hash::merge($goalMember, $res);
        unset($res['goal_member']);
        // TODO: goal -> leader の関係がhasManyのため、leaderのデータを配列で取ってきてしまう。
        //       Modelのアソシエーションを直す必要あり。
        $res['goal']['leader'] = Hash::extract($res, 'goal.leaders.0');

        // 画像パス追加
        $res['user'] = $User->attachImgUrl($res['user'], 'User');
        $res['goal'] = $Goal->attachImgUrl($res['goal'], 'Goal');

        // 認定履歴の文言を追加
        /** @var ApiApprovalHistoryService $ApiApprovalHistoryService */
        $ApiApprovalHistoryService = ClassRegistry::init("ApiApprovalHistoryService");
        $res['approval_histories'] = $ApiApprovalHistoryService->processApprovalHistories($res['approval_histories']);
        $historiesCount = count($res['approval_histories']);
        $res['histories_view_more_text'] = '';
        if ($historiesCount > 1) {
            // 画面上に一件デフォルトで履歴を表示するので、
            // 画面から隠れている履歴は(合計 - 1)件。
            $res['histories_view_more_text'] = __('View %s comments', $historiesCount - 1);
        }
        $res['latest_coach_action_statement'] = $ApiApprovalHistoryService->getLatestCoachActionStatement($res['id'],
            $myUserId);

        // TKRの整形
        $res['goal']['top_key_result'] = $KeyResultService->processKeyResult($res['goal']['top_key_result']);

        // ゴール/TKRの変更前のスナップショットを取得
        $res['goal'] = $this->processChangeLog($res['goal']);
        if (Hash::get($res, 'goal.kr_change_log')) {
            // 画像パス追加
            $res['goal']['goal_change_log'] = $Goal->attachImgUrl($res['goal']['goal_change_log'], 'Goal');
            // TKRの整形
            $res['goal']['kr_change_log'] = $KeyResultService->processKeyResult($res['goal']['kr_change_log']);
            // カテゴリ追加
            $category = $GoalCategory->findById($res['goal']['goal_change_log']['goal_category_id'], ['name']);
            $res['goal']['goal_change_log']['goal_category'] = Hash::get($category, 'GoalCategory');
        }

        // フロント側の実装を楽にするためにユーザーステータス等を挿入
        $res['is_leader'] = (boolean)$res['type'];
        $res['is_mine'] = $res['user']['id'] == $myUserId;
        $res['type'] = GoalMember::$TYPE[$res['type']];

        // 不要な要素の削除
        unset($res['User'], $res['Goal'], $res['ApprovalHistory'], $res['goal']['GoalCategory'], $res['goal']['Leader'], $res['goal']['TopKeyResult'], $res['goal']['leader']['User']);

        return $res;
    }

    /**
     * ゴール編集ログの差分を確認し、差分があればレスポンスにログを追加する
     *
     * @param  $goal
     *
     * @return $goal
     */
    function processChangeLog($goal)
    {
        // goal
        $goalId = Hash::get($goal, 'id');
        $checkColumns = [
            'name' => Hash::get($goal, 'name'),
            'photo_file_name' => Hash::get($goal, 'photo_file_name'),
            'goal_category_id' => Hash::get($goal, 'goal_category_id')
        ];
        $goal['goal_change_log'] = $this->extractGoalChangeDiff($goalId, $checkColumns);

        // kr
        $tkr = Hash::get($goal, 'top_key_result');
        $checkColumns = [
            'name' => Hash::get($tkr, 'name'),
            'start_value' => Hash::get($tkr, 'start_value'),
            'target_value' => Hash::get($tkr, 'target_value'),
            'value_unit' => Hash::get($tkr, 'value_unit'),
            'description' => Hash::get($tkr, 'description')
        ];
        $goal['kr_change_log'] = $this->extractKrChangeDiff($goalId, $checkColumns);

        return $goal;
    }

    function extractGoalChangeDiff(int $goalId, array $checkPresentColumns)
    {
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");

        $goalChangeLog = $GoalChangeLog->findLatestSnapshot($goalId);
        if (!$goalChangeLog) {
            return null;
        }

        // 現在のゴールと変更ログとの差分を計算。値が違うキーだけ抽出される
        $diff = [];
        foreach($checkPresentColumns as $key => $val) {
            if($goalChangeLog[$key] !== $val) {
                $diff[$key] = $goalChangeLog[$key];
            }
        }

        return $diff;
    }

    function extractKrChangeDiff(int $goalId, array $checkPresentColumns)
    {
        /** @var KrChangeLog $KrChangeLog */
        $KrChangeLog = ClassRegistry::init("KrChangeLog");

        $krChangeLog = $KrChangeLog->getLatestSnapshot($goalId, $KrChangeLog::TYPE_APPROVAL_BY_COACH);
        if (!$krChangeLog) {
            return null;
        }

        // 現在のtkrと変更ログとの差分を計算。値が違うキーだけ抽出される
        $diff = [];
        foreach($checkPresentColumns as $key => $val) {
            if($krChangeLog[$key] !== $val) {
                $diff[$key] = $krChangeLog[$key];
            }
        }

        return $diff;
    }

}
