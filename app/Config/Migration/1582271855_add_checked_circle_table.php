<?php
class AddCheckedCirclesTbl extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'AddCheckedCirclesTbl';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_table' => array(
				'checked_circles' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'circle_id'  => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '= circles.id'),
					'user_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '= users.id'),
					'team_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '= teams.id'),
					'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
					'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => 'id', 'unique' => 1),
						'circle_user' => array('column' => array('circle_id', 'user_id'), 'unique' => 0),
						'paging_index' => array('column' => array('team_id', 'user_id', 'id'), 'unique' => 0),
						'tuple' => array('column' => array('team_id', 'circle_id', 'user_id'),'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine'  => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_table' => array(
                'checked_circles'
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
