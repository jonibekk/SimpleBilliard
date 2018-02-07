<?php
class AddColumnForRecharge extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_for_recharge';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'invoice_histories' => array(
					'reorder_target_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '再注文対象の後払い.com注文ID', 'charset' => 'utf8mb4', 'after' => 'order_status'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'invoice_histories' => array('reorder_target_code'),
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
