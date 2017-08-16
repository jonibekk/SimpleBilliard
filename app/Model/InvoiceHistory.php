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
}
