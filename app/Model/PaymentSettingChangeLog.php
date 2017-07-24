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
        $payment = $this->PaymentSetting->findById($paymentSettingId);
        $payment = Hash::get($payment, 'PaymentSetting');
        if (empty($payment)) {
            return false;
        }
        /** @noinspection PhpUndefinedFunctionInspection */
        $paymentData = msgpack_pack($payment);
        $data = [
            'team_id'            => $this->current_team_id,
            'user_id'            => $userId,
            'payment_setting_id' => $paymentSettingId,
            'data'               => base64_encode($paymentData),
        ];
        $this->create();
        $ret = $this->save($data);
        return $ret;
    }
}
