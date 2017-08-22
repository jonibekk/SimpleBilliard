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
        'order_date'        => [
            'date' => [
                'rule' => ['date'],
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
        $options = [
            'fields'     => [
                'id',
                'order_date'
            ],
            'conditions' => [
                'team_id'    => $teamId,
                'order_date' => $date,
            ],
        ];
        return $this->find('first', $options);
    }

}
