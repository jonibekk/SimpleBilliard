<?php
App::import('Service/Api', 'ApiService');
App::uses('ApprovalHistory', 'Model');

/**
 * Class ApiApprovalHistoryService
 */
class ApiApprovalHistoryService extends ApiService
{
    /**
     * 認定履歴一覧をレスポンス用に変換
     *
     * @param  array $approvalHistories
     *
     * @return array
     */
    function processApprovalHistories($approvalHistories): array
    {
        if (!$approvalHistories) {
            return [];
        }

        return Hash::map($approvalHistories, '', function ($approvalHistory) {
            $clearStatus = $approvalHistory['select_clear_status'];
            $importantStatus = $approvalHistory['select_important_status'];
            $approvalHistory['clear_and_important_word'] = $this->getClearImportantWord($clearStatus, $importantStatus);

            return $approvalHistory;
        });
    }

    /**
     * コーチによるゴール認定文言の追加
     *
     * @param int $clearStatus
     * @param int $importantStatus
     *
     * @return string
     */
    function getClearImportantWord(int $clearStatus, int $importantStatus): string
    {
        if ($clearStatus == ApprovalHistory::STATUS_IS_NOT_CLEAR) {
            return __('This Top Key Result is not clear.');
        } elseif ($clearStatus == ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == ApprovalHistory::STATUS_IS_IMPORTANT) {
            return __('This Top Key Result is clear and most important.');
        } elseif ($clearStatus == ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == ApprovalHistory::STATUS_IS_NOT_IMPORTANT) {
            return __('This Top Key Result is not most important.');
        }
        return '';
    }

    /**
     * 認定におけるコーチの最新アクションのstatementを追加
     *
     * @param int $goalMemberId
     * @param int $userId
     *
     * @return string
     */
    function getLatestCoachActionStatement(int $goalMemberId, int $userId): string
    {
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");

        $statement = '';
        $latestHistory = $ApprovalHistory->findLatestByUserId($goalMemberId, $userId);
        if (!$latestHistory) {
            return $statement;
        }

        $actionStatus = Hash::get($latestHistory, 'action_status');
        if ($actionStatus == $ApprovalHistory::STATUS_ACTION_IS_TARGET_FOR_EVALUATION) {
            $statement = __("You have added this Goal as a target of evaluation.");
        } elseif ($actionStatus == $ApprovalHistory::STATUS_ACTION_IS_NOT_TARGET_FOR_EVALUATION) {
            $statement = __("You have not added this Goal as a target of evaluation.");
        }

        return $statement;
    }

}
