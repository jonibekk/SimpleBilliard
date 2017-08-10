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

    const CURRENCY_LABELS = [
        self::CURRENCY_JPY => "¥",
        self::CURRENCY_USD => "$",
    ];

    function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        //
        $countries = Configure::read("countries");
        $countryCodes = Hash::extract($countries, '{n}.code');
        $this->validate['company_country']['inList'] = [
            'rule' => [
                'inList',
                $countryCodes
            ]
        ];

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
        'type'     => [
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
        'company_name'     => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_country'  => [
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_post_code'       => [
            'maxLength'    => ['rule' => ['maxLength', 16]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_region'       => [
            'maxLength'    => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'rcacequired' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_city'       => [
            'maxLength'    => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_street'       => [
            'maxLength'    => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_tel'          => [
            'maxLength' => ['rule' => ['maxLength', 20]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'phoneNo'   => [
                'rule' => 'phoneNo',
            ],
        ],
        'contact_person_first_name'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_first_name_kana'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'     => ['rule' => 'notBlank'],
        ],
        'contact_person_last_name'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_last_name_kana'         => [
            'maxLength'    => ['rule' => ['maxLength', 128]],
            'notBlank'     => ['rule' => 'notBlank'],
        ],
        'contact_person_tel'       => [
            'maxLength'    => ['rule' => ['maxLength', 20]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_email' => [
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

    /**
     * @param int $teamId
     *
     * @return array|null
     */
    public function findMonthlyChargeCcTeams()
    {
        $options = [
            'fields'     => [
                'PaymentSetting.id',
                'PaymentSetting.team_id',
                'PaymentSetting.payment_base_day',
                'Team.timezone',
            ],
            'conditions' => [
                'PaymentSetting.type'    => PaymentSetting::PAYMENT_TYPE_CREDIT_CARD,
                'PaymentSetting.del_flg' => false
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'credit_cards',
                    'alias'      => 'CreditCard',
                    'conditions' => [
                        'PaymentSetting.id = CreditCard.payment_setting_id',
                        'CreditCard.del_flg' => false
                    ],
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'teams',
                    'alias'      => 'Team',
                    'conditions' => [
                        'PaymentSetting.team_id = Team.id',
                        'Team.service_use_status' => Team::SERVICE_USE_STATUS_PAID,
                        'Team.del_flg'            => false,
                    ],
                ],
            ]
        ];
        $res = $this->find('all', $options);

        return $res;
    }
}
