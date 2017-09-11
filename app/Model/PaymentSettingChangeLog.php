<?php
App::uses('AppModel', 'Model');
/**
 * PaymentSettingChangeLog Model
 *
 */
class PaymentSettingChangeLog extends AppModel
{
    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'data'    => [
            'notBlank' => [
                'rule' => ['notBlank'],
            ],
        ],
        'del_flg' => [
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
        'PaymentSetting',
    ];

    /**
     * Save Snapshot of PaymentSettings
     *
     * @param $paymentSettingId
     * @param $userId
     *
     * @return bool|mixed
     */
    function saveSnapshot($paymentSettingId, $userId)
    {
        $payment = $this->PaymentSetting->getById($paymentSettingId);
        if (empty($payment)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $paymentData = msgpack_pack($payment);
        $data = [
            'team_id'            => Hash::get($payment, 'team_id'),
            'user_id'            => $userId,
            'payment_setting_id' => $paymentSettingId,
            'data'               => base64_encode($paymentData),
        ];
        $this->create();
        $ret = $this->save($data);
        return $ret;
    }

    /**
     * LatestSnapShot
     *
     * @param int $teamId
     *
     * @return array
     * @internal param $goalId
     */
    function getLatest(int $teamId): array
    {
        $data = $this->find('first', [
            'conditions' => [
                'team_id' => $teamId,
            ],
            'order'      => ['id' => 'desc']
        ]);

        if (empty($data)) {
            return $data;
        }

        $data = Hash::extract($data, 'PaymentSettingChangeLog');
        /** @noinspection PhpUndefinedFunctionInspection */
        $data['plain_data'] = msgpack_unpack(base64_decode($data['data']));
        return $data;
    }
}
