<?php
/**
 * AdminActivityLog Fixture
 */
class AdminActivityLogFixture extends CakeTestFixtureEx {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'admin_user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'data' => array('type' => 'binary', 'null' => false, 'default' => null),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'admin_user_id' => array('column' => 'admin_user_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '',
			'admin_user_id' => '',
			'data' => 'Lorem ipsum dolor sit amet',
			'del_flg' => 1,
			'deleted' => 1,
			'created' => 1,
			'modified' => 1
		),
	);

}
