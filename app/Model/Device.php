<?php
App::uses('AppModel', 'Model');

/**
 * Device Model
 *
 * @property User $User
 */
class Device extends AppModel
{
    const OS_TYPE_IOS = 0;
    const OS_TYPE_ANDROID = 1;
    const OS_TYPE_OTHER = 99;

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'user_id'         => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
            'notZero'  => [
                'rule' => ['naturalNumber'],
            ],
        ],
        'device_token'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'installation_id' => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
            'isUnique' => [
                // is unique with installation_id and del_flg
                'rule' => ['isUnique', ['installation_id', 'del_flg'], false],
            ]
        ],
        'os_type'         => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'         => [
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
     * ユーザーId,device_token でDeviceを取得する
     *
     * @param $user_id
     * @param $device_token
     *
     * @return array|bool|null
     */
    function getDevicesByUserIdAndDeviceToken($user_id, $device_token)
    {
        if (empty($user_id) || empty($device_token)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Device.user_id'      => $user_id,
                'Device.device_token' => $device_token,
                'Device.del_flg'      => false,
            ],
        ];

        $data = $this->find('all', $options);
        return $data;
    }

    /**
     * Return the device by its Token
     *
     * @param string $deviceToken
     *
     * @return array|bool|null
     */
    function getDeviceByToken(string $deviceToken)
    {
        if (empty($deviceToken)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Device.device_token' => $deviceToken,
                'Device.del_flg'      => false,
            ],
        ];

        $data = $this->find('first', $options);
        return $data;
    }

    /**
     * Return the device by its installation id
     *
     * @param string $installationId
     *
     * @return array|bool|null
     */
    function getDeviceByInstallationId(string $installationId)
    {
        if (empty($installationId)) {
            return false;
        }

        $options = [
            'conditions' => [
                'Device.installation_id' => $installationId,
                'Device.del_flg'      => false,
            ],
        ];

        $data = $this->find('first', $options);
        return $data;
    }

    /**
     * Return device_token and installation_id for all devices of user
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
            $deviceTokens[] = [
                'device_token' => $d['Device']['device_token'],
                'installation_id' => $d['Device']['installation_id'],
                'os_type' => $d['Device']['os_type']
            ];
        }

        return $deviceTokens;
    }

    function isInstalledMobileApp($user_id)
    {
        $options = [
            'conditions' => [
                'Device.user_id' => $user_id
            ],
            'fields'     => [
                'Device.id'
            ]
        ];

        return (bool)$this->findWithoutTeamId('first', $options);
    }

}
