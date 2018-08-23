<?php
class AddedLastPostedToCircleMembers extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'Added_last_posted_to_circle_members';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'circle_members' => array(
					'last_posted' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => true, 'after' => 'get_notification_flg'),
				),
			),
			'alter_field' => array(
				'circles' => array(
					'latest_post_created' => array('type' => 'integer', 'null' => true, 'default' => '0', 'unsigned' => true),
				),
			),
			'create_table' => array(
				'test' => array(
					'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'type' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
					'indexes' => array(
						'PRIMARY' => array('column' => array('id', 'type'), 'unique' => 1),
					),
					'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'circle_members' => array('last_posted'),
			),
			'alter_field' => array(
				'circles' => array(
					'latest_post_created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true),
				),
			),
			'drop_table' => array(
				'test'
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
