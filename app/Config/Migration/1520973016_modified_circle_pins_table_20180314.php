<?php
class ModifiedCirclePinsTable20180314 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modified_circle_pins_table_20180314';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'circle_pins' => array(
					'circle_orders' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8', 'after' => 'user_id'),
				),
			),
			'drop_field' => array(
				'circle_pins' => array('circle_id', 'orders', 'indexes' => array('circle_id')),
			),
		),
		'down' => array(
			'drop_field' => array(
				'circle_pins' => array('circle_orders'),
			),
			'create_field' => array(
				'circle_pins' => array(
					'circle_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
					'orders' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
					'indexes' => array(
						'circle_id' => array('column' => 'circle_id', 'unique' => 0),
					),
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
