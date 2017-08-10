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
     * Find CreditCard within the customer code list.
     *
     * @param array $customerCodes
     *
     * @return array|null
     */
    public function findByCustomerCodes(array $customerCodes)
    {
        $options = [
            'fields'     => [
                'id',
                'team_id',
                'customer_code',
                'payment_setting_id',
            ],
            'conditions' => [
                'del_flg'    => false,
                'customer_code'  => $customerCodes,
            ],
        ];
        $res = $this->find('all', $options);

        return $res;
    }
}
