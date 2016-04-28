<?php
App::uses('AppController', 'Controller');
App::uses('Circle', 'Model');
App::uses('User', 'Model');

/**
 * Setup Controller
 */
class SetupController extends AppController
{
    var $uses = [
        'Circle', 'User'
    ];
    var $components = ['RequestHandler'];
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Security->validatePost = false;
        $this->Security->csrfCheck = false;
        $this->layout = LAYOUT_ONE_COLUMN;
        $this->set('without_footer', true);
    }

    public function index()
    {
        return $this->render();
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

    public function ajax_add_goal()
    {
        return true;
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
        $this->request->allowMethod('put');
        $this->layout = false;
        $this->autoRender = false;

        $msg = null;
        $this->User->id = $this->Auth->user('id');
        $team_member_id = $this->User->TeamMember->getIdByTeamAndUserId($this->current_team_id, $this->my_uid);
        $this->request->data['TeamMember'][0]['id'] = $team_member_id;
        // キャッシュ削除
        Cache::delete($this->User->getCacheKey(CACHE_KEY_MY_PROFILE, true, null, false), 'user_data');
        $this->log($this->request->data);
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
