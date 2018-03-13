<?php
class AddColumnToCircleMembersCircleOrderCsv20180313 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_column_to_circle_members_circle_order_csv_20180313';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_table' => array(
				'circle_pins'
			),
		),
		'down' => array(
			'create_table' => array(
				'circle_pins' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true),
					'team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true),
					'circle_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true),
					'pin_order' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true),
					'del_flg' => array('type' => 'boolean', 'null' => true, 'default' => null),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'InnoDB'),
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
