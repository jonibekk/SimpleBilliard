<?php
class DropGoalProgressDailyLogs extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'drop_goal_progress_daily_logs';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'drop_table' => array(
				'goal_progress_daily_logs'
			),
		),
		'down' => array(
			'create_table' => array(
				'goal_progress_daily_logs' => array(
					'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
					'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
					'goal_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'ゴールID(belongsToでGoalモデルに関連)'),
					'progress' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'comment' => '進捗率%(0-100)'),
					'target_date' => array('type' => 'date', 'null' => false, 'default' => null, 'key' => 'primary', 'comment' => '対象の日付'),
					'created' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					'modified' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true),
					'indexes' => array(
						'PRIMARY' => array('column' => array('id', 'target_date'), 'unique' => 1),
						'goal_id_target_date_unique' => array('column' => array('goal_id', 'target_date'), 'unique' => 1),
						'target_date' => array('column' => 'target_date', 'unique' => 0),
						'team_id' => array('column' => 'team_id', 'unique' => 0),
						'goal_id' => array('column' => 'goal_id', 'unique' => 0),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
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
