<?php
App::uses('AppModel', 'Model');

/**
 * InvoiceHistoriesChargeHistory Model
 *
 * @property InvoiceHistory $InvoiceHistory
 * @property ChargeHistory  $ChargeHistory
 */
class InvoiceHistoriesChargeHistory extends AppModel
{

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = [
        'del_flg' => [
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
        'InvoiceHistory',
        'ChargeHistory',
    ];
}
