<?php
App::uses('AppController', 'Controller');

/**
 * Circles Controller
 *
 * @property Circle $Circle
 */
class CirclesController extends AppController
{
    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $this->Circle->create();

        if ($this->Circle->add($this->request->data)) {
            $this->NotifyBiz->execSendNotify(Notification::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                                             $this->Circle->add_new_member_list);
            $this->Pnotify->outSuccess(__d('gl', "サークルを作成しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "サークルの作成に失敗しました。"));
        }
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function ajax_get_edit_modal($circle_id)
    {
        $this->_ajaxPreProcess();
        $this->request->data = $this->Circle->getEditData($circle_id);
        //htmlレンダリング結果
        $response = $this->render('modal_edit_circle');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    function ajax_select2_init_circle_members($circle_id)
    {
        $this->_ajaxPreProcess();
        $res = $this->Circle->CircleMember->getCircleInitMemberSelect2($circle_id);
        return $this->_ajaxGetResponse($res);
    }

    public function edit($id)
    {
        $this->Circle->id = $id;
        try {
            if (!$this->Circle->exists()) {
                throw new RuntimeException(__('gl', "このサークルは存在しません。"));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $id)) {
                throw new RuntimeException(__('gl', "サークルの変更ができるのはサークル管理者のみです。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('put');
        if ($this->Circle->edit($this->request->data)) {
            $this->NotifyBiz->execSendNotify(Notification::TYPE_CIRCLE_ADD_USER, $this->Circle->id,
                                             $this->Circle->add_new_member_list);
            $this->Pnotify->outSuccess(__d('gl', "サークル設定を保存しました。"));
        }
        else {
            $this->Pnotify->outError(__d('gl', "サークル設定の保存に失敗しました。"));
        }
        $this->redirect($this->referer());
    }

    public function delete($id)
    {
        $this->Circle->id = $id;
        try {
            if (!$this->Circle->exists()) {
                throw new RuntimeException(__('gl', "このサークルは存在しません。"));
            }
            if (!$this->Circle->CircleMember->isAdmin($this->Auth->user('id'), $id)) {
                throw new RuntimeException(__('gl', "サークルの削除ができるのはサークル管理者のみです。"));
            }
        } catch (RuntimeException $e) {
            $this->Pnotify->outError($e->getMessage());
            $this->redirect($this->referer());
        }
        $this->request->allowMethod('post');
        $this->Circle->delete();
        $this->Pnotify->outSuccess(__d('gl', "サークルを削除しました。"));
        $this->redirect($this->referer());
    }

    public function ajax_get_public_circles_modal()
    {
        $this->_ajaxPreProcess();
        $circles = $this->Circle->getPublicCircles();
        $this->set(compact('circles'));
        //エレメントの出力を変数に格納する
        //htmlレンダリング結果
        $response = $this->render('modal_public_circles');
        $html = $response->__toString();

        return $this->_ajaxGetResponse($html);
    }

    public function join()
    {
        $this->request->allowMethod('post');
        if ($this->Circle->CircleMember->joinCircle($this->request->data)) {
            if (!empty($this->Circle->CircleMember->new_joined_circle_list)) {
                foreach ($this->Circle->CircleMember->new_joined_circle_list as $circle_id) {
                    $this->NotifyBiz->execSendNotify(Notification::TYPE_CIRCLE_USER_JOIN, $circle_id);
                }
            }
            $this->Pnotify->outSuccess(__d('gl', "公開サークルの参加設定を保存しました。"));
        }
        else {
            $this->Pnotify->outSuccess(__d('gl', "公開サークルの参加設定の保存に失敗しました。"));
        }
        $this->redirect($this->referer());
    }

}
