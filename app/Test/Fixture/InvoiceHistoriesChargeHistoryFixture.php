<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * InvoiceHistoriesChargeHistory Fixture
 */
class InvoiceHistoriesChargeHistoryFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id'                 => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary',
            'comment'  => 'ID'
        ],
        'invoice_history_id' => ['type'     => 'biginteger',
                                 'null'     => false,
                                 'default'  => null,
                                 'unsigned' => true,
                                 'key'      => 'index',
                                 'comment'  => 'belongsTo InvoiceHistory'
        ],
        'charge_history_id'  => ['type'     => 'biginteger',
                                 'null'     => false,
                                 'default'  => null,
                                 'unsigned' => true,
                                 'key'      => 'index',
                                 'comment'  => 'belongsTo ChargeHistory'
        ],
        'del_flg'            => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'            => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'            => ['type'     => 'integer',
                                 'null'     => true,
                                 'default'  => null,
                                 'unsigned' => true,
                                 'key'      => 'index'
        ],
        'modified'           => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'            => [
            'PRIMARY'              => ['column' => 'id', 'unique' => 1],
            'invoice_history_id_2' => ['column' => ['invoice_history_id', 'charge_history_id'], 'unique' => 1],
            'created'              => ['column' => 'created', 'unique' => 0],
            'invoice_history_id'   => ['column' => 'invoice_history_id', 'unique' => 0],
            'charge_history_id'    => ['column' => 'charge_history_id', 'unique' => 0]
        ],
        'tableParameters'    => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
    ];

}
