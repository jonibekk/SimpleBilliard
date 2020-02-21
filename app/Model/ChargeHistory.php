<?php
App::uses('AppModel', 'Model');

use Goalous\Enum as Enum;

/**
 * ChargeHistory Model
 */
class ChargeHistory extends AppModel
{
    /**
     * @deprecated use \Goalous\Enum\Model\PaymentSetting\Type
     */
    const PAYMENT_TYPE_INVOICE = 0;
    /**
     * @deprecated use \Goalous\Enum\Model\PaymentSetting\Type
     */
    const PAYMENT_TYPE_CREDIT_CARD = 1;

    /**
     * @deprecated use \Goalous\Enum\Model\ChargeHistory\ChargeType
     */
    const CHARGE_TYPE_MONTHLY = 0;
    /**
     * @deprecated use \Goalous\Enum\Model\ChargeHistory\ChargeType
     */
    const CHARGE_TYPE_ADD_USER = 1;
    /**
     * @deprecated use \Goalous\Enum\Model\ChargeHistory\ChargeType
     */
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
        'charge_type'      => [
            'inEnumList' => [
                'rule' => [
                    'inEnumList',
                    "Goalous\Enum\Model\ChargeHistory\ChargeType"
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
                    "Goalous\Enum\Model\ChargeHistory\ResultType"
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
     * @param int    $teamId
     * @param string $orderField
     *
     * @return array
     */
    function getLastChargeHistoryByTeamId(int $teamId, string $orderField = 'created'): array
    {
        $res = $this->find('first', [
                'conditions' => [
                    'team_id' => $teamId,
                ],
                'order'      => [$orderField => 'DESC'],
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
                'charge_type'        => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
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
                'ChargeHistory.charge_type'        => [
                    Enum\Model\ChargeHistory\ChargeType::USER_INCREMENT_FEE,
                    Enum\Model\ChargeHistory\ChargeType::USER_ACTIVATION_FEE,
                    Enum\Model\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF,
                ],
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
     * find charge histories related failed invoice order.
     *
     * @param int $teamId
     * @param int $invoiceHistoryId
     *
     * @return array
     */
    public function findRelatedFailedInvoiceOrder(int $teamId, int $invoiceHistoryId)
    {
        $options = [
            'conditions' => [
                'ChargeHistory.team_id' => $teamId,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'invoice_histories_charge_histories',
                    'alias'      => 'InvoiceHistoriesChargeHistory',
                    'conditions' => [
                        'ChargeHistory.id = InvoiceHistoriesChargeHistory.charge_history_id',
                        'InvoiceHistoriesChargeHistory.invoice_history_id' => $invoiceHistoryId,
                        'InvoiceHistoriesChargeHistory.del_flg'            => false,
                    ]
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'invoice_histories',
                    'alias'      => 'InvoiceHistory',
                    'conditions' => [
                        'InvoiceHistoriesChargeHistory.invoice_history_id = InvoiceHistory.id',
                        'InvoiceHistory.order_status' => Enum\Model\Invoice\CreditStatus::NG,
                        'InvoiceHistory.del_flg'      => false,
                    ]
                ],
            ],
        ];
        $res = $this->find('all', $options);
        return Hash::extract($res, '{n}.ChargeHistory');
    }

    /**
     * find charge histories by invoice order code.
     *
     * @param int    $teamId
     * @param string $orderCode
     *
     * @return array
     */
    public function findByInvoiceOrderCode(int $teamId, string $orderCode)
    {
        $options = [
            'conditions' => [
                'ChargeHistory.team_id' => $teamId,
            ],
            'joins'      => [
                [
                    'type'       => 'INNER',
                    'table'      => 'invoice_histories_charge_histories',
                    'alias'      => 'InvoiceHistoriesChargeHistory',
                    'conditions' => [
                        'ChargeHistory.id = InvoiceHistoriesChargeHistory.charge_history_id',
                        'InvoiceHistoriesChargeHistory.del_flg'            => false,
                    ]
                ],
                [
                    'type'       => 'INNER',
                    'table'      => 'invoice_histories',
                    'alias'      => 'InvoiceHistory',
                    'conditions' => [
                        'InvoiceHistoriesChargeHistory.invoice_history_id = InvoiceHistory.id',
                        'InvoiceHistory.system_order_code'      => $orderCode,
                        'InvoiceHistory.del_flg'      => false,
                    ]
                ],
            ],
        ];
        $res = $this->find('all', $options);
        return Hash::extract($res, '{n}.ChargeHistory');
    }

    /**
     * @param int  $teamId
     * @param int  $time
     * @param int  $subTotalCharge
     * @param int  $tax
     * @param int  $amountPerUser
     * @param int  $usersCount
     * @param null $userId
     * @param int  $currencyType
     * @param null $campaignTeamId
     * @param null $pricePlanPurchaseId
     *
     * @return array|mixed
     * @throws Exception
     */
    public function addInvoiceMonthlyCharge(
        int $teamId,
        int $time,
        int $subTotalCharge,
        int $tax,
        int $amountPerUser,
        int $usersCount,
        $userId = null,
        int $currencyType = PaymentSetting::CURRENCY_TYPE_JPY,
        $campaignTeamId = null,
        $pricePlanPurchaseId = null
    ) {
        $historyData = [
            'team_id'                     => $teamId,
            'user_id'                     => $userId,
            'payment_type'                => PaymentSetting::PAYMENT_TYPE_INVOICE,
            'charge_type'                 => self::CHARGE_TYPE_MONTHLY,
            'amount_per_user'             => $amountPerUser,
            'total_amount'                => $subTotalCharge,
            'tax'                         => $tax,
            'charge_users'                => $usersCount,
            'currency'                    => $currencyType,
            'charge_datetime'             => $time,
            'result_type'                 => Enum\Model\ChargeHistory\ResultType::SUCCESS,
            'max_charge_users'            => $usersCount,
            'campaign_team_id'            => $campaignTeamId,
            'price_plan_purchase_team_id' => $pricePlanPurchaseId,
        ];
        $this->clear();
        $ret = $this->save($historyData);
        $ret = Hash::extract($ret, 'ChargeHistory');
        return $ret;
    }

    /**
     * Get data for receipt
     *
     * @param int $historyId
     *
     * @return array
     */
    public function getForReceipt(int $historyId): array
    {
        $options = [
            'conditions' => [
                'ChargeHistory.id' => $historyId,
            ],
            'fields'     => [
                'ChargeHistory.id',
                'ChargeHistory.team_id',
                'ChargeHistory.charge_datetime',
                'ChargeHistory.tax',
                'ChargeHistory.total_amount',
                'ChargeHistory.payment_type',
                'ChargeHistory.charge_users',
                'ChargeHistory.charge_type',
                'ChargeHistory.result_type',
                'ChargeHistory.reorder_charge_history_id',
                'Team.name',
                'PaymentSetting.company_country',
                'PaymentSetting.company_name',
                'PaymentSetting.contact_person_email',
                'CreditCard.customer_code',
            ],
            'joins'      => [
                [
                    'table'      => 'teams',
                    'alias'      => 'Team',
                    'type'       => 'INNER',
                    'conditions' => [
                        'ChargeHistory.team_id = Team.id'
                    ]
                ],
                [
                    'table'      => 'payment_settings',
                    'alias'      => 'PaymentSetting',
                    'type'       => 'INNER',
                    'conditions' => [
                        'ChargeHistory.team_id = PaymentSetting.team_id',
                        'PaymentSetting.del_flg' => false
                    ]
                ],
                [
                    'table'      => 'credit_cards',
                    'alias'      => 'CreditCard',
                    'type'       => 'LEFT',
                    'conditions' => [
                        'ChargeHistory.team_id = CreditCard.team_id',
                        'CreditCard.del_flg' => false
                    ]
                ],
            ]
        ];
        $res = $this->find('first', $options);
        if (!$res) {
            return [];
        }
        return $res;
    }

    /**
     * Get by charge datetime range
     *
     * @param int $startTimestamp
     * @param int $endTimestamp
     *
     * @return array
     */
    public function findByChargeDatetimeRange(int $startTimestamp, int $endTimestamp): array
    {
        $options = [
            'conditions' => [
                'charge_datetime >=' => $startTimestamp,
                'charge_datetime <=' => $endTimestamp,
                'result_type !='     => Enum\Model\ChargeHistory\ResultType::ERROR,
                // TODO: Remove this condition and add checking for inconsistency of campaign team's charge in DetectInconsistentChargeShell.
                'campaign_team_id'   => null
            ],
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::extract($res, '{n}.ChargeHistory');
    }
    /**
     * Get monthly charge history after ts 
     *
     * @param int $startTimestamp
     * @param int $endTimestamp
     *
     * @return array
     */
    public function getRecordAfterTs(int $timestamp): array
    {
        $options = [
            'conditions' => [
                'charge_datetime >=' => $timestamp,
                'charge_type ='      => Enum\Model\ChargeHistory\ChargeType::MONTHLY_FEE,
                'result_type !='     => Enum\Model\ChargeHistory\ResultType::ERROR,
                'del_flg ='         => false,
                // TODO: Remove this condition and add checking for inconsistency of campaign team's charge in DetectInconsistentChargeShell.
                // 'campaign_team_id'   => null
            ],
        ];
        $res =  $this->find('first', $options);
        if (empty($res)) {
            return [];
        }
        return $res;
    }
}
