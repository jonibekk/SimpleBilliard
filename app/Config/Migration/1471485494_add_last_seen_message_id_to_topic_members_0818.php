<?php
class AddLastSeenMessageIdToTopicMembers0818 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_last_seen_message_id_to_topic_members_0818';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'topic_members' => array(
					'last_seen_message_id' => array('type' => 'biginteger', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '読んだ最後のmessage_id', 'after' => 'team_id'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'topic_members' => array('last_seen_message_id'),
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
