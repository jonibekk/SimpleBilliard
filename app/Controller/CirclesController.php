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

    public function edit()
    {
        $this->request->allowMethod('put');
        if (isset($this->request->data['Circle']) && !empty($this->request->data['Circle'])) {
            if ($this->Circle->edit($this->request->data)) {
                $this->Pnotify->outSuccess(__d('gl', "サークル設定を保存しました。"));
            }
            else {
                $this->Pnotify->outError(__d('gl', "サークル設定の保存に失敗しました。"));
            }
        }
        $this->redirect($this->referer());
    }

}
