<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * ChargeHistory Fixture
 */
class ChargeHistoryFixture extends CakeTestFixtureEx
{

    /**
     * Fields
     *
     * @var array
     */
    public $fields = array(
        'id'                          => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'primary'
        ),
        'team_id'                     => array(
            'type'     => 'biginteger',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'key'      => 'index'
        ),
        'user_id'                     => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '請求操作実行ユーザー'
        ),
        'payment_type'                => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '支払いタイプ(0: 請求書,1: クレジットカード)'
        ),
        'charge_type'                 => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => '請求タイプ(0: 毎月支払い,1: ユーザー追加,2: ユーザーアクティブ化)'
        ),
        'amount_per_user'             => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'Service use amount per user in a charge'
        ),
        'total_amount'                => array(
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.00',
            'length'   => '17,2',
            'unsigned' => false,
            'comment'  => 'total amount excluding tax in a charge'
        ),
        'tax'                         => array(
            'type'     => 'decimal',
            'null'     => false,
            'default'  => '0.00',
            'length'   => '17,2',
            'unsigned' => false,
            'comment'  => 'tax in a charge'
        ),
        'charge_users'                => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'Charge user number'
        ),
        'currency'                    => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'Team country currency'
        ),
        'charge_datetime'             => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'Charge datetime unix timestamp'
        ),
        'result_type'                 => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'Result type(0: Success, 1,2,3...: Failuer each type)'
        ),
        'max_charge_users'            => array(
            'type'     => 'integer',
            'null'     => false,
            'default'  => null,
            'unsigned' => true,
            'comment'  => 'チャージした結果のmax支払い人数'
        ),
        'stripe_payment_code'         => array(
            'type'    => 'string',
            'null'    => true,
            'default' => null,
            'collate' => 'utf8mb4_general_ci',
            'comment' => 'stripe payment id. if invoice, it will be null',
            'charset' => 'utf8mb4'
        ),
        'campaign_team_id'            => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => false,
            'comment'  => 'campaign_team.id'
        ),
        'price_plan_purchase_team_id' => array(
            'type'     => 'biginteger',
            'null'     => true,
            'default'  => null,
            'unsigned' => false,
            'comment'  => 'price_plan_purchase_teams.id'
        ),
        'del_flg'                     => array('type' => 'boolean', 'null' => false, 'default' => '0'),
        'deleted'                     => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'created'                     => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'modified'                    => array(
            'type'     => 'integer',
            'null'     => true,
            'default'  => null,
            'unsigned' => true
        ),
        'indexes'                     => array(
            'PRIMARY' => array('column' => 'id', 'unique' => 1),
            'team_id' => array('column' => 'team_id', 'unique' => 0)
        ),
        'tableParameters'             => array(
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        )
    );

    /**
     * Records
     *
     * @var array
     */
    public $records = array();
}