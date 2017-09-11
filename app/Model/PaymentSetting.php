<?php
App::uses('AppModel', 'Model');
App::uses('Invoice', 'Model');

use Goalous\Model\Enum as Enum;
/**
 * PaymentSetting Model
 */
class PaymentSetting extends AppModel
{

    const PAYMENT_TYPE_INVOICE = 0;
    const PAYMENT_TYPE_CREDIT_CARD = 1;

    const CURRENCY_TYPE_JPY = 1;
    const CURRENCY_TYPE_USD = 2;

    const CURRENCY_JPY = 'JPY';
    const CURRENCY_USD = 'USD';

    const CURRENCY_SYMBOLS_EACH_TYPE = [
        self::CURRENCY_TYPE_JPY => "¥",
        self::CURRENCY_TYPE_USD => "$",
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
        'team_id'                        => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'type'                           => [
            'inEnumList' => [
                'rule' => [
                    'inEnumList',
                    "PaymentSetting\Type"
                ],
            ],
            'notBlank'   => [
                'rule'     => 'notBlank',
            ],
        ],
        'currency'                       => [
            'inList'   => [
                'rule' => [
                    'inList',
                    [
                        self::CURRENCY_TYPE_JPY,
                        self::CURRENCY_TYPE_USD
                    ]
                ],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'amount_per_user'                => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'payment_base_day'               => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
            'range'    => [
                // allow 1 ~ 31
                'rule' => ['range', 0, 32]
            ]
        ],
        'company_name'                   => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'isString'  => ['rule' => 'isString'],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_country'                => [
            'notBlank' => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_post_code'              => [
            'maxLength' => ['rule' => ['maxLength', 16]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_region'                 => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'rcacequired' => true,
                'rule'        => 'notBlank',
            ],
        ],
        'company_city'                   => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'company_street'                 => [
            'maxLength' => ['rule' => ['maxLength', 255]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_first_name'      => [
            'maxLength' => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_last_name'       => [
            'maxLength' => ['rule' => ['maxLength', 128]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_tel'             => [
            'maxLength' => ['rule' => ['maxLength', 20]],
            'notBlank'  => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
        ],
        'contact_person_email'           => [
            'notBlank'    => [
                'required' => true,
                'rule'     => 'notBlank',
            ],
            'emailsCheck' => [
                'rule' => ['emailsCheck']
            ],
        ],
    ];

    public $validateJp = [
        'contact_person_first_name_kana' => [
            'notBlank'  => [
                'required' => true,
                'rule' => 'notBlank'
            ],
            'katakanaOnly' => ['rule' => ['katakanaOnly']],
            'maxLength' => ['rule' => ['maxLength', 128]],
        ],
        'contact_person_last_name_kana'  => [
            'notBlank'  => [
                'required' => true,
                'rule' => 'notBlank'
            ],
            'katakanaOnly' => ['rule' => ['katakanaOnly']],
            'maxLength' => ['rule' => ['maxLength', 128]],
        ],
    ];

    public $validateCreate = [
        'team_id' => [
            'isUnique' => [
                'rule'     => ['isUnique', ['team_id']],
                'required' => 'create'
            ],
        ],
    ];

    public $belongsTo = [
        'Team',
    ];

    public $hasMany = [
        'CreditCard',
        'Invoice',
    ];

    /**
     * @param int $teamId
     *
     * @return array|null
     */
    public function getCcByTeamId(int $teamId)
    {
        $options = [
            'fields' => [
                'PaymentSetting.id',
                'PaymentSetting.team_id',
                'PaymentSetting.type',
                'PaymentSetting.currency',
                'PaymentSetting.amount_per_user',
                'PaymentSetting.payment_base_day',
                'PaymentSetting.company_country',
                'CreditCard.customer_code'
            ],
            'conditions' => [
                'PaymentSetting.team_id' => $teamId,
            ],
            'joins'      => [
                [
                    'table'      => 'credit_cards',
                    'alias'      => 'CreditCard',
                    'type'       => 'INNER',
                    'conditions' => [
                        'PaymentSetting.team_id = CreditCard.team_id',
                        'CreditCard.del_flg' => false
                    ]
                ]
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
    public function getInvoiceByTeamId(int $teamId)
    {
        $options = [
            'fields' => [
                'PaymentSetting.id',
                'PaymentSetting.team_id',
                'PaymentSetting.currency',
                'PaymentSetting.amount_per_user',
                'PaymentSetting.payment_base_day',
                'PaymentSetting.company_country',
                'Invoice.credit_status',
                'Invoice.company_name',
                'Invoice.company_post_code',
                'Invoice.company_region',
                'Invoice.company_city',
                'Invoice.company_street',
                'Invoice.contact_person_first_name',
                'Invoice.contact_person_first_name_kana',
                'Invoice.contact_person_last_name',
                'Invoice.contact_person_last_name_kana',
                'Invoice.contact_person_tel',
                'Invoice.contact_person_email',
            ],
            'conditions' => [
                'PaymentSetting.team_id' => $teamId,
            ],
            'joins'      => [
                [
                    'table'      => 'invoices',
                    'alias'      => 'Invoice',
                    'type'       => 'INNER',
                    'conditions' => [
                        'PaymentSetting.team_id = Invoice.team_id',
                        'Invoice.del_flg' => false
                    ]
                ]
            ]
        ];
        $res = $this->find('first', $options);
        return $res;
    }

    /**
     * @param int $paymentType
     *
     * @return array
     */
    public function findMonthlyChargeTeams(Enum\PaymentSetting\Type $paymentType): array
    {
        $options = [
            'fields'     => [
                'PaymentSetting.id',
                'PaymentSetting.team_id',
                'PaymentSetting.payment_base_day',
                'Team.timezone',
            ],
            'conditions' => [
                'PaymentSetting.type'    => $paymentType->getValue(),
                'PaymentSetting.del_flg' => false
            ],
            'joins'      => [
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
        if ($paymentType->getValue() == Enum\PaymentSetting\Type::CREDIT_CARD) {
            $options['joins'][] = [
                'type'       => 'INNER',
                'table'      => 'credit_cards',
                'alias'      => 'CreditCard',
                'conditions' => [
                    'PaymentSetting.id = CreditCard.payment_setting_id',
                    'CreditCard.del_flg' => false
                ],
            ];
        } else {
            $options['joins'][] = [
                'type'       => 'INNER',
                'table'      => 'invoices',
                'alias'      => 'Invoice',
                'conditions' => [
                    'PaymentSetting.id = Invoice.payment_setting_id',
                    'Invoice.credit_status' => Invoice::CREDIT_STATUS_OK,
                    'Invoice.del_flg'       => false
                ],
            ];
        }
        $res = $this->find('all', $options);

        return $res;
    }

    /**
     * get amount per user by team id
     *
     * @param int $teamId
     *
     * @return int|null
     */
    public function getAmountPerUser(int $teamId)
    {
        $options = [
            'conditions' => [
                'team_id' => $teamId
            ],
            'fields'     => ['amount_per_user']
        ];
        $res = $this->find('first', $options);
        if (!$res) {
            return null;
        }

        return Hash::get($res, 'PaymentSetting.amount_per_user');
    }

    /**
     * Get by team id
     *
     * @param int $teamId
     *
     * @return array
     */
    public function getUnique(int $teamId): array
    {
        $res = $this->findByTeamId($teamId);
        if (empty($res)) {
            return [];
        }
        return Hash::get($res, 'PaymentSetting');
    }

}
