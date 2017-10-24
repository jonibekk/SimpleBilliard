<?php
class AddPricePlanIdToPricePlanPurchaseTeams extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_price_plan_id_to_price_plan_purchase_teams';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'price_plan_purchase_teams' => array(
					'price_plan_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'after' => 'team_id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'price_plan_purchase_teams' => array('price_plan_id'),
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
