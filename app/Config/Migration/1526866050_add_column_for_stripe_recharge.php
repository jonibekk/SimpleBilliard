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
					'reorder_charge_history_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'charge_histories.id that is target to be reordered', 'after' => 'stripe_payment_code'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'charge_histories' => array('reorder_charge_history_id'),
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
