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
