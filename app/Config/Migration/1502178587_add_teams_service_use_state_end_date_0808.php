<?php
class AddTeamsServiceUseStateEndDate0808 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_teams_service_use_state_end_date_0808';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'teams' => array(
					'service_use_state_end_date' => array('type' => 'date', 'null' => false, 'default' => null, 'comment' => '各ステートの終了日', 'after' => 'service_use_state_start_date'),
				),
			),
			'drop_field' => array(
				'teams' => array('free_trial_days'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'teams' => array('service_use_state_end_date'),
			),
			'create_field' => array(
				'teams' => array(
					'free_trial_days' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'フリートライアル日数'),
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
