<?php
App::uses('AppController', 'Controller');
App::uses('PostsController', 'Controller');
App::uses('Circle', 'Model');
App::uses('Post', 'Model');

/**
 * Setup Controller
 *
 * @property Circle       $Circle
 * @property User         $User
 * @property Goal         $Goal
 * @property Team         $Team
 * @property KeyResult    $KeyResult
 * @property Post         $Post
 * @property Device       $Device
 * @property ActionResult $ActionResult
 */
class SetupController extends AppController
{
    var $uses = [
        'Circle',
        'User',
        'Goal',
        'Team',
        'KeyResult',
        'Post',
        'Device',
        'ActionResult'
    ];
    var $components = ['RequestHandler'];

    public function beforeFilter()
    {
        parent::beforeFilter();
        // TODO: delete these uncheck setting to enable security check.
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->Goal->KeyResult->_setUnitName();
        $without_footer = true;
        if (empty($this->current_team_id)) {
            $current_term_start_date_format = "";
            $current_term_end_date_format = "";
        } else {
            try {
                $currentTerm = $this->Team->Term->getCurrentTermData();
                if (empty($currentTerm)) {
                    throw new Exception(sprintf("Failed to get term data. team_id:%s", $this->current_team_id));
                }
                $current_term_start_date_format = AppUtil::dateYmdReformat($currentTerm['start_date'], "/");
                $current_term_end_date_format = AppUtil::dateYmdReformat($currentTerm['end_date'], "/");
            } catch (Exception $e) {
                $this->log(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
                $this->log($e->getTraceAsString());
                $current_term_start_date_format = "";
                $current_term_end_date_format = "";
            }
        }
        $this->set(compact('without_footer', 'current_term_start_date_format', 'current_term_end_date_format'));
    }

    public function index()
    {
        return $this->render();
    }

    public function top()
    {
        return $this->render('index');
    }

    public function goal()
    {
        return $this->render('index');
    }

    public function profile()
    {
        return $this->render('index');
    }

    public function circle()
    {
        return $this->render('index');
    }

    public function app()
    {
        return $this->render('index');
    }

    public function action()
    {
        return $this->render('index');
    }

    public function post()
    {
        return $this->render('index');
    }

    public function ajax_get_setup_status()
    {
        $this->_ajaxPreProcess();

        $setup_guide_is_completed = $this->Auth->user('setup_complete_flg');
        if ($setup_guide_is_completed == User::SETUP_GUIDE_IS_COMPLETED) {
            $status = [
                1 => true,
                2 => true,
            ];
        } else {
            $status = $this->_getStatusWithRedisSave();
        }
        $res = [
            'status'           => $status,
            'rest_count'       => $this->_calcSetupRestCount($status),
            'complete_percent' => $this->_calcSetupCompletePercent($status),
        ];

        return $this->_ajaxGetResponse($res);
    }

    /**
     * TODO:削除
     *
     * @deprecated
     */
    public function ajax_create_goal()
    {
        $this->_ajaxPreProcess();

        $goal = $this->request->data['Goal'];
        // $_FILESとGoalオブジェクトマージ
        $goal['photo'] = $_FILES['photo'];
        // Goal保存
        //FIXME: [START] tKRモデル対応のために一時的にゴールの内容を無理やりtKRとして登録しているが、React側からこれらの値を渡すようにする！
        $goal['term_type'] = 'current';
        $goal['goal_category_id'] = key($this->Goal->GoalCategory->getCategoryList());
        $tkr['name'] = $goal['name'];
        $tkr['value_unit'] = $goal['value_unit'];
        $tkr['start_value'] = $goal['start_value'];
        $tkr['target_value'] = $goal['target_value'];
        //FIXME: [END]
        $res = $this->Goal->add(['Goal' => $goal, 'KeyResult' => [$tkr]]);
        if ($res) {
            $this->Notification->outSuccess($msg = __("Created a Goal."));
            $error = false;
        } else {
            $msg = __("Failed to save a Goal.");
            $error = true;
        }
        $this->_updateSetupStatusIfNotCompleted();

        return $this->_ajaxGetResponse(['error' => $error, 'msg' => $msg]);
    }

    public function ajax_get_circles()
    {
        $this->_ajaxPreProcess();

        $not_joined_circles = array_values($this->Circle->getPublicCircles('non-joined'));
        $res = [
            'not_joined_circles' => $not_joined_circles,
            'error'              => false
        ];
        return $this->_ajaxGetResponse($res);
    }

    /**
     * Create circle
     *
     * @return CakeResponse
     */
    public function ajax_create_circle()
    {
        $this->_ajaxPreProcess();

        App::import('Service', 'CircleService');
        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        $userId = $this->Auth->user('id');
        $data = $this->request->data;
        if (Hash::get($_FILES, 'photo')) {
            $data['Circle']['photo'] = $_FILES['photo'];
        }
        // extract adding member ids
        $memberIds = [];
        $members = Hash::get($data, 'Circle.members');
        if ($members) {
            $memberIds = $CircleService->extractUserIds($members);
            unset($data['Circle']['members']);
        }

        // validation
        if (!$CircleService->validateCreate($data, $userId)) {
            $this->Notification->outError($validateCreate);
            return $this->_ajaxGetResponse(['msg' => __("Failed to create a circle."), 'error' => true]);
        }

        // create circle
        if (!$CircleService->create($data, $userId)) {
            $this->Notification->outError(__("Failed to create a circle."));
            return $this->_ajaxGetResponse(['msg' => __("Failed to create a circle."), 'error' => true]);
        }

        // Notification
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $data['Circle']['id'],
            null, $memberIds);
        $this->Notification->outSuccess(__("Created a circle."));

        $this->_updateSetupStatusIfNotCompleted();

        return $this->_ajaxGetResponse(['msg' => __("Created a circle."), 'error' => false]);
    }

    /**
     * Join circle
     *
     * @return CakeResponse
     */
    public function ajax_join_circle()
    {
        $this->_ajaxPreProcess();

        App::import('Service', 'CircleService');
        /** @var ExperimentService $ExperimentService */
        $CircleService = ClassRegistry::init('CircleService');

        $circleIds = Hash::extract($this->request->data, 'Circle.{n}.circle_id');
        $userId = $this->Auth->user('id');

        // Join circles
        $isJoined = $CircleService->joinMultiple($circleIds, $userId);
        if (!$isJoined) {
            $msg = __("Failed to change circle belonging status.");
            return $this->_ajaxGetResponse(['msg' => $msg, 'error' => true]);
        }

        // Notification
        foreach ($circleIds as $circleId) {
            $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circleId);
        }
        $this->_updateSetupStatusIfNotCompleted();

        $this->Notification->outSuccess(__("Join a circle."));
        return $this->_ajaxGetResponse(['msg' => __("Join a circle."), 'error' => false]);
    }

    public function ajax_add_profile()
    {
        $this->_ajaxPreProcess();

        $msg = '';
        $team_member_id = $this->User->TeamMember->getIdByTeamAndUserId($this->current_team_id, $this->my_uid);
        $this->request->data['TeamMember'][0]['id'] = $team_member_id;
        $this->request->data['User']['id'] = $this->User->id = $this->my_uid;
        if (isset($_FILES['photo']) && !empty($_FILES['photo'])) {
            $this->request->data['User']['photo'] = $_FILES['photo'];
        }
        // キャッシュ削除
        Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');
        $this->User->saveAll($this->request->data);
        //セットアップガイドステータスの更新
        $this->_updateSetupStatusIfNotCompleted();
        $msg = __("Saved user profile.");
        $error = false;
        $this->Notification->outSuccess($msg);

        return $this->_ajaxGetResponse(['msg' => $msg, 'error' => $error]);
    }

    public function ajax_register_no_device()
    {
        $this->_ajaxPreProcess();

        if ($this->Device->isInstalledMobileApp($this->my_uid)) {
            $res = false;
        } else {
            $res = $this->Device->add([
                'Device' => [
                    'user_id'      => $this->my_uid,
                    'os_type'      => 99,
                    'device_token' => 'No devices.'
                ]
            ]);
        }
        $this->_updateSetupStatusIfNotCompleted();

        return $this->_ajaxGetResponse(['error' => !$res]);
    }

    public function ajax_get_circles_for_post()
    {
        $this->_ajaxPreProcess();

        $circles = $this->Circle->CircleMember->getMyCircle();

        return $this->_ajaxGetResponse(['circles' => $circles]);
    }

    public function ajax_get_file_upload_form_element()
    {
        $this->_ajaxPreProcess();

        $response = $this->render('/Elements/file_upload_form');
        $html = $response->__toString();

        return $this->_ajaxGetResponse(['html' => $html]);
    }

    public function ajax_get_goals()
    {
        $this->_ajaxPreProcess();

        App::uses('UploadHelper', 'View/Helper');
        $this->Upload = new UploadHelper(new View());
        $goals = $this->Goal->getGoalsForSetupBy($this->Auth->user('id'));
        foreach ($goals as $key => $goal) {
            $goals[$key]['Goal']['photo_file_path'] = $this->Upload->uploadUrl($goal, 'Goal.photo',
                ['style' => 'medium']);
        }
        $res = [
            'goals' => $goals,
        ];
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_add_action()
    {
        $this->_ajaxPreProcess();

        if (!$goal_id = isset($this->request->params['named']['goal_id']) ? $this->request->params['named']['goal_id'] : null) {
            $goal_id = isset($this->request->data['ActionResult']['goal_id']) ? $this->request->data['ActionResult']['goal_id'] : null;
        }
        if (!$goal_id) {
            throw new RuntimeException(__("You have no permission."));
        }
        $this->request->allowMethod('post');

        /** @var KeyResultService $KeyResultService */
        $KeyResultService = ClassRegistry::init("KeyResultService");

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
            $msg = $e->getMessage();
            $res = [
                'error' => true,
                'msg'   => $msg
            ];
            return $this->_ajaxGetResponse($res);
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
        // $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_ACTION, $goal_id, $kr_id,
        //                            $this->Goal->ActionResult->getLastInsertID());
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
            $this->Goal->ActionResult->getLastInsertID());

        // ダッシュボードのKRキャッシュ削除
        $KeyResultService->removeGoalMembersCacheInDashboard($goal_id, false);

        // push
        $this->Notification->outSuccess($msg = __("Added an action."));
        //セットアップガイドステータスの更新
        $this->_updateSetupStatusIfNotCompleted();
        $res = [
            'error' => false,
            'msg'   => $msg
        ];

        return $this->_ajaxGetResponse($res);
    }

    public function ajax_get_default_user_profile()
    {
        $this->_ajaxPreProcess();

        App::uses('UploadHelper', 'View/Helper');
        $me = $this->User->getDetail($this->Auth->user('id'));
        $this->Upload = new UploadHelper(new View());
        $res = [
            'error'           => false,
            'default_profile' => [
                'photo_file_path' => $this->Upload->uploadUrl($me, 'User.photo', ['style' => 'x_large']),
                'photo_file_name' => $me['User']['photo_file_name'],
                'comment'         => $me['TeamMember'][0]['comment'],
            ]
        ];

        return $this->_ajaxGetResponse($res);
    }
}
