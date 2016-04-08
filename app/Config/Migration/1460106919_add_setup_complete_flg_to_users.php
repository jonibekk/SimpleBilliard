<?php
class AddSetupCompleteFlgToUsers extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_setup_complete_flg_to_users';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'users' => array(
					'setup_complete_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'after' => 'middle_name'),
				),
			),
			'drop_field' => array(
				'users' => array('during_setup_flg'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'users' => array('setup_complete_flg'),
			),
			'create_field' => array(
				'users' => array(
					'during_setup_flg' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
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
