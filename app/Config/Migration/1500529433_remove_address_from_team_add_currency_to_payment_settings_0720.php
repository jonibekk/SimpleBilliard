<?php
class RemoveAddressFromTeamAddCurrencyToPaymentSettings0720 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'remove_address_from_team_add_currency_to_payment_settings_0720';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'payment_settings' => array(
					'currency' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'currencty type(ex 1: yen, 2: US Doller...)', 'after' => 'type'),
				),
			),
			'drop_field' => array(
				'teams' => array('address'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'payment_settings' => array('currency'),
			),
			'create_field' => array(
				'teams' => array(
					'address' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'charset' => 'utf8mb4'),
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
