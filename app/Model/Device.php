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

        error_log("FURU:".$res."\n",3,"/tmp/test.log");
        return $res;
    }

    function getDevicesByUserId($user_id)
    {
        $ret = [];
        $ret[] = ['device_token' => 'ios_dummy1'];
        $ret[] = ["2"];
        return $ret;
    }

}
