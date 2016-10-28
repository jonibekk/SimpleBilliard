<?php
App::import('Service/Api', 'ApiService');
App::import('Service', 'KeyResultService');

/**
 * Class ApiGoalApprovalService
 */
class ApiGoalApprovalService extends ApiService
{
    /**
     * 認定詳細ページの初期データレスポンスのためにモデルデータをフォーマット
     *
     * @param  $resByModel
     * @param  $myUserId
     *
     * @return $res
     */
    public function processGoalApprovalForResponse($resByModel, $myUserId)
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
        $goal_memberUserId = $res['user']['id'];
        $res['approval_histories'] = $this->addClearImportantWordToApprovalHistories($res['approval_histories'],
            $goal_memberUserId);
        $res['histories_view_more_text'] = __('View %s comments', count($res['approval_histories']) - 1);

        // TKRの整形
        $res['goal']['top_key_result'] = $KeyResultService->processKeyResult($res['goal']['top_key_result']);

        // ゴール/TKRの変更前のスナップショットを取得
        $res['goal'] = $this->processChangeLog($res['goal']);
        if(Hash::get($res, 'goal.tkr_change_log')) {
            // 画像パス追加
            $res['goal']['goal_change_log'] = $Goal->attachImgUrl($res['goal']['goal_change_log'], 'Goal');
            // TKRの整形
            $res['goal']['tkr_change_log'] = $KeyResultService->processKeyResult($res['goal']['tkr_change_log']);
            // カテゴリ追加
            $category =  $GoalCategory->findById($res['goal']['goal_change_log']['goal_category_id'], ['name']);
            $res['goal']['goal_change_log']['goal_category'] = Hash::get($category, 'GoalCategory');
        }

        // マッピング
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
        $goalDiffCheckPaths = ['name', 'photo_file_name', 'goal_category_id'];
        $goal['goal_change_log'] = $this->processChangeGoalLog($goal, $goalDiffCheckPaths);

        // tkr
        $tkrDiffCheckPaths = ['name', 'start_value', 'target_value', 'value_unit', 'description'];
        $goal['tkr_change_log'] = $this->processChangeTkrLog($goal, $tkrDiffCheckPaths);

        return $goal;
    }

    function processChangeGoalLog($goal, $diffCheckPaths)
    {
        /** @var GoalChangeLog $GoalChangeLog */
        $GoalChangeLog = ClassRegistry::init("GoalChangeLog");

        $goalId = Hash::extract($goal, 'id');
        $goalChangeLog = $GoalChangeLog->findLatestSnapshot($goalId);
        if(!$goalChangeLog) {
            return null;
        }

        // 現在のゴールと変更ログとの差分を計算。値が違うキーだけ抽出される
        $goalChangeDiff = Hash::diff($goal, $goalChangeLog);

        // Calc goal diff
        foreach($diffCheckPaths as $path) {
            if(Hash::get($goalChangeDiff, $path)) {
                return $goalChangeLog;
            }
        }

        return null;
    }

    function processChangeTkrLog($goal, $diffCheckPaths)
    {
        /** @var TkrChangeLog $TkrChangeLog */
        $TkrChangeLog = ClassRegistry::init("TkrChangeLog");

        $goalId = Hash::extract($goal, 'id');
        $tkrChangeLog = $TkrChangeLog->findLatestSnapshot($goalId);
        if(!$tkrChangeLog) {
            return null;
        }

        // 現在のtkrと変更ログとの差分を計算。値が違うキーだけ抽出される
        $tkrChangeDiff = Hash::diff($goal['top_key_result'], $tkrChangeLog);

        // Calc tkr diff
        foreach($diffCheckPaths as $path) {
            if(Hash::get($tkrChangeDiff, $path)) {
                return $tkrChangeLog;
            }
        }

        return null;
    }

    /**
     * コーチによるゴール認定文言の追加
     * @param $approvalHistories
     * @param $goal_memberUserId
     */
    function addClearImportantWordToApprovalHistories($approvalHistories, $goalMemberUserId)
    {
        if(!$approvalHistories || !$goalMemberUserId) {
            return [];
        }

        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");
        return Hash::map($approvalHistories, '',
            function ($approvalHistory) use ($goalMemberUserId, $ApprovalHistory) {
                $clearStatus = $approvalHistory['select_clear_status'];
                $importantStatus = $approvalHistory['select_important_status'];

                $clearAndImportantWord = '';
                if ($clearStatus == $ApprovalHistory::STATUS_IS_NOT_CLEAR) {
                    $clearAndImportantWord = __('This Top Key Result is not clear.');
                } elseif ($clearStatus == $ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == $ApprovalHistory::STATUS_IS_IMPORTANT) {
                    $clearAndImportantWord = __('This Top Key Result is clear and most important.');
                } elseif ($clearStatus == $ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == $ApprovalHistory::STATUS_IS_NOT_IMPORTANT) {
                    $clearAndImportantWord = __('This Top Key Result is not most important.');
                }

                $approvalHistory['clear_and_important_word'] = $clearAndImportantWord;
                return $approvalHistory;
            });
    }

}
