<?php
App::uses('AppModel', 'Model');

/**
 * InvoiceHistory Model
 *
 * @property Team $Team
 */
class InvoiceHistory extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'order_datetime'    => [
            'numeric'  => [
                'rule' => ['numeric'],
            ],
            'notBlank' => [
                'rule' => 'notBlank',
            ],
        ],
        'system_order_code' => [
            'notBlank' => [
                'rule' => ['notBlank'],
                'on'   => 'update',
            ],
        ],
        'order_status'      => [
            'numeric' => [
                'rule' => ['numeric'],
            ],
        ],
        'del_flg'           => [
            'boolean' => [
                'rule' => ['boolean'],
            ],
        ],
    ];

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = [
        'Team' => [
            'className'  => 'Team',
            'foreignKey' => 'team_id',
            'conditions' => '',
            'fields'     => '',
            'order'      => ''
        ]
    ];

    /**
     * Filter: team_id and charge date
     *
     * @param int    $teamId
     * @param string $date
     *
     * @return array
     */
    public function getByOrderDate(int $teamId, string $date): array
    {
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $team = $Team->getById($teamId);

        $dateStart = AppUtil::getStartTimestampByTimezone($date, $team['timezone']);
        $dateEnd = AppUtil::getEndTimestampByTimezone($date, $team['timezone']);

        $options = [
            'fields'     => [
                'id',
                'order_datetime'
            ],
            'conditions' => [
                'team_id'           => $teamId,
                'order_datetime >=' => $dateStart,
                'order_datetime <=' => $dateEnd,
            ],
        ];
        return $this->find('first', $options);
    }

    /**
     * Filter: charge history ids
     * // [Note] currently this method is not called anywhere, but this will be used in near future
     * Called: inner DetectInconsistentChargeShell.checkAtobaraiComAmountEachHistory method
     *
     * @param array $chargeHistoryIds
     *
     * @return array
     */
    public function findEachChargeHistoryId(array $chargeHistoryIds): array
    {
        $options = [
            'fields'     => [
                'InvoiceHistoriesChargeHistory.charge_history_id',
                'InvoiceHistory.system_order_code',
            ],
            'conditions' => [
                'InvoiceHistory.del_flg' => false
            ],
            'join'       => [
                [
                    'type'       => 'INNER',
                    'table'      => 'invoice_histories_charge_histories',
                    'alias'      => 'InvoiceHistoriesChargeHistory',
                    'conditions' => [
                        'InvoiceHistory.id = InvoiceHistoriesChargeHistory.invoice_history_id',
                        'InvoiceHistoriesChargeHistory.charge_history_id' => $chargeHistoryIds,
                        'InvoiceHistoriesChargeHistory.del_flg'           => false,
                    ]
                ],
            ]
        ];
        $res = $this->find('all', $options);
        if (empty($res)) {
            return [];
        }
        return Hash::combine($res, '{n}.ChargeHistory.charge_history_id', '{n}.ChargeHistory.system_order_code');
    }

    /**
     * Return list of invoice history by their order status
     *
     * @param int $creditStatus
     *
     * @return array
     */
    public function getByOrderStatus(int $creditStatus): array
    {
        $options = [
            'conditions' => [
                'order_status' => $creditStatus,
            ],
        ];
        return $this->find('all', $options);
    }
}
