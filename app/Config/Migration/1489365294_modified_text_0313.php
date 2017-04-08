<?php
class ModifiedText0313 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'modified_text_0313';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'messages' => array(
					'target_user_ids_if_member_changed' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'comma spalated list for target users(e.g. 1,2,3) if add or remove members.', 'charset' => 'utf8mb4'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'messages' => array(
					'target_user_ids_if_member_changed' => array('type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => 'comma spalated list for target users(e.g. 1,2,3) if add or remove members.', 'charset' => 'utf8mb4'),
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
