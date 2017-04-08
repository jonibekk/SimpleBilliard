<?php
class FixCommentOnly0313 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fix_comment_only_0313';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'topic_members' => array(
					'last_read_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'message_id as last read.'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'topic_members' => array(
					'last_read_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'message_id as last seen'),
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
