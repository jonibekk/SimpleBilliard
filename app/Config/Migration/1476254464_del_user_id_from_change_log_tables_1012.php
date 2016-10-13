<?php
class DelUserIdFromChangeLogTables1012 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'del_user_id_from_change_log_tables_1012';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_field' => array(
				'goal_change_logs' => array('user_id', 'indexes' => array('user_id')),
				'tkr_change_logs' => array('user_id', 'indexes' => array('user_id')),
			),
		),
		'down' => array(
			'create_field' => array(
				'goal_change_logs' => array(
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
					'indexes' => array(
						'user_id' => array('column' => 'user_id', 'unique' => 0),
					),
				),
				'tkr_change_logs' => array(
					'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ID(belongsToでUserモデルに関連)'),
					'indexes' => array(
						'user_id' => array('column' => 'user_id', 'unique' => 0),
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
