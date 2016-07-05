<?php
class AddColumnsIntoDevices extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_columns_into_devices';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'devices' => array(
					'installation_id' => array('type' => 'string', 'null' => false, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリインストール毎に発行される識別子', 'charset' => 'utf8mb4', 'after' => 'device_token'),
					'version' => array('type' => 'string', 'null' => false, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリバージョン', 'charset' => 'utf8mb4', 'after' => 'installation_id'),
					'indexes' => array(
						'device_token' => array('column' => 'device_token', 'unique' => 0, 'length' => array('191')),
						'installation_id' => array('column' => 'installation_id', 'unique' => 0, 'length' => array('191')),
					),
				),
			),
			'alter_field' => array(
				'devices' => array(
					'device_token' => array('type' => 'string', 'null' => false, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'nitfy cloud id', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'devices' => array('installation_id', 'version', 'indexes' => array('device_token', 'installation_id')),
			),
			'alter_field' => array(
				'devices' => array(
					'device_token' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'アプリインストール毎に発行される識別子', 'charset' => 'utf8mb4'),
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
