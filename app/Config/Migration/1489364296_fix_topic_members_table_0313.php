<?php
class FixTopicMembersTable0313 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'fix_topic_members_table_0313';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'topic_members' => array(
					'last_read_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'message_id as last seen', 'after' => 'team_id'),
					'last_message_sent' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'It\'s update when member send message.', 'after' => 'last_read_message_id'),
					'indexes' => array(
						'last_read_message_id' => array('column' => 'last_read_message_id', 'unique' => 0),
						'last_message_sent' => array('column' => 'last_message_sent', 'unique' => 0),
					),
				),
			),
			'drop_field' => array(
				'topic_members' => array('last_seen_message_id'),
			),
		),
		'down' => array(
			'drop_field' => array(
				'topic_members' => array('last_read_message_id', 'last_message_sent', 'indexes' => array('last_read_message_id', 'last_message_sent')),
			),
			'create_field' => array(
				'topic_members' => array(
					'last_seen_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'message_id as last seen'),
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
