<?php
class AddColumnToMessagesTable0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_to_messages_table_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'messages' => array(
					'target_user_ids_if_member_changed' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '変更したユーザのuser_idをカンマ区切りで指定', 'charset' => 'utf8mb4', 'after' => 'type'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'messages' => array('target_user_ids_if_member_changed'),
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
