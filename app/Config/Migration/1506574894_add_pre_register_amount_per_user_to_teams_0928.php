<?php
class AddPreRegisterAmountPerUserToTeams0928 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_pre_register_amount_per_user_to_teams_0928';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'teams' => array(
					'pre_register_amount_per_user' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'comment' => 'Amount per user before registering payment plan', 'after' => 'service_use_state_end_date'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'teams' => array('pre_register_amount_per_user'),
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
