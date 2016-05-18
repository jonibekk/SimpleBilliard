<?php
App::uses('AppController', 'Controller');

/**
 * Devices Controller
 */
class DevicesController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        //アプリからのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        if ($this->request->params['action'] === 'add') {
            $this->Security->validatePost = false;
        }
    }

    /**
     * デバイス情報を追加する
     *
     * @return CakeResponse
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $user_id = $this->request->data['user_id'];
        $installation_id = $this->request->data['installation_id'];

        $this->log("DeviceController#add:$installation_id\n");

        //デバイス情報を保存する
        $saved = $this->NotifyBiz->saveDeviceInfo($user_id, $installation_id);
        if ($saved === false) {
            return $this->_ajaxGetResponse(['response' => [
                'message' => 'error do not save',
                'user_id' => $user_id,
                'installation_id' => $installation_id,
            ]]);
        }

        return $this->_ajaxGetResponse(['response' => [
            'message' => 'saved',
            'user_id' => $user_id,
            'installation_id' => $installation_id,
        ]]);
    }
}
