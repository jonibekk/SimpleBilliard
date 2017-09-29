<?php
class ChangeCountryIntToVarchar0724 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_country_int_to_varchar_0724';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'teams' => array(
					'country' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 2, 'collate' => 'utf8mb4_general_ci', 'comment' => '国コード', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'teams' => array(
					'country' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '国コード'),
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
