<?php
class ChangeForPayment0718 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_for_payment_0718';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'admin_activity_logs' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'admin_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'data' => array('type' => 'binary', 'null' => false, 'default' => null),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'admin_user_id' => array('column' => 'admin_user_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'admin_users' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'email' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Admin user email for signin'),
					'password' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'charset' => 'utf8mb4', 'comment' => 'Admin user password'),
					'name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'charset' => 'utf8mb4', 'comment' => 'Admin user name'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'charge_histories' => array(
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
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'credit_cards' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
					'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
					'customer_code' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Customer id by stripe'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'invoices' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
					'credit_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => '与信審査ステータス(0: 審査待ち,1: 与信OK,2: 与信NG)'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'paymemt_setting_change_logs' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'length' => 11, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'payment_setting_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true),
					'data' => array('type' => 'binary', 'null' => false, 'default' => null, 'comment' => '変更後のスナップショット'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'user_id' => array('column' => 'user_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
				'payment_settings' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'charge type(0: Invoice, 1: Credit card)'),
					'amount_per_user' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => true, 'comment' => 'Service use amount per user'),
					'payer_name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Payer name'),
					'company_name' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Company name'),
					'company_address' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Company address'),
					'company_tel' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Company tel number'),
					'payment_base_day' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Payment base day(1 - 31)'),
					'email' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'comment' => 'Payer email'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'created' => array('column' => 'created', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'team_members' => array(
					'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクティブステータス(0: 招待中,1: アクティブ,2: インアクティブ)', 'after' => 'comment'),
				),
				'teams' => array(
					'service_use_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => 'サービス利用ステータス(0: free trial,1: payed,2: read only,3: service expired)', 'after' => 'timezone'),
					'country' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '国コード', 'after' => 'service_use_status'),
					'address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4', 'after' => 'country'),
					'service_use_state_start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '各ステートの開始日', 'after' => 'address'),
					'free_trial_days' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'フリートライアル日数', 'after' => 'service_use_state_start_date'),
					'indexes' => array(
						'service_use_status' => array('column' => 'service_use_status', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
				'admin_activity_logs', 'admin_users', 'charge_histories', 'credit_cards', 'invoices', 'paymemt_setting_change_logs', 'payment_settings'
			),
			'drop_field' => array(
				'team_members' => array('status'),
				'teams' => array('service_use_status', 'country', 'address', 'service_use_state_start_date', 'free_trial_days', 'indexes' => array('service_use_status')),
			),
		),
	);

/**
 * Before migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction Direction of migration process (up or down)
 * @return bool Should process continue
 */
	public function after($direction) {
		return true;
	}
}
