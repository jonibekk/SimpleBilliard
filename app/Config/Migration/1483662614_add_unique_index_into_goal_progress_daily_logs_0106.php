<?php
class AddUniqueIndexIntoGoalProgressDailyLogs0106 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_unique_index_into_goal_progress_daily_logs_0106';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'goal_progress_daily_logs' => array(
					'indexes' => array(
						'goal_id_target_date_unique' => array('column' => array('goal_id', 'target_date'), 'unique' => 1),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'goal_progress_daily_logs' => array('indexes' => array('goal_id_target_date_unique')),
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
