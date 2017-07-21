<?php
class RenameTablePaymemtToPayment0721 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'rename_table_paymemt_to_payment_0721';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'payment_setting_change_logs' => array(
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
			),
			'drop_table' => array(
				'paymemt_setting_change_logs'
			),
		),
		'down' => array(
			'drop_table' => array(
				'payment_setting_change_logs'
			),
			'create_table' => array(
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
