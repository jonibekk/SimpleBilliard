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
                        PaymentSetting::CURRENCY_JPY,
                        PaymentSetting::CURRENCY_USD
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
}
