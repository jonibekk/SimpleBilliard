<?php

App::import('Service', 'AppService');

class ApprovalHistoryService extends AppService
{
    /**
     * 認定履歴一覧をレスポンス用に整形するためのラッパー
     *
     * @param  $resByModel
     *
     * @return $approvalHistory
     */
    function processApprovalHistories($resByModel)
    {
        $approvalHistories = Hash::map($resByModel, 'ApprovalHistory', function ($value) {
            return $this->processApprovalHistory($value);
        });
        return $approvalHistories;
    }

    /**
     * 認定履歴を整形する
     *
     * @param  $approvalHistory
     *
     * @return $approvalHistory
     */
    function processApprovalHistory($approvalHistory)
    {
        if(Hash::get($approvalHistory, 'User')) {
            $approvalHistory['user'] = Hash::get($approvalHistory, 'User');
            unset($approvalHistory['User']);
        }
        return $approvalHistory;
    }
}
