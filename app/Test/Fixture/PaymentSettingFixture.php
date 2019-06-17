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
    public $fields = [
        'id'                             => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ],
        'team_id'                        => [
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ],
        'type'                           => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'unsigned' => true,
            'comment'  => 'charge type(0: Invoice, 1: Credit card)'
        ],
        'currency'                       => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'currencty type(ex 1: yen, 2: US Doller...)'
        ],
        'amount_per_user'                => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => '0',
            'length'   => 10,
            'unsigned' => true,
            'comment'  => 'Service use amount per user'
        ], 'start_date'                  => [
            'type'    => 'date',
            'null'    => false,
            'default' => null,
            'comment' => 'paid plan start date(team timezone)'
        ],
        'payment_base_day'               => [
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'Payment base day(1 - 31)'
        ],
        'payment_skip_flg'               => [
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0'
        ],
        'company_name'                   => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company name',
            'charset' => 'utf8mb4'
        ],
        'company_country'                => [
            'type'    => 'string',
            'null'    => false,
            'length'  => 2,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company address(country)',
            'charset' => 'utf8mb4'
        ],
        'company_post_code'              => [
            'type'    => 'string',
            'null'    => false,
            'length'  => 16,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company address(post_code)',
            'charset' => 'utf8mb4'
        ],
        'company_region'                 => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company address(region)',
            'charset' => 'utf8mb4'
        ],
        'company_city'                   => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company address(city)',
            'charset' => 'utf8mb4'
        ],
        'company_street'                 => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company address(street)',
            'charset' => 'utf8mb4'
        ],
        'company_tel'                    => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 20,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Company tel number',
            'charset' => 'utf8mb4'
        ],
        'contact_person_first_name'      => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Contact person.first name',
            'charset' => 'utf8mb4'
        ],
        'contact_person_first_name_kana' => [
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Contact person.first name kana',
            'charset' => 'utf8mb4'
        ],
        'contact_person_last_name'       => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Contact person.last name',
            'charset' => 'utf8mb4'
        ],
        'contact_person_last_name_kana'  => [
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'length'  => 128,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Contact person.last name kana',
            'charset' => 'utf8mb4'
        ],
        'contact_person_tel'             => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'length'  => 20,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Contact person.tel number',
            'charset' => 'utf8mb4'
        ],
        'contact_person_email'           => [
            'type'    => 'string',
            'null'    => false,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'Contact person.email address',
            'charset' => 'utf8mb4'
        ],
        'del_flg'                        => [
            'type'    => 'boolean',
            'null'    => false,
            'default' => '0'
        ],
        'deleted'                        => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ],
        'created'                        => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ],
        'modified'                       => [
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ],
        'indexes'                        => [
            'PRIMARY' => ['column' => 'id', 'unique' => 1],
            'team_id' => ['column' => 'team_id', 'unique' => 0],
            'created' => ['column' => 'created', 'unique' => 0]
        ],
        'tableParameters'                => [
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ]
    ];

    /**
     * Records
     *
     * @var array
     */
    public $records = array();

}
