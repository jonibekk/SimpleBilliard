<?php App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * AdminUser Fixture
 */
class AdminUserFixture extends CakeTestFixtureEx {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'email' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user email for signin', 'charset' => 'utf8mb4'),
		'password' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user password', 'charset' => 'utf8mb4'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Admin user name', 'charset' => 'utf8mb4'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array();

}
