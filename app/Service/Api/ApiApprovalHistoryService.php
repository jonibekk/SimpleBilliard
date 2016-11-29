<?php
App::import('Service/Api', 'ApiService');
App::uses('ApprovalHistory', 'Model');

/**
 * Class ApiApprovalHistoryService
 */
class ApiApprovalHistoryService extends ApiService
{

    function processApprovalHistories($approvalHistories)
    {
        if(!$approvalHistories) {
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
     * @param $approvalHistories
     * @param $goalMemberUserId
     */
    function getClearImportantWord($clearStatus, $importantStatus)
    {
        $clearAndImportantWord = '';
        if ($clearStatus == ApprovalHistory::STATUS_IS_NOT_CLEAR) {
            $clearAndImportantWord = __('This Top Key Result is not clear.');
        } elseif ($clearStatus == ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == ApprovalHistory::STATUS_IS_IMPORTANT) {
            $clearAndImportantWord = __('This Top Key Result is clear and most important.');
        } elseif ($clearStatus == ApprovalHistory::STATUS_IS_CLEAR && $importantStatus == ApprovalHistory::STATUS_IS_NOT_IMPORTANT) {
            $clearAndImportantWord = __('This Top Key Result is not most important.');
        }
        return $clearAndImportantWord;
    }

    function getLatestCoachActionStatement($goalMemberId, $userId)
    {
        /** @var ApprovalHistory $ApprovalHistory */
        $ApprovalHistory = ClassRegistry::init("ApprovalHistory");

        $statement = '';
        $latestHistory = $ApprovalHistory->findLatestByUserId($goalMemberId, $userId);
        if(!$latestHistory) return $statement;

        $actionStatus = Hash::get($latestHistory, 'action_status');
        if($actionStatus == $ApprovalHistory::STATUS_ACTION_IS_TARGET_FOR_EVALUATION) {
            $statement = __("You have added this goal as a target of evaluation.");
        } elseif($actionStatus == $ApprovalHistory::STATUS_ACTION_IS_NOT_TARGET_FOR_EVALUATION) {
            $statement = __("You have not added this goal as a target of evaluation.");
        }

        return $statement;
    }


}
