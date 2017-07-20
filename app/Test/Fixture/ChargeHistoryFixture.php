<?php
/**
 * ChargeHistory Fixture
 */
class ChargeHistoryFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'payment_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '支払いタイプ(0: 請求書,1: クレジットカード)'),
		'charge_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '請求タイプ(0: 毎月支払い,1: ユーザー追加,2: ユーザーアクティブ化)'),
		'amount_per_user' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Service use amount per user in a charge'),
		'total_amount' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => 'total amount in a charge'),
		'charge_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Charge user number'),
		'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Team country currency'),
		'charge_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'Charge date'),
		'result_type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Result type(0: Success, 1,2,3...: Failuer each type)'),
		'max_charge_users' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'チャージした結果のmax支払い人数'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'team_id' => array('column' => 'team_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '',
			'team_id' => '',
			'payment_type' => 1,
			'charge_type' => 1,
			'amount_per_user' => 1,
			'total_amount' => 1,
			'charge_users' => 1,
			'currency' => 1,
			'charge_date' => '2017-07-20',
			'result_type' => 1,
			'max_charge_users' => 1,
			'del_flg' => 1,
			'deleted' => 1,
			'created' => 1,
			'modified' => 1
		),
	);

}
