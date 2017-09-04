<?php
App::uses('AppModel', 'Model');

use Goalous\Model\Enum as Enum;

/**
 * ChargeHistory Model
 */
class ChargeHistory extends AppModel
{
    // TODO.Payment: Change to enum and remove defined these
    const PAYMENT_TYPE_INVOICE = 0;
    const PAYMENT_TYPE_CREDIT_CARD = 1;

    // TODO.Payment: Change to enum and remove defined these
    const CHARGE_TYPE_MONTHLY = 0;
    const CHARGE_TYPE_ADD_USER = 1;
    const CHARGE_TYPE_ACTIVATE_USER = 2;

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
                'rule' => 'notBlank',
            ],
        ],
        'payment_type'     => [
            'inEnumList' => [
                'rule' => [
                    'inEnumList',
                    "PaymentSetting\Type"
                ],
            ],
            'notBlank'   => [
                'rule' => 'notBlank',
            ],
        ],
        'charge_type'      => [
            'inEnumList' => [
                'rule' => [
                    'inEnumList',
                    "ChargeHistory\ChargeType"
                ],
            ],
            'notBlank'   => [
                'rule' => 'notBlank',
            ],
        ],
        'amount_per_user'  => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'total_amount'     => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'tax'              => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'charge_users'     => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'currency'         => [
            'inList'   => [
                'rule' => [
                    'inList',
                    [
                        PaymentSetting::CURRENCY_TYPE_JPY,
                        PaymentSetting::CURRENCY_TYPE_USD
                    ]
                ],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'charge_datetime'  => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'payment_type'     => [
            'inEnumList' => [
                'rule' => [
                    'inEnumList',
                    "ChargeHistory\ResultType"
                ],
            ],
            'notBlank'   => [
                'rule' => 'notBlank',
            ],
        ],
        'max_charge_users' => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
    ];

    /**
     * Get latest max charge users
     *
     * @param int $teamId
     *
     * @return int
     */
    function getLatestMaxChargeUsers(int $teamId): int
    {
        $res = $this->find('first', [
                'fields'     => ['max_charge_users'],
                'conditions' => [
                    'team_id' => $teamId,
                ],
                'order'      => ['id' => 'DESC'],
            ]
        );
        return (int)Hash::get($res, 'ChargeHistory.max_charge_users');
    }

    /**
     * returns last ChargeHistory of team
     * if team has no ChargeHistory, this returns empty array
     *
     * @param int $teamId
     *
     * @return array
     */
    function getLastChargeHistoryByTeamId(int $teamId): array
    {
        $res = $this->find('first', [
                'conditions' => [
                    'team_id' => $teamId,
                ],
                'order'      => ['created' => 'DESC'],
            ]
        );
        if ($res === false) {
            return [];
        }
        return Hash::get($res, 'ChargeHistory') ?? [];
    }

    /**
     * Filter: team_id and charge date(Y-m-d 00:00:00　〜　Y-m-d 23:59:59)
     *
     * @param int    $teamId
     * @param string $date
     *
     * @return array
     */
    public function getByChargeDate(int $teamId, string $date): array
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $team = $Team->getById($teamId);

        $dateStart = AppUtil::getStartTimestampByTimezone($date, $team['timezone']);
        $dateEnd = AppUtil::getEndTimestampByTimezone($date, $team['timezone']);
        $options = [
            'fields'     => [
                'id',
                'charge_datetime'
            ],
            'conditions' => [
                'team_id'            => $teamId,
                'charge_datetime >=' => $dateStart,
                'charge_datetime <=' => $dateEnd,
                'del_flg'            => false
            ],
        ];
        return $this->find('first', $options);
    }

    /**
     * find target histories for invoice.
     * It should be not charged yet.
     *
     * @param int $teamId
     * @param int $timestamp
     *
     * @return array
     */
    public function findForInvoiceBeforeTs(int $teamId, int $timestamp)
    {
        $options = [
            'conditions' => [
                'ChargeHistory.team_id'            => $teamId,
                'ChargeHistory.payment_type'       => self::PAYMENT_TYPE_INVOICE,
                'ChargeHistory.charge_type'        => [self::CHARGE_TYPE_ACTIVATE_USER, self::CHARGE_TYPE_ADD_USER],
                'ChargeHistory.charge_datetime <=' => $timestamp,
                'InvoiceHistoriesChargeHistory.id' => null,
                'InvoiceHistory.id'                => null,
            ],
            'joins'      => [
                [
                    'type'       => 'LEFT',
                    'table'      => 'invoice_histories_charge_histories',
                    'alias'      => 'InvoiceHistoriesChargeHistory',
                    'conditions' => [
                        'ChargeHistory.id = InvoiceHistoriesChargeHistory.charge_history_id',
                        'InvoiceHistoriesChargeHistory.del_flg' => false,
                    ]
                ],
                [
                    'type'       => 'LEFT',
                    'table'      => 'invoice_histories',
                    'alias'      => 'InvoiceHistory',
                    'conditions' => [
                        'InvoiceHistory.id = InvoiceHistoriesChargeHistory.invoice_history_id',
                        'InvoiceHistory.del_flg' => false,
                    ]
                ],
            ],
        ];
        $res = $this->find('all', $options);
        return Hash::extract($res, '{n}.ChargeHistory');
    }

    /**
     * @param int $teamId
     * @param int $time
     * @param int $subTotalCharge
     * @param int $tax
     * @param int $amountPerUser
     * @param int $usersCount
     * @param int $currencyType
     *
     * @return mixed
     */
    public function addInvoiceMonthlyCharge(
        int $teamId,
        int $time,
        int $subTotalCharge,
        int $tax,
        int $amountPerUser,
        int $usersCount,
        int $currencyType = PaymentSetting::CURRENCY_TYPE_JPY
    ) {
        $historyData = [
            'team_id'          => $teamId,
            'payment_type'     => PaymentSetting::PAYMENT_TYPE_INVOICE,
            'charge_type'      => self::CHARGE_TYPE_MONTHLY,
            'amount_per_user'  => $amountPerUser,
            'total_amount'     => $subTotalCharge,
            'tax'              => $tax,
            'charge_users'     => $usersCount,
            'currency'         => $currencyType,
            'charge_datetime'  => $time,
            'result_type'      => Enum\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users' => $usersCount
        ];
        $this->clear();
        $ret = $this->save($historyData);
        $ret = Hash::extract($ret, 'ChargeHistory');
        return $ret;
    }
}
