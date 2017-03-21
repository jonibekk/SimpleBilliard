<?php
class AddAttachedFileCount0317 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_attached_file_count_0317';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'messages' => array(
					'attached_file_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'after' => 'target_user_ids'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'messages' => array('attached_file_count'),
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
