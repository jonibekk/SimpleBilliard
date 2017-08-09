<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * PaymentSetting Fixture
 */
class PaymentSettingFixture extends CakeTestFixtureEx
{
    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'           => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'type'              => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => 'charge type(0: Invoice, 1: Credit card)'
        ),
        'currency'          => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'currencty type(ex 1: yen, 2: US Doller...)'
        ),
        'amount_per_user'   => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'length'   => 10,
            'unsigned' => true,
            'comment'  => 'Service use amount per user'
        ),
        'payer_name'        => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Payer name',
            'charset' => 'utf8mb4'
        ),
        'company_name'      => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company name',
            'charset' => 'utf8mb4'
        ),
        'company_country'   => array(
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 2,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company address(country)',
            'charset' => 'utf8mb4'
        ),
        'company_post_code' => array('type'    => 'string',
                                     'null'    => false,
                                     'default' => null,
                                     'length'  => 16,
                                     'collate' => 'utf8mb4_general_ci',
                                     'comment' => 'Company address(post_code)',
                                     'charset' => 'utf8mb4'
        ),
        'company_region'    => array('type'    => 'string',
                                     'null'    => false,
                                     'default' => null,
                                     'collate' => 'utf8mb4_general_ci',
                                     'comment' => 'Company address(region)',
                                     'charset' => 'utf8mb4'
        ),
        'company_city'      => array('type'    => 'string',
                                     'null'    => false,
                                     'default' => null,
                                     'collate' => 'utf8mb4_general_ci',
                                     'comment' => 'Company address(city)',
                                     'charset' => 'utf8mb4'
        ),
        'company_street'    => array('type'    => 'string',
                                     'null'    => false,
                                     'default' => null,
                                     'collate' => 'utf8mb4_general_ci',
                                     'comment' => 'Company address(street)',
                                     'charset' => 'utf8mb4'
        ),
        'company_tel'       => array('type'    => 'string',
                                     'null'    => false,
                                     'default' => null,
                                     'collate' => 'utf8mb4_general_ci',
                                     'comment' => 'Company tel number',
                                     'charset' => 'utf8mb4'
        ),
        'payment_base_day'  => array('type'     => 'integer',
                                     'null'     => false,
                                     'default'  => null,
                                     'unsigned' => true,
                                     'comment'  => 'Payment base day(1 - 31)'
        ),
        'email'             => array('type'    => 'string',
                                     'null'    => false,
                                     'default' => null,
                                     'collate' => 'utf8mb4_general_ci',
                                     'comment' => 'Payer email',
                                     'charset' => 'utf8mb4'
        ),
        'del_flg'           => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'           => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'created'           => array('type'     => 'integer',
                                     'null'     => true,
                                     'default'  => null,
                                     'unsigned' => true,
                                     'key'      => 'index'
        ),
        'modified'          => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
        'indexes'           => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0),
            'created' => array('column' => 'created', 'unique' => 0)
        ),
        'tableParameters'   => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array();

}
