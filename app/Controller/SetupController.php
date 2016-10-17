<?php
App::uses('AppController', 'Controller');
App::uses('PostsController', 'Controller');
App::uses('Circle', 'Model');
App::uses('Post', 'Model');

/**
 * Setup Controller
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
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->Goal->KeyResult->_setUnitName();
        $current_term = $this->Team->EvaluateTerm->getCurrentTermData();
        $current_term_start_date_format = date('Y/m/d', $current_term['start_date'] + $current_term['timezone'] * HOUR);
        $current_term_end_date_format = date('Y/m/d', $current_term['end_date'] + $current_term['timezone'] * HOUR);
        $without_footer = true;
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
                3 => true,
                4 => true,
                5 => true,
                6 => true,
            ];
        } else {
            $status = $this->getStatusWithRedisSave();
        }
        $res = [
            'status'           => $status,
            'rest_count'       => $this->calcSetupRestCount($status),
            'complete_percent' => $this->calcSetupCompletePercent($status),
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
            $this->Pnotify->outSuccess($msg = __("Created a goal."));
            $error = false;
        } else {
            $msg = __("Failed to save a goal.");
            $error = true;
        }
        $this->updateSetupStatusIfNotCompleted();

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

    public function ajax_create_circle()
    {
        $this->_ajaxPreProcess();

        $this->request->data['Circle']['photo'] = $_FILES['photo'];
        $this->Circle->create();
        if ($res = $this->Circle->add($this->request->data)) {
            if (!empty($this->Circle->add_new_member_list)) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                    null, $this->Circle->add_new_member_list);
            }
            $this->updateSetupStatusIfNotCompleted();
            $error = false;
            $this->Pnotify->outSuccess($msg = __("Created a circle."));
        } else {
            $msg = __("Failed to create a circle.");
            $error = true;
        }

        return $this->_ajaxGetResponse(['msg' => $msg, 'error' => $error]);
    }

    /**
     * サークルの 参加/不参加 切り替え
     *
     * @return CakeResponse
     */
    public function ajax_join_circle()
    {
        $this->_ajaxPreProcess();

        $msg = '';
        if ($this->Circle->CircleMember->joinCircle($this->request->data)) {
            if (!empty($this->Circle->CircleMember->new_joined_circle_list)) {
                foreach ($this->Circle->CircleMember->new_joined_circle_list as $circle_id) {
                    $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circle_id);
                }
                $this->updateSetupStatusIfNotCompleted();
                $msg = __("Join a circle.");
            } else {
                $msg = __("Leave a circle.");
            }
            $this->Pnotify->outSuccess($msg);
            $error = false;
        } else {
            $msg = __("Failed to change circle belonging status.");
            $error = true;
        }

        return $this->_ajaxGetResponse(['msg' => $msg, 'error' => $error]);
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
        $this->updateSetupStatusIfNotCompleted();
        $msg = __("Saved user profile.");
        $error = false;
        $this->Pnotify->outSuccess($msg);

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
        $this->updateSetupStatusIfNotCompleted();

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
        $socket_id = viaIsSet($this->request->data['socket_id']);
        $channelName = "goal_" . $goal_id;
        $this->NotifyBiz->push($socket_id, $channelName);
        $kr_id = isset($this->request->data['ActionResult']['key_result_id']) ? $this->request->data['ActionResult']['key_result_id'] : null;
        // $this->Mixpanel->trackGoal(MixpanelComponent::TRACK_CREATE_ACTION, $goal_id, $kr_id,
        //                            $this->Goal->ActionResult->getLastInsertID());
        $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_FEED_CAN_SEE_ACTION,
            $this->Goal->ActionResult->getLastInsertID());
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_MY_GOAL_AREA, true), 'user_data');
        Cache::delete($this->Goal->getCacheKey(CACHE_KEY_ACTION_COUNT, true), 'user_data');
        // push
        $this->Pnotify->outSuccess($msg = __("Added an action."));
        //セットアップガイドステータスの更新
        $this->updateSetupStatusIfNotCompleted();
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
