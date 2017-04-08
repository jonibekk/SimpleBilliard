<?php
class ChangeLatestMessageIdNoLengthInTopics0315 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'change_latest_message_id_no_length_in_topics_0315';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'alter_field' => array(
				'topics' => array(
					'latest_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'latest message id associated with topic'),
				),
			),
		),
		'down' => array(
			'alter_field' => array(
				'topics' => array(
					'latest_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => 11, 'unsigned' => true, 'key' => 'index', 'comment' => 'latest message id associated with topic'),
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
