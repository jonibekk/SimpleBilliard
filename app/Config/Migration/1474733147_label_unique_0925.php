<?php
class LabelUnique0925 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'label_unique_0925';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'labels' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'key' => 'index', 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
				),
			),
			'create_field' => array(
				'labels' => array(
					'indexes' => array(
						'unique_name_team_id' => array('column' => array('name', 'team_id'), 'unique' => 1),
					),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'labels' => array(
					'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
				),
			),
			'drop_field' => array(
				'labels' => array('indexes' => array('unique_name_team_id')),
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
