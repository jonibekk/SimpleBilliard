<?php

use Goalous\Enum\Model\Translation\ContentType as TranslationContentType;
use Goalous\Exception\Follow\ValidationToFollowException;

App::uses('AppController', 'Controller');
App::uses('PostShareCircle', 'Model');
App::uses('Translation', 'Controller');
App::import('Service', 'GoalService');
App::import('Service', 'KeyResultService');
App::import('Service', 'GoalMemberService');
App::import('Service', 'ActionService');
App::import('Service', 'TranslationService');
App::import('Service', 'FollowService');
/** @noinspection PhpUndefinedClassInspection */
App::import('Service', 'KeyResultService');
App::import('Controller/Traits/Notification', 'TranslationNotificationTrait');

/**
 * Goals Controller
 *
 * @property Goal $Goal
 */
class GoalsController extends AppController
{
    use TranslationNotificationTrait;

    public $components = [
        'Security',
    ];

    public function beforeFilter()
    {
        parent::beforeFilter();
        $allowed_actions = ['add_completed_action'];
        //アプリからのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        if (in_array($this->request->params['action'], $allowed_actions) && $this->is_mb_app) {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }
    }

    /**
     * ゴール一覧画面
     */
    public function index()
    {
        //headerのアイコン下にバーを表示するための判定用変数をviewに渡す
        $current_global_menu = "goal";
        $this->set(compact('current_global_menu'));
    }

    public function create($step = null)
    {
        if ($step !== 'step1') {
            return $this->redirect(['step1']);
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

    /**
     * ゴール完了
     *
     * @param $goalId
     *
     * @return \Cake\Network\Response|CakeResponse|null
     */
    public function complete($goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        // バリデーション
        $errMsg = $this->_validateComplete($goalId);
        if ($errMsg !== true) {
            $this->Notification->outError($errMsg);
            return $this->redirect($this->referer());
        }

        // ゴール完了
        if (!$GoalService->complete($goalId)) {
            $this->Notification->outSuccess(__("Internal Server Error."));
            return $this->redirect($this->referer());
        }

        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_ACHIEVE_GOAL, $goalId);
        $this->Notification->outSuccess(__("Completed a Goal."));
        // pusherに通知
        $socketId = Hash::get($this->request->data, 'socket_id');
        $channelName = "goal_" . $goalId;
        $this->NotifyBiz->push($socketId, $channelName);

        return $this->redirect('/');

    }

    /**
     * @param $goalId
     *
     * @return string
     */
    public function _validateComplete($goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        if (empty($goalId) || is_int($goalId)) {
            return __("Not exist");
        }
        $goal = $GoalService->get($goalId);
        // 対象のゴールが存在するか
        if (empty($goal)) {
            return __("Not exist");
        }
        // ゴールのリーダーか
        if (!$GoalMemberService->isLeader($goalId, $this->Auth->user('id'))) {
            return __("You have no permission.");
        }
        // 未完了のKRが残っている場合はゴール完了できない
        if ($KeyResultService->countIncomplete($goalId) > 0) {
            return __("You have no permission.");
        }
        return true;
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
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $goalId = $this->request->params['named']['goal_id'];
        try {
            $this->Goal->isPermittedAdmin($goalId);
            $this->Goal->isNotExistsEvaluation($goalId);
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post', 'delete');

        // ゴール削除
        if (!$GoalService->delete($goalId)) {
            $this->Notification->outError(__("An error has occurred."));
            return $this->redirect($this->referer());
        }

        $this->Notification->outSuccess(__("Deleted a Goal."));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && $params_referer['pass'][0] == 'home') {
            $this->redirect('/goals/kr_progress');
        } else {
            $userId = $this->Auth->user('id');
            return $this->redirect(['controller' => 'users', 'action' => 'view_goals', 'user_id' => $userId]);
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
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $goalId = Hash::get($this->request->params, 'named.goal_id');
        $userId = Hash::get($this->request->params, 'named.user_id');
        $evaluateTermId = Hash::get($this->request->params, 'named.evaluate_term_id');
        $krs = [];
        if ($goalId && $userId) {
            $krs = $this->Goal->KeyResult->getKeyResultsForEvaluation($goalId, $userId);
            foreach ($krs as $k => $v) {
                $krs[$k] = $KeyResultService->processKeyResult($v, '/');
            }
        }

        $allKrCount = count($krs);
        $myActionKrCount = count(Hash::extract($krs, "{n}.ActionResult.0.id"));

        $this->_ajaxPreProcess();

        //htmlレンダリング結果
        $this->set(compact('krs', 'allKrCount', 'myActionKrCount', 'evaluateTermId', 'userId'));
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
        $krShortValueUnitList = $KeyResultService->buildKrUnitsSelectList($isShort = true);

        // ゴールが属している評価期間データ
        $goalTerm = $this->Goal->getGoalTermData($goalId);

        $limitEndDate = AppUtil::dateYmdReformat($goal['Goal']['end_date'], "/");

        $isCurrentTermGoal = $this->Goal->isPresentTermGoal($goalId);
        if ($isCurrentTermGoal) {
            $limitStartDate = GoalousDateTime::now()->format('Y/m/d');
        } else {
            $limitStartDate = GoalousDateTime::parse($goal['Goal']['start_date'])->format('Y/m/d');
        }

        $this->set(compact(
            'goal',
            'goalId',
            'goalCategoryList',
            'goalTerm',
            'priorityList',
            'krPriorityList',
            'krValueUnitList',
            'krShortValueUnitList',
            'limitEndDate',
            'limitStartDate',
            'currentKrId'
        ));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_add_key_result');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    /**
     * TKR交換モーダル表示
     *
     * @return CakeResponse|null
     */
    public function ajax_get_exchange_tkr_modal()
    {
        $this->_ajaxPreProcess();
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        $goalId = Hash::get($this->request->params, 'named.goal_id');
        if (empty($goalId)) {
            return $this->_ajaxGetResponse(null);
        }

        $isApproval = $GoalMemberService->isApprovableByGoalId($goalId, $this->Auth->user('id'));

        $allKrs = $KeyResultService->findByGoalId($goalId);
        $tkr = [];
        $krs = [];
        foreach ($allKrs as $kr) {
            if ($kr['tkr_flg']) {
                $tkr = $kr;
            } else {
                $krs[$kr['id']] = $kr['name'];
            }
        }

        $this->set(compact(
            'goalId',
            'tkr',
            'krs',
            'isApproval'
        ));
        //htmlレンダリング結果
        $response = $this->render('Goal/modal_exchange_tkr');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    /**
     * リーダー変更モーダル表示
     *
     * @return CakeResponse|null
     */
    public function ajax_get_exchange_leader_modal()
    {
        $this->_ajaxPreProcess();

        // ゴール存在チェック
        $goalId = Hash::get($this->request->params, 'named.goal_id');
        if (empty($goalId)) {
            return $this->_ajaxGetResponse(null);
        }

        // ビュー変数セット
        $isLeader = $this->Goal->GoalMember->isLeader($goalId, $this->Auth->user('id'));
        $goalMembers = $this->Goal->GoalMember->getActiveCollaboratorList($goalId);
        // リーダーが非アクティブの可能性もあるので、
        // アクティブ/非アクティブに関わらず取得する
        $currentLeader = $this->Goal->GoalMember->getLeader($goalId);
        $priorityList = $this->Goal->priority_list;
        $this->set(compact(
            'goalId',
            'isLeader',
            'goalMembers',
            'currentLeader',
            'priorityList'
        ));

        //htmlレンダリング結果
        $response = $this->render('Goal/modal_exchange_leader');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function ajax_get_collabo_change_modal()
    {
        $goal_id = $this->request->params['named']['goal_id'];
        $this->_ajaxPreProcess();
        $goal = $this->Goal->getCollaboModalItem($goal_id);
        $priority_list = $this->Goal->priority_list;

        /** @var GoalApprovalService $GoalApprovalService */
        $GoalApprovalService = ClassRegistry::init("GoalApprovalService");
        $canApprove = $GoalApprovalService->isApprovable(
            $this->Auth->user('id'), $this->Session->read('current_team_id')
        );

        $this->set(compact('goal', 'priority_list', 'canApprove'));

        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_collabo');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function edit_collabo()
    {
        $goalMemberId = Hash::get($this->request->params, 'named.goal_member_id');
        $myUserId = $this->Auth->user('id');
        $new = $goalMemberId ? false : true;
        $this->request->allowMethod('post', 'put');
        $coachId = $this->User->TeamMember->getCoachUserIdByMemberUserId($myUserId);

        if (!isset($this->request->data['GoalMember'])) {
            $this->_editCollaboError();
            return $this->redirect($this->referer());
        }

        $goalMember = Hash::get($this->request->data, 'GoalMember');
        $goalId = Hash::get($this->request->data, 'GoalMember.goal_id');
        $goal = $this->Goal->findById($goalId);

        // Goal do not exists
        if (empty($goal)) {
            $this->Notification->outError(__("The Goal doesn't exist."));
            return $this->redirect($this->referer());
        }

        // leader cannot edit goal member status..
        if ($this->Goal->GoalMember->isLeader($goalId, $myUserId)) {
            $this->_editCollaboError();
            $this->log(sprintf("Invalid operation. leader attempted to edit collaborator status! requestData: %s teamId: %s",
                var_export($this->request->data, true), $this->current_team_id));
            $this->log(Debugger::trace());

            return $this->redirect($this->referer());
        }

        // Check if the goal is completed
        if ($this->Goal->isCompleted($goalId)) {
            $this->Notification->outError(__("You cannot follow or collaborate with a completed Goal."));
            return $this->redirect($this->referer());
        }

        // Check if it is an old goal
        if ($this->Goal->isFinished($goalId)) {
            $this->Notification->outError(__("You cannot follow or collaborate with a past Goal."));
            return $this->redirect($this->referer());
        }

        // コラボを編集した場合は必ずコラボを認定対象外にし、認定ステータスを「Reapplication」にする
        $this->request->data['GoalMember']['approval_status'] = $new ? GoalMember::APPROVAL_STATUS_NEW : GoalMember::APPROVAL_STATUS_REAPPLICATION;
        $this->request->data['GoalMember']['is_target_evaluation'] = false;

        $this->request->data['GoalMember']['is_wish_approval'] = !empty($this->request->data['GoalMember']['is_wish_approval']);


        if (!$this->Goal->GoalMember->edit($this->request->data)) {
            $this->_editCollaboError();
            return $this->redirect($this->referer());
        }

        $goalMemberId = $goalMemberId ? $goalMemberId : $this->Goal->GoalMember->getLastInsertID();
        $goalLeaderUserId = Hash::get($goal, 'Goal.user_id');

        //success case.
        $this->Notification->outSuccess(__("Start to collaborate."));

        // ダッシュボードのKRキャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_KR_COUNT, true), 'user_data');
        // アクション可能ゴール一覧キャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
        // ユーザページのマイゴール一覧キャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

        //mixpanel
        if ($new) {
            // コラボしたのがコーチーの場合は、コーチとしての通知を送るのでゴールリーダーとしての通知は送らない
            if ($goalLeaderUserId != $coachId) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_COLLABORATE, $goalId);
            }
        }

        //コーチへ通知 & 未認定件数キャッシュクリア
        $isOver1Priority = (isset($goalMember['priority']) && $goalMember['priority'] >= '1');
        if ($coachId && $isOver1Priority && $this->Goal->isPresentTermGoal($goalId)) {
            if ($new) {
                //新規の場合
                $this->_sendNotifyToCoach($goalMemberId, NotifySetting::TYPE_COACHEE_COLLABORATE_GOAL);
            } else {
                //更新の場合
                $this->_sendNotifyToCoach($goalMemberId, NotifySetting::TYPE_COACHEE_CHANGE_ROLE);
            }

            Cache::delete($this->Goal->getCacheKey(CACHE_KEY_UNAPPROVED_COUNT, true, $coachId),
                'user_data');
        }
        return $this->redirect($this->referer());
    }

    function _editCollaboError()
    {
        $this->Notification->outError(__("Failed to collaborate."));
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
            $this->Notification->outError($e->getMessage());
            $this->redirect($this->referer());
        }

        $this->Goal->commit();
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_KR, $goal_id,
            $this->Goal->KeyResult->getLastInsertID());
        $this->_flashClickEvent("KRsOpen_" . $goal_id);
        $this->Notification->outSuccess(__("Added a Key Result."));
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && !empty($params_referer['pass'][0]) && $params_referer['pass'][0] == 'home') {
            $this->redirect('/goals/kr_progress');
        } else {
            return $this->redirect($this->referer());
        }
    }

    /**
     * 別のKRをTKRとして変更
     */
    public function exchange_tkr()
    {
        $formData = $this->request->data('KeyResult');
        $krId = Hash::get($formData, 'id');

        $errMsg = $this->_validateExchangeTkr($krId);
        if (!empty($errMsg)) {
            $this->Notification->outError($errMsg);
            return $this->redirect($this->referer());
        }

        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        // TKRを変更
        if (!$KeyResultService->exchangeTkr($krId, $this->Auth->user('id'))) {
            $this->Notification->outError(__("Some error occurred. Please try again from the start."));
            return $this->redirect($this->referer());
        }

        //コーチへの通知
        $goalId = Hash::get($KeyResultService->get($krId), 'goal_id');
        $this->_sendNotifyToCoach($goalId, NotifySetting::TYPE_COACHEE_EXCHANGE_TKR);
        //コラボレータへの通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_TKR_EXCHANGED_BY_LEADER, $goalId, null);

        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_UPDATE_KR, $krId);
        $this->Notification->outSuccess(__("Success."));
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $paramsReferer = Router::parse($this->referer(null, true));
        if ($paramsReferer['controller'] == 'pages' && $paramsReferer['pass'][0] == 'home') {
            return $this->redirect('/goals/kr_progress');
        } else {
            return $this->redirect($this->referer());
        }
    }

    /**
     * TKR交換バリデーション
     *
     * @param $krId
     *
     * @return bool|mixed
     */
    private function _validateExchangeTkr($krId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        // パラメータ存在チェック
        if (empty($krId)) {
            return __("Invalid Request.");
        }

        // KRが存在するか
        $kr = $KeyResultService->get($krId);
        if (empty($kr)) {
            return __("Some error occurred. Please try again from the start.");
        }
        // TKRでないか
        if ($kr['tkr_flg']) {
            return __("Some error occurred. Please try again from the start.");
        }

        $goalId = $kr['goal_id'];
        $goal = $this->Goal->getById($goalId);
        // ログインユーザーのゴールか
        if ($goal['user_id'] !== $this->Auth->user('id')) {
            return __("Some error occurred. Please try again from the start.");
        }

        // 今期以降のゴールか
        $termType = $GoalService->getTermType($goal['start_date']);
        if ($termType == GoalService::TERM_TYPE_PREVIOUS) {
            return __("Some error occurred. Please try again from the start.");
        }
        // 評価開始前か
        if ($termType == GoalService::TERM_TYPE_CURRENT) {
            $currentTermId = $this->Team->Term->getCurrentTermId();
            $isStartedEvaluation = $this->Team->Term->isStartedEvaluation($currentTermId);
            if ($isStartedEvaluation) {
                return __("Some error occurred. Please try again from the start.");
            }
        }

        return "";
    }

    /**
     * 現リーダーによるリーダー交換アクション
     * - Form値のバリデーション
     * - リーダー交換処理実行
     * - 関係者に通知
     */
    public function exchange_leader_by_leader()
    {
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        // バリデーション
        $formData = $this->request->data;
        $changeType = Hash::get($formData, 'change_type');
        $validationRes = $GoalMemberService->validateChangeLeader($formData, $changeType);
        if ($validationRes !== true) {
            $this->Notification->outError($validationRes);
            return $this->redirect($this->referer());
        }

        $changedLeader = $GoalMemberService->changeLeader($formData, $changeType);
        if (!$changedLeader) {
            $this->Notification->outError(__("Some error occurred. Please try again from the start."));
            return $this->redirect($this->referer());
        }

        // 通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EXCHANGED_LEADER, Hash::get($formData, 'Goal.id'),
            $this->Auth->user('id'));

        $this->Notification->outSuccess(__("Changed leader."));
        return $this->redirect($this->referer());
    }

    /**
     * ゴールメンバーによるリーダーアサインアクション
     * 現リーダーがinactiveの場合のみ
     * - Form値のバリデーション
     * - リーダー交換処理実行
     * - 関係者に通知
     */
    public function assign_leader_by_goal_member()
    {
        /** @var GoalMemberService $GoalMemberService */
        $GoalMemberService = ClassRegistry::init("GoalMemberService");

        // バリデーション
        $formData = $this->request->data;
        $changeType = $GoalMemberService::CHANGE_LEADER_FROM_GOAL_MEMBER;
        $validationRes = $GoalMemberService->validateChangeLeader($formData, $changeType);
        if ($validationRes !== true) {
            $this->Notification->outError($validationRes);
            return $this->redirect($this->referer());
        }

        $changedLeader = $GoalMemberService->changeLeader($formData, $changeType);
        if (!$changedLeader) {
            $this->Notification->outError(__("Some error occurred. Please try again from the start."));
            return $this->redirect($this->referer());
        }

        // 通知
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_EXCHANGED_LEADER, Hash::get($formData, 'Goal.id'));

        $this->Notification->outSuccess(__("Changed leader."));
        return $this->redirect($this->referer());
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
                $this->Notification->outSuccess(__("Completed a Goal."));
            } else {
                $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_ACHIEVE_KR,
                    $key_result['KeyResult']['goal_id'],
                    $kr_id);
                $this->Notification->outSuccess(__("Completed a Key Result."));
            }
        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            $this->Notification->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->commit();

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
        if ($params_referer['controller'] == 'pages' && !empty($params_referer['pass'][0]) && $params_referer['pass'][0] == 'home') {
            $this->redirect('/goals/kr_progress');
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
            $this->Notification->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Goal->commit();
        $this->_flashClickEvent("KRsOpen_" . $key_result['KeyResult']['goal_id']);
        $this->Notification->outSuccess(__("Made a Key Result uncompleted."));
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $params_referer = Router::parse($this->referer(null, true));
        if ($params_referer['controller'] == 'pages' && !empty($params_referer['pass'][0]) && $params_referer['pass'][0] == 'home') {
            $this->redirect('/goals/kr_progress');
        } else {
            return $this->redirect($this->referer());
        }
    }

    public function delete_key_result()
    {
        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $krId = $this->request->params['named']['key_result_id'];
        $this->request->allowMethod('post', 'delete');
        try {
            $kr = $KeyResultService->get($krId);
            if (empty($kr)) {
                throw new RuntimeException(__("No exist kr."));
            }
            if (!$this->Goal->KeyResult->isPermitted($krId)) {
                throw new RuntimeException(__("You have no permission."));
            }
            if ($this->Goal->KeyResult->isCompleted($krId)) {
                throw new RuntimeException(__("You can't delete achieved KR."));
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }

        if (!$KeyResultService->delete($krId)) {
            $this->Notification->outError(__("An error has occurred."));
            return $this->redirect($this->referer());
        }

        $goalId = Hash::get($kr, 'goal_id');
        $this->_flashClickEvent("KRsOpen_" . $goalId);
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_DELETE_KR, $goalId, $krId);

        $this->Notification->outSuccess(__("Deleted a Key Result."));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $paramsReferer = Router::parse($this->referer(null, true));
        if ($paramsReferer['controller'] == 'pages' && $paramsReferer['pass'][0] == 'home') {
            $this->redirect('/goals/kr_progress');
        } else {
            return $this->redirect($this->referer());
        }
    }

    /**
     * アクション削除
     * TODO:トランザクション処理をサービス移行、アクション削除をAPI化
     *
     * @return \Cake\Network\Response|null
     */
    public function delete_action()
    {
        $arId = $this->request->params['named']['action_result_id'];
        $this->request->allowMethod('post', 'delete');

        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        try {
            $this->Goal->ActionResult->begin();
            $action = $this->Goal->ActionResult->getById($arId);
            if (empty($action)) {
                throw new RuntimeException(__("There is no action."));
            }

            if (!$this->Team->TeamMember->isAdmin() && !$this->Goal->GoalMember->isCollaborated($action['goal_id'])) {
                throw new RuntimeException(__("You have no permission."));
            }

            $post = $this->Goal->Post->getByActionResultId($arId);

            $this->Goal->ActionResult->id = $arId;
            $this->Goal->ActionResult->delete();
            $this->Goal->ActionResult->ActionResultFile->AttachedFile->deleteAllRelatedFiles($arId,
                AttachedFile::TYPE_MODEL_ACTION_RESULT);

            /* アクション時に進めたKR進捗分を戻す */
            $krPrgChangeVal = $action['key_result_change_value'];
            $krId = $action['key_result_id'];
            if (!is_null($krPrgChangeVal) && $krPrgChangeVal != 0 && !empty($krId)) {
                $kr = $this->Goal->KeyResult->getById($krId);
                if (empty($kr)) {
                    throw new RuntimeException(__("No exist kr."));
                }
                $updateKr = [
                    'id'            => $krId,
                    'current_value' => $kr['current_value'] - $krPrgChangeVal
                ];
                if (!empty($kr['completed'])) {
                    $updateKr['completed'] = null;
                }
                // KR進捗更新
                $this->Goal->KeyResult->save($updateKr, false);

                // KR最新アクション日時更新
                $updatedLatestActioned = $KeyResultService->updateLatestActioned($krId);
                if (!$updatedLatestActioned) {
                    throw new RuntimeException(sprintf("Failed to update latest actioned timestamp to key_results table. data:%s"
                        , var_export(compact('krId'), true)));
                }

                // ダッシュボードのKRキャッシュ削除
//                $kr = $KeyResultService->get($krId);
//                $KeyResultService->removeGoalMembersCacheInDashboard($kr['goal_id'], false);
            }

            // Delete translations
            /** @var Translation $Translation */
            $Translation = ClassRegistry::init('Translation');
            $Translation->eraseAllTranslations(TranslationContentType::ACTION_POST(), $post['Post']['id']);

            $this->Goal->ActionResult->commit();
        } catch (RuntimeException $e) {
            $this->Goal->ActionResult->rollback();
            $this->Notification->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_DELETE_ACTION,
            $action['goal_id'],
            $action['key_result_id'],
            $arId);
        if (isset($action['goal_id']) && !empty($action['goal_id'])) {
            $this->_flashClickEvent("ActionListOpen_" . $action['goal_id']);
        }

        $this->Notification->outSuccess(__("Deleted an action."));
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    /**
     * コラボ削除アクション
     */
    public function delete_collabo()
    {
        $this->request->allowMethod('post', 'put');

        // ゴールメンバー存在チェック
        $goalMemberId = $this->request->params['named']['goal_member_id'];
        $this->Goal->GoalMember->id = $goalMemberId;
        if (!$this->Goal->GoalMember->exists()) {
            $this->Notification->outError(__("He/She might quit collaborating."));
        }
        // 権限チェック
        if (!$this->Goal->GoalMember->isOwner($this->Auth->user('id'))) {
            $this->Notification->outError(__("You have no right to operate it."));
        }
        $goalMember = $this->Goal->GoalMember->findById($goalMemberId);
        if (!empty($goalMember)) {
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_WITHDRAW_COLLABORATE,
                $goalMember['GoalMember']['goal_id']);
        }

        // コラボ削除実行
        $this->Goal->GoalMember->delete();
        $this->Notification->outSuccess(__("Quitted a collaborator."));

        $goalLeaderUserId = Hash::get($goalMember, 'Goal.user_id');

        // ダッシュボードのKRキャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_KRS_IN_DASHBOARD, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_KR_COUNT, true), 'user_data');
        // アクション可能ゴール一覧キャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_ACTIONABLE_GOALS, true), 'user_data');
        // ユーザページのマイゴール一覧キャッシュ削除
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_CHANNEL_COLLABO_GOALS, true), 'user_data');

        $this->redirect($this->referer());
    }

    /**
     * フォロー、アンフォローの切り換え
     *
     * @return CakeResponse
     */
    public function ajax_toggle_follow()
    {
        $goalId = $this->request->params['named']['goal_id'];
        $this->_ajaxPreProcess();

        $return = [
            'error' => false,
            'msg'   => null,
            'add'   => true,
        ];

        /** @var FollowService $FollowService */
        $FollowService = ClassRegistry::init("FollowService");
        try {
            $FollowService->validateToFollow(
                $this->Session->read('current_team_id'),
                $goalId,
                $this->Auth->user('id')
            );
        } catch (ValidationToFollowException $e) {
            $return['error'] = true;
            $return['msg'] = $e->getMessage();
            return $this->_ajaxGetResponse($return);
        }

        //既にフォローしているかどうかのチェック
        if ($this->Goal->Follower->isExists($goalId)) {
            $return['add'] = false;
        }

        if ($return['add']) {
            $this->Goal->Follower->addFollower($goalId);
            $return['msg'] = __("Start to follow.");
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goalId);
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_MY_GOAL_FOLLOW, $goalId);
        } else {
            $this->Goal->Follower->deleteFollower($goalId);
            $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_FOLLOW_GOAL, $goalId);
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

        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");
        $key_results = $KeyResultService->processKeyResults($key_results, 'KeyResult', '/');

        // 未完了のキーリザルト数
        $incomplete_kr_count = $this->Goal->KeyResult->getIncompleteKrCount($goal_id);

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($goal_id);
        $current_term = $this->Goal->Team->Term->getCurrentTermData();
        //ゴールが今期の場合はアクション追加可能
        $can_add_action = $goal_term['end_date'] === $current_term['end_date'] ? true : false;
        $this->set(compact('key_results', 'incomplete_kr_count', 'kr_can_edit', 'goal_id', 'goal_term',
            'can_add_action'));

        $response = $this->render('Goal/key_results');
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
            $key_result['KeyResult'] = $KeyResultService->processKeyResult($key_result['KeyResult']);
        } catch (RuntimeException $e) {
            return $this->_ajaxGetResponse(null);
        }
        $goal_id = $key_result['KeyResult']['goal_id'];
        $kr_id = $key_result['KeyResult']['id'];
        $goal = $this->Goal->getGoalMinimum($goal_id);
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $krValueUnitList = $KeyResultService->buildKrUnitsSelectList();
        $krShortValueUnitList = $KeyResultService->buildKrUnitsSelectList($isShort = true);

        // 認定可能フラグ追加
        $is_approvable = false;
        $goal_leader_id = $this->Goal->GoalMember->getGoalLeaderId($goal_id);
        if ($goal_leader_id) {
            $is_approvable = $GoalMemberService->isApprovableGoalMember($goal_leader_id);
        }

        // ゴールが属している評価期間データ
        $goal_term = $this->Goal->getGoalTermData($goal_id);

        $kr_start_date_format = AppUtil::dateYmdReformat($key_result['KeyResult']['start_date'], "/");
        $kr_end_date_format = AppUtil::dateYmdReformat($key_result['KeyResult']['end_date'], "/");
        $limit_end_date = AppUtil::dateYmdReformat($goal['Goal']['end_date'], "/");
        $limit_start_date = AppUtil::dateYmdReformat($goal['Goal']['start_date'], "/");
        $this->set(compact(
            'goal',
            'goal_id',
            'kr_id',
            'goal_category_list',
            'priority_list',
            'kr_priority_list',
            'krValueUnitList',
            'krShortValueUnitList',
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
        $goalId = $this->request->params['named']['goal_id'];
        $this->_ajaxPreProcess();
        if (empty($goalId)) {
            return $this->_ajaxGetResponse([
                'html' => '',
            ]);
        }

        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

        $krs = $KeyResultService->findIncomplete($goalId);
        $this->set('krs', $krs);
        // HTML出力
        $response = $this->render('Action/input_kr_progress');
        $html = $response->__toString();
        return $this->_ajaxGetResponse([
            'html' => $html,
        ]);
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
        $goalId = Hash::get($this->request->params, 'named.goal_id');
        $keyResultId = Hash::get($this->request->params, 'named.key_result_id');
        try {
            if (!empty($goalId) && !$this->Goal->GoalMember->isCollaborated($goalId)) {
                throw new RuntimeException(__("This action can't be edited."));
            }
            if (!empty($keyResultId) && !$this->Goal->KeyResult->isPermitted($keyResultId)) {
                throw new RuntimeException(__("This action can't be edited."));
            }
        } catch (RuntimeException $e) {
            $this->Notification->outError($e->getMessage());
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }
        $this->request->data['ActionResult']['goal_id'] = $goalId;
        $this->request->data['ActionResult']['key_result_id'] = $keyResultId;
        $krList = [null => '---'] + $this->Goal->KeyResult->getKeyResults($goalId, 'list');
        $this->set(['kr_list' => $krList, 'key_result_id' => $keyResultId]);
        $this->set('common_form_type', 'action');
        $this->set('common_form_only_tab', 'action');
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->_setGoalsForTopAction();
        $this->render('edit_action');
    }

    /**
     * アクションの編集
     */
    public function edit_action()
    {
        $ar_id = $this->request->params['named']['action_result_id'];

        if (!$this->Goal->ActionResult->isOwner($this->Auth->user('id'), $ar_id)) {
            $this->Notification->outError(__("This action can't be edited."));
            /** @noinspection PhpVoidFunctionResultUsedInspection */
            return $this->redirect($this->referer());
        }

        // フォームが submit された時
        if ($this->request->is('put')) {
            $this->request->data['ActionResult']['id'] = $ar_id;
            if (!$this->Goal->ActionResult->actionEdit($this->request->data)) {
                $this->Notification->outError(__("Failed to save data."));
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect($this->referer());
            }
            $this->Notification->outSuccess(__("Edited the action."));
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

            // Delete translations
            /** @var Translation $Translation */
            $Translation = ClassRegistry::init('Translation');
            $Translation->eraseAllTranslations(TranslationContentType::ACTION_POST(), $post['Post']['id']);
            $this->Goal->Post->clearLanguage($post['Post']['id']);

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
        $this->_setGoalsForTopAction();
        $row = $this->Goal->ActionResult->getWithAttachedFiles($ar_id);
        $this->request->data = $row;
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
            __("Description"),
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
                        $record['end_date'] = AppUtil::dateYmdReformat($c_v['Goal']['end_date'], "/");
                        $record['start_date'] = AppUtil::dateYmdReformat($c_v['Goal']['start_date'], "/");
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
     * TODO:API化したので廃止予定
     */
    public function add_completed_action()
    {
        if (!$goal_id = isset($this->request->params['named']['goal_id']) ? $this->request->params['named']['goal_id'] : null) {
            $goal_id = isset($this->request->data['ActionResult']['goal_id']) ? $this->request->data['ActionResult']['goal_id'] : null;
        }
        if (!$goal_id) {
            $this->Notification->outError(__("Failed to add an action."));
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
                || !$goalPost = $this->Goal->Post->addGoalPost(Post::TYPE_ACTION, $goal_id, $this->Auth->user('id'), false,
                        $this->Goal->ActionResult->getLastInsertID(), $share,
                        PostShareCircle::SHARE_TYPE_ONLY_NOTIFY)
                    || !$this->Goal->Post->PostFile->AttachedFile->saveRelatedFiles($this->Goal->ActionResult->getLastInsertID(),
                        AttachedFile::TYPE_MODEL_ACTION_RESULT,
                        $file_ids)
            ) {
                throw new RuntimeException(__("Failed to add an action."));
            }

            // Make translation
            /** @var TranslationService $TranslationService */
            $TranslationService = ClassRegistry::init('TranslationService');

            $teamId = TeamStatus::getCurrentTeam()->getTeamId();

            if ($TranslationService->canTranslate($teamId)) {
                try {
                    $TranslationService->createDefaultTranslation($teamId, TranslationContentType::ACTION_POST(), $goalPost['Post']['id']);
                    // I need to write Email send process here, NotifyBizComponent Can't call from Service class.
                    $this->sendTranslationUsageNotification($teamId);
                } catch (Exception $e) {
                    GoalousLog::error('Failed create translation on new post', [
                        'message'  => $e->getMessage(),
                        'posts.id' => $this->Post->getLastInsertID(),
                    ]);
                }
            }

        } catch (RuntimeException $e) {
            $this->Goal->rollback();
            if ($action_result_id = $this->Goal->ActionResult->getLastInsertID()) {
                $this->Goal->Post->PostFile->AttachedFile->deleteAllRelatedFiles($action_result_id,
                    AttachedFile::TYPE_MODEL_ACTION_RESULT);
            }
            $this->Notification->outError($e->getMessage());
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

        // push
        $this->Notification->outSuccess(__("Added an action."));
        //セットアップガイドステータスの更新
        $this->_updateSetupStatusIfNotCompleted();

        $post = $this->Goal->Post->getByActionResultId($this->Goal->ActionResult->getLastInsertID());
        $url = $post ? [
            'controller' => 'posts',
            'action'     => 'feed',
            'post_id'    => $post['Post']['id']
        ] : $this->referer();
        return $this->redirect($url);

    }

    function _setGoalAddViewVals()
    {
        $goal_category_list = $this->Goal->GoalCategory->getCategoryList();
        $priority_list = $this->Goal->priority_list;
        $kr_priority_list = $this->Goal->KeyResult->priority_list;
        $kr_value_unit_list = KeyResult::$UNIT;
        $current_term = $this->Team->Term->getCurrentTermData();
        $next_term = $this->Team->Term->getNextTermData();
        $current_term_start_date_format = AppUtil::dateYmdReformat($current_term['start_date'], "/");
        $current_term_end_date_format = AppUtil::dateYmdReformat($current_term['end_date'], "/");
        $next_term_start_date_format = AppUtil::dateYmdReformat($next_term['start_date'], "/");
        $next_term_end_date_format = AppUtil::dateYmdReformat($next_term['end_date'], "/");
        $today_format = date('Y/m/d', REQUEST_TIMESTAMP + $current_term['timezone'] * HOUR);
        $is_next_term_goal = false;

        // ゴール編集時
        if (isset($this->request->data['Goal']) && !empty($this->request->data['Goal'])) {
            // ゴールが属している評価期間データ
            $goal_start_date_format = AppUtil::dateYmdReformat($this->request->data['Goal']['start_date'], "/");
            $goal_end_date_format = AppUtil::dateYmdReformat($this->request->data['Goal']['end_date'], "/");

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
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $followers = $this->Goal->Follower->getFollowerByGoalId($goal_id, [
            'limit'      => GOAL_PAGE_FOLLOWER_NUMBER,
            'with_group' => true,
        ]);
        $goalTerm = $this->Goal->getGoalTermData($goal_id);
        $this->set(compact('followers', 'goalTerm'));
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
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $members = $this->Goal->GoalMember->getGoalMemberByGoalId($goal_id, [
            'limit' => GOAL_PAGE_MEMBER_NUMBER,
        ]);
        $goalTerm = $this->Goal->getGoalTermData($goal_id);
        $this->set('members', $members);
        $followers = $this->Goal->Follower->getFollowerByGoalId($goal_id, [
            'limit'      => GOAL_PAGE_FOLLOWER_NUMBER,
            'with_group' => true,
        ]);
        $this->set('followers', $followers);
        $this->set('goalTerm', $goalTerm);
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
            $this->Notification->outError(__("Invalid screen transition."));
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
        $goalTerm = $this->Goal->getGoalTermData($goal_id);
        $followers = $this->Goal->Follower->getFollowerByGoalId($goal_id, [
            'limit'      => GOAL_PAGE_FOLLOWER_NUMBER,
            'with_group' => true,
        ]);
        $this->set('followers', $followers);
        // TODO: Duplicate variable. But both are used, so we have to unify.
        $this->set('goalTerm', $goalTerm);
        $this->set('goal_term', $goalTerm);

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    function view_actions()
    {
        /** @var ActionResult $ActionResult */
        $ActionResult = ClassRegistry::init('ActionResult');

        $myUid = $this->Auth->user('id');
        $namedParams = $this->request->params['named'];
        $goalId = Hash::get($namedParams, "goal_id");
        if (!$goalId || !$this->_setGoalPageHeaderInfo($goalId)) {
            // ゴールが存在しない
            $this->Notification->outError(__("Invalid screen transition."));
            return $this->redirect($this->referer());
        }
        $pageType = Hash::get($namedParams, "page_type");
        if (!in_array($pageType, ['list', 'image'])) {
            $this->Notification->outError(__("Invalid screen transition."));
            $this->redirect($this->referer());
        }
        $keyResultId = Hash::get($namedParams, 'key_result_id');
        $params = [
            'type'          => Post::TYPE_ACTION,
            'goal_id'       => $goalId,
            'key_result_id' => $keyResultId,
        ];
        $posts = [];
        switch ($pageType) {
            case 'list':
                $posts = $this->Post->get(1, POST_FEED_PAGE_ITEMS_NUMBER, null, null, $params);
                break;
            case 'image':
                $posts = $this->Post->get(1, MY_PAGE_CUBE_ACTION_IMG_NUMBER, null, null, $params);
                break;
        }
        $krSelectOptions = $this->Goal->KeyResult->getKrNameList($goalId, true);
        $goalBaseUrl = Router::url([
            'controller' => 'goals',
            'action'     => 'view_actions',
            'goal_id'    => $goalId,
            'page_type'  => $pageType
        ]);
        $goalTerm = $this->Goal->getGoalTermData($goalId);

        if ($keyResultId && $this->Goal->KeyResult->isCompleted($keyResultId)) {
            $canAction = false;
        } else {
            $canAction = $this->Goal->isActionable($myUid, $goalId);
        }

        if ($keyResultId) {
            $actionCount = $ActionResult->getCountByKrId($keyResultId);
        } else {
            $actionCount = $ActionResult->getCountByGoalId($goalId);
        }

        $this->set('long_text', false);
        $this->set(compact(
            'goalTerm',
            'keyResultId',
            'goalId',
            'posts',
            'goalBaseUrl',
            'krSelectOptions',
            'actionCount',
            'canAction'
        ));

        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }

    /**
     * ゴールページの上部コンテンツの表示に必要なView変数をセット
     *
     * @param $goalId
     *
     * @return bool
     */
    function _setGoalPageHeaderInfo($goalId)
    {
        /** @var GoalService $GoalService */
        $GoalService = ClassRegistry::init("GoalService");

        $userId = $this->Auth->user('id');

        $goal = $this->Goal->getGoal($goalId);
        if (!isset($goal['Goal']['id'])) {
            // ゴールが存在しない
            return false;
        }
        $goal['goal_labels'] = Hash::extract($this->Goal->GoalLabel->findByGoalId($goal['Goal']['id']), '{n}.Label');
        // 進捗情報を追加
        $goal['Goal']['progress'] = $GoalService->calcProgressByOwnedPriorities($goal['KeyResult']);
        $this->set('goal', $goal);

        $this->set('item_created', isset($goal['Goal']['created']) ? $goal['Goal']['created'] : null);

        // KR count
        $krCount = $this->Goal->KeyResult->getKrCount($goalId);
        $this->set('kr_count', $krCount);

        // アクション数
        $actionCount = $this->Goal->ActionResult->getCountByGoalId($goalId);
        $this->set('action_count', $actionCount);

        // メンバー数
        $memberCount = count($goal['Leader']) + count($goal['GoalMember']);
        $this->set('member_count', $memberCount);

        // フォロワー数
        $followerCount = count($goal['Follower']);
        $this->set('follower_count', $followerCount);

        // フォロワー
        $followers = $this->Goal->Follower->getFollowerByGoalId($goalId, [
            'limit'      => GOAL_PAGE_FOLLOWER_NUMBER,
            'with_group' => true,
        ]);
        $this->set('followers', $followers);

        // 閲覧者がゴールのリーダーかを判別
        $isLeader = false;
        foreach ($goal['Leader'] as $v) {
            if ($this->Auth->user('id') == $v['User']['id']) {
                $isLeader = true;
                break;
            }
        }
        $this->set('is_leader', $isLeader);

        // 閲覧者がゴールのコラボレーターかを判別
        $isGoalMember = false;
        foreach ($goal['GoalMember'] as $v) {
            if ($this->Auth->user('id') == $v['User']['id']) {
                $isGoalMember = true;
                break;
            }
        }
        $this->set('is_goal_member', $isGoalMember);

        // 閲覧者がコーチしているゴールかを判別
        $isCoachingGoal = false;
        $coachingGoalIds = $this->Team->TeamMember->getCoachingGoalList($userId);
        if (isset($coachingGoalIds[$goalId])) {
            $isCoachingGoal = true;
        }
        $this->set('is_coaching_goal', $isCoachingGoal);

        // Is the goal completable?
        $isCanComplete = $this->Goal->isCanComplete($userId, $goalId);
        $this->set('isCanComplete', $isCanComplete);

        $isGoalAfterCurrentTerm = $GoalService->isGoalAfterCurrentTerm($goalId);
        $this->set(compact('isGoalAfterCurrentTerm'));

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

    /**
     * krのプログレスのみを1カラムで表示
     *
     * @return
     */
    public function kr_progress()
    {
        $this->layout = LAYOUT_ONE_COLUMN;
        return $this->render();
    }
}
