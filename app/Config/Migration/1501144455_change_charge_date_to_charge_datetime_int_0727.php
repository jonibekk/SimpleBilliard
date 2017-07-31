<?php
class ChangeChargeDateToChargeDatetimeInt0727 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_charge_date_to_charge_datetime_int_0727';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'charge_histories' => array(
					'charge_datetime' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => 'Charge datetime unix timestamp', 'after' => 'currency'),
				),
			),
			'drop_field' => array(
				'charge_histories' => array('charge_date'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'charge_histories' => array('charge_datetime'),
			),
			'create_field' => array(
				'charge_histories' => array(
					'charge_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => 'Charge date'),
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
