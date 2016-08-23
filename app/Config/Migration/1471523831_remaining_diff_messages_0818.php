<?php
class RemainingDiffMessages0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'remaining_diff_messages_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'messages' => array(
					'created' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'messages' => array(
					'created' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'key' => 'primary'),
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
