<?php

App::uses('CakeTestFixtureEx', 'Test/Fixture');
/**
 * CheckedCircle Fixture
 */
class CircleDiscoverTabOpenedUserFixture extends CakeTestFixtureEx {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
		'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'comment' => '= teams.id'),
		'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
		'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'paging_index' => array('column' => array('team_id', 'user_id', 'id'), 'unique' => 0),
			'tuple' => array('column' => array('team_id', 'user_id'),'unique' => 1),
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine'  => 'InnoDB'),
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'user_id' => '1',
			'team_id' => '1',
			'del_flg' => 0,
			'deleted' => null,
			'created' => 1,
			'modified' => 1
		),
		array(
			'id' => '2',
			'user_id' => '2',
			'team_id' => '1',
			'del_flg' => 0,
			'deleted' => null,
			'created' => 1,
			'modified' => 1
		),
		array(
			'id' => '3',
			'user_id' => '1',
			'team_id' => '2',
			'del_flg' => 0,
			'deleted' => null,
			'created' => 1,
			'modified' => 1
		),
		array(
			'id' => '4',
			'user_id' => '3',
			'team_id' => '1',
			'del_flg' => 0,
			'deleted' => null,
			'created' => 1,
			'modified' => 1
		),
	);

}
