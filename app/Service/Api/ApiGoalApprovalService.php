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
     * @param  array $goalMember
     * @param  int   $myUserId
     *
     * @return array
     */
    public function process(array $goalMember, int $myUserId): array
    {
        /** @var GoalCategory $GoalCategory */
        $GoalCategory = ClassRegistry::init("GoalCategory");
        /** @var Goal $Goal */
        $Goal = ClassRegistry::init("Goal");
        /** @var User $User */
        $User = ClassRegistry::init("User");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");
        /** @var KrChangeLog $KrChangeLog */
        $KrChangeLog = ClassRegistry::init("KrChangeLog");

        // モデル名整形(大文字->小文字)
        $ret = $this->formatResponseData($goalMember);

        // フロント側で処理しやすいように、goalとtop_key_resultのデータを同階層にする
        $ret['top_key_result'] = $ret['goal']['top_key_result'];
        unset($ret['goal']['top_key_result']);

        // TODO: goal -> leader の関係がhasManyのため、leaderのデータを配列で取ってきてしまう。
        //       Modelのアソシエーションを直す必要あり。
        $ret['goal']['leader'] = Hash::extract($ret, 'goal.leaders.0');

        // 画像パス追加
        $ret['user'] = $User->attachImgUrl($ret['user'], 'User');
        $ret['goal'] = $Goal->attachImgUrl($ret['goal'], 'Goal');

        // 認定履歴の文言を追加
        /** @var ApiApprovalHistoryService $ApiApprovalHistoryService */
        $ApiApprovalHistoryService = ClassRegistry::init("ApiApprovalHistoryService");
        $ret['approval_histories'] = $ApiApprovalHistoryService->processApprovalHistories($ret['approval_histories']);

        // 画面上に一件デフォルトで履歴を表示するので、
        // 画面から隠れている履歴は(合計 - 1)件。
        $historiesCount = count($ret['approval_histories']);
        $ret['histories_view_more_text'] = ($historiesCount > 1) ? __('View %s comments', $historiesCount - 1) : '';

        $ret['latest_coach_action_statement'] = $ApiApprovalHistoryService->getLatestCoachActionStatement($ret['goal_member']['id'],
            $myUserId);

        // TKRの整形
        $ret['top_key_result'] = $KeyResultService->processKeyResult($ret['top_key_result']);

        // ゴールのログ&変更カラムを取得
        $ret['goal_change_log'] = $GoalChangeLog->findLatestSnapshot($ret['goal']['id']);
        $ret['goal_changed_columns'] = $this->extractGoalChangeDiffColumns($ret['goal'], $ret['goal_change_log']);

        // TKRのログ&変更カラムを取得
        $tkr_change_log = $KrChangeLog->getLatestSnapshot($ret['goal']['id'], $KrChangeLog::TYPE_APPROVAL_BY_COACH);
        $ret['tkr_change_log'] = $KeyResultService->processKeyResult($tkr_change_log);
        $ret['tkr_changed_columns'] = $this->extractTkrChangeDiffColumns($ret['top_key_result'], $ret['tkr_change_log']);

        if (Hash::get($ret, 'tkr_change_log')) {
            // 画像パス追加
            $ret['goal_change_log'] = $Goal->attachImgUrl($ret['goal_change_log'], 'Goal');
            // TKRの整形
            $ret['tkr_change_log'] = $KeyResultService->processKeyResult($ret['tkr_change_log']);
            // カテゴリ追加
            $category = $GoalCategory->findById($ret['goal_change_log']['goal_category_id'], ['name']);
            $ret['goal_change_log']['goal_category'] = Hash::get($category, 'GoalCategory');
        }

        // フロント側の実装を楽にするためにユーザーステータス等を挿入
        $ret['is_leader'] = (boolean)$ret['goal_member']['type'];
        $ret['is_mine'] = $ret['user']['id'] == $myUserId;
        $ret['type'] = GoalMember::$TYPE[$ret['goal_member']['type']];

        return $ret;
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
        $goal['tkr_change_log'] = $this->extractKrChangeDiff($goalId, $checkColumns);

        return $goal;
    }

    function extractGoalChangeDiffColumns(array $goal, array $goalChangeLog): array
    {
        // 現在のゴールと変更ログとの差分を計算。値が違うキーだけ抽出される
        $diff = [];
        foreach($goal as $key => $val) {
            if(empty($goalChangeLog[$key]) || $goalChangeLog[$key] !== $val) {
                $diff[$key] = $key;
            }
        }

        return $diff;
    }

    function extractTkrChangeDiffColumns(array $tkr, array $tkrChangeLog): array
    {
        // 現在のtkrと変更ログとの差分を計算。値が違うキーだけ抽出される
        $diff = [];
        foreach($tkr as $key => $val) {
            if(empty($tkrChangeLog[$key]) || $tkrChangeLog[$key] !== $val) {
                $diff[$key] = $key;
            }
        }
        $this->log($diff);

        return $diff;
    }

}
