<?php
class AddOpeUserIdToTeamsTable extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_ope_user_id_to_teams_table';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'teams' => array(
					'ope_user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'チーム情報変更者', 'after' => 'pre_register_amount_per_user'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'teams' => array('ope_user_id'),
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
