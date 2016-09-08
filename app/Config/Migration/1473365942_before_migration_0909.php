<?php
class BeforeMigration0909 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'before_migration_0909';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'labels' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'labels' => array(
					'name' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
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
