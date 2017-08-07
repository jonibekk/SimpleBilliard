<?php
App::uses('AppModel', 'Model');

/**
 * ChargeHistory Model
 */
class ChargeHistory extends AppModel
{
    const TRANSACTION_RESULT_ERROR = 0;
    const TRANSACTION_RESULT_SUCCESS = 1;
    const TRANSACTION_RESULT_FAIL = 2;

    const PAYMENT_TYPE_INVOICE = 0;
    const PAYMENT_TYPE_CREDIT_CARD = 1;

    const CHARGE_TYPE_MONTHLY = 0;
    const CHARGE_TYPE_ADD_USER = 1;
    const CHARGE_TYPE_ACTIVATE_USER = 2;

    /**
     * Get latest max charge users
     *
     * @return int
     */
    function getLatestMaxChargeUsers(): int
    {
        $res = $this->find('first', [
                'fields'     => ['max_charge_users'],
                'conditions' => [
                    'team_id' => $this->current_team_id,
                ],
                'order'      => ['id' => 'DESC'],
            ]
        );
        return (int)Hash::get($res, 'ChargeHistory.max_charge_users');
    }

    /* Validation rules
    *
    * @var array
    */
    public $validate = [
        'team_id'          => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'payment_type'     => [
            'inList'   => [
                'rule' => [
                    'inList',
                    [
                        PaymentSetting::PAYMENT_TYPE_INVOICE,
                        PaymentSetting::PAYMENT_TYPE_CREDIT_CARD
                    ]
                ],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'charge_type'      => [
            'inList'   => [
                'rule' => [
                    'inList',
                    [
                        PaymentSetting::CHARGE_TYPE_MONTHLY_FEE,
                        PaymentSetting::CHARGE_TYPE_USER_INCREMENT_FEE,
                        PaymentSetting::CHARGE_TYPE_USER_ACTIVATION_FEE
                    ]
                ],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'amount_per_user'  => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'total_amount'     => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'charge_users'     => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'currency'         => [
            'inList'   => [
                'rule' => [
                    'inList',
                    [
                        PaymentSetting::CURRENCY_CODE_JPY,
                        PaymentSetting::CURRENCY_CODE_USD
                    ]
                ],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'charge_datetime'  => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'result_type'      => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'max_charge_users' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
    ];

    /**
     * Filter: team_id and charge date(Y-m-d 00:00:00　〜　Y-m-d 23:59:59)
     *
     * @param int $teamId
     * @param string $date
     *
     * @return array
     */
    public function getByChargeDate(int $teamId, string $date): array
    {
        $dateStart = AppUtil::getStartTimestampByTimezone($date);
        $dateEnd = AppUtil::getEndTimestampByTimezone($date);
        $options = [
            'fields' => [
                'id',
                'charge_datetime'
            ],
            'conditions' => [
                'team_id' => $teamId,
                'charge_datetime >=' => $dateStart,
                'charge_datetime <=' => $dateEnd,
                'del_flg' => false
            ],
        ];
        return $this->find('first', $options);
    }

}
