<?php
class AddLatestMessagedDatatimeToTopics0315 extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 */
	public $description = 'add_latest_messaged_datatime_to_topics_03_15';

/**
 * Actions to be performed
 *
 * @var array $migration
 */
	public $migration = array(
		'up' => array(
			'create_field' => array(
				'topics' => array(
					'latest_message_datetime' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => 'latest messaged datetime associated with topic', 'after' => 'title'),
				),
			),
		),
		'down' => array(
			'drop_field' => array(
				'topics' => array('latest_message_datetime'),
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
