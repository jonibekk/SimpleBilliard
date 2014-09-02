<?php
App::uses('AppController', 'Controller');

/**
 * Goals Controller
 *
 * @property Goal $Goal
 */
class GoalsController extends AppController
{

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    public function add()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        if ($this->request->is('post') && !empty($this->request->data)) {
            if ($this->Goal->add($this->request->data)) {
                $this->Pnotify->outSuccess(__d('gl', "ゴールを作成しました。"));
            }
            else {
                $this->Pnotify->outError(__d('gl', "ゴールの作成に失敗しました。"));
            }
            $this->redirect("/");
        }
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $this->set(compact('goal_category_list', 'priority_list'));
    }

}
