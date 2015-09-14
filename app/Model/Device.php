<?php
App::uses('AppModel', 'Model');

/**
 * Device Model
 *
 * @property User $User
 */
class Device extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'device_token' => [
            'notEmpty' => [
                'rule' => ['notEmpty'],
            ],
        ],
        'os_type'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'      => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'User',
    ];

    /**
     * デバイスを追加する
     *
     * @param $data
     *
     * @return bool|mixed
     */
    function add($data)
    {
        if (!isset($data['Device']) || empty($data['Device'])) {
            return false;
        }

        $data['Device']['user_id'] |= $this->my_uid;
        $this->set($data['Device']);
        if (!$this->validates()) {
            return false;
        }
        $this->create();
        $res = $this->saveAll($data);
        return $res;
    }

    /**
     * ユーザーId でDeviceを取得する
     *
     * @param $user_id
     *
     * @return array|bool|null
     */
    function getDevicesByUserId($user_id)
    {
        if (empty($user_id)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Device.user_id' => $user_id,
                'Device.del_flg' => false,
            ],
        ];

        $data = $this->find('all', $options);
        return $data;
    }

    /**
     * ユーザーIDでDevice.device_tokenのみを配列で取得する
     * これがメインメソッドで、user_idで取得するのは自明なのでメソッド名は短めにしてみた
     *
     * @param $user_id
     *
     * @return array|bool device_tokenの配列
     */
    function getDeviceTokens($user_id)
    {
        $devices = $this->getDevicesByUserId($user_id);

        if (empty($devices)) {
            return false;
        }

        $deviceTokens = [];

        foreach ($devices as $d) {
            $deviceTokens[] = $d['Device']['device_token'];
        }

        return $deviceTokens;
    }

}
