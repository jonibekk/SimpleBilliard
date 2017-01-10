<?php
class AddLatestActionedDateToKeyResults0110 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_latest_actioned_date_to_key_results_0110';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'key_results' => array(
					'latest_actioned_date' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '最新アクション日時(unixtime)', 'after' => 'action_result_count'),
					'indexes' => array(
						'latest_actioned_date' => array('column' => 'latest_actioned_date', 'unique' => 0),
					),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'key_results' => array('latest_actioned_date', 'indexes' => array('latest_actioned_date')),
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
