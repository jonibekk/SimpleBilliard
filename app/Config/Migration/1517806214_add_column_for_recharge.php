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
				'charge_histories' => array(
					'recharge_target_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'recharge target charge_histories.id', 'after' => 'price_plan_purchase_team_id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'charge_histories' => array('recharge_target_id'),
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
