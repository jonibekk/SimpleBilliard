<?php
class AddDuringSetupFlgToUsers0405 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_during_setup_flg_to_users_0405';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'during_setup_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0', 'after' => 'middle_name'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('during_setup_flg'),
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
