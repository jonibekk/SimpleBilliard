<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * CreditCard Fixture
 */
class CreditCardFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                 => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'            => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
        'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
        'customer_code'      => array('type'    => 'string',
                                      'null'    => false,
                                      'default' => null,
                                      'collate' => 'utf8mb4_general_ci',
                                      'comment' => 'Customer id by stripe',
                                      'charset' => 'utf8mb4'
        ),
        'del_flg'            => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'            => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'modified'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'            => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1)
        ),
        'tableParameters'    => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id'                 => 1,
            'team_id'            => 1,
            'payment_setting_id' => 1,
            'customer_code'      => 'cus_XXXXXXXXXX',
            'del_flg'            => 0,
            'deleted'            => null,
            'created'            => 1500000000,
            'modified'           => 1500000000,
        ],
        [
            'id'                 => 2,
            'team_id'            => 2,
            'payment_setting_id' => 2,
            'customer_code'      => 'cus_XXXXXXXXXX',
            'del_flg'            => 0,
            'deleted'            => null,
            'created'            => 1500000000,
            'modified'           => 1500000000,
        ],
    ];

}
