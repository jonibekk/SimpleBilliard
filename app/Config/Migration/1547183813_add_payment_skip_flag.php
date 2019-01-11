<?php
class AddPaymentSkipFlag extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_payment_skip_flag';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'payment_settings' => array(
					'payment_skip' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'payment_base_day'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'payment_settings' => array('payment_skip'),
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
