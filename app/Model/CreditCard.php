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

    /**
     * find customer codes by team ids
     *
     * @param array $teamIds
     *
     * @return array
     * @internal param int $teamId
     */
    public function findCustomerCode(array $teamIds): array
    {
        if (empty($teamIds)) {
            return [];
        }
        $options = [
            'conditions' => [
                'team_id' => $teamIds
            ],
            'fields' => [
                'customer_code'
            ]
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.CreditCard.customer_code');
    }

    /*
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
