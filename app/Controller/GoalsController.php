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
        $this->Security->unlockedActions = ['add_key_result', 'edit_key_result'];
    }

    /**
     * ゴール一覧画面
     */
    public function index()
    {
        $this->_setMyCircle();
        $goals = $this->Goal->getAllGoals();
        $my_goals = $this->Goal->getMyGoals();
        $collabo_goals = $this->Goal->getMyCollaboGoals();
        $follow_goals = $this->Goal->getMyFollowedGoals();
        $current_global_menu = "goal";
        $this->set(compact('goals', 'my_goals', 'collabo_goals', 'follow_goals', 'current_global_menu'));
    }

    /**
     * ゴール作成
     * URLパラメータでmodeを付ける
     * mode なしは目標を決める,2はゴールを定める,3は情報を追加
     *
     * @param null $id
     *
     * @return \CakeResponse
     */
    public function add($id = null)
    {
        $purpose_id = isset($this->request->params['named']['purpose_id']) ? $this->request->params['named']['purpose_id'] : null;
        $this->layout = LAYOUT_ONE_COLUMN;
        //編集権限を確認。もし権限がある場合はデータをセット
        if ($id) {
            $this->request->data['Goal']['id'] = $id;
            try {
                $this->Goal->isPermittedAdmin($id);

            } catch (RuntimeException $e) {
                $this->Pnotify->outError($e->getMessage());
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect($this->referer());
            }
        }

        if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data)) {
            if (isset($this->request->params['named']['mode'])) {
                if ($this->Goal->add($this->request->data)) {
                    switch ($this->request->params['named']['mode']) {
                        case 2:
                            $this->Pnotify->outSuccess(__d('gl', "ゴールを保存しました。"));
                            //「ゴールを定める」に進む
                            $this->redirect([$this->Goal->id, 'mode' => 3, '#' => 'AddGoalFormOtherWrap']);
                            break;
                        case 3:
                            //完了
                            $this->Pnotify->outSuccess(__d('gl', "ゴールの作成が完了しました。"));
                            //TODO 一旦、トップにリダイレクト
                            $this->redirect("/");
                            break;
                    }
                }
                else {
                    $this->Pnotify->outError(__d('gl', "ゴールの保存に失敗しました。"));
                    $this->redirect($this->referer());
                }
            }
            else {
                if ($this->Goal->Purpose->add($this->request->data)) {
                    $this->Pnotify->outSuccess(__d('gl', "ゴールの目的を保存しました。"));
                    //「ゴールを定める」に進む
                    $url = ['mode' => 2, 'purpose_id' => $this->Goal->Purpose->id, '#' => 'AddGoalFormKeyResultWrap'];
                    $url = $id ? array_merge([$id], $url) : $url;
                    $this->redirect($url);
                }
                else {
                    $this->Pnotify->outError(__d('gl', "ゴールの目的の保存に失敗しました。"));
                    $this->redirect($this->referer());
                }
            }
        }
        else {
            //新規作成時以外はデータをセット
            if ($id) {
                $this->request->data = $this->Goal->getAddData($id);
            }
            elseif ($purpose_id) {
                //目的ID指定の場合はpurposeをセット
                if ($this->Goal->Purpose->isOwner($this->Auth->user('id'), $purpose_id)) {
                    $this->request->data = $this->Goal->Purpose->findById($purpose_id);
                }
                else {
                    $this->Pnotify->outError(__d('gl', "権限がありません。"));
                    $this->redirect($this->referer());
                }
            }
        }
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;
        $goal_start_date_limit_format = date('Y/m/d',
                                             $this->Goal->Team->getTermStartDate() + ($this->Auth->user('timezone') * 60 * 60));
        $goal_end_date_limit_format = date('Y/m/d', strtotime("- 1 day",
                                                              $this->Goal->Team->getTermEndDate()) + ($this->Auth->user('timezone') * 60 * 60));
        if (isset($this->request->data['Goal']) && !empty($this->request->data['Goal'])) {
            $goal_start_date_format = $goal_start_date_limit_format;
            $goal_end_date_format = $goal_end_date_limit_format;
        }
        else {
            $goal_start_date_format = date('Y/m/d', time() + ($this->Auth->user('timezone') * 60 * 60));
            //TODO 将来的には期間をまたぐ当日+6ヶ月を期限にするが、現状期間末日にする
            //$goal_end_date_format = date('Y/m/d', $this->getEndMonthLocalDateTime());
            $goal_end_date_format = $goal_end_date_limit_format;
        }
        $this->set(compact('goal_category_list',
                           'priority_list',
                           'kr_priority_list',
                           'kr_value_unit_list',
                           'goal_start_date_format',
                           'goal_end_date_format',
                           'goal_start_date_limit_format',
                           'goal_end_date_limit_format'
                   ));
        return $this->render();
    }

    /**
     * delete method
     *
     * @param string $id
     *
     * @return void
     */
    public function delete($id)
    {
        try {
            $this->Goal->isPermittedAdmin($id);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post', 'delete');
        $this->Goal->id = $id;
        $this->Goal->delete();
        $this->Pnotify->outSuccess(__d('gl', "ゴールを削除しました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * delete method
     *
     * @param $purpose_id
     *
     * @return void
     */
    public function delete_purpose($purpose_id)
    {
        try {
            if (!$this->Goal->Purpose->isOwner($this->Auth->user('id'), $purpose_id)) {
                throw new RuntimeException(__d('gl', "権限がありません。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post', 'delete');
        $this->Goal->Purpose->id = $purpose_id;
        $this->Goal->Purpose->delete();
        $this->Pnotify->outSuccess(__d('gl', "ゴールを削除しました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function ajax_get_more_index_items()
    {
        $this->_ajaxPreProcess();
        $goals = $this->Goal->getAllGoals(20, $this->request->params);
        $this->set(compact('goals'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Goal/index_items');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_goal_detail_modal($goal_id)
    {
        $this->_ajaxPreProcess();
        $goal = $this->Goal->getGoal($goal_id);
        $this->set(compact('goal'));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_goal_detail');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_add_key_result_modal($goal_id, $current_kr_id = null)
    {
        $this->_ajaxPreProcess();
        try {
            if (!$this->Goal->Collaborator->isCollaborated($goal_id)) {
                throw new RuntimeException();
            }
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $goal = $this->Goal->getGoalMinimum($goal_id);
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;

        $kr_start_date_format = date('Y/m/d', time() + ($this->Auth->user('timezone') * 60 * 60));

        //期限は現在+2週間にする
        //もしそれがゴールの期限を超える場合はゴールの期限にする
        $end_date = strtotime('+2 weeks', time());
        if ($end_date > $goal['Goal']['end_date']) {
            $end_date = $goal['Goal']['end_date'];
        }
        $kr_end_date_format = date('Y/m/d', $end_date + ($this->Auth->user('timezone') * 60 * 60));
        $limit_end_date = date('Y/m/d',
                               $goal['Goal']['end_date'] + ($this->Auth->user('timezone') * 60 * 60));
        $limit_start_date = date('Y/m/d',
                                 $goal['Goal']['start_date'] + ($this->Auth->user('timezone') * 60 * 60));

        $this->set(compact(
                       'goal',
                       'goal_id',
                       'goal_category_list',
                       'priority_list',
                       'kr_priority_list',
                       'kr_value_unit_list',
                       'kr_start_date_format',
                       'kr_end_date_format',
                       'limit_end_date',
                       'limit_start_date',
                       'current_kr_id'
                   ));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_add_key_result');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_collabo_change_modal($goal_id)
    {
        $this->_ajaxPreProcess();
        $goal = $this->Goal->getCollaboModalItem($goal_id);
        $priority_list = $this->Goal->priority_list;
        $this->set(compact('goal', 'priority_list'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_collabo');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function edit_collabo()
    {
        $this->request->allowMethod('post', 'put');
        if ($this->Goal->Collaborator->edit($this->request->data)) {
            $this->Pnotify->outSuccess(__d('gl', "コラボレータを保存しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "コラボレータの保存に失敗しました。"));
        }
        $this->redirect($this->referer());
    }

    public function add_key_result($goal_id, $current_kr_id = null)
    {
        $this->request->allowMethod('post');
        $key_result = null;
        try {
            $this->Goal->begin();
            if (!$this->Goal->Collaborator->isCollaborated($goal_id)) {
                throw new RuntimeException(__d('gl', "権限がありません。"));
            }
            $this->Goal->KeyResult->add($this->request->data, $goal_id);
            if ($current_kr_id) {
                if (!$this->Goal->KeyResult->isPermitted($current_kr_id)) {
                    throw new RuntimeException(__d('gl', "権限がありません。"));
                }
                $this->Goal->KeyResult->complete($current_kr_id);
            }
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }

        $this->Goal->commit();
        $this->_flashOpenKrs($goal_id);
        $this->Pnotify->outSuccess(__d('gl', "出したい成果を追加しました。"));
        $this->redirect($this->referer());
    }

    /**
     * @param $kr_id
     */
    public function edit_key_result($kr_id)
    {
        $this->request->allowMethod('post', 'put');
        $kr = null;
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__d('gl', "権限がありません。"));
            }
            if (!$kr = $this->Goal->KeyResult->saveEdit($this->request->data)) {
                throw new RuntimeException(__d('gl', "データの保存に失敗しました。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->_flashOpenKrs($kr['KeyResult']['goal_id']);
        $this->Pnotify->outSuccess(__d('gl', "成果を更新しました。"));
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function complete($kr_id, $with_goal = null)
    {
        $key_result = null;
        $this->request->allowMethod('post');
        try {
            $this->Goal->begin();
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__d('gl', "権限がありません。"));
            }
            $this->Goal->KeyResult->complete($kr_id);
            $key_result = $this->Goal->KeyResult->findById($kr_id);
            //ゴールも一緒に完了にする場合
            if ($with_goal) {
                $goal = $this->Goal->findById($key_result['KeyResult']['goal_id']);
                $this->Goal->complete($goal['Goal']['id']);
                $this->Pnotify->outSuccess(__d('gl', "ゴールを完了にしました。"));
            }
            else {
                $this->Pnotify->outSuccess(__d('gl', "成果を完了にしました。"));
            }
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->commit();
        $this->_flashOpenKrs($key_result['KeyResult']['goal_id']);
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function incomplete($kr_id)
    {
        $this->request->allowMethod('post');
        try {
            $this->Goal->begin();
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__d('gl', "権限がありません。"));
            }
            $this->Goal->KeyResult->incomplete($kr_id);
            $key_result = $this->Goal->KeyResult->findById($kr_id);
            $goal = $this->Goal->findById($key_result['KeyResult']['goal_id']);
            $this->Goal->incomplete($goal['Goal']['id']);
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->commit();
        $this->_flashOpenKrs($key_result['KeyResult']['goal_id']);
        $this->Pnotify->outSuccess(__d('gl', "成果を未完了にしました。"));
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function delete_key_result($kr_id)
    {
        $this->request->allowMethod('post', 'delete');
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__d('gl', "権限がありません。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->KeyResult->id = $kr_id;
        $kr = $this->Goal->KeyResult->read();
        $this->Goal->KeyResult->delete();
        $this->_flashOpenKrs($kr['KeyResult']['goal_id']);
        $this->Pnotify->outSuccess(__d('gl', "成果を削除しました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function delete_collabo($key_result_user_id)
    {
        $this->request->allowMethod('post', 'put');
        $this->Goal->Collaborator->id = $key_result_user_id;
        if (!$this->Goal->Collaborator->exists()) {
            $this->Pnotify->outError(__('gl', "既にコラボレータから抜けている可能性があります。"));
        }
        if (!$this->Goal->Collaborator->isOwner($this->Auth->user('id'))) {
            $this->Pnotify->outError(__('gl', "この操作の権限がありません。"));
        }
        $this->Goal->Collaborator->delete();
        $this->Pnotify->outSuccess(__d('gl', "コラボレータから外れました。"));
        $this->redirect($this->referer());
    }

    /**
     * フォロー、アンフォローの切り換え
     *
     * @param $goal_id
     *
     * @return CakeResponse
     */
    public function ajax_toggle_follow($goal_id)
    {
        $this->_ajaxPreProcess();

        $return = [
            'error' => false,
            'msg'   => null,
            'add'   => true,
        ];

        //存在チェック
        if (!$this->Goal->isBelongCurrentTeam($goal_id)) {
            $return['error'] = true;
            $return['msg'] = __d('gl', "存在しないゴールです。");
            return $this->_ajaxGetResponse($return);
        }

        //既にフォローしているかどうかのチェック
        if ($this->Goal->Follower->isExists($goal_id)) {
            $return['add'] = false;
        }

        if ($return['add']) {
            if ($this->Goal->Follower->addFollower($goal_id)) {
                $return['msg'] = __d('gl', "フォローしました。");
            }
            else {
                $return['error'] = true;
                $return['msg'] = __d('gl', "フォローに失敗しました。");
            }
        }
        else {
            $this->Goal->Follower->deleteFollower($goal_id);
            $return['msg'] = __d('gl', "フォロー解除しました。");
        }

        return $this->_ajaxGetResponse($return);
    }

    function ajax_get_key_results($goal_id, $kr_can_edit = false)
    {
        $this->_ajaxPreProcess();

        $key_results = $this->Goal->KeyResult->getKeyResults($goal_id);
        $incomplete_kr_count = 0;
        foreach ($key_results as $k => $v) {
            if (empty($v['KeyResult']['completed'])) {
                $incomplete_kr_count++;
            }
        }

        $this->set(compact('key_results', 'incomplete_kr_count', 'kr_can_edit', 'goal_id'));
        $response = $this->render('Goal/key_result_items');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_edit_key_result_modal($kr_id)
    {
        $this->_ajaxPreProcess();
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException();
            }
            $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $kr_id]]);
            $key_result['KeyResult']['start_value'] = (double)$key_result['KeyResult']['start_value'];
            $key_result['KeyResult']['current_value'] = (double)$key_result['KeyResult']['current_value'];
            $key_result['KeyResult']['target_value'] = (double)$key_result['KeyResult']['target_value'];
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $goal_id = $key_result['KeyResult']['goal_id'];
        $kr_id = $key_result['KeyResult']['id'];
        $goal = $this->Goal->getGoalMinimum($goal_id);
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;

        $kr_start_date_format = date('Y/m/d',
                                     $key_result['KeyResult']['start_date'] + ($this->Auth->user('timezone') * 60 * 60));

        $kr_end_date_format = date('Y/m/d',
                                   $key_result['KeyResult']['end_date'] + ($this->Auth->user('timezone') * 60 * 60));
        $limit_end_date = date('Y/m/d',
                               $goal['Goal']['end_date'] + ($this->Auth->user('timezone') * 60 * 60));
        $limit_start_date = date('Y/m/d',
                                 $goal['Goal']['start_date'] + ($this->Auth->user('timezone') * 60 * 60));
        $this->set(compact(
                       'goal',
                       'goal_id',
                       'kr_id',
                       'goal_category_list',
                       'priority_list',
                       'kr_priority_list',
                       'kr_value_unit_list',
                       'kr_start_date_format',
                       'kr_end_date_format',
                       'limit_end_date',
                       'limit_start_date'
                   ));
        $this->request->data = $key_result;
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_edit_key_result');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_last_kr_confirm($kr_id)
    {
        $this->_ajaxPreProcess();
        $goal = null;
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException();
            }
            $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $kr_id]]);
            $goal = $this->Goal->getGoalMinimum($key_result['KeyResult']['goal_id']);
            $goal['Goal']['start_value'] = (double)$goal['Goal']['start_value'];
            $goal['Goal']['current_value'] = (double)$goal['Goal']['current_value'];
            $goal['Goal']['target_value'] = (double)$goal['Goal']['target_value'];
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $this->set(compact(
                       'goal',
                       'kr_id'
                   ));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_last_kr_confirm');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    function download_all_goal_csv()
    {
        $this->request->allowMethod('post');
        $this->layout = false;
        $filename = 'all_goal_' . date('YmdHis');

        //見出し
        $th = [
            __d('gl', "Sei"),
            __d('gl', "Mei"),
            __d('gl', "姓"),
            __d('gl', "名"),
            __d('gl', "目的"),
            __d('gl', "ゴールカテゴリ"),
            __d('gl', "ゴール名"),
            __d('gl', "単位"),
            __d('gl', "程度(達成時)"),
            __d('gl', "程度(開始時)"),
            __d('gl', "期限"),
            __d('gl', "開始日"),
            __d('gl', "詳細"),
            __d('gl', "重要度"),
            __d('gl', "認定"),
        ];
        $user_goal = $this->Goal->getAllUserGoal();
        $this->set(compact('filename', 'th', 'td'));

    }

    private function _flashOpenKrs($goal_id)
    {
        $this->Session->setFlash(null, "flash_open_krs", ['goal_id' => $goal_id], 'open_krs');
    }
}
