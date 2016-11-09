<?php
App::uses('AppController', 'Controller');
App::uses('PostShareCircle', 'Model');
App::import('Service', 'GoalService');
App::import('Service', 'KeyResultService');
/** @noinspection PhpUndefinedClassInspection */
App::import('Service', 'KeyResultService');
/** @noinspection PhpUndefinedClassInspection */

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

    /**
     * ゴール一覧画面
     */
    public function index()
    {
        $this->_setViewValOnRightColumn();
    }

    public function create($step = null)
    {
        if ($step !== 'step1') {
            throw new NotFoundException();
        }
        $this->layout = LAYOUT_ONE_COLUMN;

        return $this->render("create");
    }

    public function edit($id)
    {
        try {
            $this->Goal->isPermittedAdmin($id);
        } catch (RuntimeException$e) {
            throw new NotFoundException();
        }

        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        if (!$GoalService->isGoalAfterCurrentTerm($id)) {
            throw new NotFoundException();
        }

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render("edit");
    }

    public function approval($type = null)
    {
        $this->layout = LAYOUT_ONE_COLUMN;

        if (!in_array($type, ['list', 'detail'])) {
            throw new NotFoundException();
        }
        return $this->render("approval");
    }

    /**
     * 旧ゴール作成ぺージ
     * 新ゴールぺージへリダイレクトさせる。およびどこから来たかログる。
     */
    public function add()
    {
        $this->log("■ Old Create Goal Page Access! referer URL: " . $this->referer());
        return $this->redirect(['action' => 'create', 'step1']);
    }

    /**
     * delete method
     *
     * @return void
     */
    public function delete()
    {
        $id = $this->request->params['named']['goal_id'];
        try {
            $this->Goal->isPermittedAdmin($id);
            $this->Goal->isNotExistsEvaluation($id);
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post', 'delete');
        $this->Goal->id = $id;
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_DELETE_GOAL, $id);
        $this->Goal->delete();
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        $this->Goal->ActionResult->releaseGoal($id);
        $this->Pnotify->outSuccess(__("Deleted a goal."));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/after_click:SubHeaderMenuGoal');
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function ajax_get_goal_description_modal()
    {
        $goal_id = Hash::get($this->request->params, 'named.goal_id');
        $this->_ajaxPreProcess();
        $goal = $this->Goal->getGoal($goal_id);
        $my_coaching_users = $this->Goal->User->TeamMember->getMyMembersList($this->my_uid);
        $this->set(compact('goal', 'my_coaching_users'));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_goal_description');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_add_action_modal()
    {
        $goal_id = Hash::get($this->request->params, 'named.goal_id');
        $key_result_id = Hash::get($this->request->params, 'named.key_result_id');
        $this->_ajaxPreProcess();
        try {
            if (!$this->Goal->GoalMember->isCollaborated($goal_id)) {
                throw new RuntimeException();
            }
            if ($key_result_id && !$this->Goal->KeyResult->isPermitted($key_result_id)) {
                throw new RuntimeException();
            }
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $goal = $this->Goal->getGoalMinimum($goal_id);
        $kr_list = [null => '---'] + $this->Goal->KeyResult->getKeyResults($goal_id, 'list');
        $kr_value_unit_list = KeyResult::$UNIT;
        $this->set(compact('goal', 'goal_id', 'kr_list', 'kr_value_unit_list', 'key_result_id'));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_add_action');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_related_kr_list_modal()
    {
        $goalId = Hash::get($this->request->params, 'named.goal_id');
        $userId = Hash::get($this->request->params, 'named.user_id');
        $krs = [];
        if ($goalId && $userId) {
            $krs = $this->Goal->KeyResult->getKeyResultsForEvaluation($goalId);
        }

        $allKrCount = count($krs);
        $myActionKrCount = count(Hash::extract($krs, "{n}[action_result_count>0]"));

        $this->_ajaxPreProcess();

        //htmlレンダリング結果
        $this->set(compact('krs', 'allKrCount', 'myActionKrCount'));
        $response = $this->render('Goal/modal_related_kr_list');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_add_key_result_modal()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $goalId = Hash::get($this->request->params, 'named.goal_id');
        $currentKrId = Hash::get($this->request->params, 'named.key_result_id');
        $this->_ajaxPreProcess();
        try {
            if (!$this->Goal->GoalMember->isCollaborated($goalId)) {
                throw new RuntimeException();
            }
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $goal = $this->Goal->getGoalMinimum($goalId);
        $tkr = $this->Goal->KeyResult->getTkr($goalId);
        $goal = array_merge($goal, $tkr);

        $goalCategoryList = $this->Goal->GoalCategory->getCategoryList();
        $priorityList = $this->Goal->priority_list;
        $krPriorityList = $this->Goal->KeyResult->priority_list;
        $krValueUnitList = $KeyResultService->buildKrUnitsSelectList();

        // ゴールが属している評価期間データ
        $goalTerm = $this->Goal->getGoalTermData($goalId);

        $krStartDateFormat = date('Y/m/d', REQUEST_TIMESTAMP + $goalTerm['timezone'] * HOUR);

        //期限は現在+2週間にする
        //もしそれがゴールの期限を超える場合はゴールの期限にする
        $endDate = strtotime('+2 weeks', REQUEST_TIMESTAMP);
        if ($endDate > $goal['Goal']['end_date']) {
            $endDate = $goal['Goal']['end_date'];
        }
        $krEndDateFormat = date('Y/m/d', $endDate + $goalTerm['timezone'] * HOUR);
        $limitEndDate = date('Y/m/d', $goal['Goal']['end_date'] + $goalTerm['timezone'] * HOUR);
        $limitStartDate = date('Y/m/d', $goal['Goal']['start_date'] + $goalTerm['timezone'] * HOUR);

        $this->set(compact(
            'goal',
            'goalId',
            'goalCategoryList',
            'goalTerm',
            'priorityList',
            'krPriorityList',
            'krValueUnitList',
            'krStartDateFormat',
            'krEndDateFormat',
            'limitEndDate',
            'limitStartDate',
            'currentKrId'
        ));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_add_key_result');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_collabo_change_modal()
    {
        $goal_id = $this->request->params['named']['goal_id'];
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
        $goal_member_id = Hash::get($this->request->params, 'named.goal_member_id');
        $new = $goal_member_id ? false : true;
        $this->request->allowMethod('post', 'put');
        $coach_id = $this->User->TeamMember->getCoachUserIdByMemberUserId(
            $this->Auth->user('id'));

        if (!isset($this->request->data['GoalMember'])) {
            $this->_editCollaboError();
            return $this->redirect($this->referer());
        }

        // コラボを編集した場合は必ずコラボを認定対象外にし、認定ステータスを「Reapplication」にする
        $this->request->data['GoalMember']['approval_status'] = $new ? GoalMember::APPROVAL_STATUS_NEW : GoalMember::APPROVAL_STATUS_REAPPLICATION;
        $this->request->data['GoalMember']['is_target_evaluation'] = false;

        if (!$this->Goal->GoalMember->edit($this->request->data)) {

            $this->_editCollaboError();
            return $this->redirect($this->referer());
        }

        $goalMember = $this->request->data['GoalMember'];
        $goal_member_id = $goal_member_id ? $goal_member_id : $this->Goal->GoalMember->getLastInsertID();
        $goal = $this->Goal->findById($goalMember['goal_id']);
        $goal_leader_id = Hash::get($goal, 'Goal.user_id');

        //success case.
        $this->Pnotify->outSuccess(__("Start to collaborate."));
        //if new
        Cache::delete($this->Goal->GoalMember->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');
        Cache::delete($this->Goal->GoalMember->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        //mixpanel
        if ($new) {
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_COLLABORATE_GOAL, $goalMember['goal_id']);
            // コラボしたのがコーチーの場合は、コーチとしての通知を送るのでゴールリーダーとしての通知は送らない
            if ($goal_leader_id != $coach_id) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_COLLABORATE, $goalMember['goal_id']);
            }
        }
        if ($coach_id && (isset($goalMember['priority']) && $goalMember['priority'] >= '1')
        ) {
            if ($new) {
                //新規の場合
                $this->_sendNotifyToCoach($goal_member_id, NotifySetting::TYPE_COACHEE_COLLABORATE_GOAL);
            } else {
                //更新の場合
                $this->_sendNotifyToCoach($goal_member_id, NotifySetting::TYPE_COACHEE_CHANGE_ROLE);
            }

            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coach_id),
                'user_data');
        }
        return $this->redirect($this->referer());
    }

    function _editCollaboError()
    {
        $this->Pnotify->outError(__("Failed to collaborate."));
    }

    public function add_key_result()
    {
        $goal_id = $this->request->params['named']['goal_id'];
        $current_kr_id = Hash::get($this->request->params, 'named.key_result_id');

        $this->request->allowMethod('post');
        $key_result = null;
        try {
            $this->Goal->begin();
            if (!$this->Goal->GoalMember->isCollaborated($goal_id)) {
                throw new RuntimeException(__("You have no permission."));
            }
            $this->Goal->KeyResult->add($this->request->data, $goal_id);
            $this->Goal->incomplete($goal_id);
            if ($current_kr_id) {
                if (!$this->Goal->KeyResult->isPermitted($current_kr_id)) {
                    throw new RuntimeException(__("You have no permission."));
                }
                $this->Goal->KeyResult->complete($current_kr_id);
                $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_ACHIEVE_KR,
                    $goal_id,
                    $current_kr_id
                );
            }
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }

        $this->Goal->commit();
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_KR, $goal_id,
            $this->Goal->KeyResult->getLastInsertID());
        $this->_flashClickEvent("KRsOpen_" . $goal_id);
        $this->Pnotify->outSuccess(__("Added a key result."));
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/after_click:SubHeaderMenuGoal');
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function edit_key_result()
    {
        $kr_id = $this->request->params['named']['key_result_id'];
        $this->request->allowMethod('post', 'put');
        $kr = null;
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__("You have no permission."));
            }
            if ($this->Goal->KeyResult->isCompleted($kr_id)) {
                throw new RuntimeException(__("You can't edit achieved KR."));
            }
            if (!$kr = $this->Goal->KeyResult->saveEdit($this->request->data)) {
                throw new RuntimeException(__("Failed to save KR."));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->_flashClickEvent("KRsOpen_" . $kr['KeyResult']['goal_id']);

        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_UPDATE_KR, $kr['KeyResult']['goal_id'], $kr_id);
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');

        $this->Pnotify->outSuccess(__("Updated KR."));
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/after_click:SubHeaderMenuGoal');
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function complete_kr($with_goal = null)
    {
        $kr_id = $this->request->params['named']['key_result_id'];
        $key_result = null;
        $this->request->allowMethod('post');
        try {
            $this->Goal->begin();
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__("You have no permission."));
            }
            $this->Goal->KeyResult->complete($kr_id);
            $key_result = $this->Goal->KeyResult->findById($kr_id);
            //KR完了の投稿
            $this->Post->addGoalPost(Post::TYPE_KR_COMPLETE, $key_result['KeyResult']['goal_id'], null, false, $kr_id);
            //ゴールも一緒に完了にする場合
            if ($with_goal) {
                $goal = $this->Goal->findById($key_result['KeyResult']['goal_id']);
                //ゴール完了の投稿
                $this->Post->addGoalPost(Post::TYPE_GOAL_COMPLETE, $key_result['KeyResult']['goal_id'], null);
                $this->Goal->complete($goal['Goal']['id']);
                $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_ACHIEVE_GOAL,
                    $key_result['KeyResult']['goal_id'],
                    $kr_id);
                $this->Pnotify->outSuccess(__("Completed a goal."));
            } else {
                $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_ACHIEVE_KR,
                    $key_result['KeyResult']['goal_id'],
                    $kr_id);
                $this->Pnotify->outSuccess(__("Completed a key result."));
            }
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->commit();

        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        // pusherに通知
        $socket_id = Hash::get($this->request->data, 'socket_id');
        $goal = viaIsSet($goal);
        if (!$goal) {
            $goal = $goal = $this->Goal->findById($key_result['KeyResult']['goal_id']);
        }
        $channelName = "goal_" . $goal['Goal']['id'];
        $this->NotifyBiz->push($socket_id, $channelName);

        $this->_flashClickEvent("KRsOpen_" . $key_result['KeyResult']['goal_id']);

        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/after_click:SubHeaderMenuGoal');
        } else {
            return $this->redirect($this->referer());
        }
        /** @noinspection PhpVoidFunctionResultUsedInspection */
    }

    public function incomplete_kr()
    {
        $kr_id = $this->request->params['named']['key_result_id'];
        $this->request->allowMethod('post');
        try {
            $this->Goal->begin();
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__("You have no permission."));
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
        $this->_flashClickEvent("KRsOpen_" . $key_result['KeyResult']['goal_id']);
        $this->Pnotify->outSuccess(__("Made a key result uncompleted."));
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/after_click:SubHeaderMenuGoal');
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function delete_key_result()
    {
        $kr_id = $this->request->params['named']['key_result_id'];
        $this->request->allowMethod('post', 'delete');
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException(__("You have no permission."));
            }
            if ($this->Goal->KeyResult->isCompleted($kr_id)) {
                throw new RuntimeException(__("You can't delete achieved KR."));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->KeyResult->id = $kr_id;
        $kr = $this->Goal->KeyResult->read();
        $this->Goal->KeyResult->delete();
        //関連アクションの紐付け解除
        $this->Goal->ActionResult->releaseKr($kr_id);

        $this->_flashClickEvent("KRsOpen_" . $kr['KeyResult']['goal_id']);
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_DELETE_KR, $kr['KeyResult']['goal_id'], $kr_id);
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');

        $this->Pnotify->outSuccess(__("Deleted a key result."));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/after_click:SubHeaderMenuGoal');
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function delete_action()
    {
        $ar_id = $this->request->params['named']['action_result_id'];
        $this->request->allowMethod('post', 'delete');
        try {
            if (!$action = $this->Goal->ActionResult->find('first',
                ['conditions' => ['ActionResult.id' => $ar_id],])
            ) {
                throw new RuntimeException(__("There is no action."));
            }
            if (!$this->Team->TeamMember->isAdmin() && !$this->Goal->GoalMember->isCollaborated($action['ActionResult']['goal_id'])) {
                throw new RuntimeException(__("You have no permission."));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->ActionResult->id = $ar_id;
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_DELETE_ACTION,
            $action['ActionResult']['goal_id'],
            $action['ActionResult']['key_result_id'],
            $ar_id);
        $this->Goal->ActionResult->delete();
        $this->Goal->ActionResult->ActionResultFile->AttachedFile->deleteAllRelatedFiles($ar_id,
            AttachedFile::TYPE_MODEL_ACTION_RESULT);
        if (isset($action['ActionResult']['goal_id']) && !empty($action['ActionResult']['goal_id'])) {
            $this->_flashClickEvent("ActionListOpen_" . $action['ActionResult']['goal_id']);
        }

        $this->Pnotify->outSuccess(__("Deleted an action."));
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function delete_collabo()
    {
        $goalMemberId = $this->request->params['named']['goal_member_id'];
        $this->request->allowMethod('post', 'put');
        $this->Goal->GoalMember->id = $goalMemberId;
        if (!$this->Goal->GoalMember->exists()) {
            $this->Pnotify->outError(__("He/She might quit collaborating."));
        }
        if (!$this->Goal->GoalMember->isOwner($this->Auth->user('id'))) {
            $this->Pnotify->outError(__("You have no right to operate it."));
        }
        $goalMember = $this->Goal->GoalMember->findById($goalMemberId);
        if (!empty($goalMember)) {
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_WITHDRAW_COLLABORATE,
                $goalMember['GoalMember']['goal_id']);
        }
        $this->Goal->GoalMember->delete();
        $this->Pnotify->outSuccess(__("Quitted a collaborator."));
        Cache::delete($this->Goal->GoalMember->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');
        Cache::delete($this->Goal->GoalMember->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        $this->redirect($this->referer());
    }

    /**
     * フォロー、アンフォローの切り換え
     *
     * @return CakeResponse
     */
    public function ajax_toggle_follow()
    {
        $goal_id = $this->request->params['named']['goal_id'];
        $this->_ajaxPreProcess();

        $return = [
            'error' => false,
            'msg'   => null,
            'add'   => true,
        ];

        //存在チェック
        if (!$this->Goal->isBelongCurrentTeam($goal_id, $this->Session->read('current_team_id'))) {
            $return['error'] = true;
            $return['msg'] = __("The goal doesn't exist.");
            return $this->_ajaxGetResponse($return);
        }

        //既にフォローしているかどうかのチェック
        if ($this->Goal->Follower->isExists($goal_id)) {
            $return['add'] = false;
        }

        if ($return['add']) {
            $this->Goal->Follower->addFollower($goal_id);
            $return['msg'] = __("Start to follow.");
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goal_id);
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_FOLLOW, $goal_id);
        } else {
            $this->Goal->Follower->deleteFollower($goal_id);
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goal_id);
            $return['msg'] = __("Stop following.");
        }

        return $this->_ajaxGetResponse($return);
    }

    /**
     * ゴールに紐づくキーリザルト一覧を返す
     *
     * @param bool $kr_can_edit
     *
     * @return CakeResponse
     */
    function ajax_get_key_results($kr_can_edit = false)
    {
        $this->_ajaxPreProcess();

        $goal_id = $this->request->params['named']['goal_id'];
        //除外する件数
        $extract_count = 0;
        if (isset($this->request->params['named']['extract_count'])) {
            $extract_count = $this->request->params['named']['extract_count'];
        }

        // テンプレを切り替える場合に指定する
        $view = isset($this->request->params['named']['view']) ? $this->request->params['named']['view'] : null;

        // ページ番号
        // 指定しない場合は全件を返す
        $page = 1;
        $limit = null;
        if (isset($this->request->params['named']['page'])) {
            $page = $this->request->params['named']['page'];
            $limit = GOAL_PAGE_KR_NUMBER;
        }

        $is_collaborated = $this->Goal->GoalMember->isCollaborated($goal_id);
        $display_action_count = MY_PAGE_ACTION_NUMBER;
        if ($is_collaborated) {
            $display_action_count--;
        }
        $this->set(compact('is_collaborated', 'display_action_count'));

        $key_results = $this->Goal->KeyResult->getKeyResults($goal_id, 'all', false, [
            'page'  => $page,
            'limit' => $limit,
        ], true, $display_action_count);
        if (!empty($key_results) && $extract_count > 0) {
            foreach ($key_results as $k => $v) {
                unset($key_results[$k]);
                if (--$extract_count === 0) {
                    break;
                }
            }
        }

        // 未完了のキーリザルト数
        $incomplete_kr_count = $this->Goal->KeyResult->getIncompleteKrCount($goal_id);

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($goal_id);
        $current_term = $this->Goal->Team->EvaluateTerm->getCurrentTermData();
        //ゴールが今期の場合はアクション追加可能
        $can_add_action = $goal_term['end_date'] === $current_term['end_date'] ? true : false;
        $this->set(compact('key_results', 'incomplete_kr_count', 'kr_can_edit', 'goal_id', 'goal_term',
            'can_add_action'));

        $response = null;
        switch ($view) {
            case "key_results":
                $response = $this->render('Goal/key_results');
                break;
            default:
                $response = $this->render('Goal/key_result_items');
                break;
        }

        $html = $response->__toString();
        $result = array(
            'html'          => $html,
            'count'         => count($key_results),
            'page_item_num' => GOAL_PAGE_KR_NUMBER,
        );
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_edit_key_result_modal()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        $kr_id = $this->request->params['named']['key_result_id'];
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
        $kr_value_unit_list = $KeyResultService->buildKrUnitsSelectList();

        // 認定可能フラグ追加
        $is_approvable = false;
        $goal_leader_id = $this->Goal->GoalMember->getGoalLeaderId($goal_id);
        if ($goal_leader_id) {
            $is_approvable = $GoalMemberService->isApprovableGoalMember($goal_leader_id);
        }

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($goal_id);

        $kr_start_date_format = date('Y/m/d', $key_result['KeyResult']['start_date'] + $goal_term['timezone'] * HOUR);
        $kr_end_date_format = date('Y/m/d', $key_result['KeyResult']['end_date'] + $goal_term['timezone'] * HOUR);
        $limit_end_date = date('Y/m/d', $goal['Goal']['end_date'] + $goal_term['timezone'] * HOUR);
        $limit_start_date = date('Y/m/d', $goal['Goal']['start_date'] + $goal_term['timezone'] * HOUR);
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
            'limit_start_date',
            'goal_term',
            'is_approvable'
        ));
        $this->request->data = $key_result;
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_edit_key_result');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_last_kr_confirm()
    {
        $kr_id = $this->request->params['named']['key_result_id'];
        $this->_ajaxPreProcess();
        $goal = null;
        try {
            if (!$this->Goal->KeyResult->isPermitted($kr_id)) {
                throw new RuntimeException();
            }
            $key_result = $this->Goal->KeyResult->find('first', ['conditions' => ['id' => $kr_id]]);
            $goal = $this->Goal->getGoalMinimum($key_result['KeyResult']['goal_id']);
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

    public function ajax_get_kr_list()
    {
        $goal_id = $this->request->params['named']['goal_id'];
        $this->_ajaxPreProcess();
        $kr_list = [];
        if ($goal_id) {
            $kr_list = $this->Goal->KeyResult->getKeyResults($goal_id, "list", true);
        }
        return $this->_ajaxGetResponse($kr_list);
    }

    /**
     * ゴールのメンバー一覧を取得
     *
     * @return CakeResponse
     */
    public function ajax_get_members()
    {
        $this->_ajaxPreProcess();
        $goal_id = $this->request->params['named']['goal_id'];
        $page = $this->request->params['named']['page'];
        // メンバー一覧
        $members = $this->Goal->GoalMember->getGoalMemberByGoalId($goal_id, [
            'limit' => GOAL_PAGE_MEMBER_NUMBER,
            'page'  => $page,
        ]);
        $this->set('members', $members);
        // HTML出力
        $response = $this->render('Goal/members');
        $html = $response->__toString();
        return $this->_ajaxGetResponse([
            'html'          => $html,
            'count'         => count($members),
            'page_item_num' => GOAL_PAGE_MEMBER_NUMBER,
        ]);
    }

    public function ajax_get_edit_action_modal()
    {
        $ar_id = $this->request->params['named']['action_result_id'];
        $this->_ajaxPreProcess();
        try {
            if (!$this->Goal->ActionResult->isOwner($this->Auth->user('id'), $ar_id)) {
                throw new RuntimeException();
            }
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $action = $this->Goal->ActionResult->find('first', ['conditions' => ['ActionResult.id' => $ar_id]]);
        $this->request->data = $action;
        $kr_list = $this->Goal->KeyResult->getKeyResults($action['ActionResult']['goal_id'], 'list');
        $this->set(compact('kr_list'));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_edit_action_result');
        $html = $response->__toString();
        return $this->_ajaxGetResponse($html);
    }

    /**
     * ゴールのフォロワー一覧を取得
     *
     * @return CakeResponse
     */
    public function ajax_get_followers()
    {
        $this->_ajaxPreProcess();
        $goal_id = $this->request->params['named']['goal_id'];
        $page = $this->request->params['named']['page'];

        // フォロワー一覧
        $followers = $this->Goal->Follower->getFollowerByGoalId($goal_id, [
            'limit'      => GOAL_PAGE_FOLLOWER_NUMBER,
            'page'       => $page,
            'with_group' => true,
        ]);
        $this->set('followers', $followers);

        // HTML出力
        $response = $this->render('Goal/followers');
        $html = $response->__toString();
        return $this->_ajaxGetResponse([
            'html'          => $html,
            'count'         => count($followers),
            'page_item_num' => GOAL_PAGE_FOLLOWER_NUMBER,
        ]);
    }

    /**
     * アクション新規登録
     *
     * @return CakeResponse
     */
    public function add_action()
    {
        $goal_id = Hash::get($this->request->params, 'named.goal_id');
        $key_result_id = Hash::get($this->request->params, 'named.key_result_id');
        try {
            if (!$this->Goal->GoalMember->isCollaborated($goal_id)) {
                throw new RuntimeException(__("This action can't be edited."));
            }
            if ($key_result_id && !$this->Goal->KeyResult->isPermitted($key_result_id)) {
                throw new RuntimeException(__("This action can't be edited."));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->request->data['ActionResult']['goal_id'] = $goal_id;
        $this->request->data['ActionResult']['key_result_id'] = $key_result_id;
        $kr_list = [null => '---'] + $this->Goal->KeyResult->getKeyResults($goal_id, 'list');
        $this->set(compact('kr_list', 'key_result_id'));

        $this->_setViewValOnRightColumn();
        $this->set('common_form_type', 'action');
        $this->set('common_form_only_tab', 'action');
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->render('edit_action');
    }

    /**
     * アクションの編集
     */
    public function edit_action()
    {
        $ar_id = $this->request->params['named']['action_result_id'];

        if (!$this->Goal->ActionResult->isOwner($this->Auth->user('id'), $ar_id)) {
            $this->Pnotify->outError(__("This action can't be edited."));
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }

        // フォームが submit された時
        if ($this->request->is('put')) {
            $this->request->data['ActionResult']['id'] = $ar_id;
            if (!$this->Goal->ActionResult->actionEdit($this->request->data)) {
                $this->Pnotify->outError(__("Failed to save data."));
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect($this->referer());
            }
            $this->Pnotify->outSuccess(__("Edited the action."));
            $action = $this->Goal->ActionResult->find('first',
                ['conditions' => ['ActionResult.id' => $ar_id]]);
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_UPDATE_ACTION,
                $action['ActionResult']['goal_id'],
                $action['ActionResult']['key_result_id'],
                $ar_id);
            if (isset($action['ActionResult']['goal_id']) && !empty($action['ActionResult']['goal_id'])) {
                $this->_flashClickEvent("ActionListOpen_" . $action['ActionResult']['goal_id']);
            }

            /** @noinspection PhpVoidFunctionResultUsedInspection */
            $url = $this->referer();
            $post = $this->Goal->Post->getByActionResultId($ar_id);
            if ($post) {
                $url = [
                    'controller' => 'posts',
                    'action'     => 'feed',
                    'post_id'    => $post['Post']['id']
                ];
            }
            return $this->redirect($url);
        }

        // 編集フォーム表示
        $row = $this->Goal->ActionResult->getWithAttachedFiles($ar_id);
        $this->request->data = $row;
        $this->_setViewValOnRightColumn();
        $this->set('common_form_type', 'action');
        $this->set('common_form_mode', 'edit');
        $this->layout = LAYOUT_ONE_COLUMN;
    }

    function download_all_goal_csv()
    {
        $this->request->allowMethod('post');
        $this->layout = false;
        $filename = 'all_goal_' . date('YmdHis');

        //見出し
        $th = [
            __("Member Number"),
            __("Last Name"),
            __("First Name"),
            __("LastName"),
            __("FirstName"),
            __("Member to be Evaluated"),
            __("Approval Status"),
            __("Purpose"),
            __("Goal Category"),
            __("Goal Owner Type"),
            __("Goal Name"),
            __("Unit"),
            __("Measurement(Final)"),
            __("Measurement(Initial)"),
            __("Due Date"),
            __("Start Date"),
            __("Desctiption"),
            __("Weight")
        ];
        $user_goals = $this->Goal->getAllUserGoal();

        $this->Goal->KeyResult->_setUnitName();
        $td = [];
        foreach ($user_goals as $ug_k => $ug_v) {
            $common_record = [];
            $common_record['member_no'] = $ug_v['TeamMember']['0']['member_no'];
            $common_record['last_name'] = $ug_v['User']['last_name'];
            $common_record['first_name'] = $ug_v['User']['first_name'];
            $common_record['local_last_name'] = isset($ug_v['LocalName'][0]['last_name']) ? $ug_v['LocalName'][0]['last_name'] : null;
            $common_record['local_first_name'] = isset($ug_v['LocalName'][0]['first_name']) ? $ug_v['LocalName'][0]['first_name'] : null;
            $common_record['evaluation_enable_flg'] = $ug_v['TeamMember']['0']['evaluation_enable_flg'] ? 'ON' : 'OFF';
            $common_record['valued'] = null;
            $common_record['purpose'] = null;
            $common_record['category'] = null;
            $common_record['collabo_type'] = null;
            $common_record['goal'] = null;
            $common_record['value_unit'] = null;
            $common_record['target_value'] = null;
            $common_record['start_value'] = null;
            $common_record['end_date'] = null;
            $common_record['start_date'] = null;
            $common_record['description'] = null;
            $common_record['priority'] = null;
            if (!empty($ug_v['GoalMember'])) {
                foreach ($ug_v['GoalMember'] as $c_v) {
                    $approval_status = null;
                    switch ($c_v['approval_status']) {
                        case GoalMember::APPROVAL_STATUS_NEW:
                            $approval_status = __("Pending approval");
                            break;
                        case GoalMember::APPROVAL_STATUS_REAPPLICATION:
                            $approval_status = __("Evaluable");
                            break;
                        case GoalMember::APPROVAL_STATUS_DONE:
                            $approval_status = __("Not Evaluable");
                            break;
                        case GoalMember::APPROVAL_STATUS_WITHDRAWN:
                            $approval_status = __("Pending modification");
                            break;
                    }
                    $record = $common_record;
                    if (!empty($c_v['Goal'])) {
                        // ゴールが属している評価期間データ
                        $goal_term = $this->Goal->getGoalTermData($c_v['Goal']['id']);

                        $record['valued'] = $approval_status;
                        $record['category'] = isset($c_v['Goal']['GoalCategory']['name']) ? $c_v['Goal']['GoalCategory']['name'] : null;
                        $record['collabo_type'] = ($c_v['type'] == GoalMember::TYPE_OWNER) ?
                            __("L") : __("C");
                        $record['goal'] = $c_v['Goal']['name'];
                        $record['end_date'] = date("Y/m/d", $c_v['Goal']['end_date'] + $goal_term['timezone'] * HOUR);
                        $record['start_date'] = date("Y/m/d",
                            $c_v['Goal']['start_date'] + $goal_term['timezone'] * HOUR);
                        $record['description'] = $c_v['Goal']['description'];
                        $record['priority'] = $c_v['priority'];

                        $td[] = $record;
                    }
                }
            } else {
                $td[] = $common_record;
            }
        }

        $this->set(compact('filename', 'th', 'td'));
        $this->_setResponseCsv($filename);
    }

    /**
     * 完了アクション追加
     * TODO 今後様々なバリエーションのアクションが追加されるが、全てこのfunctionで処理する
     */
    public function add_completed_action()
    {
        if (!$goal_id = isset($this->request->params['named']['goal_id']) ? $this->request->params['named']['goal_id'] : null) {
            $goal_id = isset($this->request->data['ActionResult']['goal_id']) ? $this->request->data['ActionResult']['goal_id'] : null;
        }
        if (!$goal_id) {
            $this->Pnotify->outError(__("Failed to add an action."));
            $this->redirect($this->referer());
        }

        $this->request->allowMethod('post');
        $file_ids = $this->request->data('file_id');
        try {
            $this->Goal->begin();
            if (!$this->Goal->GoalMember->isCollaborated($goal_id)) {
                throw new RuntimeException(__("You have no permission."));
            }
            $share = isset($this->request->data['ActionResult']['share']) ? $this->request->data['ActionResult']['share'] : null;
            //アクション追加,投稿
            if (!$this->Goal->ActionResult->addCompletedAction($this->request->data, $goal_id)
                || !$this->Goal->Post->addGoalPost(Post::TYPE_ACTION, $goal_id, $this->Auth->user('id'), false,
                    $this->Goal->ActionResult->getLastInsertID(), $share,
                    PostShareCircle::SHARE_TYPE_ONLY_NOTIFY)
                || !$this->Goal->Post->PostFile->AttachedFile->saveRelatedFiles($this->Goal->ActionResult->getLastInsertID(),
                    AttachedFile::TYPE_MODEL_ACTION_RESULT,
                    $file_ids)
            ) {
                throw new RuntimeException(__("Failed to add an action."));
            }
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            if ($action_result_id = $this->Goal->ActionResult->getLastInsertID()) {
                $this->Goal->Post->PostFile->AttachedFile->deleteAllRelatedFiles($action_result_id,
                    AttachedFile::TYPE_MODEL_ACTION_RESULT);
            }
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->Goal->commit();

        // 添付ファイルが存在する場合は一時データを削除
        if (is_array($file_ids)) {
            foreach ($file_ids as $hash) {
                $this->GlRedis->delPreUploadedFile($this->current_team_id, $this->my_uid, $hash);
            }
        }

        // pusherに通知
        $socket_id = Hash::get($this->request->data, 'socket_id');
        $channelName = "goal_" . $goal_id;
        $this->NotifyBiz->push($socket_id, $channelName);

        $kr_id = isset($this->request->data['ActionResult']['key_result_id']) ? $this->request->data['ActionResult']['key_result_id'] : null;
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_ACTION, $goal_id, $kr_id,
            $this->Goal->ActionResult->getLastInsertID());
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
            $this->Goal->ActionResult->getLastInsertID());

        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');

        // push
        $this->Pnotify->outSuccess(__("Added an action."));
        //セットアップガイドステータスの更新
        $this->updateSetupStatusIfNotCompleted();

        $post = $this->Goal->Post->getByActionResultId($this->Goal->ActionResult->getLastInsertID());
        $url = $post ? [
            'controller' => 'posts',
            'action'     => 'feed',
            'post_id'    => $post['Post']['id']
        ] : $this->referer();
        return $this->redirect($url);

    }

    public function ajax_get_new_action_form()
    {
        $goal_id = $this->request->params['named']['goal_id'];
        $result = [
            'error' => true,
            'msg'   => __("An error has occurred."),
            'html'  => null
        ];
        $this->_ajaxPreProcess();
        if (isset($this->request->params['named']['ar_count'])
            && $this->Goal->isBelongCurrentTeam($goal_id, $this->Session->read('current_team_id'))
        ) {
            $this->set('ar_count', $this->request->params['named']['ar_count']);
            $this->set(compact('goal_id'));
            $response = $this->render('Goal/add_new_action_form');
            $html = $response->__toString();
            $result['html'] = $html;
            $result['error'] = false;
            $result['msg'] = null;
        }
        return $this->_ajaxGetResponse($result);
    }

    public function ajax_get_my_goals()
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $param_named = $this->request->params['named'];
        $this->_ajaxPreProcess();
        if (isset($param_named['page']) && !empty($param_named['page'])) {
            $page_num = $param_named['page'];
        } else {
            $page_num = 1;
        }

        $type = Hash::get($param_named, 'type');
        if (!$type) {
            return;
        }

        //今期、来期のゴールを取得する
        $start_date = $this->Team->EvaluateTerm->getCurrentTermData()['start_date'];
        $end_date = $this->Team->EvaluateTerm->getCurrentTermData()['end_date'];

        if ($type === 'leader') {
            $goals = $this->Goal->getMyGoals(MY_GOALS_DISPLAY_NUMBER, $page_num, 'all', null, $start_date, $end_date);
        } elseif ($type === 'collabo') {
            $goals = $this->Goal->getMyCollaboGoals(MY_COLLABO_GOALS_DISPLAY_NUMBER, $page_num, 'all', null,
                $start_date, $end_date);
        } elseif ($type === 'follow') {
            $goals = $this->Goal->getMyFollowedGoals(MY_FOLLOW_GOALS_DISPLAY_NUMBER, $page_num, 'all', null,
                $start_date, $end_date);
        } elseif ($type === 'my_prev') {
            $goals = $this->Goal->getMyPreviousGoals(MY_PREVIOUS_GOALS_DISPLAY_NUMBER, $page_num);
        } else {
            $goals = [];
        }
        $goals = $GoalService->processGoals($goals);
        $current_term = $this->Goal->Team->EvaluateTerm->getCurrentTermData();
        $this->set(compact('goals', 'type', 'current_term'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('Goal/my_goal_area_items');
        $html = $response->__toString();
        $result = array(
            'html'  => $html,
            'count' => count($goals)
        );
        return $this->_ajaxGetResponse($result);
    }

    function _setGoalAddViewVals()
    {
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;
        $current_term = $this->Team->EvaluateTerm->getCurrentTermData();
        $next_term = $this->Team->EvaluateTerm->getNextTermData();
        $current_term_start_date_format = date('Y/m/d', $current_term['start_date'] + $current_term['timezone'] * HOUR);
        $current_term_end_date_format = date('Y/m/d', $current_term['end_date'] + $current_term['timezone'] * HOUR);
        $next_term_start_date_format = date('Y/m/d', $next_term['start_date'] + $next_term['timezone'] * HOUR);
        $next_term_end_date_format = date('Y/m/d', $next_term['end_date'] + $next_term['timezone'] * HOUR);
        $today_format = date('Y/m/d', REQUEST_TIMESTAMP + $current_term['timezone'] * HOUR);
        $is_next_term_goal = false;

        // ゴール編集時
        if (isset($this->request->data['Goal']) && !empty($this->request->data['Goal'])) {
            // ゴールが属している評価期間データ
            $goal_term = $this->Goal->getGoalTermData($this->request->data['Goal']['id']);
            $goal_start_date_format =
                date('Y/m/d', $this->request->data['Goal']['start_date'] + $goal_term['timezone'] * HOUR);
            $goal_end_date_format =
                date('Y/m/d', $this->request->data['Goal']['end_date'] + $goal_term['timezone'] * HOUR);

            // ゴールが来期のものかチェック
            if ($next_term['start_date'] <= $this->request->data['Goal']['end_date'] &&
                $this->request->data['Goal']['end_date'] <= $next_term['end_date']
            ) {
                $this->request->data['Goal']['term_type'] = 'next';
                $is_next_term_goal = true;
            }
        } // ゴール新規登録時
        else {
            $goal_start_date_format = $today_format;
            $goal_end_date_format = $current_term_end_date_format;
        }
        $this->set(compact('goal_category_list',
            'priority_list',
            'kr_priority_list',
            'kr_value_unit_list',
            'goal_start_date_format',
            'goal_end_date_format',
            'current_term_start_date_format',
            'current_term_end_date_format',
            'next_term_start_date_format',
            'next_term_end_date_format',
            'today_format',
            'current_term',
            'next_term',
            'is_next_term_goal'
        ));
    }

    /**
     *
     */
    function _getSearchVal()
    {
        $options = $this->Goal->getSearchOptions();
        $res = [];
        foreach (array_keys($options) as $type) {
            //URLパラメータ取得
            $res[$type][0] = Hash::get($this->request->params, "named.$type");
            //パラメータチェック
            if (!in_array($res[$type][0], array_keys($options[$type]))) {
                $res[$type] = null;
            }
            //表示名取得
            if (Hash::get($res, $type)) {
                $res[$type][1] = $options[$type][$res[$type][0]];
            } ///デフォルト表示名取得
            else {
                $res[$type][1] = reset($options[$type]);
            }
        }
        return $res;
    }

    function _getSearchUrl($search_option)
    {
        $res = ['controller' => 'goals', 'action' => 'index'];
        foreach ($search_option as $key => $val) {
            if (Hash::get($val, '0')) {
                $res[$key] = $val[0];
            }
        }
        return $res;
    }

    /**
     * フォロワー一覧
     *
     * @return CakeResponse
     */
    function view_followers()
    {
        $goal_id = Hash::get($this->request->params, "named.goal_id");
        if (!$goal_id || !$this->_setGoalPageHeaderInfo($goal_id)) {
            // ゴールが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $followers = $this->Goal->Follower->getFollowerByGoalId($goal_id, [
            'limit'      => GOAL_PAGE_FOLLOWER_NUMBER,
            'with_group' => true,
        ]);
        $this->set('followers', $followers);
        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    /**
     * メンバー一覧
     *
     * @return CakeResponse
     */
    function view_members()
    {
        $goal_id = Hash::get($this->request->params, "named.goal_id");
        if (!$goal_id || !$this->_setGoalPageHeaderInfo($goal_id)) {
            // ゴールが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $members = $this->Goal->GoalMember->getGoalMemberByGoalId($goal_id, [
            'limit' => GOAL_PAGE_MEMBER_NUMBER,
        ]);
        $this->set('members', $members);
        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    /**
     * キーリザルト一覧
     *
     * @return CakeResponse
     */
    function view_krs()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $goal_id = Hash::get($this->request->params, "named.goal_id");
        if (!$goal_id || !$this->_setGoalPageHeaderInfo($goal_id)) {
            // ゴールが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        //コラボってる？
        $is_collaborated = $this->Goal->GoalMember->isCollaborated($goal_id);
        $display_action_count = MY_PAGE_ACTION_NUMBER;
        if ($is_collaborated) {
            $display_action_count--;
        }
        $this->set(compact('is_collaborated', 'display_action_count'));
        $key_results = $this->Goal->KeyResult->getKeyResults($goal_id, 'all', false, [
            'page'  => 1,
            'limit' => GOAL_PAGE_KR_NUMBER,
        ], true, $display_action_count);
        $key_results = $KeyResultService->processKeyResults($key_results, 'KeyResult', '/');
        $this->set('key_results', $key_results);
        // 未完了のキーリザルト数
        $incomplete_kr_count = $this->Goal->KeyResult->getIncompleteKrCount($goal_id);
        $this->set('incomplete_kr_count', $incomplete_kr_count);

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($goal_id);
        $this->set('goal_term', $goal_term);

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    function view_actions()
    {
        $goal_id = Hash::get($this->request->params, "named.goal_id");
        if (!$goal_id || !$this->_setGoalPageHeaderInfo($goal_id)) {
            // ゴールが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $page_type = Hash::get($this->request->params, "named.page_type");
        if (!in_array($page_type, ['list', 'image'])) {
            $this->Pnotify->outError(__("Invalid screen transition."));
            $this->redirect($this->referer());
        }
        $key_result_id = Hash::get($this->request->params, 'named.key_result_id');
        $params = [
            'type'          => Post::TYPE_ACTION,
            'goal_id'       => $goal_id,
            'key_result_id' => $key_result_id,
        ];
        $posts = [];
        switch ($page_type) {
            case 'list':
                $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null, $params);
                break;
            case 'image':
                $posts = $this->Post->get(1, MY_PAGE_CUBE_ACTION_IMG_NUMBER, null, null, $params);
                break;
        }
        $kr_select_options = $this->Goal->KeyResult->getKrNameList($goal_id, true, true);
        $goal_base_url = Router::url([
            'controller' => 'goals',
            'action'     => 'view_actions',
            'goal_id'    => $goal_id,
            'page_type'  => $page_type
        ]);
        $this->set('long_text', false);
        $this->set(compact('key_result_id', 'goal_id', 'posts', 'kr_select_options', 'goal_base_url'));

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    function view_info()
    {
        $goalId = Hash::get($this->request->params, "named.goal_id");
        if (!$goalId || !$this->_setGoalPageHeaderInfo($goalId)) {
            // ゴールが存在しない
            $this->Pnotify->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        // ゴールが属している評価期間データ
        $goalTerm = $this->Goal->getGoalTermData($goalId);
        $goalLabels = Hash::extract($this->Goal->GoalLabel->findByGoalId($goalId), '{n}.Label');

        $this->set(compact('goalTerm', 'goalLabels'));

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    /**
     * ゴールページの上部コンテンツの表示に必要なView変数をセット
     *
     * @param $goal_id
     *
     * @return bool
     */
    function _setGoalPageHeaderInfo($goal_id)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $goal = $this->Goal->getGoal($goal_id);
        if (!isset($goal['Goal']['id'])) {
            // ゴールが存在しない
            return false;
        }
        // 進捗情報を追加
        $goal['Goal']['progress'] = $GoalService->getProgress($goal['KeyResult']);
        $this->set('goal', $goal);

        $this->set('item_created', isset($goal['Goal']['created']) ? $goal['Goal']['created'] : null);

        // アクション数
        $action_count = $this->Goal->ActionResult->getCountByGoalId($goal_id);
        $this->set('action_count', $action_count);

        // メンバー数
        $member_count = count($goal['Leader']) + count($goal['GoalMember']);
        $this->set('member_count', $member_count);

        // フォロワー数
        $follower_count = count($goal['Follower']);
        $this->set('follower_count', $follower_count);

        // 閲覧者がゴールのリーダーかを判別
        $is_leader = false;
        foreach ($goal['Leader'] as $v) {
            if ($this->Auth->user('id') == $v['User']['id']) {
                $is_leader = true;
                break;
            }
        }
        $this->set('is_leader', $is_leader);

        // 閲覧者がゴールのコラボレーターかを判別
        $is_goal_member = false;
        foreach ($goal['GoalMember'] as $v) {
            if ($this->Auth->user('id') == $v['User']['id']) {
                $is_goal_member = true;
                break;
            }
        }
        $this->set('is_goal_member', $is_goal_member);

        // 閲覧者がコーチしているゴールかを判別
        $is_coaching_goal = false;
        $coaching_goal_ids = $this->Team->TeamMember->getCoachingGoalList($this->Auth->user('id'));
        if (isset($coaching_goal_ids[$goal_id])) {
            $is_coaching_goal = true;
        }
        $this->set('is_coaching_goal', $is_coaching_goal);

        return true;
    }

    /**
     * select2のゴール名検索
     */
    function ajax_select2_goals()
    {
        $this->_ajaxPreProcess();
        $query = $this->request->query;
        $res = ['results' => []];
        if (isset($query['term']) && $query['term'] && count($query['term']) <= SELECT2_QUERY_LIMIT && isset($query['page_limit']) && $query['page_limit']) {
            $res = $this->Goal->getGoalsSelect2($query['term'], $query['page_limit']);
        }
        return $this->_ajaxGetResponse($res);
    }
}
