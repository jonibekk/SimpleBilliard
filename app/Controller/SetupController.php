<?php
App::uses('AppController', 'Controller');
App::uses('Circle', 'Model');

/**
 * Setup Controller
 */
class SetupController extends AppController
{
    var $uses = [
        'Circle'
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
        $not_joined_circles = $this->Circle->getCirclesForSetupGuide();
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

}
