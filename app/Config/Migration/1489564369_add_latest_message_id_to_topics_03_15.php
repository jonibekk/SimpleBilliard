<?php
class AddLatestMessageIdToTopics0315 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_latest_message_id_to_topics_03_15';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'topics' => array(
					'latest_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'length' => 11, 'unsigned' => true, 'key' => 'index', 'comment' => 'latest message id associated with topic', 'after' => 'title'),
					'indexes' => array(
						'latest_message_id' => array('column' => 'latest_message_id', 'unique' => 0),
						'latest_message_datetime' => array('column' => 'latest_message_datetime', 'unique' => 0),
					),
				),
			),
			'alter_field' => array(
				'topics' => array(
					'latest_message_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'latest messaged datetime associated with topic'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'topics' => array('latest_message_id', 'indexes' => array('latest_message_id', 'latest_message_datetime')),
			),
			'alter_field' => array(
				'topics' => array(
					'latest_message_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'latest messaged datetime associated with topic'),
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
