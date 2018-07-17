<?php
class AddColumnForLatestPostTime extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'n';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'circles' => array(
					'latest_post_created' => array('type' => 'integer', 'null' => true, 'default' => 0, 'unsigned' => true, 'after' => 'circle_member_count'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'circles' => array('latest_post_created'),
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
