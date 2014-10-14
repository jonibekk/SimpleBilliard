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
        $this->Security->unlockedActions = array('add_key_result');
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
     * mode なしは目標を決める,2はゴールを定める,3は他の情報を追加
     *
     * @param null $id
     */
    public function add($id = null)
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        //編集権限を確認。もし権限がある場合はデータをセット
        if ($id) {
            $this->request->data['Goal']['id'] = $id;
            try {
                $this->Goal->isPermittedAdmin($id);

            } catch (RuntimeException $e) {
                $this->Pnotify->outError($e->getMessage());
                $this->redirect($this->referer());
            }
        }

        if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data)) {
            if ($this->Goal->add($this->request->data)) {
                if (isset($this->request->params['named']['mode'])) {
                    switch ($this->request->params['named']['mode']) {
                        case 2:
                            $this->Pnotify->outSuccess(__d('gl', "ゴールを保存しました。"));
                            //「ゴールを定める」に進む
                            $this->redirect([$id, 'mode' => 3, '#' => 'AddGoalFormOtherWrap']);
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
                    $this->Pnotify->outSuccess(__d('gl', "ゴールを目的を保存しました。"));
                    //「ゴールを定める」に進む
                    $this->redirect([$this->Goal->id, 'mode' => 2, '#' => 'AddGoalFormKeyResultWrap']);
                }
            }
            else {
                $this->Pnotify->outError(__d('gl', "ゴールの保存に失敗しました。"));
                $this->redirect($this->referer());
            }
        }
        else {
            //新規作成時以外はデータをセット
            if ($id) {
                $this->request->data = $this->Goal->getAddData($id);
            }

        }
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;
        if (isset($this->request->data['KeyResult'][0]) && !empty($this->request->data['KeyResult'][0])) {
            $kr_start_date_format = date('Y/m/d',
                                         $this->request->data['KeyResult'][0]['start_date'] + ($this->Auth->user('timezone') * 60 * 60));
            $kr_end_date_format = date('Y/m/d',
                                       $this->request->data['KeyResult'][0]['end_date'] + ($this->Auth->user('timezone') * 60 * 60));
        }
        else {
            $kr_start_date_format = date('Y/m/d', time() + ($this->Auth->user('timezone') * 60 * 60));
            //TODO 将来的には期間をまたぐ当日+6ヶ月を期限にするが、現状期間末日にする
            //$kr_end_date_format = date('Y/m/d', $this->getEndMonthLocalDateTime());
            $kr_end_date_format = date('Y/m/d', strtotime("- 1 day",
                                                          $this->Goal->Team->getTermEndDate()) + ($this->Auth->user('timezone') * 60 * 60));
        }
        $this->set(compact('goal_category_list', 'priority_list', 'kr_priority_list', 'kr_value_unit_list',
                           'kr_start_date_format', 'kr_end_date_format'));
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

    public function ajax_get_add_key_result_modal($key_result_id)
    {
        $this->_ajaxPreProcess();
        $key_result = null;
        try {
            $this->Goal->isPermittedCollabo($key_result_id);
            $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $key_result_id]]);
            if (empty($key_result)) {
                throw new RuntimeException();
            }
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $goal_id = $key_result['KeyResult']['goal_id'];
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;

        $kr_start_date_format = date('Y/m/d', time() + ($this->Auth->user('timezone') * 60 * 60));

        //期限は現在+2週間にする
        //もしそれがゴールの期限を超える場合はゴールの期限にする
        $end_date = strtotime('+2 weeks', time());
        if ($end_date > $key_result['KeyResult']['end_date']) {
            $end_date = $key_result['KeyResult']['end_date'];
        }
        $kr_end_date_format = date('Y/m/d', $end_date + ($this->Auth->user('timezone') * 60 * 60));
        $limit_end_date = date('Y/m/d',
                               $key_result['KeyResult']['end_date'] + ($this->Auth->user('timezone') * 60 * 60));
        $limit_start_date = date('Y/m/d',
                                 $key_result['KeyResult']['start_date'] + ($this->Auth->user('timezone') * 60 * 60));

        $this->set(compact(
                       'goal_id',
                       'key_result_id',
                       'goal_category_list',
                       'priority_list',
                       'kr_priority_list',
                       'kr_value_unit_list',
                       'kr_start_date_format',
                       'kr_end_date_format',
                       'limit_end_date',
                       'limit_start_date'
                   ));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_add_key_result');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_collabo_change_modal($key_result_id)
    {
        $this->_ajaxPreProcess();
        $skr = $this->Goal->KeyResult->getCollaboModalItem($key_result_id);
        $this->set(compact('skr'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_collabo');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function edit_collabo()
    {
        $this->request->allowMethod('post', 'put');
        if ($this->Goal->KeyResult->KeyResultUser->edit($this->request->data)) {
            $this->Pnotify->outSuccess(__d('gl', "コラボレータを保存しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "コラボレータの保存に失敗しました。"));
        }
        $this->redirect($this->referer());
    }

    public function add_key_result($key_result_id)
    {
        $this->request->allowMethod('post');
        $key_result = null;
        try {
            $this->Goal->isPermittedCollabo($key_result_id);
            $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $key_result_id]]);
            $this->Goal->KeyResult->add($this->request->data, $key_result['KeyResult']['goal_id']);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }

        $this->Pnotify->outSuccess(__d('gl', "基準を追加しました。"));
        $this->redirect($this->referer());
    }

    public function delete_collabo($key_result_user_id)
    {
        $this->request->allowMethod('post', 'put');
        $this->Goal->KeyResult->KeyResultUser->id = $key_result_user_id;
        if (!$this->Goal->KeyResult->KeyResultUser->exists()) {
            $this->Pnotify->outError(__('gl', "既にコラボレータから抜けている可能性があります。"));
        }
        if (!$this->Goal->KeyResult->KeyResultUser->isOwner($this->Auth->user('id'))) {
            $this->Pnotify->outError(__('gl', "この操作の権限がありません。"));
        }
        $this->Goal->KeyResult->KeyResultUser->delete();
        $this->Pnotify->outSuccess(__d('gl', "コラボレータから外れました。"));
        $this->redirect($this->referer());
    }

    /**
     * フォロー、アンフォローの切り換え
     *
     * @param $key_result_id
     *
     * @return CakeResponse
     */
    public function ajax_toggle_follow($key_result_id)
    {
        $this->_ajaxPreProcess();

        $return = [
            'error' => false,
            'msg'   => null,
            'add'   => true,
        ];

        //存在チェック
        if (!$this->Goal->KeyResult->isBelongCurrentTeam($key_result_id)) {
            $return['error'] = true;
            $return['msg'] = __d('gl', "存在しないゴールです。");
            return $this->_ajaxGetResponse($return);
        }

        //既にフォローしているかどうかのチェック
        if ($this->Goal->KeyResult->Follower->isExists($key_result_id)) {
            $return['add'] = false;
        }

        if ($return['add']) {
            if ($this->Goal->KeyResult->Follower->addFollower($key_result_id)) {
                $return['msg'] = __d('gl', "フォローしました。");
            }
            else {
                $return['error'] = true;
                $return['msg'] = __d('gl', "フォローに失敗しました。");
            }
        }
        else {
            $this->Goal->KeyResult->Follower->deleteFollower($key_result_id);
            $return['msg'] = __d('gl', "フォロー解除しました。");
        }

        return $this->_ajaxGetResponse($return);
    }

    function ajax_get_key_results($goal_id)
    {
        $this->_ajaxPreProcess();

        $key_results = $this->Goal->KeyResult->getKeyResults($goal_id);
        $this->set(compact('key_results'));
        $response = $this->render('Goal/key_result_items');
        $html = $response->__toString();
        $result = array(
            'html' => $html
        );
        return $this->_ajaxGetResponse($result);
    }

}
