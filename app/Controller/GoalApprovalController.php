<?php
App::uses('AppController', 'Controller');
App::uses('GoalMember', 'Model');

/**
 * OLD GoalApproval Controller
 */
class GoalApprovalController extends AppController
{
    /*
     * 旧ゴール認定ぺージ
     * 処理待ちページ
     * 新ゴール認定リストぺージへリダイレクト
     */
    public function index()
    {
        $this->log("■ Old Goal Approval index page! referer URL: " . $this->referer());
        return $this->redirect(['controller' => 'goals', 'action' => 'approval', 'list']);
    }

    /*
     * 旧ゴール認定ぺージ
     * 処理済みページ
     * 新ゴール認定リストぺージへリダイレクト
     */
    public function done()
    {
        $this->log("■ Old Goal Approval done page! referer URL: " . $this->referer());
        return $this->redirect(['controller' => 'goals', 'action' => 'approval', 'list']);
    }

}
