<?php
App::uses('AppController', 'Controller');

/**
 * Devices Controller
 *
 * @property Device $Device
 */
class DevicesController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        $allowed_actions = ['add', 'get_version_info'];
        //アプリからのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        if (in_array($this->request->params['action'], $allowed_actions)) {
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

        //デバイス情報を保存する
        $saved = $this->NotifyBiz->saveDeviceInfo($user_id, $installation_id);
        if ($saved === false) {
            return $this->_ajaxGetResponse([
                'response' => [
                    'message'         => 'error do not save',
                    'user_id'         => $user_id,
                    'installation_id' => $installation_id,
                ]
            ]);
        }

        return $this->_ajaxGetResponse([
            'response' => [
                'message'         => 'saved',
                'user_id'         => $user_id,
                'installation_id' => $installation_id,
            ]
        ]);
    }

    public function get_version_info()
    {
        $this->request->allowMethod('post');
        $user_id = $this->request->data['user_id'];
        $installation_id = $this->request->data['installation_id'];
        $current_version = $this->request->data['current_version'];
        try {
            //まずinstalltion_idとuser_idをキーにしてdbからデータとってくる
            $device = $this->Device->find('first',
                [
                    'conditions' =>
                        ['user_id' => $user_id, 'installation_id' => $installation_id]
                ]);
            if (!empty($device)) {
                //あればバージョン情報を取得
                $version = $device['Device']['version'];

            } else {
                //もしなければNifty CloudからDeviceTokenを取得
                $device_info = $this->NotifyBiz->getDeviceInfo($installation_id);
                if (isset($device_info['deviceToken'])) {
                    throw new RuntimeException(__('Device Information not exists'));
                }
                
            }
        } catch (RuntimeException $e) {

        }

    }
}
