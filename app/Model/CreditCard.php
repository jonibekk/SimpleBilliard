<?php
App::uses('AppModel', 'Model');

/**
 * CreditCard Model
 */
class CreditCard extends AppModel
{

    public $validate = [
        'team_id'            => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'isUnique' => [
                'rule'     => ['isUnique', ['team_id', 'team_id'], false],
                'required' => 'create'
            ],
        ],
        'payment_setting_id' => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'customer_code'      => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'PaymentSetting'
    ];

    /**
     * get customer code by team id
     *
     * @param int $teamId
     * 
     * @return string
     */
    public function getCustomerCode(int $teamId): string
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId
            ]
        ];
        $res = $this->find('first', $options);
        if (!$res) {
            return '';
        }
        return Hash::get($res, 'CreditCard.customer_code');
    }
}
