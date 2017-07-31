<?php
App::uses('AppModel', 'Model');

/**
 * PaymentSetting Model
 */
class PaymentSetting extends AppModel
{

    const PAYMENT_TYPE_INVOICE = 0;
    const PAYMENT_TYPE_CREDIT_CARD = 1;

    const CURRENCY_CODE_JPY = 1;
    const CURRENCY_CODE_USD = 2;

    const CURRENCY_JPY = 'JPY';
    const CURRENCY_USD = 'USD';

    const CHARGE_TYPE_MONTHLY_FEE = 0;
    const CHARGE_TYPE_USER_INCREMENT_FEE = 1;
    const CHARGE_TYPE_USER_ACTIVATION_FEE = 2;

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
        'type'             => [
            'inList'   => [
                'rule' => [
                    'inList',
                    [
                        self::PAYMENT_TYPE_INVOICE,
                        self::PAYMENT_TYPE_CREDIT_CARD
                    ]
                ],
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
                        self::CURRENCY_CODE_JPY,
                        self::CURRENCY_CODE_USD
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
        'payer_name'       => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_name'     => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_address'  => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_tel'      => [
            'maxLength' => ['rule' => ['maxLength', 20]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'phoneNo'   => [
                'rule' => 'phoneNo',
            ],
        ],
        'payment_base_day' => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'range'    => [
                // allow 1 ~ 31
                'rule' => ['range', 0, 32]
            ]
        ],
        'email'            => [
            'notBlank'    => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'emailsCheck' => [
                'rule' => ['emailsCheck']
            ],
        ],
    ];

    public $validateCreate = [
        'team_id' => [
            'isUnique' => [
                'rule'     => ['isUnique', ['team_id', 'team_id'], false],
                'required' => 'create'
            ],
        ],
    ];

    public $belongsTo = [
        'Team',
    ];

    public $hasMany = [
        'CreditCard',
    ];

    /**
     * @param int $teamId
     *
     * @return array|null
     */
    public function getByTeamId(int $teamId)
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId,
            ],
            'contain'    => [
                'CreditCard',
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }
}
