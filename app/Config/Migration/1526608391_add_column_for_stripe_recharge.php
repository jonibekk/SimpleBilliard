<?php
class AddColumnForStripeRecharge extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_for_stripe_recharge';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'charge_histories' => array(
					'reorder_stripe_payment_code' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'stripe payment id. when reordering stripe charge', 'charset' => 'utf8mb4', 'after' => 'stripe_payment_code'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'charge_histories' => array('reorder_stripe_payment_code'),
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
