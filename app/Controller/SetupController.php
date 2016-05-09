<?php
App::uses('AppController', 'Controller');

/**
 * Setup Controller
 */
class SetupController extends AppController
{
    var $uses = [
        'Circle', 'User', 'Goal', 'Team', 'KeyResult'
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

    public function goal()
    {
        $this->KeyResult->_setUnitName();
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

    public function ajax_get_setup_status()
    {
        $this->layout = false;
        $status = $this->getStatusWithRedisSave();
        $res = [
            'status'           => $status,
            'setup_rest_count' => $this->calcSetupRestCount($status)
        ];
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_create_goal()
    {
      $this->_ajaxPreProcess();

      // Purpose保存
      $this->Goal->Purpose->add($this->request->data);
      $purpose_id = $this->Goal->Purpose->id;
      $this->request->data['Goal']['purpose_id'] = $purpose_id;

      // $_FILESとGoalオブジェクトマージ
      $this->request->data['Goal']['photo'] = $_FILES['photo'];

      // Goal保存
      $res = $this->Goal->add(['Goal' => $this->request->data['Goal']]);

      $this->updateSetupStatusIfNotCompleted();
      return $this->_ajaxGetResponse(['res' => $res, 'validate_errors' => $this->Goal->validateErrors]);
    }

    public function ajax_get_circles()
    {
        $this->layout = false;

        $not_joined_circles = array_values($this->Circle->getPublicCircles('non-joined'));
        $res = [
            'not_joined_circles' => $not_joined_circles,
        ];
        return $this->_ajaxGetResponse($res);
    }

    public function ajax_create_circle()
    {
        // $this->_ajaxPreProcess();
        $this->layout = false;
        $this->request->allowMethod('post');
        $this->Circle->create();
        if ($res = $this->Circle->add($this->request->data)) {
            if (!empty($this->Circle->add_new_member_list)) {
                $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                                                 null, $this->Circle->add_new_member_list);
            }
            $this->updateSetupStatusIfNotCompleted();
            $this->Pnotify->outSuccess(__("Created a circle."));
        }

        return $this->_ajaxGetResponse(['res' => $res]);
    }

    /**
     * サークルの 参加/不参加 切り替え
     *
     * @return CakeResponse
     */
    public function ajax_join_circle()
    {
        $this->layout = false;
        $this->request->allowMethod('post');
        $msg = null;
        if ($this->Circle->CircleMember->joinCircle($this->request->data)) {
            if (!empty($this->Circle->CircleMember->new_joined_circle_list)) {
                foreach ($this->Circle->CircleMember->new_joined_circle_list as $circle_id) {
                    $this->NotifyBiz->execSendNotify(NotifySetting::TYPE_CIRCLE_USER_JOIN, $circle_id);
                }
                $this->updateSetupStatusIfNotCompleted();
                $msg = __("Join a circle.");
            }
            else {
                $msg = __("Leave a circle.");
            }
        }
        else {
            $msg = __("Failed to change circle belonging status.");
        }
        return $this->_ajaxGetResponse(['msg' => $msg]);
    }

    public function ajax_add_profile()
    {
        $this->_ajaxPreProcess();

        $msg = null;
        $team_member_id = $this->User->TeamMember->getIdByTeamAndUserId($this->current_team_id, $this->my_uid);
        $this->request->data['TeamMember'][0]['id'] = $team_member_id;
        $this->request->data['User']['id'] = $this->User->id = $this->my_uid;
        $this->request->data['User']['photo'] = $_FILES['photo'];
        // キャッシュ削除
        Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');
        if ($this->User->saveAll($this->request->data)) {
            //セットアップガイドステータスの更新
            $this->updateSetupStatusIfNotCompleted();
            $msg = __("Saved user profile.");
        }
        else {
            $msg = __("Failed to save user profile.");
        }
        return $this->_ajaxGetResponse(['msg' => $msg]);
    }

}
