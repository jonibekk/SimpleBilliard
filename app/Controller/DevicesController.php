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
            //まずinstallation_idとuser_idをキーにしてdbからデータとってくる
            $device = $this->Device->find('first',
                [
                    'conditions' =>
                        ['user_id' => $user_id, 'installation_id' => $installation_id]
                ]);
            if (empty($device)) {
                //もしなければNifty CloudからDeviceTokenを取得
                $device_info = $this->NotifyBiz->getDeviceInfo($installation_id);
                if (isset($device_info['deviceToken'])) {
                    throw new RuntimeException(__('Device Information not exists'));
                }
                //device_tokenとuser_idをキーにしてdbからデータ取ってくる
                $device = $this->Device->find('first',
                    [
                        'conditions' =>
                            ['user_id' => $user_id, 'device_token' => $device_info['deviceToken']]
                    ]);
                if (empty($device)) {
                    throw new RuntimeException(__('Device Information not exists'));
                }
                //installation_idを保存
                $this->Device->saveField('installation_id', $installation_id);
            }
            /* @var AppMeta $AppMeta */
            $AppMeta = ClassRegistry::init('AppMeta');
            $app_metas = $AppMeta->getMetas();
            if (count($app_metas) < 4) {
                $this->log('App Meta does not exists.');
                throw new RuntimeException(__('Internal Server Error'));
            }

            //バージョン情報を比較
            $key_name = $device['Device']['os_type'] == Device::OS_TYPE_IOS ? "iOS_version" : "android_version";
            $store_url = $device['Device']['os_type'] == Device::OS_TYPE_IOS ? $app_metas['iOS_install_url'] : $app_metas['android_install_url'];

            $is_latest_version = version_compare($current_version, $app_metas[$key_name]) === -1 ? false : true;
            $message = __('This device is latest version.');
            //最新バージョンでなければdbのバージョン情報を更新
            if ($is_latest_version === false) {
                $this->Device->saveField('version', $current_version);
                $message = __('This device is not latest version.');
            }
            $ret_array = [
                'response' => [
                    'error'             => false,
                    'message'           => $message,
                    'is_latest_version' => $is_latest_version,
                    'user_id'           => $user_id,
                    'installation_id'   => $installation_id,
                    'store_url'         => $store_url,
                ]
            ];
        } catch (RuntimeException $e) {
            $ret_array = [
                'response' => [
                    'error'   => true,
                    'message' => $e->getMessage(),
                ]
            ];
        }

        return $this->_ajaxGetResponse($ret_array);
    }
}
