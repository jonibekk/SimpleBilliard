<?php
class AddPaymentTables extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_payment_tables';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'evaluate_terms' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => 'ID'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '評価対象期間の開始日'),
					'end_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '評価対象期間の終了日'),
					'evaluate_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '評価ステータス(0 = 評価開始前, 1 = 評価中,2 = 評価凍結中, 3 = 最終評価終了)'),
					'timezone' => array('type' => 'float', 'null' => true, 'default' => null, 'unsigned' => false, 'comment' => '評価期間のタイムゾーン'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'created' => array('column' => 'created', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
			'create_field' => array(
				'evaluations' => array(
					'evaluate_term_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index', 'comment' => '評価対象期間ID(belongsToでEvaluateTermモデルに関連)', 'after' => 'term_id'),
					'indexes' => array(
						'evaluate_term_id' => array('column' => 'evaluate_term_id', 'unique' => 0),
					),
				),
				'goals' => array(
					'old_start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日', 'after' => 'end_date'),
					'old_end_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日', 'after' => 'old_start_date'),
				),
				'key_results' => array(
					'old_start_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '開始日', 'after' => 'end_date'),
					'old_end_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '終了日', 'after' => 'old_start_date'),
				),
				'post_share_circles' => array(
					'indexes' => array(
						'PRIMARY' => array('column' => array('id', 'modified'), 'unique' => 1),
					),
				),
				'post_share_users' => array(
					'indexes' => array(
						'PRIMARY' => array('column' => array('id', 'modified'), 'unique' => 1),
					),
				),
				'posts' => array(
					'indexes' => array(
						'PRIMARY' => array('column' => array('id', 'modified'), 'unique' => 1),
					),
				),
			),
			'alter_field' => array(
				'post_share_circles' => array(
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を更新した日付時刻'),
				),
				'post_share_users' => array(
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を更新した日付時刻'),
				),
				'posts' => array(
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を更新した日付時刻'),
				),
			),
			'drop_field' => array(
				'post_share_circles' => array('indexes' => array('PRIMARY')),
				'post_share_users' => array('indexes' => array('PRIMARY')),
				'posts' => array('indexes' => array('PRIMARY')),
				'team_members' => array('status'),
				'teams' => array('service_use_status', 'country', 'service_use_state_start_date', 'free_trial_days', 'indexes' => array('service_use_status')),
			),
			'drop_table' => array(
				'admin_activity_logs', 'admin_users', 'charge_histories', 'credit_cards', 'invoices', 'paymemt_setting_change_logs', 'payment_settings'
			),
		),
		'down' => array(
			'drop_table' => array(
				'evaluate_terms'
			),
			'drop_field' => array(
				'evaluations' => array('evaluate_term_id', 'indexes' => array('evaluate_term_id')),
				'goals' => array('old_start_date', 'old_end_date'),
				'key_results' => array('old_start_date', 'old_end_date'),
				'post_share_circles' => array('indexes' => array('PRIMARY')),
				'post_share_users' => array('indexes' => array('PRIMARY')),
				'posts' => array('indexes' => array('PRIMARY')),
			),
			'alter_field' => array(
				'post_share_circles' => array(
					'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
				),
				'post_share_users' => array(
					'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
				),
				'posts' => array(
					'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary', 'comment' => '投稿を追加した日付時刻'),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => '投稿を更新した日付時刻'),
				),
			),
			'create_field' => array(
				'post_share_circles' => array(
					'indexes' => array(
						'PRIMARY' => array(),
					),
				),
				'post_share_users' => array(
					'indexes' => array(
						'PRIMARY' => array(),
					),
				),
				'posts' => array(
					'indexes' => array(
						'PRIMARY' => array(),
					),
				),
				'team_members' => array(
					'status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'アクティブステータス(0: 招待中,1: アクティブ,2: インアクティブ)'),
				),
				'teams' => array(
					'service_use_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'index', 'comment' => 'サービス利用ステータス(0: free trial,1: payed,2: read only,3: service expired)'),
					'country' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '国コード'),
					'service_use_state_start_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '各ステートの開始日'),
					'free_trial_days' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'フリートライアル日数'),
					'indexes' => array(
						'service_use_status' => array('column' => 'service_use_status', 'unique' => 0),
					),
				),
			),
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
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user email for signin', 'charset' => 'utf8mb4'),
					'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user password', 'charset' => 'utf8mb4'),
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user name', 'charset' => 'utf8mb4'),
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
					'customer_code' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Customer id by stripe', 'charset' => 'utf8mb4'),
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
					'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'currencty type(ex 1: yen, 2: US Doller...)'),
					'amount_per_user' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10, 'unsigned' => true, 'comment' => 'Service use amount per user'),
					'payer_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Payer name', 'charset' => 'utf8mb4'),
					'company_name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company name', 'charset' => 'utf8mb4'),
					'company_address' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company address', 'charset' => 'utf8mb4'),
					'company_tel' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Company tel number', 'charset' => 'utf8mb4'),
					'payment_base_day' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Payment base day(1 - 31)'),
					'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Payer email', 'charset' => 'utf8mb4'),
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
